<?php

namespace App\Listeners;

use App\Events\ServiceCompleted;
use App\Models\FreelancerProfile;
use App\Modules\Admin\Services\AuditLogger;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;

class UpdateFreelancerMetricsOnCompletion implements ShouldQueue
{
    use Queueable;

    public function handle(ServiceCompleted $event): void
    {
        $profile = FreelancerProfile::where('user_id', $event->freelancer->id)->first();

        if ($profile) {
            $metrics = $profile->metrics ?? [];
            $metrics['projetos_concluidos'] = ($metrics['projetos_concluidos'] ?? 0) + 1;
            $profile->update(['metrics' => $metrics]);
        }

        AuditLogger::log(
            'service_completed',
            "Projeto \"{$event->service->titulo}\" concluído. Cliente: {$event->client->name}, Freelancer: {$event->freelancer->name}",
            'Service',
            $event->service->id
        );
    }
}
