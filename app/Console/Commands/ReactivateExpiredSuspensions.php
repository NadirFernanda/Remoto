<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/**
 * Reactiva contas suspensas automaticamente (por advertências) cujo prazo já
 * passou. A middleware EnsureNotSuspended já faz isto na hora quando o
 * próprio utilizador tenta aceder à plataforma — este comando serve para
 * quem nunca mais volta a tentar entrar, mantendo a lista de suspensos do
 * admin correcta mesmo sem esse gatilho.
 */
class ReactivateExpiredSuspensions extends Command
{
    protected $signature = 'users:reactivate-expired-suspensions';
    protected $description = 'Reactiva utilizadores cuja suspensão automática (por advertências) já expirou';

    public function handle(): int
    {
        $count = User::where('is_suspended', true)
            ->whereNotNull('suspended_until')
            ->where('suspended_until', '<=', now())
            ->update([
                'is_suspended'    => false,
                'status'          => 'active',
                'suspended_until' => null,
            ]);

        $this->info("{$count} conta(s) reactivada(s).");

        return self::SUCCESS;
    }
}
