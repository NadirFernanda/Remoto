<?php

namespace Tests\Feature;

use App\Models\FreelancerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KycMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_freelancer_without_kyc_profile_is_redirected_from_wallet(): void
    {
        $user = User::factory()->create(['role' => 'freelancer']);

        $response = $this->actingAs($user)->get('/freelancer/carteira');

        $response->assertRedirect(route('kyc.submit'));
    }

    public function test_freelancer_with_pending_kyc_is_redirected_from_wallet(): void
    {
        $user = User::factory()->create(['role' => 'freelancer']);
        FreelancerProfile::create(['user_id' => $user->id, 'kyc_status' => 'pending']);

        $response = $this->actingAs($user)->get('/freelancer/carteira');

        $response->assertRedirect(route('kyc.submit'));
    }

    public function test_freelancer_with_rejected_kyc_is_redirected_from_financial_panel(): void
    {
        $user = User::factory()->create(['role' => 'freelancer']);
        FreelancerProfile::create(['user_id' => $user->id, 'kyc_status' => 'rejected']);

        $response = $this->actingAs($user)->get('/freelancer/financeiro');

        $response->assertRedirect(route('kyc.submit'));
    }

    public function test_approved_freelancer_can_access_wallet(): void
    {
        $user = User::factory()->create(['role' => 'freelancer', 'kyc_status' => 'verified']);
        FreelancerProfile::create(['user_id' => $user->id, 'kyc_status' => 'verified']);

        $response = $this->actingAs($user)->get('/freelancer/carteira');

        // Não deve redirecionar para KYC — /freelancer/carteira é uma página
        // antiga, mantida só como redirect para o Painel Financeiro (único
        // ponto de saque), nunca 404, para não partir links/notificações antigas.
        $response->assertRedirect(route('freelancer.financial'));
    }

    public function test_kyc_redirect_carries_warning_message(): void
    {
        $user = User::factory()->create(['role' => 'freelancer']);

        $response = $this->actingAs($user)->get('/freelancer/carteira');

        $response->assertSessionHas('warning');
    }

    public function test_client_user_is_not_affected_by_kyc_middleware(): void
    {
        $user = User::factory()->create(['role' => 'cliente']);

        $response = $this->actingAs($user)->get('/cliente/dashboard');

        // Cliente não deve ser redirecionado para KYC — acede ao dashboard normalmente
        $response->assertOk();
    }
}
