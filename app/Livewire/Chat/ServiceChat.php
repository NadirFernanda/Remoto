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

        $mensagem = trim($this->mensagem ?? '');

        if ($mensagem === '' && !$this->anexo) {
            return;
        }

        $anexoNome    = null;
        $nomeOriginal = null;

        if ($this->anexo) {
            try {
                $nomeOriginal = $this->anexo->getClientOriginalName();
                \Log::info('Chat upload start', ['nome' => $nomeOriginal]);

                $safe = preg_replace('/[^a-zA-Z0-9._-]/', '_', $nomeOriginal);
                $path = $this->anexo->storeAs('anexos', time() . '_' . $safe, 'public');

                \Log::info('Chat upload result', ['path' => $path]);

                if (!$path) {
                    $this->addError('anexo', 'Não foi possível guardar o ficheiro. Tente novamente.');
                    return;
                }

                $anexoNome = basename($path);

            } catch (\Throwable $e) {
                \Log::error('Chat upload exception: ' . $e->getMessage());
                $this->addError('anexo', 'Erro ao enviar ficheiro: ' . $e->getMessage());
                return;
            }
        }

        try {
            $this->service->messages()->create([
                'user_id'             => Auth::id(),
                'conteudo'            => $mensagem,
                'anexo'               => $anexoNome,
                'nome_original_anexo' => $nomeOriginal,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Chat message create exception: ' . $e->getMessage());
            $this->addError('mensagem', 'Erro ao enviar mensagem: ' . $e->getMessage());
            return;
        }

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
