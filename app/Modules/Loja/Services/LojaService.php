<?php

namespace App\Modules\Loja\Services;

use App\Models\Infoproduto;
use App\Models\InfoprodutoCompra;
use App\Models\Wallet;
use App\Models\WalletLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Services\FeeService;

class LojaService
{
    /**
     * Caminho da carteira: debita o comprador e regista a compra.
     *
     * @throws \RuntimeException on validation failure
     */
    public function comprar(User $user, Infoproduto $produto): InfoprodutoCompra
    {
        if ($produto->freelancer_id === $user->id) {
            throw new \RuntimeException('Não pode comprar o seu próprio produto.');
        }

        if ($produto->jaCompradoPor($user->id)) {
            throw new \RuntimeException('Já adquiriu este produto.');
        }

        $compra = DB::transaction(function () use ($user, $produto) {
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
            if (!$wallet || $wallet->saldo < $produto->preco) {
                throw new \RuntimeException('Saldo insuficiente. Recarregue a sua carteira antes de comprar.');
            }

            // Reconfirma sob lock — fecha a mesma race condition que a constraint
            // única em (infoproduto_id, comprador_id) protege ao nível da BD.
            if ($produto->jaCompradoPor($user->id)) {
                throw new \RuntimeException('Já adquiriu este produto.');
            }

            $wallet->decrement('saldo', $produto->preco);
            WalletLog::create([
                'user_id'   => $user->id,
                'wallet_id' => $wallet->id,
                'valor'     => -$produto->preco,
                'tipo'      => 'compra_infoproduto',
                'descricao' => "Compra do infoproduto \"{$produto->titulo}\".",
            ]);

            return $this->activate($user, $produto, $produto->preco, 'wallet');
        });

        (new \App\Services\AffiliateService())->creditCommissionForReferredAction($user, 'buy_product', $produto->id);

        return $compra;
    }

    /**
     * Regista a compra e credita a carteira do freelancer — partilhado entre
     * comprar() (após débito da carteira) e a reconciliação AppyPay (após
     * confirmação do pagamento). Nunca debita o comprador.
     */
    public function activate(User $user, Infoproduto $produto, float $preco, string $paymentMethodUsed): InfoprodutoCompra
    {
        $fee = (new FeeService())->calculateLojaFee($preco);

        $compra = InfoprodutoCompra::create([
            'infoproduto_id'      => $produto->id,
            'comprador_id'        => $user->id,
            'valor_pago'          => $preco,
            'comissao_plataforma' => $fee['comissao'],
            'valor_freelancer'    => $fee['valor_freelancer'],
        ]);

        // firstOrCreate: garante que a carteira existe antes de creditar
        // (evita perda silenciosa de pagamento se wallet row não existir)
        $freelancerWallet = Wallet::firstOrCreate(
            ['user_id' => $produto->freelancer_id],
            ['saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => 1000, 'taxa_saque' => 0]
        );
        $freelancerWallet->increment('saldo', $fee['valor_freelancer']);
        WalletLog::create([
            'user_id'   => $produto->freelancer_id,
            'wallet_id' => $freelancerWallet->id,
            'valor'     => $fee['valor_freelancer'],
            'tipo'      => 'ganho_infoproduto',
            'descricao' => "Venda do infoproduto \"{$produto->titulo}\" via {$paymentMethodUsed} — comissão retida.",
        ]);

        $produto->increment('vendas_count');

        return $compra;
    }
}
