<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\Service;
use App\Models\User;
use App\Notifications\NewProjectNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Job: notifica freelancers activos da publicação de um novo projecto.
 *
 * Executado em background via queue para evitar bloquear o request HTTP
 * com N inserts + N emails síncronos (problema de N+1 crítico).
 *
 * Dispatch: NotifyFreelancersOfNewProject::dispatch($service);
 */
class NotifyFreelancersOfNewProject implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Tentativas máximas em caso de falha. */
    public int $tries = 3;

    /** Timeout por tentativa (segundos). */
    public int $timeout = 120;

    public function __construct(
        private readonly Service $service
    ) {}

    public function handle(): void
    {
        // Processa em chunks para evitar carregar todos os freelancers em memória
        User::query()
            ->where('role', 'freelancer')
            ->where('status', 'active')
            ->whereNull('is_suspended')
            ->orWhere('is_suspended', false)
            ->select('id', 'name', 'email', 'notify_new_project_email')
            ->chunkById(100, function ($freelancers) {
                $now = now();

                // Bulk insert de notificações internas (1 query por chunk)
                $notifications = $freelancers->map(fn($freelancer) => [
                    'user_id'    => $freelancer->id,
                    'service_id' => $this->service->id,
                    'type'       => 'novo_projeto',
                    'title'      => 'Novo projeto publicado',
                    'message'    => 'Um novo projeto foi publicado: ' . $this->service->titulo,
                    'read'       => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->toArray();

                Notification::insert($notifications);

                // Emails: apenas para freelancers que optaram por receber
                $serviceUrl = route('freelancer.service.review', $this->service->id);
                foreach ($freelancers->where('notify_new_project_email', true) as $freelancer) {
                    $freelancer->notify(new NewProjectNotification($this->service, $serviceUrl));
                }
            });
    }

    /**
     * Loga falhas no job para auditoria.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('NotifyFreelancersOfNewProject falhou', [
            'service_id' => $this->service->id,
            'error'      => $exception->getMessage(),
        ]);
    }
}
