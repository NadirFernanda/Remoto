<?php

namespace App\Modules\Payments\Services;

use App\Jobs\NotifyFreelancersOfNewProject;
use App\Models\CreatorSubscriptionCheckout;
use App\Models\Infoproduto;
use App\Models\InfoprodutoCompraCheckout;
use App\Models\InfoprodutoPatrocinioCheckout;
use App\Models\Notification;
use App\Models\Service;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletLog;
use App\Models\WalletTopUp;
use App\Modules\Admin\Services\AuditLogger;
use App\Modules\Loja\Services\LojaService;
use App\Modules\Loja\Services\PatrocinioService;
use App\Services\AffiliateService;
use App\Services\CreatorSubscriptionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Lógica partilhada de reconciliação de pagamentos AppyPay — chamada tanto pelo
 * webhook (App­yPayWebhookController) como pelos jobs de polling (PollAppyPayChargeJob,
 * PollAppyPayWalletTopUpJob, PollAppyPaySubscriptionCheckoutJob,
 * PollAppyPayInfoprodutoCompraCheckoutJob, PollAppyPayInfoprodutoPatrocinioCheckoutJob),
 * para nunca duplicar o efeito de marcar um pagamento como pago.
 *
 * Um charge_id da AppyPay pertence sempre a um único tipo de registo — por isso
 * procuramos por ordem em Service, CreatorSubscriptionCheckout,
 * InfoprodutoCompraCheckout, InfoprodutoPatrocinioCheckout e, por fim,
 * WalletTopUp, antes de desistir.
 */
class AppyPayReconciliationService
{
    /**
     * Marca um pagamento como pago a partir do ID da cobrança AppyPay.
     * Idempotente — se já estiver pago, não faz nada.
     */
    public function markPaidByChargeId(string $chargeId, ?float $amountFromGateway = null): void
    {
        if (Service::where('appypay_charge_id', $chargeId)->exists()) {
            $this->markServicePaid($chargeId, $amountFromGateway);
            return;
        }

        if (CreatorSubscriptionCheckout::where('appypay_charge_id', $chargeId)->exists()) {
            $this->markCreatorSubscriptionCheckoutPaid($chargeId, $amountFromGateway);
            return;
        }

        if (InfoprodutoCompraCheckout::where('appypay_charge_id', $chargeId)->exists()) {
            $this->markInfoprodutoCompraCheckoutPaid($chargeId, $amountFromGateway);
            return;
        }

        if (InfoprodutoPatrocinioCheckout::where('appypay_charge_id', $chargeId)->exists()) {
            $this->markInfoprodutoPatrocinioCheckoutPaid($chargeId, $amountFromGateway);
            return;
        }

        $this->markWalletTopUpPaid($chargeId, $amountFromGateway);
    }

