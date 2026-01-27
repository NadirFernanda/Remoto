<?php

namespace App\Livewire\Client;

use Livewire\Component;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class ServiceCancel extends Component
{
    public Service $service;

    public function mount(Service $service)
    {
        $this->service = $service;
    }

    public function cancelService()
    {
        if ($this->service->cliente_id !== Auth::id()) {
            abort(403, 'Ação não permitida. Apenas o cliente pode cancelar este pedido.');
        }
        if ($this->service->status === 'published') {
            $this->service->status = 'cancelled';
            $this->service->save();
            session()->flash('success', 'Pedido cancelado com sucesso.');
            return redirect()->route('client.orders');
        }
        session()->flash('error', 'Não é possível cancelar este pedido.');
        return redirect()->route('client.orders');
    }

    public function render()
    {
        return view('livewire.client.service-cancel', [
            'service' => $this->service
        ]);
    }
}
