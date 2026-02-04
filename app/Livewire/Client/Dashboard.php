<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Service;

class Dashboard extends Component
{
    public $orders = [];
        public $recent_messages = [];
    public $kpi_total_gasto = 0;
    public $kpi_projetos_publicados = 0;
    public $kpi_freelancers_contratados = 0;
    public $kpi_projetos_andamento = 0;
    public $kpi_projetos_concluidos = 0;

    public function mount()
    {
        \Log::debug('DASHBOARD CLIENTE: mount iniciado');
        $user = Auth::user();
        \Log::debug('DASHBOARD CLIENTE: user', ['user' => $user]);
        if (!$user) {
            \Log::error('DASHBOARD CLIENTE: Usuário não autenticado');
            throw new \Exception('Usuário não autenticado. Faça login para acessar o dashboard do cliente.');
        }
        $services = Service::where('cliente_id', $user->id)->get();
        \Log::debug('DASHBOARD CLIENTE: services', ['services' => $services]);
        if ($services === null) {
            \Log::error('DASHBOARD CLIENTE: Nenhum serviço retornado para o cliente');
            throw new \Exception('Nenhum serviço encontrado para o cliente.');
        }
        $this->orders = $services->sortByDesc('created_at')->take(5);
        \Log::debug('DASHBOARD CLIENTE: orders', ['orders' => $this->orders]);
        // Mensagens recentes (exemplo básico)
        $this->recent_messages = \App\Models\Message::with(['user', 'service'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();
        \Log::debug('DASHBOARD CLIENTE: recent_messages', ['recent_messages' => $this->recent_messages]);
        // KPIs
        $this->kpi_total_gasto = $services->where('status', 'completed')->sum('valor');
        $this->kpi_projetos_publicados = $services->count();
        $this->kpi_freelancers_contratados = $services->whereNotNull('freelancer_id')->count();
        $this->kpi_projetos_andamento = $services->whereIn('status', ['in_progress', 'accepted'])->count();
        $this->kpi_projetos_concluidos = $services->where('status', 'completed')->count();
        \Log::debug('DASHBOARD CLIENTE: mount finalizado');
    }

    public function render()
    {
        return view('livewire.client.dashboard', [
            'orders' => $this->orders,
            'recent_messages' => $this->recent_messages,
            'kpi_total_gasto' => $this->kpi_total_gasto,
            'kpi_projetos_publicados' => $this->kpi_projetos_publicados,
            'kpi_freelancers_contratados' => $this->kpi_freelancers_contratados,
            'kpi_projetos_andamento' => $this->kpi_projetos_andamento,
            'kpi_projetos_concluidos' => $this->kpi_projetos_concluidos,
        ])->layout('layouts.livewire');
    }
}
