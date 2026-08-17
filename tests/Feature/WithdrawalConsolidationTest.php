<?php

namespace Tests\Feature;

use App\Livewire\Creator\SubscriptionManager;
use App\Livewire\Freelancer\Loja;
use App\Livewire\Freelancer\ProjectManager;
use App\Models\CreatorProfile;
use App\Models\FreelancerProfile;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Regressão para um bug financeiro crítico: os saques "por fonte" (Saque de
 * Projectos, Saque da Loja, Saque de Assinaturas) nunca debitavam
 * wallet.saldo/saldo_pendente — só o saque do Painel Financeiro
 * (FinancialPanel::solicitarSaque) o fazia correctamente. Isso permitia
 * pedir e ver aprovado um saque "de Projectos", por exemplo, sem que o saldo
 * real da carteira alguma vez descesse — deixando a mesma verba disponível
 * para ser sacada outra vez pelo Painel Financeiro (pagamento duplicado).
 *
 * A correcção remove os três fluxos duplicados por completo — o saque passa
 * a existir só no Painel Financeiro, a única fonte de verdade do saldo.
 */
class WithdrawalConsolidationTest extends TestCase
{
    use RefreshDatabase;

    private function makeFreelancer(): User
    {
        $freelancer = User::factory()->create([
            'role'                 => 'freelancer',
            'email_verified_at'    => now(),
            'status'               => 'active',
            'kyc_status'           => 'verified',
            'has_creator_profile'  => true,
        ]);

        FreelancerProfile::create([
            'user_id'    => $freelancer->id,
            'kyc_status' => 'verified',
            'skills'     => [],
            'languages'  => [],
        ]);

        Wallet::create([
            'user_id'        => $freelancer->id,
            'saldo'          => 100000,
            'saldo_pendente' => 0,
            'saque_minimo'   => 1000,
            'taxa_saque'     => 0,
        ]);

        CreatorProfile::create(['user_id' => $freelancer->id]);

        return $freelancer;
    }

    #[Test]
    public function project_manager_ja_nao_tem_o_metodo_de_saque_vulneravel(): void
    {
        $this->assertFalse(
            method_exists(ProjectManager::class, 'solicitarSaqueProjectos'),
            'solicitarSaqueProjectos() devia ter sido removido — nunca debitava wallet.saldo, permitindo saque duplicado.'
        );
    }

    #[Test]
    public function loja_ja_nao_tem_o_metodo_de_saque_vulneravel(): void
    {
        $this->assertFalse(
            method_exists(Loja::class, 'solicitarSaqueLoja'),
            'solicitarSaqueLoja() devia ter sido removido — nunca debitava wallet.saldo, permitindo saque duplicado.'
        );
    }

    #[Test]
    public function subscription_manager_ja_nao_tem_o_metodo_de_saque_vulneravel(): void
    {
        $this->assertFalse(
            method_exists(SubscriptionManager::class, 'solicitarSaqueAssin'),
            'solicitarSaqueAssin() devia ter sido removido — nunca debitava wallet.saldo, permitindo saque duplicado.'
        );
    }

    #[Test]
    public function pagina_meus_projectos_renderiza_sem_a_ui_de_saque_removida(): void
    {
        $freelancer = $this->makeFreelancer();

        Livewire::actingAs($freelancer)
            ->test(ProjectManager::class)
            ->assertOk()
            ->assertDontSee('Solicitar Saque')
            ->assertSee('Sacar no Painel Financeiro');
    }

    #[Test]
    public function pagina_minha_loja_renderiza_sem_a_ui_de_saque_removida(): void
    {
        $freelancer = $this->makeFreelancer();

        Livewire::actingAs($freelancer)
            ->test(Loja::class)
            ->assertOk()
            ->assertDontSee('Solicitar Saque')
            ->assertSee('Sacar no Painel Financeiro');
    }

    // Nota: não há aqui um teste de render() completo para SubscriptionManager
    // — a página usa EXTRACT(MONTH FROM ...) (sintaxe PostgreSQL) numa query
    // de estatísticas mensais, incompatível com o SQLite usado nos testes.
    // É um problema de portabilidade pré-existente, sem relação com esta
    // correcção; o teste acima já confirma que o método vulnerável foi
    // removido, que é o que importa para esta regressão.
}
