<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use App\Models\Service;

class AvailableProjects extends Component
{
    public $projects;

    public function mount()
    {
        // Exibe apenas projetos publicados e ainda não aceitos por nenhum freelancer
        $this->projects = Service::where('status', 'published')
            ->whereNull('freelancer_id')
            ->orderByDesc('created_at')
            ->get();
    }

    public function acceptService($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        $user = auth()->user();
        if (!$user || $user->id === $service->cliente_id) {
            abort(403, 'Ação não permitida.');
        }
        $service->status = 'accepted';
        $service->freelancer_id = $user->id;
        $service->save();
        session()->flash('success', 'Serviço aceito com sucesso!');
        return redirect()->route('freelancer.dashboard');
    }

    public function refuseService($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        $user = auth()->user();
        if (!$user || $user->id === $service->cliente_id) {
            abort(403, 'Ação não permitida.');
        }
        $service->status = 'published';
        $service->freelancer_id = null;
        $service->save();
        session()->flash('info', 'Serviço recusado.');
        return redirect()->route('freelancer.available-projects');
    }

    public function render()
    {
        return view('livewire.freelancer.available-projects')->layout('layouts.livewire');
    }
}
