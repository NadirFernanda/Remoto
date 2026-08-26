<?php

namespace Tests\Feature;

use App\Jobs\InitiateAppyPayChargeJob;
use App\Livewire\Client\PaymentEscrow;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Regressão: chargeAppyPayPhone() chamava a AppyPay de forma síncrona,
 * bloqueando a página do cliente à espera da resposta — mas o cliente sai
 * da página para aprovar no telemóvel, o que pode demorar mais do que
 * qualquer timeout razoável. Confirmado em produção (20/08/2026): a
 * página falhava exactamente enquanto o cliente estava a aprovar no
 * telemóvel, mesmo quando o pagamento se concluía a seguir com sucesso.
 *
 * Agora a chamada corre em InitiateAppyPayChargeJob (segundo plano) — o
 * pedido web só regista o serviço e passa para o ecrã de espera, que faz
 * polling ao NOSSO serviço (não à AppyPay directamente).
 */
class AppyPayAsyncChargeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function chargeAppyPayPhone_despacha_o_job_em_vez_de_chamar_a_appypay_directamente(): void
    {
        Queue::fake();

        $client = User::factory()->create(['role' => 'cliente']);
        session(['client_order' => [
            'briefing_raw'  => 'Preciso de um site.',
            'briefing_text' => 'Preciso de um site.',
            'title'         => 'Site',
        ]]);

        Livewire::actingAs($client)
            ->test(PaymentEscrow::class)
            ->set('phone_number', '923456789')
            ->call('chargeAppyPayPhone')
            ->assertSet('appypay_step', 'waiting')
            ->assertSet('appypay_error', '');

        Queue::assertPushed(InitiateAppyPayChargeJob::class);

        // A criação do serviço não depende do job ter corrido — deve
        // existir logo após o pedido web, com o merchantTransactionId já
        // gravado (mesmo antes de qualquer resposta da AppyPay).
        $service = Service::where('cliente_id', $client->id)->first();
        $this->assertNotNull($service);
        $this->assertNotEmpty($service->appypay_merchant_transaction_id);
        $this->assertNull($service->appypay_charge_id);
    }

    #[Test]
    public function ecra_de_espera_continua_a_aguardar_enquanto_o_job_nao_tem_charge_id(): void
    {
        $client  = User::factory()->create(['role' => 'cliente']);
        $service = Service::create([
            'cliente_id'     => $client->id,
            'titulo'         => 'Site',
            'briefing'       => 'Preciso de um site.',
            'valor'          => 5,
            'taxa'           => 0.5,
            'valor_liquido'  => 4.5,
            'status'         => 'payment_pending',
            'payment_status' => 'initiated',
        ]);

        // Job ainda não conseguiu charge_id nenhum — não deve haver erro
        // nem redirecionamento, só continuar em espera.
        Livewire::actingAs($client)
            ->test(PaymentEscrow::class)
            ->set('appypay_service_id', $service->id)
            ->set('appypay_step', 'waiting')
            ->call('checkAppyPayStatus')
            ->assertSet('appypay_step', 'waiting')
            ->assertSet('appypay_error', '');
    }

    #[Test]
    public function ecra_de_espera_mostra_erro_se_o_job_marcar_o_pagamento_como_falhado(): void
    {
        $client  = User::factory()->create(['role' => 'cliente']);
        $service = Service::create([
            'cliente_id'     => $client->id,
            'titulo'         => 'Site',
            'briefing'       => 'Preciso de um site.',
            'valor'          => 5,
            'taxa'           => 0.5,
            'valor_liquido'  => 4.5,
            'status'         => 'payment_pending',
            'payment_status' => 'failed', // já marcado pelo job (falha ambígua)
        ]);

        Livewire::actingAs($client)
            ->test(PaymentEscrow::class)
            ->set('appypay_service_id', $service->id)
            ->set('appypay_step', 'waiting')
            ->call('checkAppyPayStatus')
            ->assertSet('appypay_step', 'form')
            ->assertSet('appypay_error', 'O pagamento não foi confirmado. Tente novamente.');
    }
}
