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

        return view('livewire.freelancer.project-manager', [
            'projects' => $projects,
            'statusCounts' => $statusCounts,
        ])->layout('layouts.dashboard', [
            'dashboardTitle' => 'Meus Projetos',
        ]);
    }
}
