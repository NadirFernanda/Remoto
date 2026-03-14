<?php

namespace App\Modules\Wallet\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletLog;
use Illuminate\Support\Facades\DB;

class WalletService
{
    /**
     * Credita um valor na carteira do utilizador.
     * Cria a carteira automaticamente se ainda não existir.
     */
    public function credit(User $user, float $amount, string $type, string $description, ?int $referenceId = null): void
    {
        DB::transaction(function () use ($user, $amount, $type, $description, $referenceId) {
            $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);
            $wallet->increment('saldo', $amount);

            WalletLog::create([
                'wallet_id'    => $wallet->id,
                'type'         => 'credit',
                'amount'       => $amount,
                'description'  => $description,
                'reference_id' => $referenceId,
            ]);
        });
    }

    /**
     * Debita um valor da carteira do utilizador.
     * Lança \RuntimeException se o saldo for insuficiente.
     */
    public function debit(User $user, float $amount, string $type, string $description, ?int $referenceId = null): void
    {
        DB::transaction(function () use ($user, $amount, $type, $description, $referenceId) {
            $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);

            if ($wallet->saldo < $amount) {
                throw new \RuntimeException('Saldo insuficiente para realizar esta operação.');
            }

            $wallet->decrement('saldo', $amount);

            WalletLog::create([
                'wallet_id'    => $wallet->id,
                'type'         => 'debit',
                'amount'       => $amount,
                'description'  => $description,
                'reference_id' => $referenceId,
            ]);
        });
    }

    /**
     * Retorna o saldo actual sem movimentar a carteira.
     */
    public function getBalance(User $user): float
    {
        return (float) Wallet::where('user_id', $user->id)->value('saldo') ?? 0.0;
    }

    /**
     * Verifica se o utilizador tem saldo suficiente.
     */
    public function hasSufficientFunds(User $user, float $amount): bool
    {
        return $this->getBalance($user) >= $amount;
    }
}
