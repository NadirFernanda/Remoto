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
     *  - O cliente paga EXACTAMENTE o valor do projecto (sem nenhum extra).
     *  - A plataforma retém 20% do valor como taxa de serviço.
     *  - O freelancer recebe 80% quando o cliente liberar o pagamento.
     *  - taxa_cliente: 10% informativo (mostrado ao cliente como taxa de plataforma)
     *  - total_cliente: igual ao valor — cliente NÃO paga nada além do acordado
     *  - taxa: 20% deduzido ao freelancer na liquidação
     *  - valor_liquido: 80% — valor que o freelancer recebe
     *
     * Exemplo: projecto de 50.000 Kz
     *   → cliente paga: 50.000 Kz
     *   → taxa plataforma: 10.000 Kz (20%)
     *   → freelancer recebe: 40.000 Kz (80%)
     *
     * @return array{taxa_cliente: float, total_cliente: float, taxa: float, valor_liquido: float}
     */
    public function calculateServiceFee(float $valor): array
    {
        $taxa_cliente  = round($valor * self::SERVICE_CLIENT_FEE_RATE, 2);   // 10% — informativo
        $total_cliente = $valor;                                               // cliente paga o valor exacto
        $taxa          = round($valor * self::SERVICE_FREELANCER_FEE_RATE, 2); // 20% plataforma
        $valor_liquido = round($valor - $taxa, 2);                             // 80% freelancer

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
