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
        } elseif ($myPostsOnly && $user) {
            $query->where('user_id', $user->id);
        } elseif ($bookmarkedOnly && $user) {
            $bookmarkedIds = $user->bookmarks()->pluck('post_id');
            $query->whereIn('id', $bookmarkedIds);
        } elseif ($user) {
            $followingIds = $user->following()->pluck('users.id');
            $visibleIds   = $followingIds->push($user->id)->unique()->values();
            $query->whereIn('user_id', $visibleIds);
        } else {
            // Guest (unauthenticated): return nothing — all content requires login
            $query->whereRaw('1 = 0');
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Indica se o utilizador ainda não segue ninguém (feed vazio de seguidos).
     * Retorna falso se filtros ativos tornam a observação irrelevante.
     */
    public function isEmptyFeed(?User $user, string $hashtag, bool $bookmarkedOnly, bool $myPostsOnly): bool
    {
        if (!$user || $hashtag || $bookmarkedOnly || $myPostsOnly) {
            return false;
        }

        return $user->following()->count() === 0;
    }
}
