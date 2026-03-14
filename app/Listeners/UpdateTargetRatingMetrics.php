<?php

namespace App\Listeners;

use App\Events\ReviewSubmitted;
use App\Models\FreelancerProfile;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;

class UpdateTargetRatingMetrics implements ShouldQueue
{
    use Queueable;

    public function handle(ReviewSubmitted $event): void
    {
        // Recalcular e guardar a média de avaliações no perfil do freelancer
        $profile = FreelancerProfile::where('user_id', $event->target->id)->first();

        if ($profile) {
            $avg = $event->target
                ->reviewsReceived()
                ->avg('rating');

            $count = $event->target
                ->reviewsReceived()
                ->count();

            $metrics = $profile->metrics ?? [];
            $metrics['rating_medio']    = round((float) $avg, 2);
            $metrics['total_avaliacoes'] = $count;
            $profile->update(['metrics' => $metrics]);
        }
    }
}
