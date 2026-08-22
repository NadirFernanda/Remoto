<?php

namespace Tests\Feature;

use App\Jobs\InitiateAppyPaySponsorshipChargeJob;
use App\Jobs\PollAppyPayInfoprodutoPatrocinioCheckoutJob;
use App\Livewire\Freelancer\Loja;
use App\Models\FreelancerProfile;
use App\Models\Infoproduto;
use App\Models\InfoprodutoPatrocinio;
use App\Models\InfoprodutoPatrocinioCheckout;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Testes para o patrocínio (boost) de infoprodutos na Loja — cobre o gap
 * reportado onde só era possível pagar com saldo da carteira ("patrocina
 * direto", sem nenhuma opção de método de pagamento). Agora cobre saldo e
 * Multicaixa Express, mirroring o padrão já usado nos outros checkouts
 * (SubscriptionCheckout, PurchaseCheckout).
 */
class PatrocinioInfoprodutoTest extends TestCase
{
    use RefreshDatabase;

    private function makeFreelancerComSaldo(float $saldo): User
    {
        $freelancer = User::factory()->create([
            'role'              => 'freelancer',
            'email_verified_at' => now(),
            'status'            => 'active',
            'kyc_status'        => 'verified',
        ]);

        FreelancerProfile::create([
            'user_id'    => $freelancer->id,
            'kyc_status' => 'verified',
            'skills'     => [],
            'languages'  => [],
        ]);

        Wallet::create([
            'user_id'        => $freelancer->id,
            'saldo'          => $saldo,
            'saldo_pendente' => 0,
            'saque_minimo'   => 1000,
            'taxa_saque'     => 0,
        ]);

        return $freelancer;
    }

    private function makeProduto(User $freelancer): Infoproduto
    {
        return Infoproduto::create([
            'freelancer_id' => $freelancer->id,
            'titulo'        => 'Ebook de Teste',
            'descricao'     => 'Descrição de teste',
            'tipo'          => 'ebook',
            'preco'         => 10000,
            'status'        => 'ativo',
            'slug'          => 'ebook-de-teste-' . uniqid(),
        ]);
    }

    private function fakeAppyPayCredentials(): void
    {
        config([
            'services.appypay.client_id'          => 'test-client',
            'services.appypay.client_secret'      => 'test-secret',
            'services.appypay.resource'           => 'test-resource',
            'services.appypay.auth_url'           => 'https://auth.appypay.test/token',
            'services.appypay.base_url'            => 'https://gwy.appypay.test',
            'services.appypay.payment_method_gpo'  => 'GPO_TEST',
        ]);
    }

    // ── Saldo da carteira ────────────────────────────────────────────────────

    #[Test]
    public function patrocinar_com_saldo_suficiente_activa_patrocinio_e_debita_carteira(): void
    {
        $freelancer = $this->makeFreelancerComSaldo(5000);
        $produto    = $this->makeProduto($freelancer);

        Livewire::actingAs($freelancer)
            ->test(Loja::class)
            ->call('openSponsor', $produto->id)
            ->set('dias', 3)
            ->call('confirmarPatrocinio')
            ->assertSet('feedbackType', 'success');

        $this->assertDatabaseHas('infoproduto_patrocinios', [
            'infoproduto_id' => $produto->id,
            'dias'           => 3,
            'status'         => 'ativo',
        ]);

        $freelancer->wallet->refresh();
        $this->assertEquals(5000 - 1800, $freelancer->wallet->saldo); // 600 Kz/dia × 3
    }

    #[Test]
    public function patrocinar_com_saldo_insuficiente_nao_debita_nem_activa(): void
    {
        $freelancer = $this->makeFreelancerComSaldo(500);
        $produto    = $this->makeProduto($freelancer);

        Livewire::actingAs($freelancer)
            ->test(Loja::class)
            ->call('openSponsor', $produto->id)
            ->set('dias', 3)
            ->call('confirmarPatrocinio')
            ->assertSet('feedbackType', 'error');

        $this->assertDatabaseMissing('infoproduto_patrocinios', ['infoproduto_id' => $produto->id]);

        $freelancer->wallet->refresh();
        $this->assertEquals(500, $freelancer->wallet->saldo);
    }

    // ── Multicaixa Express ───────────────────────────────────────────────────

    #[Test]
    public function patrocinar_via_express_nao_precisa_de_saldo_e_desperta_o_job_em_segundo_plano(): void
    {
        // chargeByPhone() corre em InitiateAppyPaySponsorshipChargeJob (segundo
        // plano), não dentro do pedido web — mesmo motivo do
        // AppyPayAsyncChargeTest (PaymentEscrow): a chamada pode demorar mais
        // do que qualquer timeout HTTP razoável. O pedido web só regista o
        // checkout e despacha o job; é o job (testado à parte) que faz a
        // chamada real à AppyPay e grava payment_method_used/appypay_charge_id.
        Bus::fake();

        $freelancer = $this->makeFreelancerComSaldo(0); // sem saldo nenhum — só Express resolve
        $produto    = $this->makeProduto($freelancer);

        Livewire::actingAs($freelancer)
            ->test(Loja::class)
            ->call('openSponsor', $produto->id)
            ->set('dias', 3)
            ->set('sponsor_payment_method', 'express')
            ->set('sponsor_phone_number', '923456789')
            ->call('chargeSponsorAppyPayPhone')
            ->assertSet('sponsor_step', 'waiting')
            ->assertSet('sponsor_error', '');

        $this->assertDatabaseHas('infoproduto_patrocinio_checkouts', [
            'infoproduto_id' => $produto->id,
            'user_id'        => $freelancer->id,
            'dias'           => 3,
            'amount'         => 1800,
            'payment_status' => 'initiated',
        ]);

        // Ainda não activado — só a reconciliação (webhook/polling) o faz
        $this->assertDatabaseMissing('infoproduto_patrocinios', ['infoproduto_id' => $produto->id]);

        Bus::assertDispatched(InitiateAppyPaySponsorshipChargeJob::class);
    }

    #[Test]
    public function job_de_inicio_grava_charge_id_e_desperta_o_polling(): void
    {
        $this->fakeAppyPayCredentials();
        Bus::fake([PollAppyPayInfoprodutoPatrocinioCheckoutJob::class]);
        Http::fake([
            'auth.appypay.test/*' => Http::response(['access_token' => 'fake-token'], 200),
            'gwy.appypay.test/*'  => Http::response(['id' => 'CHARGE123', 'responseStatus' => ['status' => 'pending']], 200),
        ]);

        $freelancer = $this->makeFreelancerComSaldo(0);
        $produto    = $this->makeProduto($freelancer);

        $checkout = InfoprodutoPatrocinioCheckout::create([
            'infoproduto_id' => $produto->id,
            'user_id'        => $freelancer->id,
            'dias'           => 3,
            'amount'         => 1800,
            'payment_status' => 'initiated',
        ]);

        (new InitiateAppyPaySponsorshipChargeJob(
            $checkout,
            '923456789',
            1800,
            'Patrocínio de teste',
            'MERCHANT123'
        ))->handle(app(\App\Modules\Payments\Services\AppyPayGateway::class));

        $this->assertDatabaseHas('infoproduto_patrocinio_checkouts', [
            'id'                  => $checkout->id,
            'payment_method_used' => 'appypay_gpo',
            'appypay_charge_id'   => 'CHARGE123',
            'payment_status'      => 'initiated',
        ]);

        Bus::assertDispatched(PollAppyPayInfoprodutoPatrocinioCheckoutJob::class);
    }

    #[Test]
    public function reconciliacao_appypay_activa_o_patrocinio_apos_pagamento_confirmado(): void
    {
        $freelancer = $this->makeFreelancerComSaldo(0);
        $produto    = $this->makeProduto($freelancer);

        $checkout = InfoprodutoPatrocinioCheckout::create([
            'infoproduto_id'      => $produto->id,
            'user_id'             => $freelancer->id,
            'dias'                => 5,
            'amount'              => 3000,
            'payment_method_used' => 'appypay_gpo',
            'appypay_charge_id'   => 'CHARGE999',
            'payment_status'      => 'initiated',
        ]);

        app(\App\Modules\Payments\Services\AppyPayReconciliationService::class)
            ->markPaidByChargeId('CHARGE999', 3000);

        $checkout->refresh();
        $this->assertEquals('paid', $checkout->payment_status);
        $this->assertNotNull($checkout->patrocinio_id);

        $this->assertDatabaseHas('infoproduto_patrocinios', [
            'id'             => $checkout->patrocinio_id,
            'infoproduto_id' => $produto->id,
            'dias'           => 5,
            'status'         => 'ativo',
        ]);

        // Confirma que o saldo NÃO foi tocado (pagamento foi via Express, não carteira)
        $freelancer->wallet->refresh();
        $this->assertEquals(0, $freelancer->wallet->saldo);
    }
}
