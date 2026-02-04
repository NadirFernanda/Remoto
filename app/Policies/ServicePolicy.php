<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Service;

class ServicePolicy
{
    /**
     * Permite que apenas o cliente do serviço cancele o pedido.
     */
    public function cancel(User $user, Service $service)
    {
        return $user->id === $service->cliente_id && $user->role === 'client';
    }

    /**
     * Permite que apenas freelancers aceitem ou recusem serviços que não sejam deles.
     */
    public function accept(User $user, Service $service)
    {
        return $user->role === 'freelancer' && $user->id !== $service->cliente_id;
    }

    public function refuse(User $user, Service $service)
    {
        return $user->role === 'freelancer' && $user->id !== $service->cliente_id;
    }
}
