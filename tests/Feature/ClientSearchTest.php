<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Livewire\Freelancer\ClientSearch;

/**
 * Directório "Buscar Clientes" — só freelancers podem aceder, lista
 * clientes com contagem de projectos publicados/concluídos.
 */
class ClientSearchTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_e_redireccionado(): void
    {
        $this->get(route('freelancer.clients'))->assertRedirect('/login');
    }

    #[Test]
    public function cliente_nao_pode_aceder(): void
    {
        $client = User::factory()->create(['role' => 'cliente']);

        // Middleware Role redirecciona (não aborta 403) para não-admins com role errado.
        $this->actingAs($client)
            ->get(route('freelancer.clients'))
            ->assertRedirect(route('dashboard'));
    }

    #[Test]
    public function freelancer_pode_aceder(): void
    {
        $freelancer = User::factory()->create(['role' => 'freelancer']);

        $this->actingAs($freelancer)
            ->get(route('freelancer.clients'))
            ->assertStatus(200)
            ->assertSee('Buscar Clientes');
    }

    private function makeService(User $client, string $status): Service
    {
        return Service::create([
            'cliente_id'    => $client->id,
            'titulo'        => 'Projecto Teste',
            'briefing'      => 'Briefing',
            'valor'         => 5000,
            'taxa'          => 500,
            'valor_liquido' => 4500,
            'status'        => $status,
        ]);
    }

    #[Test]
    public function lista_clientes_com_contagem_de_projectos(): void
    {
        $freelancer = User::factory()->create(['role' => 'freelancer']);
        $client     = User::factory()->create(['role' => 'cliente', 'name' => 'Empresa Exemplo']);

        $this->makeService($client, 'published');
        $this->makeService($client, 'published');
        $this->makeService($client, 'completed');
        $this->makeService($client, 'draft');

        Livewire::actingAs($freelancer)
            ->test(ClientSearch::class)
            ->assertSee('Empresa Exemplo')
            ->assertSee('3 projectos publicados')
            ->assertSee('1 concluído');
    }
}
