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

        // Enviar notificação ao utilizador (email + database nativa do Laravel)
        $event->user->notify(new KycStatusNotification(
            $event->status,
            route('kyc.submit'),
            $event->adminNote
        ));

        // A notificação nativa acima fica na tabela 'notifications', que não é
        // lida pelo sino/painel de notificações do site (esse usa o modelo
        // App\Models\Notification, tabela 'user_notifications'). Sem este
        // registo aqui, o utilizador rejeitado nunca via, dentro da própria
        // plataforma, uma forma de voltar à página de reenvio — só recebia
        // isso por email.
        \App\Models\Notification::create([
            'user_id' => $event->user->id,
            'type'    => $event->status === 'verified' ? 'kyc_verified' : 'kyc_rejected',
            'title'   => $event->status === 'verified' ? 'Identidade verificada' : 'Verificação KYC rejeitada',
            'message' => $event->status === 'verified'
                ? 'A sua identidade foi verificada com sucesso! Pode agora receber pagamentos.'
                : 'A verificação KYC foi rejeitada.' . ($event->adminNote ? ' Motivo: ' . $event->adminNote : ' Por favor, reenvie os documentos.'),
        ]);

        AuditLogger::log(
            'kyc_status_changed',
            "KYC de \"{$event->user->name}\" alterado para \"{$event->status}\"" . ($event->adminNote ? " — nota: {$event->adminNote}" : ''),
            'User',
            $event->user->id
        );
    }
}
