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

    public function mount(Service $service)
    {
        $this->service = $service;

        $user = auth()->user();

        // Verifica se o utilizador tem acesso ao chat:
        // - cliente dono do projeto
        // - freelancer contratado
        // - candidato com proposta enviada (pré-contratação)
        $isOwner     = $user && $user->id === $service->cliente_id;
        $isFreelancer = $user && $user->id === $service->freelancer_id;
        $isCandidate  = $user && $service->candidates()->where('freelancer_id', $user->id)
            ->whereIn('status', ['pending', 'proposal_sent', 'invited', 'chosen'])
            ->exists();

        if (!$isOwner && !$isFreelancer && !$isCandidate) {
            abort(403, 'Acesso não autorizado ao chat.');
        }

        // Chat desbloqueado para negociação pré-contratação e fases activas
        $this->chat_bloqueado = !in_array($service->status, [
            'published', 'negotiating', 'accepted', 'in_progress', 'delivered', 'completed'
        ]);

        if (auth()->check()) {
            \App\Models\ChatRead::markRead($service->id, auth()->id());
        }
    }

    public function updatedChatFile()
    {
        $this->skipRender();
    }

    public function enviarMensagem()
    {
        if ($this->chat_bloqueado) return;

        $mensagem = trim($this->mensagem ?? '');

        if ($mensagem === '' && !$this->chatFile) {
            $this->skipRender();
            return;
        }

        $anexoFilename = null;
        $anexoOriginal = null;

        if ($this->chatFile) {
            $this->validate(['chatFile' => 'nullable|file|max:51200']);
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
        $this->dispatch('scroll-bottom');
        $this->dispatch('message-sent');
        $this->dispatch('chat-file-cleared');
    }

    public function render()
    {
        $messages = $this->service->messages()
            ->with('user')
            ->orderBy('created_at')
            ->get();

        return view('livewire.chat.service-chat', ['messages' => $messages])->layout('layouts.livewire');
    }
}

