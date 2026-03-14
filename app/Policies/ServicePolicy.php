<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Service;

class ServicePolicy
{
    /** Admin tem acesso total a serviços. */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->role === 'admin') {
            return true;
        }
        return null;
    }

    /**
     * Permite que apenas o cliente do serviço cancele o pedido.
     */
    public function cancel(User $user, Service $service)
    {
        return $user->id === $service->cliente_id && in_array($user->activeRole(), ['client', 'cliente']);
    }

    /**
     * Permite que apenas freelancers aceitem ou recusem serviços que não sejam deles.
     */
    public function accept(User $user, Service $service)
    {
        return $user->activeRole() === 'freelancer' && $user->id !== $service->cliente_id;
    }

    public function refuse(User $user, Service $service)
    {
        return $user->activeRole() === 'freelancer' && $user->id !== $service->cliente_id;
    }
}
