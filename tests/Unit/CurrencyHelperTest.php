<?php

namespace Tests\Unit;

use Tests\TestCase;

class CurrencyHelperTest extends TestCase
{
    public function test_convert_brl_to_aoa_multiplies_by_configured_rate(): void
    {
        config(['currency.aoa_per_brl' => 100, 'currency.decimals' => 2]);

        $result = convert_brl_to_aoa(10);

        $this->assertEquals(1000.0, $result);
    }

    public function test_convert_brl_to_aoa_rounds_to_configured_decimal_places(): void
    {
        config(['currency.aoa_per_brl' => 3.333, 'currency.decimals' => 2]);

        $result = convert_brl_to_aoa(10);

        $this->assertEquals(33.33, $result);
    }

    public function test_convert_brl_to_aoa_returns_zero_for_zero_input(): void
    {
        $this->assertEquals(0.0, convert_brl_to_aoa(0));
    }

    public function test_convert_brl_to_aoa_works_with_negative_value(): void
    {
        config(['currency.aoa_per_brl' => 50, 'currency.decimals' => 2]);

        $result = convert_brl_to_aoa(-5);

        $this->assertEquals(-250.0, $result);
    }

    public function test_money_aoa_returns_string(): void
    {
        config(['currency.aoa_per_brl' => 41.5, 'currency.decimals' => 2, 'currency.symbol' => 'Kz']);

        $result = money_aoa(100);

        $this->assertIsString($result);
        $this->assertStringContainsString('Kz', $result);
    }

    /**
     * Todos os valores monetários desta plataforma já são guardados em Kz —
     * money_aoa() NÃO deve converter por omissão. Se este teste falhar,
     * qualquer chamada a money_aoa($valor) sem o segundo argumento volta a
     * inflacionar todos os valores em Kz do admin/financeiro por ~41,5x
     * (o bug real reportado nos Saques: 50.000 Kz a aparecer como 2.075.000 Kz).
     */
    public function test_money_aoa_nao_converte_por_omissao(): void
    {
        config(['currency.aoa_per_brl' => 41.5, 'currency.decimals' => 2, 'currency.symbol' => 'Kz']);

        $this->assertSame('Kz 50.000,00', money_aoa(50000));
        $this->assertSame(money_aoa(50000), money_aoa(50000, false));
    }
}
