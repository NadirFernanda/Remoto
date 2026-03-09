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
            'anexo'    => 'nullable|file|max:51200',
        ]);

        if (empty(trim($this->mensagem ?? '')) && !$this->anexo) {
            return;
        }

        $anexoPath    = null;
        $nomeOriginal = null;

        if ($this->anexo) {
            try {
                $nomeOriginal = $this->anexo->getClientOriginalName();
                $anexoPath    = $this->anexo->storePublicly('anexos', 'public');
                if (!$anexoPath) {
                    $this->addError('anexo', 'Erro ao guardar o ficheiro. Tente novamente.');
                    return;
                }
            } catch (\Throwable $e) {
                $this->addError('anexo', 'Falhou o envio do ficheiro: ' . $e->getMessage());
                return;
            }
        }

        $this->service->messages()->create([
            'user_id'             => Auth::id(),
            'conteudo'            => $this->mensagem ?? '',
            'anexo'               => $anexoPath ? basename($anexoPath) : null,
            'nome_original_anexo' => $nomeOriginal,
        ]);

        \App\Models\ChatRead::markRead($this->service->id, Auth::id());
        $this->mensagem = '';
        $this->anexo    = null;
        $this->atualizarMensagens();
        $this->dispatch('scroll-bottom');
    }

    public function render()
    {
        return view('livewire.chat.service-chat')->layout('layouts.livewire');
    }
}
