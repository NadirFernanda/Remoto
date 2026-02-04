<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Service;

class Dashboard extends Component
{
    public array $stats = [];
        public $users;

    public function mount(): void
    {
        $this->stats = [
            'users_total' => User::count(),
            'users_clients' => User::where('role', 'cliente')->count(),
            'users_freelancers' => User::where('role', 'freelancer')->count(),
            'users_admins' => User::where('role', 'admin')->count(),
            'services_total' => Service::count(),
            'services_published' => Service::where('status', 'published')->count(),
            'services_in_progress' => Service::where('status', 'in_progress')->count(),
            'services_delivered' => Service::where('status', 'delivered')->count(),
            'services_cancelled' => Service::where('status', 'cancelled')->count(),
            'revenue_fees' => Service::where('status', 'delivered')->sum('taxa'),
        ];
            $this->users = User::orderByDesc('created_at')->take(10)->get();
    }

    public function render()
    {
        return view('livewire.admin.dashboard')
            ->layout('layouts.livewire');
    }
}
