<?php

namespace Tests\Feature;

use App\Models\FreelancerProfile;
use App\Models\Service;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletLog;
use App\Services\FeeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Testes para carteira (Wallet) e fluxo de escrow.
 *
 * Cobre:
 *  - Criação e relação user → wallet
 *  - Incremento e decremento de saldo
 *  - saldo_pendente durante escrow
 *  - WalletLog criado em cada operação financeira
 *  - Saldo do freelancer após conclusão do serviço (90%)
 *  - Saldo do cliente após pagamento em escrow
 */
class WalletEscrowTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function makeUser(string $role = 'cliente'): User
    {
        return User::factory()->create(['role' => $role, 'status' => 'active']);
    }

    private function makeWallet(User $user, float $saldo = 0): Wallet
    {
        return Wallet::create([
            'user_id'        => $user->id,
            'saldo'          => $saldo,
            'saldo_pendente' => 0,
            'saque_minimo'   => 1000,
            'taxa_saque'     => 0,
        ]);
    }

    private function logEntry(Wallet $wallet, float $valor, string $tipo, string $desc = 'Teste'): WalletLog
    {
        return WalletLog::create([
            'user_id'   => $wallet->user_id,
            'wallet_id' => $wallet->id,
            'valor'     => $valor,
            'tipo'      => $tipo,
            'descricao' => $desc,
        ]);
    }

    // ── Relações ─────────────────────────────────────────────────────────────

    #[Test]
    public function carteira_pertence_ao_utilizador(): void
    {
        $user   = $this->makeUser();
        $wallet = $this->makeWallet($user);

        $this->assertTrue($wallet->user->is($user));
    }

    #[Test]
    public function utilizador_tem_carteira(): void
    {
        $user   = $this->makeUser();
        $wallet = $this->makeWallet($user);

        $this->assertTrue($user->wallet->is($wallet));
    }

    // ── Saldo ─────────────────────────────────────────────────────────────────

    #[Test]
    public function saldo_incrementa_correctamente(): void
    {
        $user   = $this->makeUser('freelancer');
        $wallet = $this->makeWallet($user, 5000);

        $wallet->saldo += 3000;
        $wallet->save();

        $this->assertEquals(8000, (float) $wallet->fresh()->saldo);
    }

    #[Test]
    public function saldo_decrementa_correctamente(): void
    {
        $user   = $this->makeUser('cliente');
        $wallet = $this->makeWallet($user, 10000);

        $wallet->saldo -= 4000;
        $wallet->save();

        $this->assertEquals(6000, (float) $wallet->fresh()->saldo);
    }

    #[Test]
    public function saldo_nao_fica_negativo_em_pagamento_valido(): void
    {
        $user   = $this->makeUser('cliente');
        $wallet = $this->makeWallet($user, 10000);

        // Simula validação antes de debitar (como faz o ProjectManager)
        $valor = 10000;
        $this->assertTrue($wallet->saldo >= $valor, 'Saldo insuficiente para o pagamento');

        $wallet->saldo          -= $valor;
        $wallet->saldo_pendente += $valor;
        $wallet->save();

        $fresh = $wallet->fresh();
        $this->assertEquals(0, (float) $fresh->saldo);
        $this->assertEquals(10000, (float) $fresh->saldo_pendente);
    }

    // ── Escrow ─────────────────────────────────────────────────────────────────

    #[Test]
    public function pagamento_em_escrow_move_saldo_para_pendente(): void
    {
        $client = $this->makeUser('cliente');
        $wallet = $this->makeWallet($client, 20000);

        $valorServico           = 15000;
        $wallet->saldo          -= $valorServico;
        $wallet->saldo_pendente += $valorServico;
        $wallet->save();

        $fresh = $wallet->fresh();
        $this->assertEquals(5000,  (float) $fresh->saldo);
        $this->assertEquals(15000, (float) $fresh->saldo_pendente);
    }

    #[Test]
    public function conclusao_do_servico_liberta_saldo_pendente(): void
    {
        $client     = $this->makeUser('cliente');
        $freelancer = $this->makeUser('freelancer');

        $clientWallet     = $this->makeWallet($client, 10000);
        $freelancerWallet = $this->makeWallet($freelancer, 0);

        $fee     = (new FeeService())->calculateServiceFee(10000);
        $liquido = $fee['valor_liquido']; // 9000

        // Escrow: debitar cliente
        $clientWallet->saldo          -= 10000;
        $clientWallet->saldo_pendente += 10000;
        $clientWallet->save();

        // Conclusão: libertar escrow e creditar freelancer
        $clientWallet->saldo_pendente -= 10000;
        $clientWallet->save();

        $freelancerWallet->saldo += $liquido;
        $freelancerWallet->save();

        $this->assertEquals(0,    (float) $clientWallet->fresh()->saldo);
        $this->assertEquals(0,    (float) $clientWallet->fresh()->saldo_pendente);
        $this->assertEquals(9000, (float) $freelancerWallet->fresh()->saldo);
    }

    #[Test]
    public function freelancer_recebe_90_porcento_do_valor(): void
    {
        foreach ([10000, 5000, 20000] as $valor) {
            $freelancer = $this->makeUser('freelancer');
            $wallet     = $this->makeWallet($freelancer, 0);

            $fee = (new FeeService())->calculateServiceFee($valor);
            $wallet->saldo += $fee['valor_liquido'];
            $wallet->save();

            $esperado = round($valor * 0.90, 2);
            $this->assertEquals($esperado, (float) $wallet->fresh()->saldo, "Freelancer deve receber 90% de {$valor}");
        }
    }

    // ── WalletLog ─────────────────────────────────────────────────────────────

    #[Test]
    public function wallet_log_e_criado_em_deposito(): void
    {
        $user   = $this->makeUser('freelancer');
        $wallet = $this->makeWallet($user, 0);

        $this->logEntry($wallet, 5000, 'deposito', 'Pagamento de serviço');

        $this->assertDatabaseHas('wallet_logs', [
            'wallet_id' => $wallet->id,
            'valor'     => 5000,
            'tipo'      => 'deposito',
        ]);
    }

    #[Test]
    public function wallet_log_e_criado_em_saque(): void
    {
        $user   = $this->makeUser('freelancer');
        $wallet = $this->makeWallet($user, 10000);

        $this->logEntry($wallet, 3000, 'saque_solicitado', 'Saque para conta bancária');

        $this->assertDatabaseHas('wallet_logs', [
            'wallet_id' => $wallet->id,
            'tipo'      => 'saque_solicitado',
        ]);
    }

    #[Test]
    public function multiplos_logs_associados_a_mesma_carteira(): void
    {
        $user   = $this->makeUser('freelancer');
        $wallet = $this->makeWallet($user, 0);

        $this->logEntry($wallet, 10000, 'deposito');
        $this->logEntry($wallet, 2000,  'saque_solicitado');
        $this->logEntry($wallet, 500,   'taxa');

        $this->assertCount(3, $wallet->logs);
    }

    #[Test]
    public function log_tem_relacao_com_carteira_e_utilizador(): void
    {
        $user   = $this->makeUser('freelancer');
        $wallet = $this->makeWallet($user, 5000);

        $log = $this->logEntry($wallet, 5000, 'deposito');

        $this->assertTrue($log->wallet->is($wallet));
        $this->assertEquals($user->id, $log->user_id);
    }
}
