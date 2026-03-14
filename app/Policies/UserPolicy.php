<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /** Admin pode listar todos os utilizadores. */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    /** Utilizador vê o próprio perfil ou admin vê qualquer um. */
    public function view(User $user, User $target): bool
    {
        return $user->id === $target->id || $user->role === 'admin';
    }

    /** Utilizador edita o próprio perfil ou admin edita qualquer um. */
    public function update(User $user, User $target): bool
    {
        return $user->id === $target->id || $user->role === 'admin';
    }

    /** Apenas admin pode apagar contas; não pode apagar a si mesmo. */
    public function delete(User $user, User $target): bool
    {
        return $user->role === 'admin' && $user->id !== $target->id;
    }

    /** Apenas admin pode suspender contas; não pode suspender a si mesmo. */
    public function suspend(User $user, User $target): bool
    {
        return $user->role === 'admin' && $user->id !== $target->id;
    }
}
