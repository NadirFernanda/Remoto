<?php

namespace App\Modules\Payments\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Gateway AppyPay — Multicaixa Express (GPO, por número de telefone) e
 * Referência de pagamento (REF, paga depois num ATM/Multicaixa/Internet Banking).
 *
 * Documentação fornecida pela AppyPay (Edson Fonseca, comercial@appy.co.ao):
 *   1. getToken()          → autentica via OAuth2 client_credentials (token válido 1h)
 *   2. chargeByPhone()      → POST /v2.0/charges com paymentMethod=GPO_...
 *   3. chargeByReference()  → POST /v2.0/charges com paymentMethod=REF_...
 *   4. getCharge()          → GET /v2.0/charges/{id} — para confirmar o estado
 *   5. mockReferencePayment() → só em sandbox, simula o pagamento de uma referência
 *
 * Timeouts HTTP: confirmado em produção (20/08/2026) que a AppyPay por
 * vezes só responde ao POST /v2.0/charges bem depois de qualquer tempo de
 * espera razoável para um pedido web síncrono — no caso do Multicaixa
 * Express (GPO), o pedido de aprovação chega ao telemóvel do cliente e ele
 * sai da nossa página para o aprovar lá, o que já por si só pode
 * ultrapassar qualquer timeout curto. Por isso chargeByPhone() passa a
 * correr sempre em segundo plano via InitiateAppyPayChargeJob (nunca
 * directamente num pedido web), o que permite um timeout mais generoso
 * (45s) sem bloquear o cliente. chargeByReference() mantém-se síncrono —
 * gerar uma referência não depende de nenhuma acção externa do cliente,
 * por isso não sofre do mesmo problema.
 *
 * getToken() e getCharge() continuam com timeout curto (12s) de propósito:
 * getToken() é sempre rápido e fica em cache 55min; getCharge() é chamado
 * repetidamente (a cada poucos segundos, tanto no ecrã de espera do
 * cliente como no job de polling), por isso um timeout longo aqui deixaria
 * a interface lenta sem necessidade — se falhar, tenta-se de novo no
 * próximo ciclo.
 *
 * Atenção: se algum chamador voltar a fazer chargeByPhone()/chargeByReference()
 * de forma síncrona num pedido web, o timeout de 45s exige
 * max_execution_time do PHP-FPM >= 75s (ver histórico deste ficheiro) —
 * caso contrário o PHP pode matar o processo a meio antes do try/catch
 * conseguir responder com um erro tratado.
 */
class AppyPayGateway
{
    private array $cfg;

    public function __construct()
    {
        $this->cfg = config('services.appypay');

        if (empty($this->cfg['client_id']) || empty($this->cfg['client_secret'])) {
            throw new \RuntimeException(
                'Credenciais AppyPay não configuradas. Defina APPYPAY_CLIENT_ID e APPYPAY_CLIENT_SECRET no ficheiro .env.'
            );
        }
    }

    /** Obtém (e cacheia) o Bearer token — válido 1h do lado da AppyPay. */
    public function getToken(): string
    {
        return Cache::remember('appypay_access_token', now()->addMinutes(55), function () {
            $response = Http::asForm()->timeout(12)->connectTimeout(8)->post($this->cfg['auth_url'], [
                'grant_type'    => 'client_credentials',
                'client_id'     => $this->cfg['client_id'],
                'client_secret' => $this->cfg['client_secret'],
                'resource'      => $this->cfg['resource'],
            ]);

            if (!$response->successful() || !$response->json('access_token')) {
                Log::error('AppyPay: falha ao obter token de acesso', ['status' => $response->status(), 'body' => $response->body()]);
                throw new \RuntimeException('Não foi possível autenticar com a AppyPay.');
            }

            return $response->json('access_token');
        });
    }

    /**
     * Cobra via Multicaixa Express — envia um pedido de aprovação para o telemóvel do cliente.
     *
     * @return array{success: bool, charge_id: string|null, status: string|null, message: string}
     */
    public function chargeByPhone(string $phoneNumber, float $amount, string $description, string $merchantTransactionId): array
    {
        return $this->postCharge([
            'amount'                => $amount,
            'currency'              => 'AOA',
            'description'           => $description,
            'merchantTransactionId' => $merchantTransactionId,
            'paymentMethod'         => $this->cfg['payment_method_gpo'],
            'paymentInfo'           => ['phoneNumber' => $phoneNumber],
        ]);
    }

