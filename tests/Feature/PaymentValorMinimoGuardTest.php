<?php

namespace Tests\Feature;

use App\Livewire\Client\PaymentEscrow;
use App\Models\PlatformSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Alguns rascunhos antigos (de tentativas de pagamento falhadas) ficaram
 * gravados com valor 0 — sem uma barreira aqui, o cliente conseguia chegar
 * a "Pagar 0 Kz via Express" e a AppyPay respondia com um erro genérico
 * (ou, pior, podia processar um valor sem sentido). Este teste confirma
 * que a tentativa de cobrança nem chega a sair do nosso lado nesse caso —
 * se chegasse, App­yPayGateway lançava excepção nestes testes (credenciais
 * não configuradas), o que seria a prova de que o guard falhou.
 */
class PaymentValorMinimoGuardTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function nao_tenta_cobrar_via_express_quando_valor_esta_abaixo_do_minimo(): void
    {
        PlatformSetting::set('project_min_value', '5');
        session(['client_order' => ['payment' => ['valor' => 0]]]);
        $client = User::factory()->create(['role' => 'cliente']);

        Livewire::actingAs($client)
            ->test(PaymentEscrow::class)
            ->assertSet('valor', 0.0)
            ->set('phone_number', '923456789')
            ->call('chargeAppyPayPhone')
            ->assertSet('appypay_step', 'form')
            ->assertSee('valor de pelo menos');
    }
}
