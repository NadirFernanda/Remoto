<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Service;
use App\Models\ServiceAttachment;
use App\Models\Notification;
use App\Notifications\ServiceDeliveredNotification;
use Illuminate\Support\Facades\Storage;

class ServiceDelivery extends Component
{
    use WithFileUploads;

    public Service $service;
    public $entrega_arquivo;
    public $entrega_mensagem = '';

    public function mount(Service $service)
    {
        if (!$service || !$service->id) {
            session()->flash('error', 'Serviço não encontrado ou inválido.');
            return redirect()->route('freelancer.dashboard');
        }
        if ($service->freelancer_id !== auth()->id()) {
            abort(403);
        }
        if (!in_array($service->status, ['in_progress', 'accepted'])) {
            session()->flash('error', 'Este projeto não está em andamento.');
            return redirect()->route('freelancer.projects');
        }
        $service->loadMissing('cliente');
        $this->service = $service;
    }

    public function entregarServico()
    {
        $this->validate([
            'entrega_arquivo'  => 'required|file|max:51200', // 50MB
            'entrega_mensagem' => 'nullable|string|max:2000',
        ]);

        $file = $this->entrega_arquivo;
        $path = $file->store("deliveries/{$this->service->id}", 'public');

        // Guardar o ficheiro como anexo de entrega
        ServiceAttachment::create([
            'service_id' => $this->service->id,
            'user_id'    => auth()->id(),
            'filename'   => $file->getClientOriginalName(),
            'path'       => $path,
            'size'       => $file->getSize(),
            'mime_type'  => $file->getMimeType(),
        ]);

        // Guardar mensagem de entrega e mudar status
        $this->service->delivery_message = $this->entrega_mensagem;
        $this->service->status = 'delivered';
        $this->service->save();

        // Notificar o cliente
        Notification::create([
            'user_id'    => $this->service->cliente_id,
            'service_id' => $this->service->id,
            'type'       => 'delivery_submitted',
            'title'      => 'Entrega recebida',
            'message'    => 'O freelancer entregou o projeto "' . $this->service->titulo . '". Revise e aprove ou solicite revisão.',
        ]);

        $cliente = $this->service->cliente;
        if ($cliente) {
            $cliente->notify(new ServiceDeliveredNotification(
                $this->service,
                auth()->user(),
                route('client.projects')
            ));
        }

        session()->flash('success', 'Entrega enviada com sucesso! O cliente foi notificado.');
        return redirect()->route('freelancer.projects');
    }

    public function render()
    {
        return view('livewire.freelancer.service-delivery');
    }
}
