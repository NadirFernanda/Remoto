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
     * Calcula as taxas duais dos projetos freelancer:
     *  - taxa_cliente: 10% cobrado ao cliente (adicionado ao preço)
     *  - total_cliente: total que o cliente paga (valor + taxa_cliente)
     *  - taxa: 20% deduzido do freelancer
     *  - valor_liquido: o que o freelancer recebe (valor - taxa)
     *
     * @return array{taxa_cliente: float, total_cliente: float, taxa: float, valor_liquido: float}
     */
    public function calculateServiceFee(float $valor): array
    {
        $taxa_cliente  = round($valor * self::SERVICE_CLIENT_FEE_RATE, 2);
        $total_cliente = round($valor + $taxa_cliente, 2);
        $taxa          = round($valor * self::SERVICE_FREELANCER_FEE_RATE, 2);
        $valor_liquido = round($valor - $taxa, 2);

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
