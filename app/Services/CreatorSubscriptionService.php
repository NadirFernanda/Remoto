<?php

namespace App\Services;

use App\Models\CreatorProfile;
use App\Models\CreatorSubscription;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletLog;
use Illuminate\Support\Facades\DB;

class CreatorSubscriptionService
{
    /**
     * Caminho da carteira: debita o assinante e activa a assinatura.
     *
     * @throws \RuntimeException se auto-assinatura, criador suspenso, já assinante, ou saldo insuficiente
     */
    public function subscribe(User $subscriber, User $creator): CreatorSubscription
    {
        if ($subscriber->id === $creator->id) {
            throw new \RuntimeException('Não pode assinar o seu próprio perfil.');
        }
        if ($creator->creator_suspended ?? false) {
            throw new \RuntimeException('Este criador não está disponível para assinatura.');
        }

        $alreadyActive = CreatorSubscription::where('subscriber_id', $subscriber->id)
            ->where('creator_id', $creator->id)
            ->active()
            ->exists();
        if ($alreadyActive) {
            throw new \RuntimeException('Já é assinante deste criador.');
        }

        $price = $creator->creatorProfile?->subscription_price ?? CreatorProfile::MIN_SUBSCRIPTION_PRICE;

        return DB::transaction(function () use ($subscriber, $creator, $price) {
            $wallet = Wallet::where('user_id', $subscriber->id)->lockForUpdate()->first();
            if (!$wallet || $wallet->saldo < $price) {
                throw new \RuntimeException('Saldo insuficiente. Recarregue a sua carteira antes de assinar.');
            }

            $wallet->decrement('saldo', $price);
            WalletLog::create([
                'user_id'   => $subscriber->id,
                'wallet_id' => $wallet->id,
                'valor'     => -$price,
                'tipo'      => 'assinatura',
                'descricao' => "Assinatura do criador \"{$creator->name}\" por 1 mês.",
            ]);

            return $this->activate($subscriber, $creator, $price, 'wallet');
        });
    }

    /**
     * Activa (cria ou renova) a assinatura e credita a carteira do criador —
     * partilhado entre subscribe() (após débito da carteira) e a reconciliação
     * AppyPay (após confirmação do pagamento). Nunca debita o pagador.
     *
     * creator_subscriptions tem unique(subscriber_id, creator_id) — em vez de
     * criar sempre, "empilha" o período sobre uma assinatura já activa (renovação
     * ou pagamento em duas abas), evitando rebentar a constraint única.
     */
    public function activate(User $subscriber, User $creator, float $amount, string $paymentMethodUsed): CreatorSubscription
    {
        $fee = (new FeeService())->calculateSubscriptionFee($amount);

        $existing = CreatorSubscription::where('subscriber_id', $subscriber->id)
            ->where('creator_id', $creator->id)
            ->lockForUpdate()
            ->first();

        $base = ($existing && $existing->status === 'active' && $existing->expires_at?->isFuture())
            ? $existing->expires_at
            : now();

        $attrs = [
            'amount'       => $amount,
            'platform_fee' => $fee['comissao'],
            'net_amount'   => $fee['valor_criador'],
            'status'       => 'active',
            'expires_at'   => $base->copy()->addMonth(),
            'cancelled_at' => null,
        ];

        if ($existing) {
            $existing->update($attrs + ['starts_at' => $existing->starts_at ?? now()]);
            $subscription = $existing;
        } else {
            $subscription = CreatorSubscription::create($attrs + [
                'subscriber_id' => $subscriber->id,
                'creator_id'    => $creator->id,
                'starts_at'     => now(),
            ]);
        }

        $creatorWallet = Wallet::firstOrCreate(
            ['user_id' => $creator->id],
            ['saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => 0, 'taxa_saque' => 0]
        );
        $creatorWallet->increment('saldo', $fee['valor_criador']);
        WalletLog::create([
            'user_id'   => $creator->id,
            'wallet_id' => $creatorWallet->id,
            'valor'     => $fee['valor_criador'],
            'tipo'      => 'ganho_assinatura',
            'descricao' => "Assinatura de \"{$subscriber->name}\" via {$paymentMethodUsed} — comissão retida pela plataforma.",
        ]);

        return $subscription;
    }
}
