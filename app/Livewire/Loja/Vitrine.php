<?php

namespace App\Livewire\Loja;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Infoproduto;
use App\Models\InfoprodutoCompra;
use App\Models\WalletLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Vitrine extends Component
{
    use WithPagination;

    public string $busca   = '';
    public string $tipo    = '';
    public string $ordenar = 'recente';

    // ─── Filter reset on search change ───────────────────────────────

    public function updatingBusca(): void  { $this->resetPage(); }
    public function updatingTipo(): void   { $this->resetPage(); }
    public function updatingOrdenar(): void { $this->resetPage(); }

    // ─── Purchase ────────────────────────────────────────────────────

    public function comprar(int $produtoId)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->redirect(route('login'));
        }

        $produto = Infoproduto::where('status', 'ativo')->findOrFail($produtoId);

        if ($produto->freelancer_id === $user->id) {
            session()->flash('error_loja', 'Não pode comprar o seu próprio produto.');
            return;
        }

        if ($produto->jaCompradoPor($user->id)) {
            session()->flash('error_loja', 'Já adquiriu este produto.');
            return;
        }

        $wallet = $user->wallet;

        if (!$wallet || $wallet->saldo < $produto->preco) {
            session()->flash('error_loja', 'Saldo insuficiente. Recarregue a sua carteira.');
            return;
        }

        $comissao       = round($produto->preco * 0.30, 2);
        $valorFreelancer = round($produto->preco - $comissao, 2);

        DB::transaction(function () use ($produto, $user, $wallet, $comissao, $valorFreelancer) {
            // Registrar compra
            InfoprodutoCompra::create([
                'infoproduto_id'       => $produto->id,
                'comprador_id'         => $user->id,
                'valor_pago'           => $produto->preco,
                'comissao_plataforma'  => $comissao,
                'valor_freelancer'     => $valorFreelancer,
            ]);

            // Debitar comprador
            $wallet->decrement('saldo', $produto->preco);
            WalletLog::create([
                'user_id'   => $user->id,
                'wallet_id' => $wallet->id,
                'valor'     => -$produto->preco,
                'tipo'      => 'compra_infoproduto',
                'descricao' => "Compra do infoproduto \"{$produto->titulo}\".",
            ]);

            // Creditar freelancer (70%)
            $freelancerWallet = $produto->freelancer->wallet;
            if ($freelancerWallet) {
                $freelancerWallet->increment('saldo', $valorFreelancer);
                WalletLog::create([
                    'user_id'   => $produto->freelancer_id,
                    'wallet_id' => $freelancerWallet->id,
                    'valor'     => $valorFreelancer,
                    'tipo'      => 'ganho_infoproduto',
                    'descricao' => "Venda do infoproduto \"{$produto->titulo}\" — comissão de 30% retida.",
                ]);
            }

            // Incrementar contador de vendas
            $produto->increment('vendas_count');
        });

        session()->flash('success_loja', 'Compra realizada! Vá ao detalhe do produto para fazer o download.');
        $this->redirect(route('loja.show', $produto->slug));
    }

    // ─── Render ──────────────────────────────────────────────────────

    public function render()
    {
        $query = Infoproduto::where('status', 'ativo')
            ->with(['freelancer:id,name,profile_photo']);

        if ($this->busca) {
            $query->where(function ($q) {
                $q->where('titulo', 'ilike', "%{$this->busca}%")
                  ->orWhere('descricao', 'ilike', "%{$this->busca}%");
            });
        }

        if ($this->tipo) {
            $query->where('tipo', $this->tipo);
        }

        // Sponsored products always on top
        $today = Carbon::today()->toDateString();
        $query->orderByRaw(
            "EXISTS (SELECT 1 FROM infoproduto_patrocinios ip
                     WHERE ip.infoproduto_id = infoprodutos.id
                       AND ip.status = 'ativo'
                       AND ip.data_inicio <= ?
                       AND ip.data_fim >= ?) DESC",
            [$today, $today]
        );

        match ($this->ordenar) {
            'preco_asc'    => $query->orderBy('preco', 'asc'),
            'preco_desc'   => $query->orderBy('preco', 'desc'),
            'mais_vendidos' => $query->orderBy('vendas_count', 'desc'),
            default        => $query->orderByDesc('created_at'),
        };

        $produtos = $query->paginate(12);

        // Tag sponsored products
        foreach ($produtos as $produto) {
            $produto->patrocinado = $produto->isPatrocinado();
        }

        return view('livewire.loja.vitrine', [
            'produtos' => $produtos,
        ])->layout('layouts.app');
    }
}
