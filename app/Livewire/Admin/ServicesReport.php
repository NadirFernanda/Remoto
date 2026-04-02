<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Service;
use Carbon\Carbon;

class ServicesReport extends Component
{
    use WithPagination;

    public string $period    = 'month';
    public string $dateStart = '';
    public string $dateEnd   = '';
    public string $status    = '';
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

    private static array $statusLabels = [
        'draft'        => 'Rascunho',
        'published'    => 'Publicado',
        'accepted'     => 'Aceite',
        'negotiating'  => 'Em Negociação',
        'in_progress'  => 'Em Progresso',
        'delivered'    => 'Entregue',
        'completed'    => 'Concluído',
        'cancelled'    => 'Cancelado',
        'em_moderacao' => 'Em Moderação',
    ];

    public function render()
    {
        $start = $this->startDate();
        $end   = $this->endDate();

        $query = Service::with(['cliente:id,name,email', 'freelancer:id,name,email'])
            ->whereBetween('created_at', [$start, $end])
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->search, fn ($q) =>
                $q->where(function ($q2) {
                    $q2->where('titulo', 'like', '%' . $this->search . '%')
                       ->orWhereHas('cliente',    fn ($u) => $u->where('name', 'like', '%' . $this->search . '%'))
                       ->orWhereHas('freelancer', fn ($u) => $u->where('name', 'like', '%' . $this->search . '%'));
                })
            )
            ->orderByDesc('created_at');

        $services = $query->paginate(50);

        // Totais por status
        $resumo = Service::selectRaw('status, count(*) as total')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $totalValor = Service::whereBetween('created_at', [$start, $end])
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->sum('valor');

        $statusLabels = self::$statusLabels;

        return view('livewire.admin.services-report', compact('services', 'resumo', 'totalValor', 'statusLabels'))
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Relatório de Serviços']);
    }
}
