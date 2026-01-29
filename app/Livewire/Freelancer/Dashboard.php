<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $services;
    public $saldo_disponivel = 0;
    public $saldo_pendente = 0;

    public function mount()
    {
        $user = Auth::user();
        $this->services = Service::where('freelancer_id', $user->id)->orderByDesc('created_at')->get();
        $this->saldo_disponivel = $user->wallet->saldo ?? 0;
        $this->saldo_pendente = $user->wallet->saldo_pendente ?? 0;
    }

    public function render()
    {
        return view('livewire.freelancer.dashboard', [
            'projects' => $this->services,
            'saldo_pendente' => $this->saldo_pendente,
            'kpi_total_recebido' => 0,
            'kpi_projetos_concluidos' => 0,
            'kpi_projetos_andamento' => 0,
        ]);
    }
}
