<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wallet;

class WalletPolicy
{
    /** Admin tem acesso total a carteiras. */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->role === 'admin') {
            return true;
        }
        return null;
    }

    /** O dono da carteira ou admin podem visualizá-la. */
    public function view(User $user, Wallet $wallet): bool
    {
        return $user->id === $wallet->user_id || $user->role === 'admin';
    }

    /** Apenas o dono com KYC verificado pode solicitar saque. */
    public function withdraw(User $user, Wallet $wallet): bool
    {
        if ($user->id !== $wallet->user_id) {
            return false;
        }

        return $user->kyc_status === 'verified';
    }

    /** Mesmas condições do saque se aplicam a transferências. */
    public function transfer(User $user, Wallet $wallet): bool
    {
        return $this->withdraw($user, $wallet);
    }
}
