<?php

namespace App\Livewire\Freelancer;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ClientSearch extends Component
{
    use WithPagination;

    public string $query = '';
    public string $sort  = 'recentes';

    protected $paginationTheme = 'tailwind';

    protected $queryString = [
        'query' => ['except' => ''],
        'sort'  => ['except' => 'recentes'],
    ];

    public function updatingQuery(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $clients = User::query()
            ->where('role', 'cliente')
            ->when($this->query, function ($q) {
                $q->where('name', 'ilike', '%' . $this->query . '%');
            })
            ->withCount(['servicesAsClient as published_projects_count' => function ($q) {
                $q->where('status', '!=', 'draft');
            }])
            ->withCount(['servicesAsClient as completed_projects_count' => function ($q) {
                $q->where('status', 'completed');
            }]);

        if ($this->sort === 'projetos') {
            $clients->orderByDesc('published_projects_count');
        } else {
            $clients->latest();
        }

        $clients = $clients->paginate(12);

        return view('livewire.freelancer.client-search', compact('clients'))
            ->layout('layouts.dashboard', ['title' => 'Buscar Clientes', 'dashboardTitle' => 'Buscar Clientes']);
    }
}
