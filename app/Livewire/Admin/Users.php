<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class Users extends Component
{
    use WithPagination;

    public string $search = '';
    public string $roleFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, fn($q) => $q->where('name', 'ilike', "%{$this->search}%")
                ->orWhere('email', 'ilike', "%{$this->search}%"))
            ->when($this->roleFilter, fn($q) => $q->where('role', $this->roleFilter))
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('livewire.admin.users', compact('users'))
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Usuários']);
    }
}
