<?php

namespace App\Policies;

use App\Models\SocialPost;
use App\Models\User;

class SocialPostPolicy
{
    /** Admin tem acesso total a posts sociais. */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->role === 'admin') {
            return true;
        }
        return null;
    }

    /** Apenas freelancers e criadores podem publicar. */
    public function create(User $user): bool
    {
        return in_array($user->role, ['freelancer', 'creator']);
    }

    /** Apenas o próprio autor pode editar o post. */
    public function update(User $user, SocialPost $post): bool
    {
        return $user->id === $post->user_id;
    }

    /** O autor ou admin podem apagar o post. */
    public function delete(User $user, SocialPost $post): bool
    {
        return $user->id === $post->user_id || $user->role === 'admin';
    }

    /** Qualquer utilizador autenticado pode denunciar, excepto o próprio autor. */
    public function report(User $user, SocialPost $post): bool
    {
        return $user->id !== $post->user_id;
    }
}
