<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wallet;

class WalletPolicy
{
    /** O dono da carteira ou admin podem visualizá-la. */
    public function view(User $user, Wallet $wallet): bool
    {
        return $user->id === $wallet->user_id || $user->role === 'admin';
    }

    /**
     * Apenas o dono com KYC aprovado pode solicitar saque.
     * Verifica o kyc_status tanto no User como no FreelancerProfile.
     */
    public function withdraw(User $user, Wallet $wallet): bool
    {
        if ($user->id !== $wallet->user_id) {
            return false;
        }

        return $user->kyc_status === 'approved'
            || $user->freelancerProfile?->kyc_status === 'approved';
    }

    /** Mesmas condições do saque se aplicam a transferências. */
    public function transfer(User $user, Wallet $wallet): bool
    {
        return $this->withdraw($user, $wallet);
    }
}
