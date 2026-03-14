<?php

namespace App\Policies;

use App\Models\Contract;
use App\Models\User;

class ContractPolicy
{
    /** Apenas admin pode listar contratos de parceria. */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function view(User $user, Contract $contract): bool
    {
        return $user->role === 'admin';
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Contract $contract): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Contract $contract): bool
    {
        return $user->role === 'admin';
    }
}
