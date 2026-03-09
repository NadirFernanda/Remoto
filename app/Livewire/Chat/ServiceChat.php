<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ServiceChat extends Component
{
    public Service $service;
    public $mensagem = '';
    public $chat_bloqueado = true;
    public $messages = [];
    public $pendingAnexo = null;
    public $pendingAnexoOriginal = null;

    public function mount(Service $service)
    {
        $this->service = $service;
        $this->chat_bloqueado = !in_array($service->status, ['negotiating', 'accepted', 'in_progress', 'delivered', 'completed']);
        $this->atualizarMensagens();
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

        $mensagem = trim($this->mensagem ?? '');

        if ($mensagem === '' && !$this->pendingAnexo) {
            return;
        }

        try {
            $this->service->messages()->create([
                'user_id'             => Auth::id(),
                'conteudo'            => $mensagem,
                'anexo'               => $this->pendingAnexo ?: null,
                'nome_original_anexo' => $this->pendingAnexoOriginal ?: null,
            ]);
        } catch (\Throwable $e) {
            Log::error('Chat message create exception: ' . $e->getMessage());
            $this->addError('mensagem', 'Erro ao enviar mensagem: ' . $e->getMessage());
            return;
        }

        \App\Models\ChatRead::markRead($this->service->id, Auth::id());
        $this->mensagem             = '';
        $this->pendingAnexo         = null;
        $this->pendingAnexoOriginal = null;
        $this->atualizarMensagens();
        $this->dispatch('scroll-bottom');
        $this->dispatch('message-sent');
    }

    public function render()
    {
        return view('livewire.chat.service-chat')->layout('layouts.livewire');
    }
}

