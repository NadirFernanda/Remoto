<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Infoproduto;
use App\Models\InfoprodutoPatrocinio;
use App\Models\InfoprodutoCompra;

class LojaAdmin extends Component
{
    use WithPagination;

    public string $filtroStatus = '';
    public string $filtroTipo   = '';
    public string $busca        = '';
    public string $feedback     = '';

    public function mount(): void
    {
        abort_if(auth()->user()?->role !== 'admin', 403);
    }

    public function updatingBusca(): void       { $this->resetPage(); }
    public function updatingFiltroStatus(): void { $this->resetPage(); }

    public function aprovar(int $id): void
    {
        Infoproduto::findOrFail($id)->update(['status' => 'ativo']);
        $this->feedback = 'Produto aprovado e publicado na Loja.';
    }

    public function rejeitar(int $id): void
    {
        Infoproduto::findOrFail($id)->update(['status' => 'inativo']);
        $this->feedback = 'Produto rejeitado e ocultado da Loja.';
    }

    public function render()
    {
        $query = Infoproduto::with('freelancer:id,name,email')
            ->withCount('compras');

        if ($this->filtroStatus) {
            $query->where('status', $this->filtroStatus);
        }

        if ($this->filtroTipo) {
            $query->where('tipo', $this->filtroTipo);
        }

        if ($this->busca) {
            $query->where(function ($q) {
                $q->where('titulo', 'ilike', "%{$this->busca}%")
                  ->orWhereHas('freelancer', fn($u) => $u->where('name', 'ilike', "%{$this->busca}%"));
            });
        }

        $produtos = $query->orderByDesc('created_at')->paginate(20);

        $stats = [
            'total'         => Infoproduto::count(),
            'em_moderacao'  => Infoproduto::where('status', 'em_moderacao')->count(),
            'ativos'        => Infoproduto::where('status', 'ativo')->count(),
            'vendas_hoje'   => InfoprodutoCompra::whereDate('created_at', today())->count(),
            'receita_total' => InfoprodutoCompra::sum('comissao_plataforma'),
            'patrocinios_ativos' => InfoprodutoPatrocinio::where('status', 'ativo')
                                        ->where('data_fim', '>=', today())->count(),
        ];

        return view('livewire.admin.loja-admin', [
            'produtos' => $produtos,
            'stats'    => $stats,
        ])->layout('layouts.dashboard', ['dashboardTitle' => 'Gestão da Loja']);
    }
}
