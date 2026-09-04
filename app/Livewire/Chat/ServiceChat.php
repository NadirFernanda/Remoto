<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use App\Jobs\InitiateAppyPayChargeJob;
use App\Models\Service;
use App\Models\Wallet;
use App\Models\WalletLog;
use App\Models\Notification;
use App\Modules\Messaging\Services\ChatService;
use App\Modules\Payments\Services\AppyPayGateway;
use App\Modules\Payments\Services\AppyPayReconciliationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ServiceChat extends Component
{
    public Service $service;
    public bool $chat_bloqueado = true;

    // ── Calculados 1x em mount — não re-executam queries em cada render ──────
    public bool $mostrarBotaoValor = false;
    public bool $mostrarBotaoFreelancerValor = false;
    public bool $isCliente = false;

    // ── Inserir Valor modal (cliente) ────────────────────────────────────────
    public bool $showValorModal = false;
    public string $novoValorTotal = '';
    public string $valorPaymentMethod = 'express'; // wallet | express
    public string $valorPhoneNumber = '';
    public string $valorAppyPayStep = 'form'; // form | waiting
    public string $valorAppyPayError = '';

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
            && in_array($service->status, ['published', 'negotiating', 'accepted', 'in_progress', 'delivered'])
            && ($service->payment_status !== 'paid' || $service->status !== 'delivered');

        $this->mostrarBotaoFreelancerValor = !$this->isCliente
            && !$this->chat_bloqueado
            && ($isFreelancer || $isCandidate)
            && in_array($service->status, ['published', 'negotiating', 'accepted', 'in_progress'])
            && ($service->payment_status !== 'paid' || $service->status !== 'in_progress');

        if ($this->isCliente && request()->boolean('payment')
            && in_array($service->status, ['accepted', 'delivered'], true)
            && $service->freelancer_id
            && $service->payment_status !== 'paid') {
            $this->novoValorTotal = (string) $service->valor;
            $this->valorPaymentMethod = 'express';
            $this->showValorModal = true;
        }

        if ($user) {
            app(ChatService::class)->markRead($service, $user);
        }
    }

    // ── Computed helpers ─────────────────────────────────────────────────────

    public function getIsDirectNegotiationProperty(): bool
    {
        return $this->service->status === 'negotiating'
            || ($this->service->status === 'accepted'
                && $this->service->freelancer_id
                && ($this->service->service_type === 'direct_invite'
                    || $this->service->payment_status !== 'paid'))
            || ($this->service->status === 'delivered'
                && $this->service->freelancer_id
                && $this->service->payment_status !== 'paid');
    }

    public function getExtraBreakdownProperty(): array
    {
        $novo       = (float) str_replace([' ', ','], ['', '.'], $this->novoValorTotal ?: '0');
        $isDirect   = $this->isDirectNegotiation;
        $clientRate = \App\Services\FeeService::serviceClientRate();
        $extra      = round(max(0.0, $isDirect ? $novo : ($novo - (float) $this->service->valor)), 2);
        // O cliente só paga a diferença agora (já pagou $atual antes), mas o
        // valor final do projecto — e o que o freelancer efectivamente recebe
        // — é sempre sobre o NOVO total acordado, nunca só sobre o extra.
        $taxa = round($novo * $clientRate, 2);

        // Sobretaxa de 10% cobrada ao cliente — aplicada só sobre o que está
        // a pagar agora ($extra), nunca outra vez sobre a parte já paga
        // anteriormente (ver pagarValorExtra() para a explicação da
        // consistência matemática entre os pagamentos parciais e o total).
        $taxaCliente  = round($extra * $clientRate, 2);
        $totalCliente = round($extra + $taxaCliente, 2);

        return [
            'atual'             => (float) $this->service->valor,
            'novo'              => $novo,
            'extra'             => $extra,
            'taxa'              => $taxa,
            'taxa_cliente'      => $taxaCliente,
            'total_cliente'     => $totalCliente,
            'valor_liquido'     => round($novo - $taxa, 2),
            'is_negotiating'    => $isDirect,
            'clientRatePercent' => round($clientRate * 100, 1),
        ];
    }

    /**
     * Decomposição mostrada ao freelancer enquanto escreve a proposta —
     * mesma lógica de getExtraBreakdownProperty(), mas a partir de
     * $valorProposto em vez de $novoValorTotal (modais diferentes), para
     * que o freelancer veja, antes de enviar, exactamente quanto vai
     * receber depois da comissão da plataforma.
     */
    public function getProposalBreakdownProperty(): array
    {
        $novo       = (float) str_replace([' ', ','], ['', '.'], $this->valorProposto ?: '0');
        $isDirect   = $this->isDirectNegotiation;
        $clientRate = \App\Services\FeeService::serviceClientRate();
        $atual      = (float) $this->service->valor;
        $extra      = round(max(0.0, $isDirect ? $novo : ($novo - $atual)), 2);
        $taxa       = round($novo * $clientRate, 2);

        return [
            'atual'             => $atual,
            'novo'              => $novo,
            'extra'             => $extra,
            'taxa'              => $taxa,
            'valor_liquido'     => round($novo - $taxa, 2),
            'is_negotiating'    => $isDirect,
            'clientRatePercent' => round($clientRate * 100, 1),
        ];
    }

    /**
     * Called by inline "Aceitar Proposta" buttons inside message bubbles.
     */
    public function abrirModalComValor(string $valorFormatado): void
    {
        if (!$this->isCliente || !$this->mostrarBotaoValor) {
            return;
        }

        $this->resetErrorBag();
        $valorNumerico = $this->normalizarValorMonetario($valorFormatado);
        if ($valorNumerico === null || $valorNumerico <= 0) {
            $this->addError('novoValorTotal', 'A proposta contém um valor inválido.');
            return;
        }

        $this->novoValorTotal = (string) $valorNumerico;
        $this->valorPaymentMethod = 'express';
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
        $this->showValorModal   = false;
        $this->novoValorTotal   = '';
        $this->valorPaymentMethod = 'express';
        $this->valorPhoneNumber  = '';
        $this->valorAppyPayStep  = 'form';
        $this->valorAppyPayError = '';
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

        // O pagamento inicial de uma proposta aceite também passa pelo
        // Multicaixa Express; apenas ajustes de projectos já em andamento
        // continuam a usar o saldo interno.
        if ($this->isDirectNegotiation && $this->valorPaymentMethod === 'express') {
            $this->pagarValorExtraAppyPay();
            return;
        }

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

        // A plataforma cobra dos dois lados: 10% retidos do lado do
        // freelancer (sobre o valor total acordado) + 10% de sobretaxa paga
        // pelo cliente, aplicada só sobre o que está a pagar AGORA ($extra)
        // — nunca outra vez sobre a parte já paga antes, ver
        // getExtraBreakdownProperty() para a identidade matemática completa.
        $clientRate    = \App\Services\FeeService::serviceClientRate();
        $taxa          = round($novo * $clientRate, 2);
        $taxaCliente   = round($extra * $clientRate, 2);
        $total_cliente = round($extra + $taxaCliente, 2);

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
            \Illuminate\Support\Facades\DB::transaction(function () use ($service, $clientWallet, $isDirect, $novo, $extra, $taxa, $taxaCliente, $total_cliente, $clientRate) {
            // Re-adquire wallet com lock para prevenir race-condition
            $clientWallet = \App\Models\Wallet::where('id', $clientWallet->id)->lockForUpdate()->firstOrFail();
            $clientWallet->decrement('saldo', $total_cliente);
            // Só a parte do valor do projecto entra em saldo_pendente (escrow,
            // eventualmente pago ao freelancer ou devolvido) — a sobretaxa do
            // cliente sai do saldo mas nunca entra em escrow, é receita
            // imediata da plataforma.
            $clientWallet->increment('saldo_pendente', $extra);

            $logDescricao = $isDirect
                ? 'Pagamento inicial em escrow — projecto "' . $service->titulo . '" (' . number_format($novo, 2, ',', '.') . ' Kz)'
                : 'Ajuste de valor — projecto "' . $service->titulo . '" (+' . number_format($extra, 2, ',', '.') . ' Kz)';

            WalletLog::create([
                'user_id'   => auth()->id(),
                'wallet_id' => $clientWallet->id,
                'valor'     => -$extra,
                'tipo'      => $isDirect ? 'escrow_retido' : 'escrow_ajuste',
                'descricao' => $logDescricao,
            ]);

            if ($taxaCliente > 0) {
                WalletLog::create([
                    'user_id'   => auth()->id(),
                    'wallet_id' => $clientWallet->id,
                    'valor'     => -$taxaCliente,
                    'tipo'      => 'taxa_cliente_plataforma',
                    'descricao' => 'Taxa da plataforma (10%) sobre o projecto: ' . $service->titulo,
                ]);
            }

            // Actualizar serviço
            $service->valor         = $novo;
            $service->taxa          = round($novo * \App\Services\FeeService::serviceClientRate(), 2);
            $service->taxa_cliente  = round($novo * $clientRate, 2);
            $service->total_cliente = round($novo + $service->taxa_cliente, 2);
            $service->valor_liquido = round($novo - $service->taxa, 2);
            $service->payment_status = 'paid';
            $service->payment_method_used = 'wallet';

            if ($isDirect && $service->status !== 'delivered') {
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

    // ── Pagar valor acordado via Multicaixa Express (AppyPay) ──────────────────
    // Só para a primeira confirmação de valor de uma negociação directa — o
    // pagamento corre em segundo plano (InitiateAppyPayChargeJob), mesmo
    // motivo/arquitectura já usada em PaymentEscrow::chargeAppyPayPhone().
    // A reconciliação (AppyPayReconciliationService::markServicePaid) já sabe
    // avançar o projecto directamente para 'in_progress' quando já tem
    // freelancer associado, em vez de 'published'.

    private function pagarValorExtraAppyPay(): void
    {
        $this->valorAppyPayError = '';

        $this->validate([
            'valorPhoneNumber' => ['required', 'regex:/^9[0-9]{8}$/'],
        ], [
            'valorPhoneNumber.required' => 'Indique o número de telefone Multicaixa Express.',
            'valorPhoneNumber.regex'    => 'Número inválido — use 9 dígitos (ex: 923456789).',
        ]);

        $service = $this->service;
        $novo    = round((float) $this->novoValorTotal, 2);

        if ($service->appypay_charge_id) {
            $this->valorAppyPayError = 'Já existe um pagamento em curso para este projecto.';
            return;
        }

        // Só chega aqui na primeira confirmação de valor (ver pagarValorExtra) —
        // o total cobrado ao cliente inclui a sobretaxa de 10% da plataforma,
        // por cima do valor acordado com o freelancer.
        $clientRate = \App\Services\FeeService::serviceClientRate();
        $taxaCliente  = round($novo * $clientRate, 2);
        $totalCliente = round($novo + $taxaCliente, 2);

        $service->valor          = $novo;
        $service->taxa_cliente   = $taxaCliente;
        $service->total_cliente  = $totalCliente;
        $service->payment_status = 'initiated';
        $service->save();

        InitiateAppyPayChargeJob::dispatch(
            $service,
            'gpo',
            $this->valorPhoneNumber,
            $totalCliente,
            strtoupper(Str::random(12))
        );

        $this->valorAppyPayStep = 'waiting';
    }

    /** Chamado via wire:poll no ecrã de espera do modal de valor. */
    public function checkValorAppyPayStatus(): void
    {
        if ($this->valorAppyPayStep !== 'waiting') {
            return;
        }

        $service = $this->service->fresh();

        if ($service->payment_status === 'paid') {
            $this->service = $service;
            $this->showValorModal   = false;
            $this->valorAppyPayStep = 'form';
            $this->novoValorTotal   = '';
            $this->dispatch('close-valor-modal');
            $totalPago = (float) ($service->total_cliente ?: $service->valor);
            session()->flash('chat_success', 'Pagamento de ' . number_format($totalPago, 2, ',', '.') . ' Kz confirmado! O projecto está agora Em andamento.');
            return;
        }

        if ($service->payment_status === 'failed') {
            $this->valorAppyPayStep  = 'form';
            $this->valorAppyPayError = 'O pagamento não foi confirmado. Tente novamente ou escolha outro método.';
            return;
        }

        if (!$service->appypay_charge_id) {
            return; // job ainda não conseguiu charge_id — continua a aguardar
        }

        $charge = (new AppyPayGateway())->getCharge($service->appypay_charge_id);
        if ($charge['success'] && in_array(strtolower((string) $charge['status']), ['paid', 'completed', 'success', 'approved'], true)) {
            app(AppyPayReconciliationService::class)->markPaidByChargeId($service->appypay_charge_id);
            $this->checkValorAppyPayStatus();
        }
    }

    // ── Propor Valor (freelancer) ─────────────────────────────────────────────

    public function abrirModalProporValor(): void
    {
        $this->skipRender();
        $this->resetErrorBag();
        // A proposta original é imutável; o freelancer envia uma nova
        // contraproposta para manter todo o histórico auditável.
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
        $mensagem = "Proposta de valor: {$valor} Kz\nPode confirmar o pagamento usando o botão \"Inserir Valor\".";

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