    /** Marca um pagamento como falhado (pagamento rejeitado, saldo insuficiente, timeout). */
    public function markFailedByChargeId(string $chargeId, string $reason = ''): void
    {
        if (Service::where('appypay_charge_id', $chargeId)->exists()) {
            $this->markServiceFailed($chargeId, $reason);
            return;
        }

        if (CreatorSubscriptionCheckout::where('appypay_charge_id', $chargeId)->exists()) {
            $this->markCreatorSubscriptionCheckoutFailed($chargeId, $reason);
            return;
        }

        if (InfoprodutoCompraCheckout::where('appypay_charge_id', $chargeId)->exists()) {
            $this->markInfoprodutoCompraCheckoutFailed($chargeId, $reason);
            return;
        }

        if (InfoprodutoPatrocinioCheckout::where('appypay_charge_id', $chargeId)->exists()) {
            $this->markInfoprodutoPatrocinioCheckoutFailed($chargeId, $reason);
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

            // O valor do projecto (e a sua decomposição taxa/taxa_cliente/
            // valor_liquido) já foi definido ANTES de iniciar a cobrança —
            // ver PaymentEscrow::resolveOrCreateService() e
            // ServiceChat::pagarValorExtraAppyPay(). Não recalculamos aqui a
            // partir do montante que a gateway confirma, porque esse
            // montante é o TOTAL cobrado ao cliente (valor + taxa_cliente de
            // 10%), não o valor base do projecto — recalcular a partir dele
            // inflacionaria o valor do projecto (e o que o freelancer
            // recebe) pela sobretaxa do cliente. Só validamos que bate certo.
            $amount        = (float) ($service->valor ?? 0);
            $totalEsperado = (float) ($service->total_cliente ?: $amount);

            if ($amountFromGateway !== null && abs($amountFromGateway - $totalEsperado) > 0.5) {
                Log::warning('AppyPay: montante confirmado pela gateway difere do total esperado', [
                    'service_id' => $service->id,
                    'charge_id'  => $chargeId,
                    'esperado'   => $totalEsperado,
                    'confirmado' => $amountFromGateway,
                ]);
            }

            // Se já tem freelancer associado, este pagamento vem de uma
            // negociação directa via chat (ServiceChat::pagarValorExtra) — o
            // projecto já tem para quem vai, avança logo para 'in_progress'.
            // Sem freelancer associado é o fluxo normal de marketplace: fica
            // 'published', à espera de alguém escolher um candidato.
            $jaTemFreelancer          = (bool) $service->freelancer_id;
            $service->status         = $service->status === 'delivered'
                ? 'delivered'
                : ($jaTemFreelancer ? 'in_progress' : 'published');
            $service->payment_status = 'paid';
            $service->transaction_id = 'APPYPAY-' . $chargeId;
            $service->save();

            // Regista a entrada em escrow — sem isto o "Total Entradas" do
            // Painel Financeiro/Fluxo de Caixa nunca via este pagamento até
            // (e só até) um freelancer ser escolhido para o projecto. Só o
            // registo, sem mexer em saldo/saldo_pendente — ver
            // PaymentEscrow::registarEntradaEmEscrow() para a explicação
            // completa (mesma lógica, duplicada aqui por ser um caminho de
            // pagamento diferente).
            if ($amount > 0) {
                $wallet = Wallet::firstOrCreate(
                    ['user_id' => $service->cliente_id],
                    ['saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => 1000, 'taxa_saque' => 2]
                );
                WalletLog::create([
                    'user_id'   => $service->cliente_id,
                    'wallet_id' => $wallet->id,
                    'valor'     => -$amount,
                    'tipo'      => 'escrow_retido',
                    'descricao' => 'Pagamento retido em escrow para o projecto: ' . $service->titulo,
                ]);

                // Sobretaxa de 10% do cliente — receita imediata da plataforma,
                // separada do escrow do projecto (ver PaymentEscrow::registarEntradaEmEscrow).
                if ((float) $service->taxa_cliente > 0) {
                    WalletLog::create([
                        'user_id'   => $service->cliente_id,
                        'wallet_id' => $wallet->id,
                        'valor'     => -(float) $service->taxa_cliente,
                        'tipo'      => 'taxa_cliente_plataforma',
                        'descricao' => 'Taxa da plataforma (10%) sobre o projecto: ' . $service->titulo,
                    ]);
                }
            }

            Log::info('AppyPay: serviço reconciliado', ['service_id' => $service->id, 'charge_id' => $chargeId]);
            AuditLogger::log('appypay_payment_confirmed', "Pagamento AppyPay confirmado para o serviço #{$service->id}", 'Service', $service->id);

            (new AffiliateService())->creditCommissionForReferredAction($service->cliente, 'publish_service', $service->id);

            if ($jaTemFreelancer) {
                Notification::create([
                    'user_id'    => $service->freelancer_id,
                    'service_id' => $service->id,
                    'type'       => 'project_started',
                    'title'      => 'Projecto iniciado',
                    'message'    => 'O cliente confirmou o pagamento de ' . number_format($amount, 2, ',', '.') . ' Kz para o projecto "' . $service->titulo . '". O projecto passou para Em andamento.',
                ]);
            } else {
                NotifyFreelancersOfNewProject::dispatch($service);
            }
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

    private function markCreatorSubscriptionCheckoutPaid(string $chargeId, ?float $amountFromGateway): void
    {
        DB::transaction(function () use ($chargeId, $amountFromGateway) {
            $checkout = CreatorSubscriptionCheckout::where('appypay_charge_id', $chargeId)->lockForUpdate()->first();

            if (!$checkout) {
                Log::warning('AppyPay: checkout de assinatura não encontrado para reconciliação', ['charge_id' => $chargeId]);
                return;
            }

            if ($checkout->payment_status === 'paid') {
                return;
            }

            $subscriber = User::find($checkout->subscriber_id);
            $creator    = User::find($checkout->creator_id);
            if (!$subscriber || !$creator) {
                $checkout->update(['payment_status' => 'failed']);
                Log::warning('AppyPay: assinante/criador em falta ao reconciliar assinatura', ['checkout_id' => $checkout->id]);
                return;
            }

            $amount = $amountFromGateway ?? (float) $checkout->amount;
            $subscription = app(CreatorSubscriptionService::class)->activate(
                $subscriber, $creator, $amount, $checkout->payment_method_used ?? 'appypay'
            );

            $checkout->payment_status  = 'paid';
            $checkout->subscription_id = $subscription->id;
            $checkout->save();

            Log::info('AppyPay: assinatura de criador reconciliada', ['checkout_id' => $checkout->id, 'subscription_id' => $subscription->id, 'charge_id' => $chargeId]);
            AuditLogger::log('appypay_subscription_confirmed', "Assinatura AppyPay confirmada — assinante #{$subscriber->id}, criador #{$creator->id}", 'CreatorSubscription', $subscription->id);

            (new AffiliateService())->creditCommissionForReferredAction($subscriber, 'subscribe_creator', $subscription->id);
        });
    }

    private function markCreatorSubscriptionCheckoutFailed(string $chargeId, string $reason): void
    {
        $checkout = CreatorSubscriptionCheckout::where('appypay_charge_id', $chargeId)->first();

        if (!$checkout || $checkout->payment_status === 'paid') {
            return;
        }

        $checkout->payment_status = 'failed';
        $checkout->save();

        Log::info('AppyPay: pagamento de assinatura falhado', ['checkout_id' => $checkout->id, 'charge_id' => $chargeId, 'reason' => $reason]);
        AuditLogger::log('appypay_subscription_failed', "Pagamento AppyPay de assinatura falhou (checkout #{$checkout->id}): {$reason}", 'CreatorSubscriptionCheckout', $checkout->id);
    }

    private function markInfoprodutoCompraCheckoutPaid(string $chargeId, ?float $amountFromGateway): void
    {
        DB::transaction(function () use ($chargeId, $amountFromGateway) {
            $checkout = InfoprodutoCompraCheckout::where('appypay_charge_id', $chargeId)->lockForUpdate()->first();

            if (!$checkout) {
                Log::warning('AppyPay: checkout de compra não encontrado para reconciliação', ['charge_id' => $chargeId]);
                return;
            }

            if ($checkout->payment_status === 'paid') {
                return;
            }

            $comprador = User::find($checkout->comprador_id);
            $produto   = Infoproduto::find($checkout->infoproduto_id);
            if (!$comprador || !$produto) {
                $checkout->update(['payment_status' => 'failed']);
                Log::warning('AppyPay: comprador/produto em falta ao reconciliar compra', ['checkout_id' => $checkout->id]);
                return;
            }

            $amount = $amountFromGateway ?? (float) $checkout->amount;

            // Infoprodutos são bens únicos — não "empilham" como assinaturas. Se já
            // foi comprado por outra via entretanto (ex: saldo numa aba enquanto a
            // Referência confirmava noutra), não duplicar a compra nem o crédito ao
            // freelancer — apenas destravar o ecrã de espera e sinalizar para
            // reembolso manual (a AppyPay não tem API de reembolso documentada).
            if ($produto->jaCompradoPor($comprador->id)) {
                $checkout->update(['payment_status' => 'paid']);
                Log::critical('AppyPay: cobrança confirmada para produto já comprado por outro meio — possível cobrança duplicada, requer reembolso manual', [
                    'checkout_id' => $checkout->id, 'infoproduto_id' => $produto->id, 'comprador_id' => $comprador->id, 'charge_id' => $chargeId,
                ]);
                AuditLogger::log('appypay_duplicate_purchase_detected', "Cobrança AppyPay duplicada detectada — produto #{$produto->id} já comprado, verificar reembolso ao utilizador #{$comprador->id}", 'InfoprodutoCompraCheckout', $checkout->id);
                return;
            }

            $compra = app(LojaService::class)->activate($comprador, $produto, $amount, $checkout->payment_method_used ?? 'appypay');

            $checkout->payment_status = 'paid';
            $checkout->compra_id      = $compra->id;
            $checkout->save();

            Log::info('AppyPay: compra de infoproduto reconciliada', ['checkout_id' => $checkout->id, 'compra_id' => $compra->id, 'charge_id' => $chargeId]);
            AuditLogger::log('appypay_purchase_confirmed', "Compra AppyPay confirmada — comprador #{$comprador->id}, produto #{$produto->id}", 'InfoprodutoCompra', $compra->id);

            (new AffiliateService())->creditCommissionForReferredAction($comprador, 'buy_product', $produto->id);
        });
    }

    private function markInfoprodutoCompraCheckoutFailed(string $chargeId, string $reason): void
    {
        $checkout = InfoprodutoCompraCheckout::where('appypay_charge_id', $chargeId)->first();

        if (!$checkout || $checkout->payment_status === 'paid') {
            return;
        }

        $checkout->payment_status = 'failed';
        $checkout->save();

        Log::info('AppyPay: pagamento de compra falhado', ['checkout_id' => $checkout->id, 'charge_id' => $chargeId, 'reason' => $reason]);
        AuditLogger::log('appypay_purchase_failed', "Pagamento AppyPay de compra falhou (checkout #{$checkout->id}): {$reason}", 'InfoprodutoCompraCheckout', $checkout->id);
    }

    private function markInfoprodutoPatrocinioCheckoutPaid(string $chargeId, ?float $amountFromGateway): void
    {
        DB::transaction(function () use ($chargeId, $amountFromGateway) {
            $checkout = InfoprodutoPatrocinioCheckout::where('appypay_charge_id', $chargeId)->lockForUpdate()->first();

            if (!$checkout) {
                Log::warning('AppyPay: checkout de patrocínio não encontrado para reconciliação', ['charge_id' => $chargeId]);
                return;
            }

            if ($checkout->payment_status === 'paid') {
                return;
            }

            $user    = User::find($checkout->user_id);
            $produto = Infoproduto::find($checkout->infoproduto_id);
            if (!$user || !$produto) {
                $checkout->update(['payment_status' => 'failed']);
                Log::warning('AppyPay: utilizador/produto em falta ao reconciliar patrocínio', ['checkout_id' => $checkout->id]);
                return;
            }

            $amount = $amountFromGateway ?? (float) $checkout->amount;
            $patrocinio = app(PatrocinioService::class)->activate($user, $produto, $checkout->dias, $amount);

            $checkout->payment_status = 'paid';
            $checkout->patrocinio_id  = $patrocinio->id;
            $checkout->save();

            Log::info('AppyPay: patrocínio de infoproduto reconciliado', ['checkout_id' => $checkout->id, 'patrocinio_id' => $patrocinio->id, 'charge_id' => $chargeId]);
            AuditLogger::log('appypay_patrocinio_confirmed', "Patrocínio AppyPay confirmado — utilizador #{$user->id}, produto #{$produto->id}", 'InfoprodutoPatrocinio', $patrocinio->id);
        });
    }

    private function markInfoprodutoPatrocinioCheckoutFailed(string $chargeId, string $reason): void
    {
        $checkout = InfoprodutoPatrocinioCheckout::where('appypay_charge_id', $chargeId)->first();

        if (!$checkout || $checkout->payment_status === 'paid') {
            return;
        }

        $checkout->payment_status = 'failed';
        $checkout->save();

        Log::info('AppyPay: pagamento de patrocínio falhado', ['checkout_id' => $checkout->id, 'charge_id' => $chargeId, 'reason' => $reason]);
        AuditLogger::log('appypay_patrocinio_failed', "Pagamento AppyPay de patrocínio falhou (checkout #{$checkout->id}): {$reason}", 'InfoprodutoPatrocinioCheckout', $checkout->id);
    }
}
