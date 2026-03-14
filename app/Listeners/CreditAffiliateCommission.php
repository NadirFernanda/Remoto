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

        WalletLog::create([
            'user_id'   => $event->affiliate->id,
            'wallet_id' => $wallet->id,
            'valor'     => $event->commission,
            'tipo'      => 'comissao_afiliado',
            'descricao' => "Comissão de afiliado pelo registo/acção de \"{$event->referred->name}\"",
        ]);

        $event->affiliate->notify(new AffiliateCommissionNotification(
            $event->commission,
            $event->referred->name,
            route('freelancer.affiliate')
        ));
    }
}
