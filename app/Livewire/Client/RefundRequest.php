<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Models\Service;
use App\Models\Refund;

class RefundRequest extends Component
{
    use WithFileUploads;

    public $service_id;
    public $reason;
    public $details;
    public $evidence = [];

    public function mount($service_id = null)
    {
        $this->service_id = $service_id;
    }

    public function submit()
    {
        $this->validate([
            'service_id' => 'required|exists:services,id',
            'reason' => 'required|string|max:255',
            'details' => 'required|string',
            'evidence.*' => 'nullable|file|max:4096',
        ]);

        $refund = Refund::create([
            'service_id' => $this->service_id,
            'user_id' => Auth::id(),
            'reason' => $this->reason,
            'details' => $this->details,
            'status' => 'pending',
        ]);

        if ($this->evidence) {
            foreach ($this->evidence as $file) {
                $refund->addMedia($file->getRealPath())
                    ->usingFileName($file->getClientOriginalName())
                    ->toMediaCollection('evidence');
            }
        }

        session()->flash('success', 'Pedido de reembolso enviado para análise. Você será notificado sobre o andamento.');
        return redirect()->route('client.refunds');
    }

    public function render()
    {
        $services = Service::where('cliente_id', Auth::id())
            ->where('is_payment_released', false)
            ->get();
        return view('livewire.client.refund-request', [
            'services' => $services,
        ]);
    }
}
