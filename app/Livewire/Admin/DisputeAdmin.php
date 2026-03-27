<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Dispute;
use App\Models\Service;
use App\Models\Wallet;
use App\Models\WalletLog;
use App\Models\Notification;
use App\Modules\Admin\Services\AuditLogger;

class DisputeAdmin extends Component
{
    use WithPagination;

    public string $statusFilter = '';
    public ?int $selectedId = null;
    public string $adminNote = '';
    public string $newStatus = '';
    public string $replyMessage = '';
    public bool  $showParcialForm     = false;
    public float $percentualFreelancer = 50.0;

    public function mount(): void
    {
        abort_if(auth()->user()?->role !== 'admin', 403);
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function select(int $id): void
    {
        $this->selectedId = $id;
        $dispute = Dispute::find($id);
        $this->adminNote   = $dispute->admin_note ?? '';
        $this->newStatus   = $dispute->status;
        $this->replyMessage = '';
    }

    public function saveChanges(): void
    {
        $this->validate([
            'newStatus' => 'required|in:aberta,em_mediacao,resolvida,encerrada',
            'adminNote' => 'nullable|string|max:2000',
        ]);

        $dispute = Dispute::findOrFail($this->selectedId);
        $before  = ['status' => $dispute->status, 'admin_note' => $dispute->admin_note];

        $dispute->update([
            'status'     => $this->newStatus,
            'admin_note' => $this->adminNote ?: null,
        ]);

        AuditLogger::log(
            'dispute_updated',
            "Disputa #{$dispute->id} (proj: {$dispute->service->titulo}) alterada para '{$this->newStatus}'",
            'Dispute', $dispute->id,
            $before,
            ['status' => $this->newStatus, 'admin_note' => $this->adminNote]
        );

        session()->flash('success', 'Disputa atualizada com sucesso.');
        $this->selectedId = null;
    }

    public function sendReply(): void
    {
        $this->validate(['replyMessage' => 'required|string|min:5|max:2000']);

        $dispute = Dispute::with('service')->findOrFail($this->selectedId);
        $dispute->messages()->create([
            'user_id' => auth()->id(),
            'message' => $this->replyMessage,
        ]);

        AuditLogger::log(
            'dispute_message_sent',
            "Admin respondeu à disputa #{$dispute->id}",
            'Dispute', $dispute->id
        );

        // Notificar cliente e freelancer envolvidos na disputa
        $service = $dispute->service;
        $link    = $service ? route('service.dispute', $service->id) : '#';
        $title   = $service ? "Mediação: {$service->titulo}" : "Disputa #{$dispute->id}";

        $notifyIds = array_filter(array_unique([
            $service?->cliente_id,
            $service?->freelancer_id,
        ]));

        foreach ($notifyIds as $userId) {
            Notification::create([
                'user_id' => $userId,
                'type'    => 'dispute_admin_reply',
                'title'   => $title,
                'message' => 'A equipa de suporte enviou uma nova mensagem na Central de Disputas.',
                'link'    => $link,
            ]);
        }

        $this->replyMessage = '';
        session()->flash('success', 'Mensagem enviada.');
    }

    public function freezePayment(int $serviceId): void
    {
        $service = Service::findOrFail($serviceId);
        $before  = ['status' => $service->status];
        $service->update(['status' => 'em_moderacao']);

        AuditLogger::log(
            'payment_frozen',
            "Pagamento congelado para o serviço #{$service->id} '{$service->titulo}'",
            'Service', $service->id, $before, ['status' => 'em_moderacao']
        );

        session()->flash('success', 'Pagamento congelado — serviço em moderação.');
    }

    public function releasePayment(int $serviceId): void
    {
        $service = Service::findOrFail($serviceId);
        $before  = ['status' => $service->status, 'is_payment_released' => $service->is_payment_released];

        $service->update([
            'status'              => 'completed',
            'is_payment_released' => true,
            'payment_released_at' => now(),
        ]);

        // Creditar carteira do freelancer
        if ($service->valor_liquido && $service->freelancer_id) {
            $freelancerWallet = Wallet::firstOrCreate(
                ['user_id' => $service->freelancer_id],
                ['saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => 1000, 'taxa_saque' => 2]
            );
            $freelancerWallet->increment('saldo', $service->valor_liquido);
            WalletLog::create([
                'user_id'   => $service->freelancer_id,
                'wallet_id' => $freelancerWallet->id,
                'valor'     => $service->valor_liquido,
                'tipo'      => 'pagamento_projeto',
                'descricao' => 'Pagamento liberado pelo admin (disputa resolvida): ' . $service->titulo,
            ]);

            // Libertar escrow do cliente
            $clientWallet = Wallet::where('user_id', $service->cliente_id)->first();
            if ($clientWallet && $clientWallet->saldo_pendente >= $service->valor) {
                $clientWallet->decrement('saldo_pendente', $service->valor);
            }
        }

        Notification::create([
            'user_id'    => $service->freelancer_id,
            'service_id' => $service->id,
            'type'       => 'payment_released',
            'title'      => 'Pagamento liberado',
            'message'    => 'O admin resolveu a disputa a seu favor. O pagamento de ' . number_format($service->valor_liquido, 0, ',', '.') . ' Kz foi creditado na sua carteira.',
        ]);

        Notification::create([
            'user_id'    => $service->cliente_id,
            'service_id' => $service->id,
            'type'       => 'dispute_resolved',
            'title'      => 'Disputa resolvida',
            'message'    => 'A disputa no projecto "' . $service->titulo . '" foi resolvida. O pagamento foi liberado ao freelancer.',
        ]);

        AuditLogger::log(
            'payment_released',
            "Pagamento liberado pelo admin para '{$service->titulo}' — {$service->valor_liquido} Kz creditados ao freelancer",
            'Service', $service->id, $before, ['status' => 'completed', 'is_payment_released' => true]
        );

        session()->flash('success', 'Pagamento liberado e creditado na carteira do freelancer.');
    }

    public function toggleParcialForm(): void
    {
        $this->showParcialForm = !$this->showParcialForm;
    }

    public function liberarParcial(int $serviceId): void
    {
        $this->validate([
            'percentualFreelancer' => 'required|numeric|min:1|max:99',
        ], [
            'percentualFreelancer.min' => 'A percentagem mínima é 1%.',
            'percentualFreelancer.max' => 'A percentagem máxima é 99% (para reembolso total use o botão Reembolsar).',
        ]);

        $service = Service::findOrFail($serviceId);

        if ($service->is_payment_released) {
            session()->flash('error', 'O pagamento já foi libertado.');
            return;
        }

        $pct             = $this->percentualFreelancer / 100;
        $valorFreelancer = round(($service->valor_liquido ?? $service->valor) * $pct, 2);
        $valorCliente    = round($service->valor * (1 - $pct), 2);

        // Creditar freelancer
        if ($service->freelancer_id && $valorFreelancer > 0) {
            $fw = Wallet::firstOrCreate(
                ['user_id' => $service->freelancer_id],
                ['saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => 1000, 'taxa_saque' => 2]
            );
            $fw->increment('saldo', $valorFreelancer);
            WalletLog::create([
                'user_id'   => $service->freelancer_id,
                'wallet_id' => $fw->id,
                'valor'     => $valorFreelancer,
                'tipo'      => 'pagamento_parcial_disputa',
                'descricao' => "Pagamento parcial ({$this->percentualFreelancer}%) — disputa: {$service->titulo}",
            ]);
        }

        // Devolver escrow ao cliente (restante)
        if ($service->cliente_id) {
            $cw = Wallet::firstOrCreate(
                ['user_id' => $service->cliente_id],
                ['saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => 1000, 'taxa_saque' => 2]
            );
            if ($cw->saldo_pendente >= $service->valor) {
                $cw->decrement('saldo_pendente', $service->valor);
            }
            if ($valorCliente > 0) {
                $cw->increment('saldo', $valorCliente);
                WalletLog::create([
                    'user_id'   => $service->cliente_id,
                    'wallet_id' => $cw->id,
                    'valor'     => $valorCliente,
                    'tipo'      => 'reembolso_parcial_disputa',
                    'descricao' => "Reembolso parcial (" . (100 - $this->percentualFreelancer) . "%) — disputa: {$service->titulo}",
                ]);
            }
        }

        $service->update([
            'status'              => 'completed',
            'is_payment_released' => true,
            'payment_released_at' => now(),
        ]);

        $fmtFl = number_format($valorFreelancer, 0, ',', '.');
        $fmtCl = number_format($valorCliente, 0, ',', '.');

        Notification::create([
            'user_id'    => $service->freelancer_id,
            'service_id' => $service->id,
            'type'       => 'payment_released',
            'title'      => 'Pagamento parcial liberado',
            'message'    => "A disputa no projecto \"{$service->titulo}\" foi resolvida com divisão. Recebeu {$fmtFl} Kz na sua carteira.",
        ]);

        Notification::create([
            'user_id'    => $service->cliente_id,
            'service_id' => $service->id,
            'type'       => 'dispute_resolved',
            'title'      => 'Disputa resolvida (parcial)',
            'message'    => "A disputa no projecto \"{$service->titulo}\" foi resolvida. Foram devolvidos {$fmtCl} Kz à sua carteira.",
        ]);

        AuditLogger::log(
            'payment_partial_released',
            "Liberar parcial: {$this->percentualFreelancer}% → freelancer ({$fmtFl} Kz), restante → cliente ({$fmtCl} Kz). Projecto: {$service->titulo}",
            'Service', $service->id
        );

        $this->showParcialForm    = false;
        $this->percentualFreelancer = 50.0;
        session()->flash('success', "Pagamento dividido: {$fmtFl} Kz ao freelancer + {$fmtCl} Kz devolvidos ao cliente.");
    }

    public function reembolsarCliente(int $serviceId): void
    {
        $service = Service::findOrFail($serviceId);

        if ($service->is_payment_released) {
            session()->flash('error', 'O pagamento já foi liberado — não é possível reembolsar.');
            return;
        }

        $before = ['status' => $service->status];

        // Devolver escrow ao cliente
        if ($service->valor && $service->cliente_id) {
            $clientWallet = Wallet::firstOrCreate(
                ['user_id' => $service->cliente_id],
                ['saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => 1000, 'taxa_saque' => 2]
            );
            if ($clientWallet->saldo_pendente >= $service->valor) {
                $clientWallet->decrement('saldo_pendente', $service->valor);
            }
            $clientWallet->increment('saldo', $service->valor);
            WalletLog::create([
                'user_id'   => $service->cliente_id,
                'wallet_id' => $clientWallet->id,
                'valor'     => $service->valor,
                'tipo'      => 'reembolso_disputa',
                'descricao' => 'Reembolso por decisão admin na disputa: ' . $service->titulo,
            ]);
        }

        $service->update(['status' => 'cancelled']);

        Notification::create([
            'user_id'    => $service->cliente_id,
            'service_id' => $service->id,
            'type'       => 'refund_processed',
            'title'      => 'Reembolso processado',
            'message'    => 'A disputa no projecto "' . $service->titulo . '" foi resolvida a seu favor. ' . number_format($service->valor, 0, ',', '.') . ' Kz foram creditados na sua carteira.',
        ]);

        Notification::create([
            'user_id'    => $service->freelancer_id,
            'service_id' => $service->id,
            'type'       => 'dispute_resolved',
            'title'      => 'Disputa resolvida',
            'message'    => 'A disputa no projecto "' . $service->titulo . '" foi resolvida. O valor foi reembolsado ao cliente.',
        ]);

        AuditLogger::log(
            'client_refunded',
            "Reembolso de {$service->valor} Kz processado ao cliente '{$service->cliente->name}' na disputa do projecto '{$service->titulo}'",
            'Service', $service->id, $before, ['status' => 'cancelled']
        );

        session()->flash('success', 'Cliente reembolsado. ' . number_format($service->valor, 0, ',', '.') . ' Kz creditados na sua carteira.');
    }

    public function render()
    {
        $query = Dispute::with(['service', 'opener'])
            ->orderByDesc('created_at');

        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        $disputes = $query->paginate(15);

        $selected = $this->selectedId
            ? Dispute::with(['service.cliente', 'service.freelancer', 'opener', 'messages.user'])->find($this->selectedId)
            : null;

        // Services em_moderacao sem disputa associada (legado ou edge-case)
        $orphanModerations = Service::where('status', 'em_moderacao')
            ->whereNotIn('id', Dispute::pluck('service_id'))
            ->with(['cliente', 'freelancer'])
            ->get();

        return view('livewire.admin.dispute-admin', compact('disputes', 'selected', 'orphanModerations'))
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Gestão de Disputas']);
    }
}

