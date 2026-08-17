<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class ProjectManager extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatus() { $this->resetPage(); }

    public function render()
    {
        $user = Auth::user();
        $query = Service::where('freelancer_id', $user->id);
        if ($this->search) {
            $query->where('titulo', 'like', '%'.$this->search.'%');
        }
        if ($this->status) {
            $query->where('status', $this->status);
        }
        $projects = $query->orderByDesc('created_at')->paginate(10);

        $statusCounts = Service::where('freelancer_id', $user->id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')->pluck('total', 'status');

        $reviewedIds = \App\Models\Review::where('author_id', $user->id)
            ->whereIn('service_id', $projects->pluck('id'))
            ->pluck('service_id')
            ->toArray();

        // Estatística informativa — o saque em si é feito só no Painel Financeiro
        // (/freelancer/financeiro), que é a única fonte de verdade do saldo real.
        $totalGanhoProjetos = Service::where('freelancer_id', $user->id)
            ->where('status', 'completed')
            ->sum('valor_liquido');

        return view('livewire.freelancer.project-manager', [
            'projects'           => $projects,
            'statusCounts'       => $statusCounts,
            'reviewedIds'        => $reviewedIds,
            'totalGanhoProjetos' => $totalGanhoProjetos,
        ])->layout('layouts.dashboard', [
            'dashboardTitle' => '',
        ]);
    }
}

