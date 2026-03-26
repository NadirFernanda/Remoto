<?php

namespace App\Listeners;

use App\Events\AffiliateCommissionEarned;
use App\Models\Wallet;
use App\Models\WalletLog;
use App\Notifications\AffiliateCommissionNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;

class CreditAffiliateCommission implements ShouldQueue
{
    use Queueable;

    public function handle(AffiliateCommissionEarned $event): void
    {
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $event->affiliate->id],
            ['saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => 1000, 'taxa_saque' => 2]
        );

        $wallet->increment('saldo', $event->commission);

        $descricao = "Comissão de afiliado pelo registo/acção de \"{$event->referred->name}\"";
        if (str_starts_with($event->reason, 'action:')) {
            $parts = explode(':', $event->reason);
            $actionType = $parts[1] ?? 'acao';
            $referenceId = $parts[2] ?? '0';
            $referredId = $parts[3] ?? (string) $event->referred->id;

            $actionLabel = match ($actionType) {
                'publish_service' => 'publicação de serviço',
                'buy_product' => 'compra de produto',
                'subscribe_creator' => 'assinatura de criador',
                'accept_proposal' => 'aceitação de proposta',
                default => 'ação elegível',
            };

            $marker = '[AFF_ACTION:' . $actionType . ':' . $referenceId . ':USER' . $referredId . ']';
            $descricao = "Comissão de afiliado por {$actionLabel} de \"{$event->referred->name}\" {$marker}";
        }

        WalletLog::create([
            'user_id'   => $event->affiliate->id,
            'wallet_id' => $wallet->id,
            'valor'     => $event->commission,
            'tipo'      => 'comissao_afiliado',
            'descricao' => $descricao,
        ]);

        $event->affiliate->notify(new AffiliateCommissionNotification(
            $event->commission,
            $event->referred->name,
            route('freelancer.affiliate')
        ));
    }
}
