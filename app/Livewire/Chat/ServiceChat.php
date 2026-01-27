<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class ServiceChat extends Component
{
    public Service $service;
    public $mensagem = '';
    public $chat_bloqueado = true;
    public $messages = [];

    public function mount(Service $service)
    {
        $this->service = $service;
        $this->chat_bloqueado = $service->status !== 'accepted' && $service->status !== 'in_progress' && $service->status !== 'delivered' && $service->status !== 'completed';
        // Aqui você pode buscar as mensagens do banco (exemplo simplificado)
        $this->messages = $service->messages()->orderBy('created_at')->get();
    }

    public function enviarMensagem()
    {
        if ($this->chat_bloqueado) return;
        $this->validate([
            'mensagem' => 'required|string|max:500',
        ]);
        $this->service->messages()->create([
            'user_id' => Auth::id(),
            'conteudo' => $this->mensagem,
        ]);
        $this->mensagem = '';
        $this->messages = $this->service->messages()->orderBy('created_at')->get();
        $this->dispatch('scroll-bottom');
    }

    public function render()
    {
        return view('livewire.chat.service-chat');
    }
}
