<?php

namespace App\Listeners;

use App\Events\PaymentReceived;
use App\Models\WalletLog;
use App\Modules\Admin\Services\AuditLogger;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;

class LogPaymentReceived implements ShouldQueue
{
    use Queueable;

    public function handle(PaymentReceived $event): void
    {
        AuditLogger::log(
            'payment_received',
            "Pagamento de {$event->amount} Kz recebido pelo freelancer \"{$event->freelancer->name}\" no projeto \"{$event->service->titulo}\"",
            'Service',
            $event->service->id
        );
    }
}
