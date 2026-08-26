<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Garante que os componentes Livewire da área Admin abortam com 403
 * para qualquer utilizador que não seja admin — independente do middleware de rota.
 */
class AdminLivewireGuardTest extends TestCase
{
    use RefreshDatabase;

    // ── Utilizador não autenticado ─────────────────────────────────────────

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $this->get('/admin/dashboard')
            ->assertRedirect('/login');
    }

    // ── Cliente autenticado não pode aceder à área admin ──────────────────

    public function test_cliente_is_forbidden_from_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'cliente']);

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertStatus(403);
    }

    public function test_freelancer_is_forbidden_from_admin_users(): void
    {
        $user = User::factory()->create(['role' => 'freelancer']);

        $this->actingAs($user)
            ->get('/admin/users')
            ->assertStatus(403);
    }

    public function test_freelancer_is_forbidden_from_admin_financial(): void
    {
        $user = User::factory()->create(['role' => 'freelancer']);

        $this->actingAs($user)
            ->get('/admin/financeiro')
            ->assertStatus(403);
    }

    // ── Admin tem acesso ───────────────────────────────────────────────────

    /**
     * EnsureTwoFactorAuthenticated exige 2FA confirmado + sessão validada
     * (2fa_passed_at) para qualquer admin — sem isto, mesmo um admin válido
     * é redireccionado para o setup/challenge de 2FA em vez de aceder à
     * página. Estes dois testes simulam um admin que já passou por esse
     * fluxo, tal como aconteceria numa sessão real após o login com 2FA.
     */
    private function makeTwoFactorVerifiedAdmin(): User
    {
        $user = User::factory()->create([
            'role'                     => 'admin',
            'two_factor_confirmed_at'  => now(),
        ]);

        session(['2fa_passed_at' => now()->timestamp]);

        return $user;
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $user = $this->makeTwoFactorVerifiedAdmin();

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertStatus(200);
    }

    public function test_admin_can_access_admin_users(): void
    {
        $user = $this->makeTwoFactorVerifiedAdmin();

        $this->actingAs($user)
            ->get('/admin/users')
            ->assertStatus(200);
    }
}
