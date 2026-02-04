<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class ServiceChat extends Component
{
    use WithFileUploads;
    public Service $service;
    public $mensagem = '';
    public $chat_bloqueado = true;
    public $messages = [];
    public $anexo;

    public function mount(Service $service)
    {
        $this->service = $service;
        $this->chat_bloqueado = $service->status !== 'accepted' && $service->status !== 'in_progress' && $service->status !== 'delivered' && $service->status !== 'completed';
        $this->atualizarMensagens();
    }

    public function atualizarMensagens()
    {
        $this->messages = $this->service->messages()->orderBy('created_at')->get();
    }

    public function enviarMensagem()
    {
        if ($this->chat_bloqueado) return;
        $this->validate([
            'mensagem' => 'nullable|string|max:500',
            'anexo' => 'nullable|file|max:5120', // 5MB
        ]);
        $anexoPath = null;
        if ($this->anexo) {
            $anexoPath = $this->anexo->store('anexos', 'public');
        }
        $this->service->messages()->create([
            'user_id' => Auth::id(),
            'conteudo' => $this->mensagem,
            'anexo' => $anexoPath ? basename($anexoPath) : null,
        ]);
        $this->mensagem = '';
        $this->anexo = null;
        $this->atualizarMensagens();
        $this->dispatch('scroll-bottom');
    }

    public function render()
    {
        return view('livewire.chat.service-chat')->layout('layouts.livewire');
    }
}
