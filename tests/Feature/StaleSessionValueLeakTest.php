<?php

namespace Tests\Feature;

use App\Livewire\Client\Briefing;
use App\Livewire\Client\ServiceValue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Regressão: um cliente que começa um projecto, define um valor, mas
 * abandona antes de pagar, ficava com esse valor "colado" à sessão — ao
 * criar um projecto NOVO e completamente diferente a seguir, o passo de
 * "Investimento" mostrava o valor da tentativa antiga e abandonada em vez
 * do valor mínimo actual. Isto explicou queixas de utilizadores a verem
 * "10.000 Kz" mesmo depois do mínimo ter sido reduzido para testes.
 */
class StaleSessionValueLeakTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function valor_de_projecto_abandonado_nao_aparece_num_projecto_novo(): void
    {
        $client = User::factory()->create(['role' => 'cliente']);

        // Projecto A: define um valor alto e abandona (nunca paga).
        Livewire::actingAs($client)
            ->test(Briefing::class)
            ->set('business_type1', 'Outro')
            ->set('business_type1_outro', 'Website')
            ->set('title1', 'Projecto Abandonado')
            ->set('necessity1', str_repeat('Preciso de algo específico. ', 3))
            ->call('goToStep3')
            ->call('submitBriefing');

        Livewire::actingAs($client)
            ->test(ServiceValue::class)
            ->set('valor', 99000)
            ->call('submitValue');

        // Projecto B: completamente novo, começado a seguir na mesma sessão.
        Livewire::actingAs($client)
            ->test(Briefing::class)
            ->set('business_type1', 'Outro')
            ->set('business_type1_outro', 'App')
            ->set('title1', 'Projecto Novo')
            ->set('necessity1', str_repeat('Preciso de outra coisa. ', 3))
            ->call('goToStep3')
            ->call('submitBriefing');

        // O passo de Investimento do projecto B NÃO deve mostrar os 99.000
        // do projecto A abandonado — deve cair no mínimo actual.
        Livewire::actingAs($client)
            ->test(ServiceValue::class)
            ->assertSet('valor', 10000.0);
    }
}
