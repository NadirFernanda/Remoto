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
        // Só permite se não for o cliente do serviço
        if (!$user || $user->id === $this->service->cliente_id) {
            abort(403, 'Ação não permitida.');
        }
        $this->service->status = 'accepted';
        $this->service->save();
        session()->flash('success', 'Serviço aceito com sucesso!');
        return redirect()->route('freelancer.dashboard');
    }

    public function refuseService()
    {
        $user = Auth::user();
        // Só permite se não for o cliente do serviço
        if (!$user || $user->id === $this->service->cliente_id) {
            abort(403, 'Ação não permitida.');
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
