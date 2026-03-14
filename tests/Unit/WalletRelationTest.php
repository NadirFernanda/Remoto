<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletRelationTest extends TestCase
{
    use RefreshDatabase;

    public function test_wallet_has_logs_relation(): void
    {
        $user   = User::factory()->create();
        $wallet = Wallet::create([
            'user_id'        => $user->id,
            'saldo'          => 0,
            'saldo_pendente' => 0,
            'saque_minimo'   => 1000,
            'taxa_saque'     => 2,
        ]);

        WalletLog::create([
            'user_id'   => $user->id,
            'wallet_id' => $wallet->id,
            'valor'     => 5000,
            'tipo'      => 'deposito',
            'descricao' => 'Teste de depósito',
        ]);

        WalletLog::create([
            'user_id'   => $user->id,
            'wallet_id' => $wallet->id,
            'valor'     => 1000,
            'tipo'      => 'saque_solicitado',
            'descricao' => 'Teste de saque',
        ]);

        $logs = $wallet->logs;

        $this->assertCount(2, $logs);
        $this->assertInstanceOf(WalletLog::class, $logs->first());
    }

    public function test_wallet_log_belongs_to_wallet(): void
    {
        $user   = User::factory()->create();
        $wallet = Wallet::create([
            'user_id'        => $user->id,
            'saldo'          => 0,
            'saldo_pendente' => 0,
            'saque_minimo'   => 1000,
            'taxa_saque'     => 2,
        ]);

        $log = WalletLog::create([
            'user_id'   => $user->id,
            'wallet_id' => $wallet->id,
            'valor'     => 100,
            'tipo'      => 'deposito',
            'descricao' => 'Relação inversa',
        ]);

        $this->assertTrue($log->wallet->is($wallet));
    }

    public function test_wallet_logs_are_empty_for_new_wallet(): void
    {
        $user   = User::factory()->create();
        $wallet = Wallet::create([
            'user_id'        => $user->id,
            'saldo'          => 0,
            'saldo_pendente' => 0,
            'saque_minimo'   => 1000,
            'taxa_saque'     => 2,
        ]);

        $this->assertCount(0, $wallet->logs);
    }
}
