<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Testa que Api\ServiceController::store() persiste os campos
 * descricao, categoria e prazo (que estavam em falta em $fillable).
 */
class ApiServiceStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_persists_descricao_categoria_prazo(): void
    {
        $user = User::factory()->create(['role' => 'cliente']);
        Sanctum::actingAs($user);

        $payload = [
            'titulo'    => 'Criar landing page',
            'descricao' => 'Preciso de uma landing page moderna em HTML/CSS.',
            'categoria' => 'Web Design',
            'prazo'     => now()->addDays(30)->toDateString(),
            'valor'     => 50000,
        ];

        $response = $this->postJson('/api/v1/services', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('services', [
            'titulo'    => 'Criar landing page',
            'descricao' => 'Preciso de uma landing page moderna em HTML/CSS.',
            'categoria' => 'Web Design',
            'cliente_id' => $user->id,
        ]);
    }

    public function test_store_requires_titulo(): void
    {
        $user = User::factory()->create(['role' => 'cliente']);
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/services', ['descricao' => 'Sem título'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['titulo']);
    }

    public function test_store_rejects_past_prazo(): void
    {
        $user = User::factory()->create(['role' => 'cliente']);
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/services', [
            'titulo' => 'Urgente',
            'prazo'  => now()->subDay()->toDateString(),
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['prazo']);
    }

    public function test_unauthenticated_store_returns_401(): void
    {
        $this->postJson('/api/v1/services', ['titulo' => 'Teste'])
            ->assertStatus(401);
    }
}
