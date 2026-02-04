<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class ServiceReview extends Component
{
    public Service $service;

    public function mount(Service $service)
    {
        $this->service = $service;
    }

    public function acceptService()
    {
        $user = Auth::user();
        if (!$user || !$user->can('accept', $this->service)) {
            throw new \Exception('Ação não permitida. Você não pode aceitar este serviço.');
        }
        $this->service->status = 'accepted';
        $this->service->save();
        session()->flash('success', 'Serviço aceito com sucesso!');
        return redirect()->route('freelancer.dashboard');
    }

    public function refuseService()
    {
        $user = Auth::user();
        if (!$user || !$user->can('refuse', $this->service)) {
            throw new \Exception('Ação não permitida. Você não pode recusar este serviço.');
        }
        $this->service->status = 'published'; // Ou lógica de recusa
        $this->service->freelancer_id = null;
        $this->service->save();
        session()->flash('info', 'Serviço recusado.');
        return redirect()->route('freelancer.dashboard');
    }

    public function render()
    {
        return view('livewire.freelancer.service-review')->layout('layouts.livewire');
    }
}
