<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WalletLog;
use App\Models\User;
use Carbon\Carbon;

class Commissions extends Component
{
    use WithPagination;

    public string $period = 'month';
    public string $search = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingPeriod(): void  { $this->resetPage(); }

    public function render()
    {
        $start = match ($this->period) {
            'week'  => Carbon::now()->startOfWeek(),
            'year'  => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfMonth(),
        };

        // Pagamentos de projeto = valor_liquido (90%). Comissão = 10% do bruto = valor_liquido * 10/90
        $logs = WalletLog::with('user')
            ->where('tipo', 'pagamento_projeto')
            ->where('created_at', '>=', $start)
            ->when($this->search, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', '%'.$this->search.'%')))
            ->orderByDesc('created_at')
            ->paginate(50);

        $totalPagamentos = (clone $logs->getQuery())->sum('valor');
        $total = round($totalPagamentos * 10 / 90, 2);

        return view('livewire.admin.commissions', compact('logs', 'total'))
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Comissões']);
    }
}
