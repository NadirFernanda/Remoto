<?php

namespace App\Modules\Payments\Services;

use App\Jobs\NotifyFreelancersOfNewProject;
use App\Models\Service;
use App\Models\Wallet;
use App\Models\WalletLog;
use App\Models\WalletTopUp;
use App\Modules\Admin\Services\AuditLogger;
use App\Services\AffiliateService;
use App\Services\FeeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Lógica partilhada de reconciliação de pagamentos AppyPay — chamada tanto pelo
 * webhook (App­yPayWebhookController) como pelos jobs de polling (PollAppyPayChargeJob,
 * PollAppyPayWalletTopUpJob), para nunca duplicar o efeito de marcar um pagamento como pago.
 *
 * Um charge_id da AppyPay pertence sempre a um único tipo de registo (Service ou
 * WalletTopUp) — por isso procuramos primeiro em Service e, se não encontrado, em
 * WalletTopUp, antes de desistir.
 */
class AppyPayReconciliationService
{
    /**
     * Marca um serviço/recarga de carteira como pago a partir do ID da cobrança AppyPay.
     * Idempotente — se já estiver pago, não faz nada.
     */
    public function markPaidByChargeId(string $chargeId, ?float $amountFromGateway = null): void
    {
        $service = Service::where('appypay_charge_id', $chargeId)->exists();
        if ($service) {
            $this->markServicePaid($chargeId, $amountFromGateway);
            return;
        }

        $this->markWalletTopUpPaid($chargeId, $amountFromGateway);
    }

    /** Marca um serviço/recarga de carteira como falhado (pagamento rejeitado, saldo insuficiente, timeout). */
    public function markFailedByChargeId(string $chargeId, string $reason = ''): void
    {
        $service = Service::where('appypay_charge_id', $chargeId)->exists();
        if ($service) {
            $this->markServiceFailed($chargeId, $reason);
            return;
        }

        $this->markWalletTopUpFailed($chargeId, $reason);
    }

    private function markServicePaid(string $chargeId, ?float $amountFromGateway): void
    {
        DB::transaction(function () use ($chargeId, $amountFromGateway) {
            $service = Service::where('appypay_charge_id', $chargeId)->lockForUpdate()->first();

            if (!$service) {
                Log::warning('AppyPay: serviço não encontrado para reconciliação', ['charge_id' => $chargeId]);
                return;
            }

            if ($service->payment_status === 'paid') {
                return;
            }

            $amount = $amountFromGateway ?? (float)($service->valor ?? 0);
            $fee    = (new FeeService())->calculateServiceFee($amount);

            $service->status         = 'published';
            $service->payment_status = 'paid';
            $service->transaction_id = 'APPYPAY-' . $chargeId;
            $service->valor          = $amount;
            $service->taxa           = $fee['taxa'];
            $service->valor_liquido  = $fee['valor_liquido'];
            $service->save();

            Log::info('AppyPay: serviço reconciliado', ['service_id' => $service->id, 'charge_id' => $chargeId]);
            AuditLogger::log('appypay_payment_confirmed', "Pagamento AppyPay confirmado para o serviço #{$service->id}", 'Service', $service->id);

            (new AffiliateService())->creditCommissionForReferredAction($service->cliente, 'publish_service', $service->id);
            NotifyFreelancersOfNewProject::dispatch($service);
        });
    }

    private function markServiceFailed(string $chargeId, string $reason): void
    {
        $service = Service::where('appypay_charge_id', $chargeId)->first();

        if (!$service || $service->payment_status === 'paid') {
            return;
        }

        $service->payment_status = 'failed';
        $service->save();

        Log::info('AppyPay: pagamento falhado', ['service_id' => $service->id, 'charge_id' => $chargeId, 'reason' => $reason]);
        AuditLogger::log('appypay_payment_failed', "Pagamento AppyPay falhou para o serviço #{$service->id}: {$reason}", 'Service', $service->id);
    }

    private function markWalletTopUpPaid(string $chargeId, ?float $amountFromGateway): void
    {
        DB::transaction(function () use ($chargeId, $amountFromGateway) {
            $topUp = WalletTopUp::where('appypay_charge_id', $chargeId)->lockForUpdate()->first();

            if (!$topUp) {
                Log::warning('AppyPay: recarga de carteira não encontrada para reconciliação', ['charge_id' => $chargeId]);
                return;
            }

            if ($topUp->payment_status === 'paid') {
                return;
            }

            $amount = $amountFromGateway ?? (float) $topUp->valor;

            $topUp->payment_status = 'paid';
            $topUp->valor          = $amount;
            $topUp->save();

            $wallet = Wallet::firstOrCreate(
                ['user_id' => $topUp->user_id],
                ['saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => 1000, 'taxa_saque' => 0]
            );
            $wallet->increment('saldo', $amount);
            WalletLog::create([
                'user_id'   => $topUp->user_id,
                'wallet_id' => $wallet->id,
                'valor'     => $amount,
                'tipo'      => 'recarga_carteira',
                'descricao' => 'Recarga de carteira via AppyPay (Multicaixa Express) — Kz ' . number_format($amount, 0, ',', '.') . '.',
            ]);

            Log::info('AppyPay: recarga de carteira reconciliada', ['top_up_id' => $topUp->id, 'user_id' => $topUp->user_id, 'charge_id' => $chargeId]);
            AuditLogger::log('appypay_wallet_topup_confirmed', "Recarga de carteira AppyPay confirmada para o utilizador #{$topUp->user_id}", 'WalletTopUp', $topUp->id);
        });
    }

    private function markWalletTopUpFailed(string $chargeId, string $reason): void
    {
        $topUp = WalletTopUp::where('appypay_charge_id', $chargeId)->first();

        if (!$topUp || $topUp->payment_status === 'paid') {
            return;
        }

        $topUp->payment_status = 'failed';
        $topUp->save();

        Log::info('AppyPay: recarga de carteira falhada', ['top_up_id' => $topUp->id, 'charge_id' => $chargeId, 'reason' => $reason]);
        AuditLogger::log('appypay_wallet_topup_failed', "Recarga de carteira AppyPay falhou para o utilizador #{$topUp->user_id}: {$reason}", 'WalletTopUp', $topUp->id);
    }
}
