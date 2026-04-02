<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Service;
use App\Models\InfoprodutoCompra;
use App\Models\CreatorSubscription;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AccountingStatement extends Component
{
    use WithPagination;

    public string $period    = 'month';
    public string $dateStart = '';
    public string $dateEnd   = '';
    public string $tipo      = '';  // freelancing | infoproduto | creator

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

    public function render()
    {
        $start = $this->startDate();
        $end   = $this->endDate();

        $rows = collect();

        // ── Freelancing (Services) ────────────────────────────────────────
        if (in_array($this->tipo, ['', 'freelancing'])) {
            Service::with(['cliente:id,name', 'freelancer:id,name'])
                ->whereNotNull('valor')
                ->whereIn('status', ['in_progress', 'delivered', 'completed', 'cancelled'])
                ->orderByDesc('updated_at')
                ->get()
                ->each(function ($s) use (&$rows) {
                    $bruto     = (float) ($s->valor ?? 0);
                    $liquido   = (float) ($s->valor_liquido ?? $bruto * 0.9);
                    $comissao  = $bruto - $liquido;
                    $rows->push([
                        'nome'           => $s->titulo ?? 'Projecto #' . $s->id,
                        'data'           => $s->updated_at->format('d/m/Y'),
                        'tipo'           => 'Freelances',
                        'user_origem'    => optional($s->cliente)->name    ?? '—',
                        'user_destino'   => optional($s->freelancer)->name ?? '—',
                        'valor_bruto'    => $bruto,
                        'comissao'       => $comissao,
                        'valor_liquido'  => $liquido,
                        'status'         => ucfirst($s->status ?? '—'),
                    ]);
                });
        }

        // ── Infoprodutos ──────────────────────────────────────────────────
        if (in_array($this->tipo, ['', 'infoproduto'])) {
            InfoprodutoCompra::with(['infoproduto:id,titulo,user_id', 'comprador:id,name', 'infoproduto.user:id,name'])
                ->whereBetween('created_at', [$start, $end])
                ->orderByDesc('created_at')
                ->get()
                ->each(function ($c) use (&$rows) {
                    $rows->push([
                        'nome'           => optional($c->infoproduto)->titulo ?? 'Infoproduto #' . $c->infoproduto_id,
                        'data'           => $c->created_at->format('d/m/Y'),
                        'tipo'           => 'Infoproduto',
                        'user_origem'    => optional($c->comprador)->name                            ?? '—',
                        'user_destino'   => optional(optional($c->infoproduto)->user)->name          ?? '—',
                        'valor_bruto'    => (float) $c->valor_pago,
                        'comissao'       => (float) $c->comissao_plataforma,
                        'valor_liquido'  => (float) $c->valor_freelancer,
                        'status'         => 'Concluído',
                    ]);
                });
        }

        // ── Creator Subscriptions ─────────────────────────────────────────
        if (in_array($this->tipo, ['', 'creator'])) {
            CreatorSubscription::with(['subscriber:id,name', 'creator:id,name'])
                ->whereBetween('created_at', [$start, $end])
                ->orderByDesc('created_at')
                ->get()
                ->each(function ($sub) use (&$rows) {
                    $rows->push([
                        'nome'           => 'Assinatura Criador',
                        'data'           => $sub->created_at->format('d/m/Y'),
                        'tipo'           => 'Criador',
                        'user_origem'    => optional($sub->subscriber)->name ?? '—',
                        'user_destino'   => optional($sub->creator)->name    ?? '—',
                        'valor_bruto'    => (float) $sub->amount,
                        'comissao'       => (float) $sub->platform_fee,
                        'valor_liquido'  => (float) $sub->net_amount,
                        'status'         => ucfirst($sub->status ?? 'active'),
                    ]);
                });
        }

        $rows = $rows->sortByDesc('data');

        // ── Totais ────────────────────────────────────────────────────────
        $totalBruto   = $rows->sum('valor_bruto');
        $totalComissao = $rows->sum('comissao');
        $totalLiquido  = $rows->sum('valor_liquido');

        // Manual pagination
        $perPage     = 50;
        $currentPage = $this->getPage();
        $paginated   = new \Illuminate\Pagination\LengthAwarePaginator(
            $rows->forPage($currentPage, $perPage)->values(),
            $rows->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url()]
        );

        return view('livewire.admin.accounting-statement', compact(
            'paginated',
            'totalBruto',
            'totalComissao',
            'totalLiquido',
        ))->layout('layouts.dashboard', ['dashboardTitle' => 'Extrato Contabilidade']);
    }
}
