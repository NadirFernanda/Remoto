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
    public const SERVICE_FREELANCER_FEE_RATE = 0.10;

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
     * Modelo de taxas — a plataforma cobra em ambas as pontas, cada uma com a
     * sua própria taxa configurável (Admin > Taxas e Comissões):
     *  - O cliente paga o valor acordado + taxa_cliente% de sobretaxa da plataforma.
     *  - O freelancer recebe (100 - taxa_freelancer)% do valor acordado, retido
     *    antes do pagamento.
     *  - Com as taxas por omissão (10% + 10%), num projecto de 100.000 Kz:
     *    cliente paga 110.000 Kz, freelancer recebe 90.000 Kz, a plataforma
     *    fica com 20.000 Kz no total.
     *
     * @return array{taxa_cliente: float, total_cliente: float, taxa: float, valor_liquido: float}
     */
    public function calculateServiceFee(float $valor): array
    {
        $clientRate     = self::serviceClientRate();
        $freelancerRate = self::serviceFreelancerRate();

        $taxa_cliente  = round($valor * $clientRate, 2);         // sobretaxa cobrada ao cliente
        $total_cliente = round($valor + $taxa_cliente, 2);       // cliente paga valor + sobretaxa
        $taxa          = round($valor * $freelancerRate, 2);     // retido do lado do freelancer
        $valor_liquido = round($valor - $taxa, 2);               // freelancer recebe o resto

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
