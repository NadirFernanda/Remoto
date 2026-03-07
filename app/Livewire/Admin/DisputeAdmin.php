<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Dispute;

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
        $this->adminNote = $dispute->admin_note ?? '';
        $this->newStatus = $dispute->status;
        $this->replyMessage = '';
    }

    public function saveChanges(): void
    {
        $this->validate([
            'newStatus' => 'required|in:aberta,em_mediacao,resolvida,encerrada',
            'adminNote' => 'nullable|string|max:2000',
        ]);

        $dispute = Dispute::findOrFail($this->selectedId);
        $dispute->update([
            'status' => $this->newStatus,
            'admin_note' => $this->adminNote ?: null,
        ]);

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

        $this->replyMessage = '';
        session()->flash('success', 'Mensagem enviada.');
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
            ->layout('layouts.main');
    }
}
