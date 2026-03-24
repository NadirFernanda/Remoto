<?php

namespace App\Console\Commands;

use App\Models\Service;
use App\Models\Review;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

/**
 * Artisan command: envia lembretes para clientes e freelancers avaliarem
 * projectos concluídos há mais de 24h sem avaliação submetida.
 *
 * Limita: não envia mais de 1 lembrete por serviço.
 * Prazo: apenas projectos concluídos nos últimos 14 dias (janela de avaliação).
 *
 * Agendamento recomendado: diariamente às 10:00.
 * Execução manual: php artisan reviews:remind
 */
class SendReviewReminders extends Command
{
    protected $signature   = 'reviews:remind';
    protected $description = 'Enviar lembretes de avaliação para projectos concluídos sem review';

    public function handle(): int
    {
        $windowStart = now()->subDays(14);
        $windowEnd   = now()->subDay(); // só após 24h da conclusão

        $reminded = 0;

        Service::query()
            ->where('status', 'completed')
            ->whereBetween('payment_released_at', [$windowStart, $windowEnd])
            ->with(['cliente', 'freelancer'])
            ->chunkById(50, function ($services) use (&$reminded) {
                foreach ($services as $service) {
                    $reminded += $this->remindIfNeeded($service, 'cliente');
                    $reminded += $this->remindIfNeeded($service, 'freelancer');
                }
            });

        $this->info("Lembretes de avaliação enviados: {$reminded}");
        return self::SUCCESS;
    }

    private function remindIfNeeded(Service $service, string $side): int
    {
        [$userId, $targetId] = $side === 'cliente'
            ? [$service->cliente_id, $service->freelancer_id]
            : [$service->freelancer_id, $service->cliente_id];

        if (!$userId) {
            return 0;
        }

        // Verificar se já existe avaliação neste sentido
        $hasReview = Review::where('service_id', $service->id)
            ->where('author_id', $userId)
            ->exists();

        if ($hasReview) {
            return 0;
        }

        // Evitar lembrete duplicado
        $alreadySent = Notification::query()
            ->where('user_id', $userId)
            ->where('service_id', $service->id)
            ->where('type', 'review_reminder')
            ->exists();

        if ($alreadySent) {
            return 0;
        }

        Notification::create([
            'user_id'    => $userId,
            'service_id' => $service->id,
            'type'       => 'review_reminder',
            'title'      => 'Avalie o seu projeto',
            'message'    => 'O projeto "' . $service->titulo . '" foi concluído. Partilhe a sua avaliação!',
            'read'       => false,
        ]);

        return 1;
    }
}
