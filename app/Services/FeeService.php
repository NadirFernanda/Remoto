<?php

namespace App\Services;

class FeeService
{
    /**
     * Chave de referência para taxa de serviços (mantida para compatibilidade).
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
     * Taxa cobrada ao Cliente nos projetos freelancer (10% sobre o valor do projeto).
     */
    public const SERVICE_CLIENT_FEE_RATE = 0.10;

    /**
     * Taxa deduzida ao Freelancer nos projetos (20% do valor do projeto).
     */
    public const SERVICE_FREELANCER_FEE_RATE = 0.20;

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
