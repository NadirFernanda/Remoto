<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Service;

class ServiceDelivery extends Component
{
    use WithFileUploads;

    public Service $service;
    public $entrega_arquivo;
    public $entrega_mensagem = '';

    public function mount(Service $service)
    {
        $this->service = $service;
    }

    public function entregarServico()
    {
        $this->validate([
            'entrega_arquivo' => 'required|file|max:10240', // 10MB
            'entrega_mensagem' => 'nullable|string|max:255',
        ]);
        // Aqui seria feita a lógica de upload e marcação como entregue
        $this->service->status = 'delivered';
        $this->service->save();
        session()->flash('success', 'Serviço entregue com sucesso!');
        return redirect()->route('freelancer.dashboard');
    }

    public function render()
    {
        return view('livewire.freelancer.service-delivery');
    }
}
