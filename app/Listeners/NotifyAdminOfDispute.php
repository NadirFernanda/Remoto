<?php

namespace App\Listeners;

use App\Events\DisputeOpened;
use App\Models\Notification;
use App\Models\User;
use App\Modules\Admin\Services\AuditLogger;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;

class NotifyAdminOfDispute implements ShouldQueue
{
    use Queueable;

    public function handle(DisputeOpened $event): void
    {
        // Notificar todos os admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id'    => $admin->id,
                'service_id' => $event->service->id,
                'type'       => 'dispute_opened_admin',
                'title'      => 'Nova disputa aberta',
                'message'    => "{$event->openedBy->name} abriu uma disputa no projeto \"{$event->service->titulo}\". ID #{$event->dispute->id}",
            ]);
        }

        AuditLogger::log(
            'dispute_opened',
            "Disputa #{$event->dispute->id} aberta por \"{$event->openedBy->name}\" no projeto \"{$event->service->titulo}\"",
            'Dispute',
            $event->dispute->id
        );
    }
}
