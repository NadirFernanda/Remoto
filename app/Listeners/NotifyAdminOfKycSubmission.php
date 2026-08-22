<?php

namespace App\Listeners;

use App\Events\KycSubmissionCreated;
use App\Models\Notification;
use App\Models\User;
use App\Modules\Admin\Services\AuditLogger;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;

class NotifyAdminOfKycSubmission implements ShouldQueue
{
    use Queueable;

    public function handle(KycSubmissionCreated $event): void
    {
        $verbo = $event->isResubmission ? 'reenviou' : 'enviou';

        // Notificar todos os admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type'    => 'kyc_submission_new',
                'title'   => $event->isResubmission ? 'Documentos KYC reenviados' : 'Nova submissão KYC',
                'message' => "{$event->user->name} {$verbo} documentos para verificação de identidade.",
            ]);
        }

        AuditLogger::log(
            'kyc_submission_created',
            "\"{$event->user->name}\" {$verbo} documentos KYC para análise",
            'User',
            $event->user->id
        );
    }
}
