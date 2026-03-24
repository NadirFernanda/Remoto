<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Service;
use App\Models\Wallet;
use App\Models\WalletLog;
use App\Models\FreelancerProfile;
use App\Models\KycSubmission;
use App\Events\KycStatusChanged;
use App\Livewire\Client\ProjectManager;
use App\Livewire\Freelancer\FinancialPanel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Testes de integração para os fluxos financeiros e KYC críticos.
 *
 * Cobre:
 *  - Aprovação/rejeição de entrega  ← escrow release para freelancer
 *  - Saque: saldo_pendente tracking
 *  - KYC: sincronização User ↔ FreelancerProfile
 *  - Middleware: acesso negado a rotas protegidas sem KYC
 */
class FinancialFlowTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function makeClient(array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'role'              => 'cliente',
            'email_verified_at' => now(),
            'status'            => 'active',
        ], $attrs));
    }

    private function makeFreelancer(array $attrs = []): User
    {
        $freelancer = User::factory()->create(array_merge([
            'role'              => 'freelancer',
            'email_verified_at' => now(),
            'status'            => 'active',
            'kyc_status'        => 'verified',
        ], $attrs));

        FreelancerProfile::create([
            'user_id'    => $freelancer->id,
            'kyc_status' => 'verified',
            'skills'     => [],
            'languages'  => [],
        ]);

        Wallet::create([
            'user_id'         => $freelancer->id,
            'saldo'           => 0,
            'saldo_pendente'  => 0,
            'saque_minimo'    => 1000,
            'taxa_saque'      => 0,
        ]);

        return $freelancer;
    }

    private function makeDeliveredService(User $client, User $freelancer, float $valor = 50000): Service
    {
        return Service::create([
            'cliente_id'    => $client->id,
            'freelancer_id' => $freelancer->id,
            'titulo'        => 'Projeto de Teste',
            'briefing'      => 'Briefing de teste',
            'valor'         => $valor,
            'taxa'          => round($valor * 0.20, 2),
            'valor_liquido' => round($valor * 0.80, 2),
            'status'        => 'delivered',
        ]);
    }

    // ── Escrow Release ───────────────────────────────────────────────────────

    #[Test]
    public function test_approving_delivery_credits_freelancer_wallet(): void
    {
        $client     = $this->makeClient();
        $freelancer = $this->makeFreelancer();
        $service    = $this->makeDeliveredService($client, $freelancer);

        $this->actingAs($client);

        $component = \Livewire\Livewire::test(ProjectManager::class)
            ->call('approveDelivery', $service->id);

        $service->refresh();
        $this->assertEquals('completed', $service->status);
        $this->assertTrue((bool) $service->is_payment_released);

        $wallet = Wallet::where('user_id', $freelancer->id)->first();
        $this->assertEquals(round(50000 * 0.80, 2), $wallet->saldo);
    }

    #[Test]
    public function test_approving_delivery_creates_wallet_log(): void
    {
        $client     = $this->makeClient();
        $freelancer = $this->makeFreelancer();
        $service    = $this->makeDeliveredService($client, $freelancer);

        $this->actingAs($client);

        \Livewire\Livewire::test(ProjectManager::class)
            ->call('approveDelivery', $service->id);

        $log = WalletLog::where('user_id', $freelancer->id)
            ->where('tipo', 'pagamento_projeto')
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals(round(50000 * 0.80, 2), $log->valor);
    }

    #[Test]
    public function test_client_cannot_approve_delivery_of_another_clients_service(): void
    {
        $client1    = $this->makeClient();
        $client2    = $this->makeClient();
        $freelancer = $this->makeFreelancer();
        $service    = $this->makeDeliveredService($client1, $freelancer);

        $this->actingAs($client2);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        \Livewire\Livewire::test(ProjectManager::class)
            ->call('approveDelivery', $service->id);
    }

    // ── Saque ────────────────────────────────────────────────────────────────

    #[Test]
    public function test_freelancer_can_request_withdrawal(): void
    {
        $freelancer = $this->makeFreelancer();
        $wallet     = Wallet::where('user_id', $freelancer->id)->first();
        $wallet->update(['saldo' => 50000]);

        $this->actingAs($freelancer);

        \Livewire\Livewire::test(FinancialPanel::class)
            ->set('valorSaque', 10000)
            ->call('solicitarSaque');

        $wallet->refresh();
        $this->assertEquals(40000, $wallet->saldo);
        $this->assertEquals(10000, $wallet->saldo_pendente);
    }

    #[Test]
    public function test_freelancer_cannot_withdraw_more_than_balance(): void
    {
        $freelancer = $this->makeFreelancer();
        $wallet     = Wallet::where('user_id', $freelancer->id)->first();
        $wallet->update(['saldo' => 5000]);

        $this->actingAs($freelancer);

        \Livewire\Livewire::test(FinancialPanel::class)
            ->set('valorSaque', 10000)
            ->call('solicitarSaque')
            ->assertHasErrors('valorSaque');

        $wallet->refresh();
        $this->assertEquals(5000, $wallet->saldo);   // saldo inalterado
        $this->assertEquals(0, $wallet->saldo_pendente);
    }

    #[Test]
    public function test_withdrawal_rejection_restores_balance(): void
    {
        $freelancer = $this->makeFreelancer();
        $wallet     = Wallet::where('user_id', $freelancer->id)->first();
        $wallet->update(['saldo' => 0, 'saldo_pendente' => 10000]);

        $log = WalletLog::create([
            'user_id'   => $freelancer->id,
            'wallet_id' => $wallet->id,
            'valor'     => -10000,
            'tipo'      => 'saque_solicitado',
            'descricao' => 'Teste',
        ]);

        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $this->actingAs($admin);

        \Livewire\Livewire::test(\App\Livewire\Admin\Payouts::class)
            ->call('rejeitarSaque', $log->id);

        $wallet->refresh();
        $this->assertEquals(10000, $wallet->saldo);
        $this->assertEquals(0, $wallet->saldo_pendente);
    }

    // ── KYC Sync ────────────────────────────────────────────────────────────

    #[Test]
    public function test_kyc_approval_syncs_freelancer_profile_status(): void
    {
        $freelancer = $this->makeFreelancer(['kyc_status' => 'pending']);
        $freelancer->freelancerProfile->update(['kyc_status' => 'pending']);

        KycStatusChanged::dispatch($freelancer, 'verified');

        $freelancer->refresh();
        $this->assertEquals('verified', $freelancer->kyc_status);
        $this->assertEquals('verified', $freelancer->freelancerProfile->fresh()->kyc_status);
    }

    #[Test]
    public function test_kyc_rejection_syncs_freelancer_profile_status(): void
    {
        $freelancer = $this->makeFreelancer(['kyc_status' => 'pending']);
        $freelancer->freelancerProfile->update(['kyc_status' => 'pending']);

        KycStatusChanged::dispatch($freelancer, 'rejected');

        $this->assertEquals('rejected', $freelancer->freelancerProfile->fresh()->kyc_status);
    }

    #[Test]
    public function test_kyc_middleware_blocks_unverified_freelancer(): void
    {
        $freelancer = $this->makeFreelancer(['kyc_status' => 'pending']);
        $this->actingAs($freelancer);

        // Tentar aceder a rota que requer kyc.verified
        $this->get('/freelancer/projetos-disponiveis')
            ->assertRedirect(route('kyc.submit'));
    }

    #[Test]
    public function test_kyc_middleware_allows_verified_freelancer(): void
    {
        $freelancer = $this->makeFreelancer(['kyc_status' => 'verified']);
        $this->actingAs($freelancer);

        $this->get('/freelancer/projetos-disponiveis')
            ->assertOk();
    }

    // ── Fee Calculation ─────────────────────────────────────────────────────

    #[Test]
    public function test_fee_service_calculates_correctly(): void
    {
        $fee = (new \App\Services\FeeService())->calculateServiceFee(50000);

        $this->assertEquals(5000.0,  $fee['taxa_cliente']);   // 10% do cliente
        $this->assertEquals(55000.0, $fee['total_cliente']);  // total pago pelo cliente
        $this->assertEquals(10000.0, $fee['taxa']);            // 20% deduzido ao freelancer
        $this->assertEquals(40000.0, $fee['valor_liquido']);   // 80% que o freelancer recebe
    }

    #[Test]
    public function test_loja_fee_service_calculates_correctly(): void
    {
        $fee = (new \App\Services\FeeService())->calculateLojaFee(10000);

        $this->assertEquals(2000.0, $fee['comissao']);
        $this->assertEquals(8000.0, $fee['valor_freelancer']);
    }
}
