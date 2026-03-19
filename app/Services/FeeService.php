<?php

namespace App\Services;

use App\Models\PlatformSetting;

class FeeService
{
    /**
     * Chave usada no PlatformSetting para a taxa de comissão de serviços.
     */
    public const SETTING_KEY = 'commission_rate';

    /**
     * Taxa fixa da Loja (infoprodutos) — 20%.
     */
    public const LOJA_FEE_RATE = 0.20;

    /**
     * Taxa fixa de Assinaturas (criadores) — 25%.
     */
    public const SUBSCRIPTION_FEE_RATE = 0.25;

    /**
     * Retorna a taxa de comissão de serviços como percentagem (ex: 10 para 10%).
     * Lê do PlatformSetting ou usa 10% como fallback.
     */
    public function getServiceFeeRate(): float
    {
        return (float) PlatformSetting::get(self::SETTING_KEY, 10);
    }

    /**
     * Calcula a taxa e o valor líquido para um serviço.
     *
     * @return array{taxa: float, valor_liquido: float}
     */
    public function calculateServiceFee(float $valor): array
    {
        $rate          = $this->getServiceFeeRate() / 100;
        $taxa          = round($valor * $rate, 2);
        $valor_liquido = round($valor - $taxa, 2);

        return [
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
