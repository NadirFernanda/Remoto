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
        $user = User::factory()->create(['role' => 'freelancer']);
        FreelancerProfile::create(['user_id' => $user->id, 'kyc_status' => 'approved']);

        $response = $this->actingAs($user)->get('/freelancer/carteira');

        // 200 ou renderização Livewire — só não deve redirecionar para KYC
        $response->assertRedirectIsNot(route('kyc.submit'));
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

        $response->assertDontSee('kyc'); // cliente chega ao dashboard normalmente
    }
}
