<?php

namespace App\Modules\Social\Services;

use App\Models\SocialPost;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class FeedService
{
    /**
     * Retorna a query paginada do feed de acordo com os filtros e utilizador.
     */
    public function getFeed(
        ?User $user,
        string $hashtag = '',
        bool $bookmarkedOnly = false,
        bool $myPostsOnly = false,
        int $perPage = 10
    ): LengthAwarePaginator {
        $query = SocialPost::with([
            'user.freelancerProfile',
            'media',
            'likes',
            'comments.user',
            'repost.user',
            'repost.media',
        ])->active();

        if ($hashtag) {
            $tag = ltrim($hashtag, '#');
            $query->where('content', 'like', '%#' . $tag . '%');
            // Visitantes só veem posts públicos na busca por hashtag
            if (!$user) {
                $query->where('visibility', 'public');
            } else {
                // Autenticados: posts públicos + os seus próprios + os de criadores subscritos
                $subscribedIds = $this->getSubscribedCreatorIds($user);
                $userId = $user->id;
                $query->where(function ($q) use ($userId, $subscribedIds) {
                    $q->where('visibility', 'public')
                      ->orWhere('user_id', $userId)
                      ->orWhere(function ($q2) use ($subscribedIds) {
                          $q2->where('visibility', 'followers')
                             ->whereIn('user_id', $subscribedIds);
                      });
                });
            }
        } elseif ($myPostsOnly && $user) {
            $query->where('user_id', $user->id);
        } elseif ($bookmarkedOnly && $user) {
            $bookmarkedIds = $user->bookmarks()->pluck('post_id');
            $query->whereIn('id', $bookmarkedIds);
        } else {
            // Feed principal: utilizadores autenticados veem TODOS os posts activos.
            // O controlo de visibilidade (followers-only) é feito no card blade —
            // não-assinantes veem um preview desfocado com overlay de subscrição.
            // Visitantes (não autenticados) só veem posts públicos.
            if (!$user) {
                $query->where('visibility', 'public');
            }
        }

        return $query->latest()->paginate($perPage);
    }

    public function isEmptyFeed(?User $user, string $hashtag, bool $bookmarkedOnly, bool $myPostsOnly): bool
    {
        return false;
    }

    /** IDs dos criadores que o utilizador subscreve activamente. */
    private function getSubscribedCreatorIds(User $user): array
    {
        try {
            return $user->subscriptionsAsSubscriber()->active()->pluck('creator_id')->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }
}
