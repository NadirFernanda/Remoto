<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Os perfis públicos de cliente (/clientes/{user}) e freelancer
 * (/freelancers/{user}) são acessíveis a visitantes não autenticados
 * (para partilha/SEO), por isso usam layouts.main. Mas quando visitados
 * por um utilizador já autenticado, devem aparecer dentro da shell do
 * dashboard (com a barra lateral) em vez de o "ejectar" para o layout
 * público — sem isso, o utilizador perde a navegação a meio da sessão.
 */
class PublicProfileDashboardShellTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function visitante_ve_perfil_de_cliente_sem_barra_lateral(): void
    {
        $client = User::factory()->create(['role' => 'cliente']);

        $response = $this->get(route('client.public', $client));

        $response->assertOk();
        $response->assertDontSee('dash-sidebar', false);
    }

    #[Test]
    public function utilizador_autenticado_ve_perfil_de_cliente_com_barra_lateral(): void
    {
        $viewer = User::factory()->create(['role' => 'freelancer']);
        $client = User::factory()->create(['role' => 'cliente']);

        $response = $this->actingAs($viewer)->get(route('client.public', $client));

        $response->assertOk();
        $response->assertSee('dash-sidebar', false);
        $response->assertSee($client->name);
    }

    #[Test]
    public function visitante_ve_perfil_de_freelancer_sem_barra_lateral(): void
    {
        $freelancer = User::factory()->create(['role' => 'freelancer']);

        $response = $this->get(route('freelancer.show', $freelancer));

        $response->assertOk();
        $response->assertDontSee('dash-sidebar', false);
    }

    #[Test]
    public function utilizador_autenticado_ve_perfil_de_freelancer_com_barra_lateral(): void
    {
        $viewer     = User::factory()->create(['role' => 'cliente']);
        $freelancer = User::factory()->create(['role' => 'freelancer']);

        $response = $this->actingAs($viewer)->get(route('freelancer.show', $freelancer));

        $response->assertOk();
        $response->assertSee('dash-sidebar', false);
        $response->assertSee($freelancer->name);
    }
}
