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
            $query->where('content', 'like', '%#' . $tag . '%')
                  ->where(function ($q) use ($user) {
                      $this->applyVisibilityFilter($q, $user);
                  });
        } elseif ($myPostsOnly && $user) {
            $query->where('user_id', $user->id);
        } elseif ($bookmarkedOnly && $user) {
            $bookmarkedIds = $user->bookmarks()->pluck('post_id');
            $query->whereIn('id', $bookmarkedIds);
        } else {
            // Main feed: todos os posts públicos + posts de assinantes para subscritores activos
            $query->where(function ($q) use ($user) {
                $this->applyVisibilityFilter($q, $user);
            });
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Aplica filtro de visibilidade ao query builder:
     *  - 'public'    → qualquer pessoa (logada ou não)
     *  - 'followers' → apenas subscritores activos do criador
     *  - posts próprios do utilizador → sempre visíveis para si mesmo
     */
    private function applyVisibilityFilter($query, ?User $user): void
    {
        $query->where('visibility', 'public');   // qualquer um vê os públicos

        if ($user) {
            // Os seus próprios posts (independente da visibilidade)
            $query->orWhere('user_id', $user->id);

            // Posts "apenas seguidores" de criadores com subscrição activa
            $subscribedCreatorIds = $user->subscriptionsAsSubscriber()
                ->active()
                ->pluck('creator_id');

            if ($subscribedCreatorIds->isNotEmpty()) {
                $query->orWhere(function ($q) use ($subscribedCreatorIds) {
                    $q->where('visibility', 'followers')
                      ->whereIn('user_id', $subscribedCreatorIds);
                });
            }
        }
    }

    /**
     * Indica se o utilizador ainda não segue ninguém (feed vazio de seguidos).
     * Retorna falso se filtros ativos tornam a observação irrelevante.
     */
    public function isEmptyFeed(?User $user, string $hashtag, bool $bookmarkedOnly, bool $myPostsOnly): bool
    {
        // O feed principal agora mostra todos os posts públicos — nunca está "vazio por falta de seguidos"
        return false;
    }
}
