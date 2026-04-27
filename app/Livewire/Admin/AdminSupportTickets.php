<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class AdminSupportTickets extends Component
{
    use WithPagination;

    public string $statusFilter = '';
    public string $categoryFilter = '';
    public string $priorityFilter = '';
    public string $search = '';

    public ?int $selectedTicketId = null;
    public string $replyMessage = '';
    public string $newStatus = '';

    public function selectTicket(int $id): void
    {
        $this->selectedTicketId = $id;
        $this->replyMessage = '';
        $ticket = SupportTicket::findOrFail($id);
        $this->newStatus = $ticket->status;
    }

    public function sendReply(): void
    {
        $this->validate([
            'replyMessage' => 'required|string|min:3|max:3000',
        ], [
            'replyMessage.required' => 'Escreva a sua resposta.',
        ]);

        $ticket = SupportTicket::findOrFail($this->selectedTicketId);

        SupportTicketReply::create([
            'ticket_id'      => $ticket->id,
            'user_id'        => Auth::id(),
            'message'        => $this->replyMessage,
            'is_admin_reply' => true,
        ]);

        // Move to in_progress if still open
        if ($ticket->status === 'aberto') {
            $ticket->status = 'em_andamento';
            $ticket->save();
            $this->newStatus = 'em_andamento';
        }

        // Notify the user
        Notification::create([
            'user_id' => $ticket->user_id,
            'type'    => 'support_ticket_reply',
            'title'   => 'Resposta ao seu ticket de suporte',
            'message' => 'A equipa de suporte respondeu ao seu ticket "' . $ticket->subject . '".',
        ]);

        $this->replyMessage = '';
        $this->dispatch('ticket-updated');
    }

    public function updateStatus(): void
    {
        $this->validate([
            'newStatus' => 'required|in:aberto,em_andamento,fechado',
        ]);

        $ticket = SupportTicket::findOrFail($this->selectedTicketId);
        $old = $ticket->status;
        $ticket->status = $this->newStatus;
        $ticket->save();

        if ($old !== $this->newStatus) {
            $statusLabel = SupportTicket::statusLabel($this->newStatus);
            Notification::create([
                'user_id' => $ticket->user_id,
                'type'    => 'support_ticket_reply',
                'title'   => 'Estado do ticket atualizado',
                'message' => 'O seu ticket "' . $ticket->subject . '" foi marcado como: ' . $statusLabel . '.',
            ]);
        }

        session()->flash('success', 'Estado actualizado.');
    }

    public function render()
    {
        $query = SupportTicket::with(['user', 'latestReply'])
            ->orderByRaw("CASE status WHEN 'aberto' THEN 0 WHEN 'em_andamento' THEN 1 ELSE 2 END")
            ->orderByRaw("CASE priority WHEN 'urgente' THEN 0 WHEN 'alta' THEN 1 ELSE 2 END")
            ->orderByDesc('updated_at');

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }
        if ($this->categoryFilter) {
            $query->where('category', $this->categoryFilter);
        }
        if ($this->priorityFilter) {
            $query->where('priority', $this->priorityFilter);
        }
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('subject', 'ilike', '%' . $this->search . '%')
                  ->orWhereHas('user', fn($u) => $u->where('name', 'ilike', '%' . $this->search . '%')
                                                    ->orWhere('email', 'ilike', '%' . $this->search . '%'));
            });
        }

        $tickets = $query->paginate(15);

        $selected = $this->selectedTicketId
            ? SupportTicket::with(['user', 'replies.user'])->find($this->selectedTicketId)
            : null;

        $counts = [
            'aberto'       => SupportTicket::where('status', 'aberto')->count(),
            'em_andamento' => SupportTicket::where('status', 'em_andamento')->count(),
            'fechado'      => SupportTicket::where('status', 'fechado')->count(),
        ];

        return view('livewire.admin.support-tickets', compact('tickets', 'selected', 'counts'))
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Tickets de Suporte']);
    }
}
