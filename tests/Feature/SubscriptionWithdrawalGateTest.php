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
 * Testes para a regra especial de saque de saldo vindo de assinaturas de
 * criador: enquanto houver ganho_assinatura ainda não "resgatado" por um
 * saque fonte=assinaturas, o saque (feito sempre através do Painel
 * Financeiro, único ponto de saque desde o fix do bug de saque duplicado)
 * exige mínimo Kz 20.000 — sem intervalo de dias entre pedidos (política
 * actual: quem atingir o mínimo pode sacar a qualquer momento).
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
    public function sem_ganhos_de_assinatura_usa_o_minimo_normal_sem_cooldown(): void
    {
        $freelancer = $this->makeFreelancer(50000);

        Livewire::actingAs($freelancer)
            ->test(FinancialPanel::class)
            ->set('valorSaque', 25000)
            ->call('solicitarSaque')
            ->assertHasNoErrors();

        $freelancer->wallet->refresh();
        $this->assertEquals(25000, $freelancer->wallet->saldo);
    }

    #[Test]
    public function com_ganhos_de_assinatura_por_resgatar_exige_minimo_20000(): void
    {
        $freelancer = $this->makeFreelancer(300000);
        $this->creditarGanhoAssinatura($freelancer, 250000);

        Livewire::actingAs($freelancer)
            ->test(FinancialPanel::class)
            ->set('valorSaque', 15000) // abaixo do mínimo gated
            ->call('solicitarSaque')
            ->assertHasErrors('valorSaque');

        $freelancer->wallet->refresh();
        $this->assertEquals(300000, $freelancer->wallet->saldo); // nada debitado
    }

    #[Test]
    public function com_ganhos_de_assinatura_saque_de_200000_ou_mais_e_aceite_e_marcado(): void
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
    public function segundo_saque_gated_no_mesmo_dia_nao_e_bloqueado_por_tempo(): void
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
        // por outro motivo, sem ligação à regra de assinaturas).
        WalletLog::where('user_id', $freelancer->id)->where('tipo', 'saque_solicitado')->update(['tipo' => 'saque_aprovado']);

        // Política actual: sem intervalo de dias — só o mínimo de 20.000
        // conta. Ainda sobra saldo atribuível (500.000 - 200.000 = 300.000),
        // mas um segundo saque no mesmo instante não deve ser bloqueado por tempo.
        Livewire::actingAs($freelancer)
            ->test(FinancialPanel::class)
            ->set('valorSaque', 200000)
            ->call('solicitarSaque')
            ->assertHasNoErrors();

        $freelancer->wallet->refresh();
        $this->assertEquals(200000, $freelancer->wallet->saldo); // 600.000 - 200.000 - 200.000
    }

    #[Test]
    public function apos_resgatar_todo_o_saldo_de_assinaturas_deixa_de_estar_gated(): void
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

        // Sem mais saldo de assinaturas por resgatar — volta ao mínimo normal,
        // sem cooldown, mesmo tendo havido um saque "assinaturas" há pouco.
        Livewire::actingAs($freelancer)
            ->test(FinancialPanel::class)
            ->set('valorSaque', 25000)
            ->call('solicitarSaque')
            ->assertHasNoErrors();

        $freelancer->wallet->refresh();
        $this->assertEquals(75000, $freelancer->wallet->saldo);
    }
}
