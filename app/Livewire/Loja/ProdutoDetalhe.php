<?php

namespace App\Livewire\Loja;

use Livewire\Component;
use App\Models\Infoproduto;
use App\Models\InfoprodutoCompra;
use App\Models\WalletLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ProdutoDetalhe extends Component
{
    public Infoproduto $produto;
    public bool $confirmando = false;
    public string $feedback  = '';
    public string $feedbackType = 'success';

    public function mount(Infoproduto $produto): void
    {
        if ($produto->status !== 'ativo') {
            abort(404);
        }
        $this->produto = $produto;
    }

    public function comprar(): void
    {
        $user = auth()->user();

        if (!$user) {
            $this->redirectRoute('login');
            return;
        }

        if ($this->produto->freelancer_id === $user->id) {
            $this->feedbackType = 'error';
            $this->feedback     = 'Não pode comprar o seu próprio produto.';
            $this->confirmando  = false;
            return;
        }

        if ($this->produto->jaCompradoPor($user->id)) {
            $this->feedbackType = 'error';
            $this->feedback     = 'Já adquiriu este produto.';
            $this->confirmando  = false;
            return;
        }

        $wallet = $user->wallet;

        if (!$wallet || $wallet->saldo < $this->produto->preco) {
            $this->feedbackType = 'error';
            $this->feedback     = 'Saldo insuficiente. Recarregue a sua carteira antes de comprar.';
            $this->confirmando  = false;
            return;
        }

        $comissao        = round($this->produto->preco * 0.30, 2);
        $valorFreelancer = round($this->produto->preco - $comissao, 2);

        DB::transaction(function () use ($user, $wallet, $comissao, $valorFreelancer) {
            InfoprodutoCompra::create([
                'infoproduto_id'      => $this->produto->id,
                'comprador_id'        => $user->id,
                'valor_pago'          => $this->produto->preco,
                'comissao_plataforma' => $comissao,
                'valor_freelancer'    => $valorFreelancer,
            ]);

            $wallet->decrement('saldo', $this->produto->preco);
            WalletLog::create([
                'user_id'   => $user->id,
                'wallet_id' => $wallet->id,
                'valor'     => -$this->produto->preco,
                'tipo'      => 'compra_infoproduto',
                'descricao' => "Compra do infoproduto \"{$this->produto->titulo}\".",
            ]);

            $freelancerWallet = $this->produto->freelancer->wallet;
            if ($freelancerWallet) {
                $freelancerWallet->increment('saldo', $valorFreelancer);
                WalletLog::create([
                    'user_id'   => $this->produto->freelancer_id,
                    'wallet_id' => $freelancerWallet->id,
                    'valor'     => $valorFreelancer,
                    'tipo'      => 'ganho_infoproduto',
                    'descricao' => "Venda do infoproduto \"{$this->produto->titulo}\".",
                ]);
            }

            $this->produto->increment('vendas_count');
        });

        $this->feedbackType = 'success';
        $this->feedback     = 'Compra realizada! Faça o download abaixo.';
        $this->confirmando  = false;
        $this->produto->refresh();
    }

    public function downloadArquivo()
    {
        $user = auth()->user();

        if (!$user || !$this->produto->jaCompradoPor($user->id)) {
            abort(403);
        }

        return Storage::disk('private')->download(
            $this->produto->arquivo_path,
            basename($this->produto->arquivo_path)
        );
    }

    public function render()
    {
        $jaComprado = auth()->check() && $this->produto->jaCompradoPor(auth()->id());
        $patrocinado = $this->produto->isPatrocinado();

        return view('livewire.loja.produto-detalhe', [
            'jaComprado'  => $jaComprado,
            'patrocinado' => $patrocinado,
        ])->layout('layouts.main', ['title' => $this->produto->titulo]);
    }
}
