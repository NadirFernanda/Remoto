<?php

namespace App\Policies;

use App\Models\Dispute;
use App\Models\Service;
use App\Models\User;

class DisputePolicy
{
    /** Admin tem acesso total a disputas. */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->role === 'admin') {
            return true;
        }
        return null;
    }

    /**
     * Cliente ou freelancer aceite no serviço podem abrir disputa.
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

    /**
     * Quem abriu a disputa, o cliente do serviço ou admin podem ver.
     */
    public function view(User $user, Dispute $dispute): bool
    {
        return $user->role === 'admin'
            || $user->id === $dispute->opened_by
            || $user->id === $dispute->service?->cliente_id;
    }

    /**
     * Partes envolvidas podem adicionar mensagens à disputa.
     */
    public function addMessage(User $user, Dispute $dispute): bool
    {
        return $this->view($user, $dispute);
    }

    /**
     * Apenas admin pode resolver disputas.
     */
    public function resolve(User $user, Dispute $dispute): bool
    {
        return $user->role === 'admin';
    }
}
