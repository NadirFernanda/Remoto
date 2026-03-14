<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\Service;
use App\Models\User;

class ReviewPolicy
{
    /**
     * Cliente ou freelancer aceite no serviço podem submeter avaliação.
     */
    public function create(User $user, Service $service): bool
    {
        if ($user->id === $service->cliente_id) {
            return true;
        }

        return $service->candidates()
            ->where('freelancer_id', $user->id)
            ->where('status', 'accepted')
            ->exists();
    }

    /** Apenas o autor ou admin podem apagar uma avaliação. */
    public function delete(User $user, Review $review): bool
    {
        return $user->id === $review->author_id || $user->role === 'admin';
    }
}
