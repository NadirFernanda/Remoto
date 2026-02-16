<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use App\Traits\UserSessionTrait;

class Dashboard extends Component
{
    use UserSessionTrait;
     // Removed extra opening curly brace
    public $services;
    public $saldo_disponivel = 0;
    public $saldo_pendente = 0;

    public function mount()
    {
        $user = $this->getCurrentUser();
        $this->services = Service::where('freelancer_id', $user->id)
            ->whereIn('status', ['accepted', 'in_progress', 'delivered', 'completed', 'em_moderacao'])
            ->orderByDesc('created_at')
            ->get();
        \Log::debug('FREELANCER DASHBOARD: projetos em andamento retornados', [
            'freelancer_id' => $user->id,
            'services' => $this->services->pluck('id', 'status')
        ]);
        $this->saldo_disponivel = $user->wallet->saldo ?? 0;
        $this->saldo_pendente = $user->wallet->saldo_pendente ?? 0;
    }

    public function render()
    {
        $kpi_projetos_andamento = $this->services->count();
        $user = $this->getCurrentUser();
        $kpi_projetos_concluidos = Service::where('freelancer_id', $user->id)
            ->where('status', 'completed')
            ->count();

        // Link de afiliado
        $affiliate_link = url('/register?ref=' . $user->affiliate_code);

        // Indicações
        $referrals = \App\Models\Referral::where('affiliate_id', $user->id)->with('user')->get();

        return view('livewire.freelancer.dashboard', [
            'projects' => $this->services,
            'saldo_pendente' => $this->saldo_pendente,
            'kpi_total_recebido' => 0,
            'kpi_projetos_concluidos' => $kpi_projetos_concluidos,
            'kpi_projetos_andamento' => $kpi_projetos_andamento,
            'affiliate_link' => $affiliate_link,
            'referrals' => $referrals,
        ])->layout('layouts.dashboard', [
            'dashboardTitle' => 'Dashboard do Freelancer',
            'dashboardActions' => '<a href="'.route('freelancer.available-projects').'" class="py-2 px-4 rounded hover:bg-cyan-100 text-cyan-700 font-bold">Projetos Disponíveis</a>'
        ]);
    }

    public function sendToModeration($serviceId)
    {
        $user = $this->getCurrentUser();
        $service = Service::where('id', $serviceId)->where('freelancer_id', $user->id)->first();
        if (!$service) {
            session()->flash('error', 'Serviço não encontrado ou sem permissão.');
            return;
        }

        $service->status = 'em_moderacao';
        $service->save();

        session()->flash('success', 'Serviço enviado para moderação.');

        // Refresh local services collection
        $this->services = Service::where('freelancer_id', $user->id)
            ->whereIn('status', ['accepted', 'in_progress', 'delivered', 'completed', 'em_moderacao'])
            ->orderByDesc('created_at')
            ->get();
    }
}
