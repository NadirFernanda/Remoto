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
        $this->chat_bloqueado = !in_array($service->status, ['negotiating', 'accepted', 'in_progress', 'delivered', 'completed']);
        $this->atualizarMensagens();
        // Marca como lido ao abrir a conversa
        if (auth()->check()) {
            \App\Models\ChatRead::markRead($service->id, auth()->id());
        }
    }

    public function atualizarMensagens()
    {
        $this->messages = $this->service->messages()->orderBy('created_at')->get()->all();
    }

    public function enviarMensagem()
    {
        if ($this->chat_bloqueado) return;
        $this->validate([
            'mensagem' => 'nullable|string|max:2000',
            'anexo'    => 'nullable|file|max:20480', // 20MB
        ]);
        if (empty(trim($this->mensagem ?? '')) && !$this->anexo) {
            return;
        }
        $anexoPath = null;
        if ($this->anexo) {
            $anexoPath = $this->anexo->store('anexos', 'public');
        }
        $nomeOriginal = $this->anexo ? $this->anexo->getClientOriginalName() : null;
        $this->service->messages()->create([
            'user_id' => Auth::id(),
            'conteudo' => $this->mensagem ?? '',
            'anexo' => $anexoPath ? basename($anexoPath) : null,
            'nome_original_anexo' => $nomeOriginal,
        ]);
        // atualiza leitura do remetente
        \App\Models\ChatRead::markRead($this->service->id, Auth::id());
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
