<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Infoproduto;
use App\Models\InfoprodutoPatrocinio;
use App\Models\InfoprodutoCompra;
use Illuminate\Support\Facades\Storage;

class LojaAdmin extends Component
{
    use WithPagination;

    public string $filtroStatus = '';
    public string $filtroTipo   = '';
    public string $busca        = '';
    public string $feedback     = '';

    // ─── Product inspection modal ───────────────────────────────────
    public bool $showInspecao    = false;
    public ?int $inspecaoId     = null;

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
        $this->fecharInspecao();
    }

    public function rejeitar(int $id): void
    {
        Infoproduto::findOrFail($id)->update(['status' => 'inativo']);
        $this->feedback = 'Produto rejeitado e ocultado da Loja.';
        $this->fecharInspecao();
    }

    // ─── Inspection ─────────────────────────────────────────────────

    public function inspecionar(int $id): void
    {
        $this->inspecaoId   = $id;
        $this->showInspecao = true;
    }

    public function fecharInspecao(): void
    {
        $this->showInspecao = false;
        $this->inspecaoId   = null;
    }

    public function downloadArquivoAdmin(int $id)
    {
        $produto = Infoproduto::findOrFail($id);

        if (!$produto->arquivo_path || !Storage::disk('private')->exists($produto->arquivo_path)) {
            $this->feedback = 'Ficheiro do produto não encontrado.';
            return;
        }

        return Storage::disk('private')->download(
            $produto->arquivo_path,
            $produto->titulo . ' — ' . basename($produto->arquivo_path)
        );
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

        $produtoInspecao = null;
        if ($this->showInspecao && $this->inspecaoId) {
            $produtoInspecao = Infoproduto::with('freelancer:id,name,email')->find($this->inspecaoId);
        }

        return view('livewire.admin.loja-admin', [
            'produtos'        => $produtos,
            'stats'           => $stats,
            'produtoInspecao' => $produtoInspecao,
        ])->layout('layouts.dashboard', ['dashboardTitle' => 'Gestão da Loja']);
    }
}
