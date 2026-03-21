<?php

namespace App\Modules\Social\Services;

use App\Models\SocialBookmark;
use App\Models\SocialComment;
use App\Models\SocialLike;
use App\Models\SocialPost;
use App\Models\SocialReport;
use App\Models\User;
use App\Notifications\PostLikedNotification;
use App\Notifications\PostCommentedNotification;

class SocialInteractionService
{
    /**
     * Alterna o like num post. Devolve true se ficou liked, false se removido.
     */
    public function toggleLike(User $user, int $postId): bool
    {
        $existing = SocialLike::where('post_id', $postId)->where('user_id', $user->id)->first();

        if ($existing) {
            $existing->delete();
            return false;
        }

        SocialLike::create(['post_id' => $postId, 'user_id' => $user->id]);

        // Notificar o criador (excepto se for o próprio a gostar)
        $post = SocialPost::find($postId);
        if ($post && $post->user_id !== $user->id) {
            $post->user->notify(new PostLikedNotification($post, $user));
        }

        return true;
    }

    /**
     * Alterna o bookmark num post. Devolve true se guardado, false se removido.
     */
    public function toggleBookmark(User $user, int $postId): bool
    {
        $existing = SocialBookmark::where('post_id', $postId)->where('user_id', $user->id)->first();

        if ($existing) {
            $existing->delete();
            return false;
        }

        SocialBookmark::create(['post_id' => $postId, 'user_id' => $user->id]);
        return true;
    }

    /**
     * Alterna o follow de um criador. Devolve true se passou a seguir, false se deixou.
     * Bloqueia auto-follow.
     */
    public function toggleFollow(User $user, int $creatorId): bool
    {
        if ($user->id === $creatorId) {
            return false;
        }

        if ($user->following()->where('following_id', $creatorId)->exists()) {
            $user->following()->detach($creatorId);
            return false;
        }

        $user->following()->syncWithoutDetaching([$creatorId]);
        return true;
    }

    /**
     * Adiciona um comentário a um post.
     */
    public function addComment(User $user, int $postId, string $content): SocialComment
    {
        $comment = SocialComment::create([
            'post_id' => $postId,
            'user_id' => $user->id,
            'content' => $content,
        ]);

        // Notificar o criador (excepto se for o próprio a comentar)
        $post = SocialPost::find($postId);
        if ($post && $post->user_id !== $user->id) {
            $preview = \Illuminate\Support\Str::limit($content, 60);
            $post->user->notify(new PostCommentedNotification($post, $user, $preview));
        }

        return $comment;
    }

    /**
     * Regista uma denúncia. Devolve false se o utilizador já tinha denunciado.
     * Ao atingir 3 denúncias pendentes, o post é automaticamente marcado como 'reported'.
     */
    public function report(User $reporter, string $type, int $id, string $reason): bool
    {
        $alreadyReported = SocialReport::where('reportable_type', $type)
            ->where('reportable_id', $id)
            ->where('reporter_id', $reporter->id)
            ->exists();

        if ($alreadyReported) {
            return false;
        }

        SocialReport::create([
            'reportable_type' => $type,
            'reportable_id'   => $id,
            'reporter_id'     => $reporter->id,
            'reason'          => $reason,
            'status'          => 'pendente',
        ]);

        if ($type === 'post') {
            $pendingCount = SocialReport::where('reportable_type', 'post')
                ->where('reportable_id', $id)
                ->where('status', 'pendente')
                ->count();

            if ($pendingCount >= 3) {
                SocialPost::where('id', $id)->update(['status' => 'reported']);
            }
        }

        return true;
    }
}
