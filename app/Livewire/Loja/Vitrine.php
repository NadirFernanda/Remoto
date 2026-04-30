<?php

namespace App\Livewire\Loja;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Infoproduto;
use App\Modules\Loja\Services\LojaService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

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

        try {
            app(LojaService::class)->comprar($user, $produto);
        } catch (\RuntimeException $e) {
            session()->flash('error_loja', $e->getMessage());
            return;
        }

        session()->flash('success_loja', 'Compra realizada! Vá ao detalhe do produto para fazer o download.');
        $this->redirect(route('loja.show', $produto->slug));
    }

    // ─── Render ──────────────────────────────────────────────────────

    public function render()
    {
        $today = Carbon::today()->toDateString();

        // Cache keyed por filtros + página — 2 min
        // Invalida automaticamente quando um produto é publicado/patrocinado (TTL natural)
        $cacheKey = 'loja_vitrine:' . md5("{$this->busca}|{$this->tipo}|{$this->ordenar}|p{$this->getPage()}");
        $produtos = Cache::remember($cacheKey, 120, function () use ($today) {
            $query = Infoproduto::where('status', 'ativo')
                ->with(['freelancer:id,name,profile_photo'])
                ->withExists([
                    'patrocinios as patrocinado' => fn ($q) => $q
                        ->where('status', 'ativo')
                        ->where('data_inicio', '<=', $today)
                        ->where('data_fim', '>=', $today),
                ]);

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
            $query->orderByRaw(
                "EXISTS (SELECT 1 FROM infoproduto_patrocinios ip
                         WHERE ip.infoproduto_id = infoprodutos.id
                           AND ip.status = 'ativo'
                           AND ip.data_inicio <= ?
                           AND ip.data_fim >= ?) DESC",
                [$today, $today]
            );

            match ($this->ordenar) {
                'preco_asc'     => $query->orderBy('preco', 'asc'),
                'preco_desc'    => $query->orderBy('preco', 'desc'),
                'mais_vendidos' => $query->orderBy('vendas_count', 'desc'),
                default         => $query->orderByDesc('created_at'),
            };

            return $query->paginate(12);
        });

        return view('livewire.loja.vitrine', [
            'produtos' => $produtos,
        ])->layout('layouts.dashboard', ['dashboardTitle' => '']);
    }
}
