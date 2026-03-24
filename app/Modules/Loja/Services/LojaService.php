<?php

namespace App\Modules\Loja\Services;

use App\Models\Infoproduto;
use App\Models\InfoprodutoCompra;
use App\Models\Wallet;
use App\Models\WalletLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LojaService
{
    /**
     * Execute the purchase of an infoproduto.
     *
     * @throws \RuntimeException on validation failure
     */
    public function comprar(User $user, Infoproduto $produto): void
    {
        if ($produto->freelancer_id === $user->id) {
            throw new \RuntimeException('Não pode comprar o seu próprio produto.');
        }

        if ($produto->jaCompradoPor($user->id)) {
            throw new \RuntimeException('Já adquiriu este produto.');
        }

        $wallet = $user->wallet;

        if (!$wallet || $wallet->saldo < $produto->preco) {
            throw new \RuntimeException('Saldo insuficiente. Recarregue a sua carteira antes de comprar.');
        }

        $comissao        = round($produto->preco * 0.20, 2);
        $valorFreelancer = round($produto->preco - $comissao, 2);

        DB::transaction(function () use ($user, $wallet, $produto, $comissao, $valorFreelancer) {
            InfoprodutoCompra::create([
                'infoproduto_id'      => $produto->id,
                'comprador_id'        => $user->id,
                'valor_pago'          => $produto->preco,
                'comissao_plataforma' => $comissao,
                'valor_freelancer'    => $valorFreelancer,
            ]);

            $wallet->decrement('saldo', $produto->preco);
            WalletLog::create([
                'user_id'   => $user->id,
                'wallet_id' => $wallet->id,
                'valor'     => -$produto->preco,
                'tipo'      => 'compra_infoproduto',
                'descricao' => "Compra do infoproduto \"{$produto->titulo}\".",
            ]);

            // firstOrCreate: garante que a carteira existe antes de creditar
            // (evita perda silenciosa de pagamento se wallet row não existir)
            $freelancerWallet = Wallet::firstOrCreate(
                ['user_id' => $produto->freelancer_id],
                ['saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => 1000, 'taxa_saque' => 0]
            );
            $freelancerWallet->increment('saldo', $valorFreelancer);
            WalletLog::create([
                'user_id'   => $produto->freelancer_id,
                'wallet_id' => $freelancerWallet->id,
                'valor'     => $valorFreelancer,
                'tipo'      => 'ganho_infoproduto',
                'descricao' => "Venda do infoproduto \"{$produto->titulo}\" — comissão de 20% retida.",
            ]);

            $produto->increment('vendas_count');
        });
    }
}
