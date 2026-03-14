<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verifica que o middleware role: impede o acesso cruzado entre papéis.
 */
class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    // ── Freelancer não pode aceder a rotas de cliente ─────────────────────

    public function test_freelancer_cannot_access_cliente_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'freelancer']);

        $this->actingAs($user)
            ->get('/cliente/dashboard')
            ->assertStatus(403);
    }

    // ── Cliente não pode aceder a rotas de freelancer ─────────────────────

    public function test_cliente_cannot_access_freelancer_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'cliente']);

        $this->actingAs($user)
            ->get('/freelancer/dashboard')
            ->assertStatus(403);
    }

    // ── Utilizador não autenticado é redirecionado para login ─────────────

    public function test_guest_is_redirected_from_freelancer_dashboard(): void
    {
        $this->get('/freelancer/dashboard')
            ->assertRedirect('/login');
    }

    public function test_guest_is_redirected_from_cliente_dashboard(): void
    {
        $this->get('/cliente/dashboard')
            ->assertRedirect('/login');
    }

    // ── Utilizador com papel correcto consegue aceder ──────────────────────

    public function test_freelancer_can_access_freelancer_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'freelancer']);

        // Livewire renderiza o componente — basta não ser 403/redirect
        $this->actingAs($user)
            ->get('/freelancer/dashboard')
            ->assertStatus(200);
    }

    public function test_cliente_can_access_cliente_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'cliente']);

        $this->actingAs($user)
            ->get('/cliente/dashboard')
            ->assertStatus(200);
    }
}
