<?php

namespace App\Policies;

use App\Models\Refund;
use App\Models\Service;
use App\Models\User;

class RefundPolicy
{
    /** Admin tem acesso total a reembolsos. */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->role === 'admin') {
            return true;
        }
        return null;
    }

    /** Apenas o cliente dono do serviço pode pedir reembolso. */
    public function create(User $user, Service $service): bool
    {
        return $user->id === $service->cliente_id;
    }

    /** O solicitante ou admin podem ver o pedido de reembolso. */
    public function view(User $user, Refund $refund): bool
    {
        return $user->id === $refund->user_id || $user->role === 'admin';
    }

    /** Apenas admin pode aprovar reembolsos. */
    public function approve(User $user): bool
    {
        return $user->role === 'admin';
    }

    /** Apenas admin pode rejeitar reembolsos. */
    public function reject(User $user): bool
    {
        return $user->role === 'admin';
    }
}
