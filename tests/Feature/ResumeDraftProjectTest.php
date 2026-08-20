<?php

namespace Tests\Feature;

use App\Livewire\Client\ProjectManager;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Um projecto fica em 'draft'/'payment_pending' quando o cliente preenche o
 * briefing e define o valor mas nunca conclui o pagamento (ex.: fechou a
 * página, ou a AppyPay falhou). Sem forma de retomar, esse projecto ficava
 * preso na lista para sempre, sem nenhuma acção disponível — este teste
 * cobre o botão "Publicar Projecto" que resolve isso.
 */
class ResumeDraftProjectTest extends TestCase
{
    use RefreshDatabase;

    private function makeService(User $client, string $status): Service
    {
        return Service::create([
            'cliente_id' => $client->id,
            'titulo'     => 'Site Institucional',
            'briefing'   => 'Preciso de um site institucional.',
            'valor'      => 8000,
            'taxa'       => 800,
            'valor_liquido' => 7200,
            'status'     => $status,
        ]);
    }

    #[Test]
    public function cliente_pode_retomar_o_proprio_rascunho(): void
    {
        $client  = User::factory()->create(['role' => 'cliente']);
        $service = $this->makeService($client, 'draft');

        Livewire::actingAs($client)
            ->test(ProjectManager::class)
            ->call('resumeDraft', $service->id)
            ->assertRedirect(route('client.payment', ['service' => $service->id, 'valor' => $service->valor]));

        $this->assertEquals($service->id, session('client_order.service_id'));
        $this->assertEquals(8000.0, (float) session('client_order.payment.valor'));
    }

    #[Test]
    public function cliente_pode_retomar_projecto_com_pagamento_pendente(): void
    {
        $client  = User::factory()->create(['role' => 'cliente']);
        $service = $this->makeService($client, 'payment_pending');

        Livewire::actingAs($client)
            ->test(ProjectManager::class)
            ->call('resumeDraft', $service->id)
            ->assertRedirect(route('client.payment', ['service' => $service->id, 'valor' => $service->valor]));
    }

    #[Test]
    public function cliente_nao_pode_retomar_rascunho_de_outro_cliente(): void
    {
        $dono    = User::factory()->create(['role' => 'cliente']);
        $outro   = User::factory()->create(['role' => 'cliente']);
        $service = $this->makeService($dono, 'draft');

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::actingAs($outro)
            ->test(ProjectManager::class)
            ->call('resumeDraft', $service->id);
    }

    #[Test]
    public function nao_e_possivel_retomar_projecto_ja_publicado(): void
    {
        $client  = User::factory()->create(['role' => 'cliente']);
        $service = $this->makeService($client, 'published');

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::actingAs($client)
            ->test(ProjectManager::class)
            ->call('resumeDraft', $service->id);
    }

    #[Test]
    public function botao_publicar_projecto_aparece_para_rascunho(): void
    {
        $client  = User::factory()->create(['role' => 'cliente']);
        $service = $this->makeService($client, 'draft');

        Livewire::actingAs($client)
            ->test(ProjectManager::class)
            ->set('selectedServiceId', $service->id)
            ->assertSee('Publicar Projecto')
            ->assertSee('Rascunho');
    }
}
