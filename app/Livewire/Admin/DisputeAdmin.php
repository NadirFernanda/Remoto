<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Dispute;
use App\Models\Service;
use App\Services\AuditLogger;

class DisputeAdmin extends Component
{
    use WithPagination;

    public string $statusFilter = '';
    public ?int $selectedId = null;
    public string $adminNote = '';
    public string $newStatus = '';
    public string $replyMessage = '';

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

        $dispute = Dispute::findOrFail($this->selectedId);
        $dispute->messages()->create([
            'user_id' => auth()->id(),
            'message' => $this->replyMessage,
        ]);

        AuditLogger::log(
            'dispute_message_sent',
            "Admin respondeu à disputa #{$dispute->id}",
            'Dispute', $dispute->id
        );

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

        AuditLogger::log(
            'payment_released',
            "Pagamento liberado pelo admin para o serviço #{$service->id} '{$service->titulo}'",
            'Service', $service->id, $before, ['status' => 'completed', 'is_payment_released' => true]
        );

        session()->flash('success', 'Pagamento liberado para o freelancer.');
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

        return view('livewire.admin.dispute-admin', compact('disputes', 'selected'))
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Gestão de Disputas']);
    }
}

