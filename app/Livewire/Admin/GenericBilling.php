<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Service;
use App\Models\InfoprodutoCompra;
use App\Models\CreatorSubscription;
use Carbon\Carbon;

class GenericBilling extends Component
{
    use WithPagination;

    public string $period    = 'month';
    public string $dateStart = '';
    public string $dateEnd   = '';
    public string $tipo      = '';  // projetos | infoprodutos | assinaturas

    public function mount(): void
    {
        abort_if(auth()->user()?->role !== 'admin', 403);
    }

    public function updatedPeriod(): void    { $this->dateStart = ''; $this->dateEnd = ''; $this->resetPage(); }
    public function updatedDateStart(): void { $this->resetPage(); }
    public function updatedDateEnd(): void   { $this->resetPage(); }
    public function updatedTipo(): void      { $this->resetPage(); }

    private function startDate(): Carbon
    {
        if ($this->dateStart) return Carbon::parse($this->dateStart)->startOfDay();
        return match ($this->period) {
            'week'  => Carbon::now()->startOfWeek(),
            'year'  => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfMonth(),
        };
    }

    private function endDate(): Carbon
    {
        return $this->dateEnd ? Carbon::parse($this->dateEnd)->endOfDay() : Carbon::now()->endOfDay();
    }

    /**
     * Gera o número de factura genérico (FAT-AAAA-NNNNN)
     */
    public static function fatNumber(string $tipo, int $seq): string
    {
        $prefix = match ($tipo) {
            'Projectos'   => 'PRJ',
            'Infoprodutos' => 'INF',
            'Assinaturas' => 'ASS',
            default       => 'FAT',
        };
        return $prefix . '-' . now()->year . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }

    public function rows(): \Illuminate\Support\Collection
    {
        $start = $this->startDate();
        $end   = $this->endDate();
        $rows  = collect();
        $seq   = 1;

        // ── Meus Projectos (Services) ─────────────────────────────────────
        if (in_array($this->tipo, ['', 'projetos'])) {
            Service::with(['cliente:id,name,email', 'freelancer:id,name'])
                ->whereNotNull('valor')
                ->where('valor', '>', 0)
                ->whereBetween('updated_at', [$start, $end])
                ->whereIn('status', ['in_progress', 'delivered', 'completed', 'cancelled'])
                ->orderByDesc('updated_at')
                ->get()
                ->each(function ($s) use (&$rows, &$seq) {
                    $bruto   = (float) ($s->valor ?? 0);
                    $liquido = (float) ($s->valor_liquido ?? $bruto * 0.9);
                    $rows->push([
                        'fat_numero'    => self::fatNumber('Projectos', $seq++),
                        'data'          => $s->updated_at->format('d/m/Y'),
                        'data_iso'      => $s->updated_at->toDateString(),
                        'tipo'          => 'Projectos',
                        'descricao'     => $s->titulo ?? 'Projecto #' . $s->id,
                        'cliente'       => optional($s->cliente)->name  ?? '—',
                        'cliente_email' => optional($s->cliente)->email ?? '—',
                        'prestador'     => optional($s->freelancer)->name ?? '—',
                        'valor_bruto'   => $bruto,
                        'comissao'      => round($bruto - $liquido, 2),
                        'valor_liquido' => $liquido,
                        'status'        => ucfirst($s->status ?? '—'),
                    ]);
                });
        }

        // ── Infoprodutos ──────────────────────────────────────────────────
        if (in_array($this->tipo, ['', 'infoprodutos'])) {
            InfoprodutoCompra::with(['infoproduto:id,titulo,user_id', 'comprador:id,name,email', 'infoproduto.user:id,name'])
                ->whereBetween('created_at', [$start, $end])
                ->orderByDesc('created_at')
                ->get()
                ->each(function ($c) use (&$rows, &$seq) {
                    $rows->push([
                        'fat_numero'    => self::fatNumber('Infoprodutos', $seq++),
                        'data'          => $c->created_at->format('d/m/Y'),
                        'data_iso'      => $c->created_at->toDateString(),
                        'tipo'          => 'Infoprodutos',
                        'descricao'     => optional($c->infoproduto)->titulo ?? 'Infoproduto #' . $c->infoproduto_id,
                        'cliente'       => optional($c->comprador)->name  ?? '—',
                        'cliente_email' => optional($c->comprador)->email ?? '—',
                        'prestador'     => optional(optional($c->infoproduto)->user)->name ?? '—',
                        'valor_bruto'   => (float) $c->valor_pago,
                        'comissao'      => (float) $c->comissao_plataforma,
                        'valor_liquido' => (float) $c->valor_freelancer,
                        'status'        => 'Concluído',
                    ]);
                });
        }

        // ── Assinaturas (Creator Subscriptions) ───────────────────────────
        if (in_array($this->tipo, ['', 'assinaturas'])) {
            CreatorSubscription::with(['subscriber:id,name,email', 'creator:id,name'])
                ->whereBetween('created_at', [$start, $end])
                ->orderByDesc('created_at')
                ->get()
                ->each(function ($sub) use (&$rows, &$seq) {
                    $rows->push([
                        'fat_numero'    => self::fatNumber('Assinaturas', $seq++),
                        'data'          => $sub->created_at->format('d/m/Y'),
                        'data_iso'      => $sub->created_at->toDateString(),
                        'tipo'          => 'Assinaturas',
                        'descricao'     => 'Assinatura Criador — ' . (optional($sub->creator)->name ?? '#' . $sub->creator_id),
                        'cliente'       => optional($sub->subscriber)->name  ?? '—',
                        'cliente_email' => optional($sub->subscriber)->email ?? '—',
                        'prestador'     => optional($sub->creator)->name ?? '—',
                        'valor_bruto'   => (float) $sub->amount,
                        'comissao'      => (float) $sub->platform_fee,
                        'valor_liquido' => (float) $sub->net_amount,
                        'status'        => ucfirst($sub->status ?? 'active'),
                    ]);
                });
        }

        return $rows->sortByDesc('data_iso')->values();
    }

    public function render()
    {
        $allRows = $this->rows();

        $totalBruto   = $allRows->sum('valor_bruto');
        $totalComissao = $allRows->sum('comissao');
        $totalLiquido  = $allRows->sum('valor_liquido');

        $byTipo = $allRows->groupBy('tipo')->map(fn($g) => [
            'total'    => $g->sum('valor_bruto'),
            'count'    => $g->count(),
        ]);

        // Manual pagination
        $perPage  = 30;
        $page     = $this->getPage();
        $items    = $allRows->forPage($page, $perPage);
        $total    = $allRows->count();
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items, $total, $perPage, $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.admin.generic-billing', compact(
            'paginator', 'totalBruto', 'totalComissao', 'totalLiquido', 'byTipo', 'total'
        ))->layout('layouts.dashboard', ['dashboardTitle' => 'Facturação Genérica']);
    }
}
