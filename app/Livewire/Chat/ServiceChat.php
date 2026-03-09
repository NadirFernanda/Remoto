<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ServiceChat extends Component
{
    use WithFileUploads;

    public Service $service;
    public $mensagem = '';
    public $chatFile = null;
    public $chat_bloqueado = true;
    public $messages = [];


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

        if ($mensagem === '' && !$this->chatFile) {
            return;
        }

        $anexoFilename = null;
        $anexoOriginal = null;

        if ($this->chatFile) {
            $this->validate(['chatFile' => 'nullable|file|max:20480']);
            $original = $this->chatFile->getClientOriginalName();
            $safe     = preg_replace('/[^a-zA-Z0-9._-]/', '_', $original);
            $filename = time() . '_' . $safe;
            $this->chatFile->storeAs('anexos', $filename, 'public');
            $anexoFilename = $filename;
            // Sempre salva o nome original, mesmo para PDF e outros tipos
            $anexoOriginal = $original ?: $filename;
            $this->chatFile = null;
        }

        try {
            $msg = $this->service->messages()->create([
                'user_id'             => Auth::id(),
                'conteudo'            => $mensagem,
                'anexo'               => $anexoFilename,
                'nome_original_anexo' => $anexoOriginal,
            ]);
            Log::info('[CHAT DEBUG] Mensagem enviada', [
                'id' => $msg->id,
                'conteudo' => $msg->conteudo,
                'anexo' => $msg->anexo,
                'nome_original_anexo' => $msg->nome_original_anexo,
                'user_id' => $msg->user_id,
                'created_at' => $msg->created_at,
            ]);
        } catch (\Throwable $e) {
            Log::error('Chat message create exception: ' . $e->getMessage());
            $this->addError('mensagem', 'Erro ao enviar mensagem: ' . $e->getMessage());
            return;
        }

        \App\Models\ChatRead::markRead($this->service->id, Auth::id());
        $this->mensagem = '';
        $this->atualizarMensagens();
        $this->dispatch('scroll-bottom');
        $this->dispatch('message-sent');
    }

    public function render()
    {
        return view('livewire.chat.service-chat')->layout('layouts.livewire');
    }
}

