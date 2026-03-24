<?php

namespace App\Listeners;

use App\Events\KycStatusChanged;
use App\Notifications\KycStatusNotification;
use App\Modules\Admin\Services\AuditLogger;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;

class HandleKycStatusChanged implements ShouldQueue
{
    use Queueable;

    public function handle(KycStatusChanged $event): void
    {
        // Sincronizar FreelancerProfile.kyc_status com o estado aprovado no User
        // O middleware KycVerified lê User.kyc_status, mas mantemos FreelancerProfile em sync
        // para consistência de queries de pesquisa e exibição de perfil.
        $profile = $event->user->freelancerProfile;
        if ($profile) {
            // 'verified' no User == 'verified' no FreelancerProfile
            $profile->kyc_status = $event->status; // 'verified' | 'rejected'
            $profile->save();
        }

        // Enviar notificação ao utilizador (email + database)
        $event->user->notify(new KycStatusNotification(
            $event->status,
            route('kyc.submit'),
            $event->adminNote
        ));

        AuditLogger::log(
            'kyc_status_changed',
            "KYC de \"{$event->user->name}\" alterado para \"{$event->status}\"" . ($event->adminNote ? " — nota: {$event->adminNote}" : ''),
            'User',
            $event->user->id
        );
    }
}
