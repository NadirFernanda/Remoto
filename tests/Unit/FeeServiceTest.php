<?php

namespace Tests\Unit;

use App\Services\FeeService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Testes unitários para FeeService — sem base de dados.
 *
 * Garante que o modelo de taxas da plataforma está matematicamente correcto:
 *   - Serviços freelancer: cliente paga o valor acordado, plataforma retira 10%, freelancer recebe 90%
 *   - Loja (infoprodutos): -20% ao vendedor
 *   - Assinaturas (criadores): -25% ao criador
 */
class FeeServiceTest extends TestCase
{
    private FeeService $fee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fee = new FeeService();
    }

    // ── calculateServiceFee ──────────────────────────────────────────────────

    #[Test]
    public function test_service_fee_on_50000(): void
    {
        $result = $this->fee->calculateServiceFee(50_000);

        $this->assertEquals(0.0,      $result['taxa_cliente'],  'cliente não paga sobretaxa');
        $this->assertEquals(50_000.0, $result['total_cliente'], 'cliente paga exatamente o valor acordado');
        $this->assertEquals(5_000.0,  $result['taxa'],          'plataforma retira 10% do valor');
        $this->assertEquals(45_000.0, $result['valor_liquido'], 'freelancer recebe 90%');
    }

    #[Test]
    public function test_service_fee_on_10000(): void
    {
        $result = $this->fee->calculateServiceFee(10_000);

        $this->assertEquals(0.0,      $result['taxa_cliente']);
        $this->assertEquals(10_000.0, $result['total_cliente']);
        $this->assertEquals(1_000.0,  $result['taxa']);
        $this->assertEquals(9_000.0,  $result['valor_liquido']);
    }

    #[Test]
    public function test_service_fee_rounding(): void
    {
        // Valor que provoca casas decimais
        $result = $this->fee->calculateServiceFee(333.33);

        $this->assertEquals(0.0,                              $result['taxa_cliente']);
        $this->assertEquals(round(333.33 * 0.90, 2), $result['valor_liquido']);
    }

    #[Test]
    public function test_service_fee_returns_correct_keys(): void
    {
        $result = $this->fee->calculateServiceFee(1000);

        $this->assertArrayHasKey('taxa_cliente',  $result);
        $this->assertArrayHasKey('total_cliente', $result);
        $this->assertArrayHasKey('taxa',           $result);
        $this->assertArrayHasKey('valor_liquido', $result);
    }

    #[Test]
    public function test_service_fee_identities(): void
    {
        $valor  = 100_000;
        $result = $this->fee->calculateServiceFee($valor);

        // total_cliente = valor + taxa_cliente
        $this->assertEquals(
            $result['total_cliente'],
            $valor + $result['taxa_cliente']
        );

        // valor_liquido + taxa = valor original
        $this->assertEquals(
            $valor,
            $result['valor_liquido'] + $result['taxa']
        );
    }

    // ── calculateLojaFee ─────────────────────────────────────────────────────

    #[Test]
    public function test_loja_fee_on_10000(): void
    {
        $result = $this->fee->calculateLojaFee(10_000);

        $this->assertEquals(2_000.0, $result['comissao'],         '20% comissão da loja');
        $this->assertEquals(8_000.0, $result['valor_freelancer'], '80% ao criador');
    }

    #[Test]
    public function test_loja_fee_identities(): void
    {
        $preco  = 25_000;
        $result = $this->fee->calculateLojaFee($preco);

        $this->assertEquals($preco, $result['comissao'] + $result['valor_freelancer']);
    }

    // ── calculateSubscriptionFee ──────────────────────────────────────────────

    #[Test]
    public function test_subscription_fee_on_5000(): void
    {
        $result = $this->fee->calculateSubscriptionFee(5_000);

        $this->assertEquals(1_250.0, $result['comissao'],    '25% da plataforma');
        $this->assertEquals(3_750.0, $result['valor_criador'], '75% ao criador');
    }

    #[Test]
    public function test_subscription_fee_identities(): void
    {
        $valor  = 8_000;
        $result = $this->fee->calculateSubscriptionFee($valor);

        $this->assertEquals($valor, $result['comissao'] + $result['valor_criador']);
    }

    // ── Constants ────────────────────────────────────────────────────────────

    #[Test]
    public function test_constants_reflect_business_rules(): void
    {
        $this->assertEquals(0.10, FeeService::SERVICE_CLIENT_FEE_RATE,    'cliente: 10%');
        $this->assertEquals(0.20, FeeService::SERVICE_FREELANCER_FEE_RATE, 'freelancer: 20%');
        $this->assertEquals(0.20, FeeService::LOJA_FEE_RATE,              'loja: 20%');
        $this->assertEquals(0.25, FeeService::SUBSCRIPTION_FEE_RATE,      'assinaturas: 25%');
    }
}
