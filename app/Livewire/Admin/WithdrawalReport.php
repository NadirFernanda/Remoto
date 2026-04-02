<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WalletLog;
use App\Models\User;
use Carbon\Carbon;

class WithdrawalReport extends Component
{
    use WithPagination;

    public string $period    = 'month';
    public string $dateStart = '';
    public string $dateEnd   = '';
    public string $status    = '';  // saque_solicitado | saque_aprovado | saque_rejeitado
    public string $search    = '';

    public function mount(): void
    {
        abort_if(auth()->user()?->role !== 'admin', 403);
    }

    public function updatedPeriod(): void    { $this->dateStart = ''; $this->dateEnd = ''; $this->resetPage(); }
    public function updatedDateStart(): void { $this->resetPage(); }
    public function updatedDateEnd(): void   { $this->resetPage(); }
    public function updatedStatus(): void    { $this->resetPage(); }
    public function updatedSearch(): void    { $this->resetPage(); }

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
        $start  = $this->startDate();
        $end    = $this->endDate();

        $tipos  = $this->status
            ? [$this->status]
            : ['saque_solicitado', 'saque_aprovado', 'saque_rejeitado'];

        $query = WalletLog::with('user:id,name,email,role')
            ->whereIn('tipo', $tipos)
            ->whereBetween('created_at', [$start, $end])
            ->when($this->search, fn ($q) =>
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%'))
            )
            ->orderByDesc('created_at');

        $logs = $query->paginate(50);

        // Totais agrupados por status
        $resumo = WalletLog::selectRaw('tipo, count(*) as total_pedidos, sum(abs(valor)) as total_valor')
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('tipo', ['saque_solicitado', 'saque_aprovado', 'saque_rejeitado'])
            ->groupBy('tipo')
            ->pluck(null, 'tipo')
            ->toArray();

        return view('livewire.admin.withdrawal-report', compact('logs', 'resumo'))
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Relatório de Saques']);
    }
}
