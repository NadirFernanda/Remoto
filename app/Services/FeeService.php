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
        try {
            return (float) PlatformSetting::get($key, $default * 100) / 100;
        } catch (\Exception $e) {
            return $default;
        }
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
     *  - O cliente paga exactamente o valor do projecto acordado (sem sobretaxa).
     *  - A plataforma retém taxa_cliente% do valor como comissão.
     *  - Na entrega o freelancer recebe (100 - taxa_cliente)% do valor do projecto.
     *
     * @return array{taxa_cliente: float, total_cliente: float, taxa: float, valor_liquido: float}
     */
    public function calculateServiceFee(float $valor): array
    {
        $clientRate = self::serviceClientRate();

        $taxa_cliente  = 0.0;                              // sem sobretaxa ao cliente
        $total_cliente = (float) $valor;                   // cliente paga o valor acordado
        $taxa          = round($valor * $clientRate, 2);   // 10% retidos pela plataforma
        $valor_liquido = round($valor - $taxa, 2);         // freelancer recebe 90%

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
