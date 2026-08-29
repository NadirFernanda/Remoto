<?php

namespace Tests\Feature;

use App\Livewire\Freelancer\FinancialPanel;
use App\Models\FreelancerProfile;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Testes para o saque no Painel Financeiro: mínimo geral de Kz 500 para
 * todos, independentemente da origem do saldo (projectos, assinaturas, ou
 * qualquer outro ganho) — sem intervalo de dias entre pedidos.
 *
 * A única coisa que ainda distingue saldo de assinaturas é a marcação
 * fonte='assinaturas' no WalletLog do saque, usada só pelo CashFlowService
 * para separar a fatia "Criador" da fatia "Freelancing" nos relatórios do
 * admin — nunca para bloquear ou exigir um mínimo diferente.
 */
class SubscriptionWithdrawalGateTest extends TestCase
{
    use RefreshDatabase;

    private function makeFreelancer(float $saldo): User
    {
        $freelancer = User::factory()->create([
            'role'              => 'freelancer',
            'email_verified_at' => now(),
            'status'            => 'active',
            'kyc_status'        => 'verified',
        ]);

        FreelancerProfile::create([
            'user_id'    => $freelancer->id,
            'kyc_status' => 'verified',
            'skills'     => [],
            'languages'  => [],
        ]);

        Wallet::create([
            'user_id'        => $freelancer->id,
            'saldo'          => $saldo,
            'saldo_pendente' => 0,
            'saque_minimo'   => 1000,
            'taxa_saque'     => 0,
        ]);

        return $freelancer;
    }

    private function creditarGanhoAssinatura(User $user, float $valor): void
    {
        WalletLog::create([
            'user_id'   => $user->id,
            'wallet_id' => $user->wallet->id,
            'valor'     => $valor,
            'tipo'      => 'ganho_assinatura',
            'descricao' => 'Assinatura de teste',
        ]);
    }

    #[Test]
    public function saque_abaixo_de_500_e_bloqueado_mesmo_sem_ganhos_de_assinatura(): void
    {
        $freelancer = $this->makeFreelancer(50000);

        Livewire::actingAs($freelancer)
            ->test(FinancialPanel::class)
            ->set('valorSaque', 400)
            ->call('solicitarSaque')
            ->assertHasErrors('valorSaque');

        $freelancer->wallet->refresh();
        $this->assertEquals(50000, $freelancer->wallet->saldo); // nada debitado
    }

    #[Test]
    public function saque_de_500_ou_mais_e_aceite_sem_ganhos_de_assinatura(): void
    {
        $freelancer = $this->makeFreelancer(50000);

        Livewire::actingAs($freelancer)
            ->test(FinancialPanel::class)
            ->set('valorSaque', 25000)
            ->call('solicitarSaque')
            ->assertHasNoErrors();

        $freelancer->wallet->refresh();
        $this->assertEquals(25000, $freelancer->wallet->saldo);

        $this->assertDatabaseHas('wallet_logs', [
            'user_id' => $freelancer->id,
            'tipo'    => 'saque_solicitado',
            'fonte'   => null,
        ]);
    }

    #[Test]
    public function com_ganhos_de_assinatura_por_resgatar_o_minimo_continua_500(): void
    {
        $freelancer = $this->makeFreelancer(300000);
        $this->creditarGanhoAssinatura($freelancer, 250000);

        Livewire::actingAs($freelancer)
            ->test(FinancialPanel::class)
            ->set('valorSaque', 400) // abaixo do mínimo geral
            ->call('solicitarSaque')
            ->assertHasErrors('valorSaque');

        $freelancer->wallet->refresh();
        $this->assertEquals(300000, $freelancer->wallet->saldo); // nada debitado
    }

    #[Test]
    public function com_ganhos_de_assinatura_saque_e_marcado_com_fonte_assinaturas(): void
    {
        $freelancer = $this->makeFreelancer(300000);
        $this->creditarGanhoAssinatura($freelancer, 250000);

        Livewire::actingAs($freelancer)
            ->test(FinancialPanel::class)
            ->set('valorSaque', 200000)
            ->call('solicitarSaque')
            ->assertHasNoErrors();

        $freelancer->wallet->refresh();
        $this->assertEquals(100000, $freelancer->wallet->saldo);

        $this->assertDatabaseHas('wallet_logs', [
            'user_id' => $freelancer->id,
            'tipo'    => 'saque_solicitado',
            'fonte'   => 'assinaturas',
        ]);
    }

    #[Test]
    public function segundo_saque_no_mesmo_dia_nao_e_bloqueado_por_tempo(): void
    {
        $freelancer = $this->makeFreelancer(600000);
        $this->creditarGanhoAssinatura($freelancer, 500000);

        Livewire::actingAs($freelancer)
            ->test(FinancialPanel::class)
            ->set('valorSaque', 200000)
            ->call('solicitarSaque')
            ->assertHasNoErrors();

        // Simula a aprovação do 1º saque pelo admin, para isolar o teste da
        // regra genérica "só um saque pendente de cada vez" (que bloquearia
        // por outro motivo, sem ligação à origem do saldo).
        WalletLog::where('user_id', $freelancer->id)->where('tipo', 'saque_solicitado')->update(['tipo' => 'saque_aprovado']);

        // Sem intervalo de dias — um segundo saque no mesmo instante, ainda
        // com saldo de assinaturas por resgatar, não deve ser bloqueado por tempo.
        Livewire::actingAs($freelancer)
            ->test(FinancialPanel::class)
            ->set('valorSaque', 200000)
            ->call('solicitarSaque')
            ->assertHasNoErrors();

        $freelancer->wallet->refresh();
        $this->assertEquals(200000, $freelancer->wallet->saldo); // 600.000 - 200.000 - 200.000
    }

    #[Test]
    public function apos_resgatar_todo_o_saldo_de_assinaturas_saque_deixa_de_ser_marcado(): void
    {
        $freelancer = $this->makeFreelancer(300000);
        $this->creditarGanhoAssinatura($freelancer, 200000);

        // Consome exactamente o saldo atribuível de assinaturas
        Livewire::actingAs($freelancer)
            ->test(FinancialPanel::class)
            ->set('valorSaque', 200000)
            ->call('solicitarSaque')
            ->assertHasNoErrors();

        // Simula a aprovação do 1º saque pelo admin, para isolar o teste da
        // regra genérica "só um saque pendente de cada vez".
        WalletLog::where('user_id', $freelancer->id)->where('tipo', 'saque_solicitado')->update(['tipo' => 'saque_aprovado']);

        // Sem mais saldo de assinaturas por resgatar — o próximo saque já não
        // é marcado com fonte='assinaturas', mas o mínimo continua o mesmo (500).
        Livewire::actingAs($freelancer)
            ->test(FinancialPanel::class)
            ->set('valorSaque', 25000)
            ->call('solicitarSaque')
            ->assertHasNoErrors();

        $freelancer->wallet->refresh();
        $this->assertEquals(75000, $freelancer->wallet->saldo);

        $this->assertDatabaseHas('wallet_logs', [
            'user_id' => $freelancer->id,
            'tipo'    => 'saque_solicitado',
            'valor'   => -25000,
            'fonte'   => null,
        ]);
    }
}
