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
    public string $mensagem = '';
    public $chatFile = null;
    public bool $chat_bloqueado = true;

    // ── Calculados 1x em mount — não re-executam queries em cada render ──────
    public bool $mostrarBotaoValor = false;
    public bool $mostrarBotaoFreelancerValor = false;
    public bool $isCliente = false;

    // ── Inserir Valor modal (cliente) ────────────────────────────────────────
    public bool $showValorModal = false;
    public string $novoValorTotal = '';

    // ── Propor Valor modal (freelancer) ──────────────────────────────────────
    public bool $showProporValorModal = false;
    public string $valorProposto = '';

    public function mount(Service $service): void
    {
        $this->service = $service;
        $user = auth()->user();

        $isOwner      = $user && $user->id === $service->cliente_id;
        $isFreelancer = $user && $user->id === $service->freelancer_id;
        $isCandidate  = $user && $service->candidates()
            ->where('freelancer_id', $user->id)
            ->whereIn('status', ['pending', 'proposal_sent', 'invited', 'chosen'])
            ->exists();

        if (!$isOwner && !$isFreelancer && !$isCandidate) {
            abort(403, 'Acesso não autorizado ao chat.');
        }

        $this->chat_bloqueado = !in_array($service->status, [
            'published', 'negotiating', 'accepted', 'in_progress', 'delivered',
        ]);

        $this->isCliente = (bool) ($user && $user->id === $service->cliente_id);

        $this->mostrarBotaoValor = $this->isCliente
            && !$this->chat_bloqueado
            && in_array($service->status, ['published', 'negotiating', 'accepted', 'in_progress']);

        $this->mostrarBotaoFreelancerValor = !$this->isCliente
            && !$this->chat_bloqueado
            && ($isFreelancer || $isCandidate)
            && in_array($service->status, ['published', 'negotiating', 'accepted', 'in_progress']);

        if ($user) {
            app(ChatService::class)->markRead($service, $user);
        }
    }

    // ── Computed helpers ─────────────────────────────────────────────────────

    public function getIsDirectNegotiationProperty(): bool
    {
        return $this->service->status === 'negotiating'
            || ($this->service->status === 'accepted' && $this->service->service_type === 'direct_invite');
    }

    public function getExtraBreakdownProperty(): array
    {
        $novo       = (float) str_replace([' ', ','], ['', '.'], $this->novoValorTotal ?: '0');
        $isDirect   = $this->isDirectNegotiation;
        $clientRate = \App\Services\FeeService::serviceClientRate();
        $extra      = round(max(0.0, $isDirect ? $novo : ($novo - (float) $this->service->valor)), 2);
        return [
            'atual'             => (float) $this->service->valor,
            'novo'              => $novo,
            'extra'             => $extra,
            'taxa'              => 0.0,
            'total_cliente'     => $extra,
            'is_negotiating'    => $isDirect,
            'clientRatePercent' => round($clientRate * 100, 1),
        ];
    }

    /**
     * Called by inline "Aceitar Proposta" buttons inside message bubbles.
     */
    public function abrirModalComValor(string $valorFormatado): void
    {
        $this->resetErrorBag();
        $plain = str_replace(['.', ','], ['', '.'], $valorFormatado);
        $this->novoValorTotal = $plain;
        $this->showValorModal = true;
        $this->dispatch('open-valor-modal');
    }

    // ── Acções do modal ──────────────────────────────────────────────────────

    public function abrirModalValor(): void
    {
        $this->resetErrorBag();
        $this->novoValorTotal = '';


        if ($this->isDirectNegotiation) {
            // Negociação directa (negotiating ou accepted+direct_invite):
            // pré-preencher com estimativa inicial se existir
            if ((float) $this->service->valor > 0) {
                $this->novoValorTotal = (string) $this->service->valor;
            }
        } else {
            // Pré-preencher com o proposal_value do candidato mais alto acima do valor actual
            $candidate = $this->service->candidates()
                ->whereNotNull('proposal_value')
                ->where('proposal_value', '>', $this->service->valor)
                ->orderByDesc('proposal_value')
                ->first();

            if ($candidate) {
                $this->novoValorTotal = (string) $candidate->proposal_value;
            }
        }

        $this->showValorModal = true;
        $this->dispatch('open-valor-modal');
        // no skipRender — need re-render to push novoValorTotal into the input
    }

    public function fecharModalValor(): void
    {
        $this->skipRender();
        $this->showValorModal = false;
        $this->novoValorTotal = '';
        $this->resetErrorBag();
        $this->dispatch('close-valor-modal');
    }

    public function pagarValorExtra(): void
    {
        if (!Auth::check()) {
            $this->redirect(route('login'));
            return;
        }

        if (!$this->isCliente) {
            $this->addError('novoValorTotal', 'Apenas o cliente pode processar pagamentos.');
            return;
        }

        $this->validate([
            'novoValorTotal' => 'required|numeric|min:1',
        ], [
            'novoValorTotal.required' => 'Indique o valor acordado.',
            'novoValorTotal.numeric'  => 'O valor deve ser numérico.',
            'novoValorTotal.min'      => 'O valor deve ser maior que zero.',
        ]);

        $service    = $this->service;
        $isDirect   = $this->isDirectNegotiation; // negotiating OU accepted+direct_invite
        $novo       = round((float) $this->novoValorTotal, 2);
        $atual      = round((float) $service->valor, 2);

        if ($isDirect) {
            // Primeiro pagamento: o valor total acordado vai inteiro para escrow
            $extra = $novo;
        } else {
            if ($novo <= $atual) {
                $this->addError('novoValorTotal', 'O novo valor (' . number_format($novo, 2, ',', '.') . ' Kz) deve ser superior ao valor actual (' . number_format($atual, 2, ',', '.') . ' Kz).');
                return;
            }
            $extra = round($novo - $atual, 2);
        }

        $taxa          = 0.0;
        $total_cliente = $extra;

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

        // Processar débito, escrow, persistência E notificação dentro de uma transacção atómica
        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($service, $clientWallet, $isDirect, $novo, $extra, $taxa, $total_cliente) {
            // Re-adquire wallet com lock para prevenir race-condition
            $clientWallet = \App\Models\Wallet::where('id', $clientWallet->id)->lockForUpdate()->firstOrFail();
            $clientWallet->decrement('saldo', $total_cliente);
            $clientWallet->increment('saldo_pendente', $extra);

            $logDescricao = $isDirect
                ? 'Pagamento inicial em escrow — projecto "' . $service->titulo . '" (' . number_format($novo, 2, ',', '.') . ' Kz)'
                : 'Ajuste de valor — projecto "' . $service->titulo . '" (+' . number_format($extra, 2, ',', '.') . ' Kz)';

            WalletLog::create([
                'user_id'   => auth()->id(),
                'wallet_id' => $clientWallet->id,
                'valor'     => -$total_cliente,
                'tipo'      => $isDirect ? 'escrow_retido' : 'escrow_ajuste',
                'descricao' => $logDescricao,
            ]);

            // Actualizar serviço
            $service->valor         = $novo;
            $service->valor_liquido = round($novo * (1 - \App\Services\FeeService::serviceClientRate()), 2);

            if ($isDirect) {
                $service->status = 'in_progress';
            } else {
                $service->valor_ajuste      = $extra;
                $service->valor_ajuste_taxa = $taxa;
                $service->valor_ajuste_pago = true;
            }

            $service->save();

            if ($service->freelancer_id) {
                if ($isDirect) {
                    $notifMsg   = 'O cliente confirmou o valor de ' . number_format($novo, 2, ',', '.') . ' Kz para o projecto "' . $service->titulo . '". O projecto passou para Em andamento.';
                    $notifType  = 'project_started';
                    $notifTitle = 'Projecto iniciado';
                } else {
                    $prazoTexto = $service->prazo
                        ? ' Data de entrega acordada: ' . \Carbon\Carbon::parse($service->prazo)->format('d/m/Y') . '.'
                        : '';
                    $notifMsg   = 'O cliente aceitou e pagou um ajuste de ' . number_format($extra, 2, ',', '.') . ' Kz para o projecto "' . $service->titulo . '". Novo valor total: ' . number_format($novo, 2, ',', '.') . ' Kz.' . $prazoTexto;
                    $notifType  = 'payment_adjustment';
                    $notifTitle = 'Pagamento adicional recebido — proposta aceite';
                }

                Notification::create([
                    'user_id'    => $service->freelancer_id,
                    'service_id' => $service->id,
                    'type'       => $notifType,
                    'title'      => $notifTitle,
                    'message'    => $notifMsg,
                ]);
            }

            }); // fim DB::transaction
        } catch (\Throwable $e) {
            Log::error('pagarValorExtra: erro na transacção', ['error' => $e->getMessage()]);
            $this->addError('novoValorTotal', 'Ocorreu um erro ao processar o pagamento: ' . $e->getMessage() . '. Por favor tente novamente ou contacte o suporte.');
            return;
        }

        $this->showValorModal = false;
        $this->novoValorTotal = '';
        $this->dispatch('close-valor-modal');
        $successMsg = 'Pagamento de ' . number_format($total_cliente, 2, ',', '.') . ' Kz processado com sucesso!';
        if ($isDirect) {
            $successMsg .= ' O projecto está agora Em andamento.';
        }
        session()->flash('chat_success', $successMsg);
    }

    public function enviarMensagem()
    {
        if (!Auth::check()) {
            $this->redirect(route('login'));
            return;
        }

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

    // ── Propor Valor (freelancer) ─────────────────────────────────────────────

    public function abrirModalProporValor(): void
    {
        $this->skipRender();
        $this->resetErrorBag();
        $this->valorProposto = '';
        $this->showProporValorModal = true;
        $this->dispatch('open-propor-valor-modal');
    }

    public function fecharModalProporValor(): void
    {
        $this->skipRender();
        $this->showProporValorModal = false;
        $this->valorProposto = '';
        $this->resetErrorBag();
        $this->dispatch('close-propor-valor-modal');
    }

    public function enviarPropostaValor(): void
    {
        if (!Auth::check()) {
            $this->redirect(route('login'));
            return;
        }

        if (!$this->mostrarBotaoFreelancerValor) {
            return;
        }

        $this->resetErrorBag('valorProposto');

        $valorNumerico = $this->normalizarValorMonetario($this->valorProposto);
        if ($valorNumerico === null) {
            $this->addError('valorProposto', 'Indique um valor válido (ex.: 40000 ou 40.000,00).');
            return;
        }

        if ($valorNumerico <= 0) {
            $this->addError('valorProposto', 'O valor deve ser maior que zero.');
            return;
        }

        $valor    = number_format($valorNumerico, 2, ',', '.');
        $mensagem = "💰 Proposta de valor: {$valor} Kz\nPode confirmar o pagamento usando o botão \"Inserir Valor\".";

        try {
            app(ChatService::class)->send($this->service, Auth::user(), $mensagem);
        } catch (\Throwable $e) {
            Log::error('enviarPropostaValor: falha ao enviar mensagem', ['error' => $e->getMessage()]);
            $this->addError('valorProposto', 'Erro ao enviar proposta. Tente novamente.');
            return;
        }

        $this->showProporValorModal = false;
        $this->valorProposto       = '';
        $this->dispatch('close-propor-valor-modal');
        $this->dispatch('scroll-bottom');
        session()->flash('chat_success', 'Proposta de ' . $valor . ' Kz enviada com sucesso! O cliente foi notificado.');
    }

    private function normalizarValorMonetario(mixed $valor): ?float
    {
        $texto = trim((string) $valor);
        if ($texto === '') {
            return null;
        }

        // Remove tudo excepto dígitos e separadores monetários comuns.
        $texto = preg_replace('/[^\d.,]/', '', $texto);
        if ($texto === null || $texto === '') {
            return null;
        }

        $temPonto = str_contains($texto, '.');
        $temVirgula = str_contains($texto, ',');

        if ($temPonto && $temVirgula) {
            // Escolhe separador decimal pelo último símbolo digitado.
            if (strrpos($texto, ',') > strrpos($texto, '.')) {
                $texto = str_replace('.', '', $texto);   // ponto como milhar
                $texto = str_replace(',', '.', $texto);  // vírgula como decimal
            } else {
                $texto = str_replace(',', '', $texto);   // vírgula como milhar
            }
        } elseif ($temVirgula) {
            // "40.000,50" ou "40000,50" -> decimal pt_BR
            $texto = str_replace('.', '', $texto);
            $texto = str_replace(',', '.', $texto);
        } elseif ($temPonto) {
            // "40.000" (milhar) ou "40000.50" (decimal)
            if (preg_match('/^\d{1,3}(\.\d{3})+$/', $texto)) {
                $texto = str_replace('.', '', $texto);
            }
        }

        if (!is_numeric($texto)) {
            return null;
        }

        return (float) $texto;
    }

    public function render()
    {
        return view('livewire.chat.service-chat', [
            'messages' => app(ChatService::class)->getMessages($this->service),
        ])
            ->extends('layouts.dashboard', ['dashboardTitle' => 'Chat do Serviço'])
            ->section('dashboard-content');
    }

}

