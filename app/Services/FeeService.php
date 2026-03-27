<?php

namespace App\Services;

use App\Models\PlatformSetting;

class FeeService
{
    /**
     * Chave de referência para taxa de serviços (mantida para compatibilidade).
     */
    public const SETTING_KEY = 'commission_rate';

    /**
     * Taxas padrão (fallback quando a setting não está no DB).
     */
    public const LOJA_FEE_RATE              = 0.20;
    public const SUBSCRIPTION_FEE_RATE      = 0.25;
    public const SERVICE_CLIENT_FEE_RATE    = 0.10;
    public const SERVICE_FREELANCER_FEE_RATE = 0.20;

    // ── helpers ──────────────────────────────────────────────────────

    private static function rate(string $key, float $default): float
    {
        return (float) PlatformSetting::get($key, $default * 100) / 100;
    }

    public static function serviceClientRate(): float
    {
        return self::rate('service_client_fee_rate', self::SERVICE_CLIENT_FEE_RATE);
    }

    public static function serviceFreelancerRate(): float
    {
        return self::rate('service_freelancer_fee_rate', self::SERVICE_FREELANCER_FEE_RATE);
    }

    public static function lojaRate(): float
    {
        return self::rate('loja_fee_rate', self::LOJA_FEE_RATE);
    }

    public static function subscriptionRate(): float
    {
        return self::rate('subscription_fee_rate', self::SUBSCRIPTION_FEE_RATE);
    }

    public static function patrocinioDiario(): float
    {
        return (float) PlatformSetting::get('patrocinio_diario', 600);
    }

    public static function affiliateSignupCommission(): float
    {
        return (float) PlatformSetting::get('affiliate_signup_commission', 200);
    }

    // ── calculations ─────────────────────────────────────────────────

    /**
     * Modelo de taxas:
     *  - O cliente paga o valor do projecto + taxa_cliente%.
     *  - Na entrega o freelancer recebe (100 - taxa_freelancer)% do valor do projecto.
     *
     * @return array{taxa_cliente: float, total_cliente: float, taxa: float, valor_liquido: float}
     */
    public function calculateServiceFee(float $valor): array
    {
        $clientRate    = self::serviceClientRate();
        $freelancerRate = self::serviceFreelancerRate();

        $taxa_cliente  = round($valor * $clientRate, 2);
        $total_cliente = round($valor + $taxa_cliente, 2);
        $taxa          = round($valor * $freelancerRate, 2);
        $valor_liquido = round($valor - $taxa, 2);

        return [
            'taxa_cliente'  => $taxa_cliente,
            'total_cliente' => $total_cliente,
            'taxa'          => $taxa,
            'valor_liquido' => $valor_liquido,
        ];
    }

    /**
     * @return array{comissao: float, valor_freelancer: float}
     */
    public function calculateLojaFee(float $preco): array
    {
        $rate            = self::lojaRate();
        $comissao        = round($preco * $rate, 2);
        $valor_freelancer = round($preco - $comissao, 2);

        return [
            'comissao'         => $comissao,
            'valor_freelancer' => $valor_freelancer,
        ];
    }

    /**
     * @return array{comissao: float, valor_criador: float}
     */
    public function calculateSubscriptionFee(float $preco): array
    {
        $rate         = self::subscriptionRate();
        $comissao     = round($preco * $rate, 2);
        $valor_criador = round($preco - $comissao, 2);

        return [
            'comissao'      => $comissao,
            'valor_criador' => $valor_criador,
        ];
    }
}


    /**
     * Modelo de taxas:
     *  - O cliente paga o valor do projecto + 10% de taxa de serviço.
     *  - Na entrega o freelancer recebe 80% do valor do projecto (plataforma fica 20%).
     *
     * Exemplo: projecto de 50.000 Kz
     *   → taxa_cliente:  5.000 Kz (10%)
     *   → total_cliente: 55.000 Kz — o que o cliente paga
     *   → taxa:          10.000 Kz (20% — deduzida ao freelancer na entrega)
     *   → valor_liquido: 40.000 Kz (80% — o que o freelancer recebe)
     *
     * @return array{taxa_cliente: float, total_cliente: float, taxa: float, valor_liquido: float}
     */
    public function calculateServiceFee(float $valor): array
    {
        $taxa_cliente  = round($valor * self::SERVICE_CLIENT_FEE_RATE, 2);   // 10% adicionado ao cliente
        $total_cliente = round($valor + $taxa_cliente, 2);                   // total que o cliente paga
        $taxa          = round($valor * self::SERVICE_FREELANCER_FEE_RATE, 2); // 20% plataforma na entrega
        $valor_liquido = round($valor - $taxa, 2);                             // 80% freelancer na entrega

        return [
            'taxa_cliente'  => $taxa_cliente,
            'total_cliente' => $total_cliente,
            'taxa'          => $taxa,
            'valor_liquido' => $valor_liquido,
        ];
    }

    /**
     * Calcula a comissão da plataforma e o valor do freelancer para infoprodutos.
     *
     * @return array{comissao: float, valor_freelancer: float}
     */
    public function calculateLojaFee(float $preco): array
    {
        $comissao        = round($preco * self::LOJA_FEE_RATE, 2);
        $valor_freelancer = round($preco - $comissao, 2);

        return [
            'comissao'        => $comissao,
            'valor_freelancer' => $valor_freelancer,
        ];
    }

    /**
     * Calcula a comissão da plataforma e o valor líquido para assinaturas de criadores.
     *
     * @return array{comissao: float, valor_criador: float}
     */
    public function calculateSubscriptionFee(float $preco): array
    {
        $comissao     = round($preco * self::SUBSCRIPTION_FEE_RATE, 2);
        $valor_criador = round($preco - $comissao, 2);

        return [
            'comissao'      => $comissao,
            'valor_criador' => $valor_criador,
        ];
    }
}