    /**
     * Gera uma referência de pagamento (entidade + número) para o cliente pagar depois.
     *
     * @return array{success: bool, charge_id: string|null, status: string|null, entity: string|null, reference: string|null, message: string}
     */
    public function chargeByReference(float $amount, string $description, string $merchantTransactionId): array
    {
        $result = $this->postCharge([
            'amount'                => $amount,
            'currency'              => 'AOA',
            'description'           => $description,
            'merchantTransactionId' => $merchantTransactionId,
            'paymentMethod'         => $this->cfg['payment_method_ref'],
        ]);

        $referenceData = $result['gateway_response']['responseStatus']['reference'] ?? [];
        $result['entity']    = $referenceData['entity'] ?? $this->cfg['entity'] ?? null;
        $result['reference'] = $referenceData['referenceNumber'] ?? null;

        return $result;
    }

    private function postCharge(array $payload): array
    {
        try {
            $response = Http::withToken($this->getToken())
                ->acceptJson()
                ->timeout(45)->connectTimeout(8)
                ->post($this->cfg['base_url'] . '/v2.0/charges', $payload);

            $body = $response->json();

            if (!$response->successful()) {
                Log::error('AppyPay: charge falhou', ['status' => $response->status(), 'body' => $body]);
                return [
                    'success'          => false,
                    'charge_id'        => null,
                    'status'           => null,
                    'message'          => $body['message'] ?? 'Erro ao processar o pagamento AppyPay.',
                    'gateway_response' => $body,
                ];
            }

            return [
                'success'          => true,
                'charge_id'        => $body['id'] ?? null,
                'status'           => $body['responseStatus']['status'] ?? 'pending',
                'message'          => 'Pedido de pagamento criado com sucesso.',
                'gateway_response' => $body,
            ];
        } catch (\Throwable $e) {
            Log::error('AppyPay: excepção ao criar charge', ['error' => $e->getMessage()]);
            return [
                'success'          => false,
                'charge_id'        => null,
                'status'           => null,
                // Uma excepção aqui (ex.: timeout) deixa o resultado ambíguo — não
                // sabemos se a AppyPay chegou a processar o pedido do outro lado.
                // Por isso o aviso pede para confirmar antes de repetir, em vez de
                // convidar a tentar de novo de imediato (risco de cobrança duplicada).
                'message'          => 'Não foi possível confirmar o pagamento a tempo. Antes de tentar novamente, verifique a app Multicaixa Express ou o extracto da sua conta — se o valor já tiver sido debitado, não repita o pagamento e contacte o suporte.',
                'gateway_response' => [],
            ];
        }
    }

    /**
     * Consulta o estado actual de uma cobrança — usado no polling e na reconciliação manual.
     *
     * @return array{success: bool, status: string|null, message: string}
     */
    public function getCharge(string $chargeId): array
    {
        try {
            $response = Http::withToken($this->getToken())
                ->acceptJson()
                ->timeout(12)->connectTimeout(8)
                ->get($this->cfg['base_url'] . '/v2.0/charges/' . rawurlencode($chargeId));

            if (!$response->successful()) {
                Log::warning('AppyPay: getCharge falhou', ['charge_id' => $chargeId, 'status' => $response->status()]);
                return ['success' => false, 'status' => null, 'message' => $response->body()];
            }

            return [
                'success'          => true,
                'status'           => $response->json('payment.status'),
                'message'          => 'OK',
                'gateway_response' => $response->json(),
            ];
        } catch (\Throwable $e) {
            Log::warning('AppyPay: excepção ao consultar charge', ['charge_id' => $chargeId, 'error' => $e->getMessage()]);
            return ['success' => false, 'status' => null, 'message' => $e->getMessage()];
        }
    }

    /**
     * Só em sandbox — simula o pagamento de uma referência (ATM/Multicaixa/Internet Banking),
     * útil para testar o fluxo de ponta a ponta sem esperar por um pagamento real.
     */
    public function mockReferencePayment(string $referenceNumber): array
    {
        if (($this->cfg['mode'] ?? 'sandbox') !== 'sandbox') {
            return ['success' => false, 'message' => 'Simulação só disponível em modo sandbox.'];
        }

        try {
            $response = Http::withToken($this->getToken())
                ->acceptJson()
                ->timeout(12)->connectTimeout(8)
                ->post($this->cfg['base_url'] . '/v2.0/mocks/referenceProcessing', [
                    'entity'          => $this->cfg['entity'],
                    'referenceNumber' => $referenceNumber,
                ]);

            return [
                'success' => $response->successful(),
                'message' => $response->successful() ? 'Pagamento simulado com sucesso.' : $response->body(),
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
