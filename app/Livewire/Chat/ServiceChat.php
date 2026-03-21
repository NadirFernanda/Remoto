<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Service;
use App\Models\Wallet;
use App\Models\WalletLog;
use App\Models\Notification;
use App\Modules\Messaging\Services\ChatService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class ServiceChat extends Component
{
    use WithFileUploads;

    public Service $service;
    public $mensagem = '';
    public $chatFile = null;
    public $chat_bloqueado = true;

    // ── Inserir Valor modal ──────────────────────────────────────────────────
    public $showValorModal = false;
    public $novoValorTotal = '';

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
            app(ChatService::class)->markRead($service, auth()->user());
        }
    }

    // ── Computed helpers ─────────────────────────────────────────────────────

    /** Breakdown do extra: extra, taxa (10%), total_cliente */
    public function getExtraBreakdownProperty(): array
    {
        $novo  = (float) str_replace([' ', ','], ['', '.'], $this->novoValorTotal ?? '0');
        $atual = (float) $this->service->valor;
        $extra = round(max(0.0, $novo - $atual), 2);
        $taxa  = round($extra * 0.10, 2);
        return [
            'atual'         => $atual,
            'novo'          => $novo,
            'extra'         => $extra,
            'taxa'          => $taxa,
            'total_cliente' => round($extra + $taxa, 2),
        ];
    }

    public function getIsClienteProperty(): bool
    {
        return auth()->check() && auth()->id() === $this->service->cliente_id;
    }

    public function getMostrarBotaoValorProperty(): bool
    {
        return $this->isCliente
            && !$this->chat_bloqueado
            && in_array($this->service->status, ['published', 'negotiating', 'accepted', 'in_progress']);
    }

    // ── Acções do modal ──────────────────────────────────────────────────────

    public function abrirModalValor(): void
    {
        $this->resetErrorBag();
        $this->novoValorTotal = '';

        // Pré-preencher com o proposal_value do candidato mais alto acima do valor actual
        $candidate = $this->service->candidates()
            ->whereNotNull('proposal_value')
            ->where('proposal_value', '>', $this->service->valor)
            ->orderByDesc('proposal_value')
            ->first();

        if ($candidate) {
            $this->novoValorTotal = (string) $candidate->proposal_value;
        }

        $this->showValorModal = true;
    }

    public function fecharModalValor(): void
    {
        $this->showValorModal = false;
        $this->novoValorTotal = '';
        $this->resetErrorBag();
    }

    public function pagarValorExtra(): void
    {
        if (!$this->isCliente) {
            $this->addError('novoValorTotal', 'Apenas o cliente pode processar pagamentos.');
            return;
        }

        $this->validate([
            'novoValorTotal' => 'required|numeric|min:1',
        ], [
            'novoValorTotal.required' => 'Indique o novo valor total acordado.',
            'novoValorTotal.numeric'  => 'O valor deve ser numérico.',
            'novoValorTotal.min'      => 'O valor deve ser maior que zero.',
        ]);

        $service = $this->service;
        $novo    = round((float) $this->novoValorTotal, 2);
        $atual   = round((float) $service->valor, 2);

        if ($novo <= $atual) {
            $this->addError('novoValorTotal', 'O novo valor (' . number_format($novo, 2, ',', '.') . ' Kz) deve ser superior ao valor actual (' . number_format($atual, 2, ',', '.') . ' Kz).');
            return;
        }

        $extra         = round($novo - $atual, 2);
        $taxa          = round($extra * 0.10, 2);
        $total_cliente = round($extra + $taxa, 2);

        $clientWallet = Wallet::firstOrCreate(
            ['user_id' => auth()->id()],
            ['saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => 1000, 'taxa_saque' => 2]
        );

        if ((float) $clientWallet->saldo < $total_cliente) {
            $this->addError(
                'novoValorTotal',
                'Saldo insuficiente. Precisas de ' . number_format($total_cliente, 2, ',', '.') . ' Kz mas tens apenas ' . number_format($clientWallet->saldo, 2, ',', '.') . ' Kz disponíveis.'
            );
            return;
        }

        // Processar débito e escrow
        $clientWallet->decrement('saldo', $total_cliente);
        $clientWallet->increment('saldo_pendente', $extra);

        WalletLog::create([
            'user_id'   => auth()->id(),
            'wallet_id' => $clientWallet->id,
            'valor'     => -$total_cliente,
            'tipo'      => 'escrow_ajuste',
            'descricao' => 'Ajuste de valor — projecto "' . $service->titulo . '" (+' . number_format($extra, 2, ',', '.') . ' Kz + ' . number_format($taxa, 2, ',', '.') . ' Kz taxa)',
        ]);

        // Actualizar serviço
        $service->valor              = $novo;
        $service->valor_liquido      = round($novo * 0.80, 2);
        $service->valor_ajuste       = $extra;
        $service->valor_ajuste_taxa  = $taxa;
        $service->valor_ajuste_pago  = true;
        $service->save();

        // Notificar freelancer
        if ($service->freelancer_id) {
            $prazoTexto = $service->prazo
                ? ' Data de entrega acordada: ' . \Carbon\Carbon::parse($service->prazo)->format('d/m/Y') . '.'
                : '';
            Notification::create([
                'user_id'    => $service->freelancer_id,
                'service_id' => $service->id,
                'type'       => 'payment_adjustment',
                'title'      => 'Pagamento adicional recebido — proposta aceite',
                'message'    => 'O cliente aceitou e pagou um ajuste de ' . number_format($extra, 2, ',', '.') . ' Kz para o projecto "' . $service->titulo . '". Novo valor total: ' . number_format($novo, 2, ',', '.') . ' Kz.' . $prazoTexto,
            ]);
        }

        $this->showValorModal = false;
        $this->novoValorTotal = '';
        session()->flash('chat_success', 'Pagamento de ' . number_format($total_cliente, 2, ',', '.') . ' Kz processado com sucesso!');
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

        $rateLimitKey = 'chat-message:' . Auth::id();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 30)) {
            $this->addError('mensagem', 'Enviou muitas mensagens. Aguarde um momento antes de continuar.');
            return;
        }
        RateLimiter::hit($rateLimitKey, 60);

        if ($this->chatFile) {
            $this->validate(['chatFile' => 'nullable|file|max:51200']);
        }

        try {
            $msg = app(ChatService::class)->send(
                $this->service,
                Auth::user(),
                $mensagem,
                $this->chatFile
            );
            Log::info('[CHAT DEBUG] Mensagem enviada', [
                'id'                 => $msg->id,
                'conteudo'           => $msg->conteudo,
                'anexo'              => $msg->anexo,
                'nome_original_anexo' => $msg->nome_original_anexo,
                'user_id'            => $msg->user_id,
                'created_at'         => $msg->created_at,
            ]);
        } catch (\Throwable $e) {
            Log::error('Chat message create exception: ' . $e->getMessage());
            $this->addError('mensagem', 'Erro ao enviar mensagem: ' . $e->getMessage());
            return;
        }

        $this->mensagem = '';
        $this->chatFile = null;
        $this->dispatch('scroll-bottom');
        $this->dispatch('message-sent');
        $this->dispatch('chat-file-cleared');
    }

    public function render()
    {
        $messages = app(ChatService::class)->getMessages($this->service);

        return view('livewire.chat.service-chat', ['messages' => $messages])->layout('layouts.dashboard', ['dashboardTitle' => 'Chat do Serviço']);
    }
}

