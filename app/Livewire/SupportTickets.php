<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class SupportTickets extends Component
{
    use WithPagination;

    // List state
    public string $statusFilter = '';

    // New ticket form
    public bool $showForm = false;
    public string $category = '';
    public string $subject = '';
    public string $message = '';
    public string $priority = 'normal';

    // View ticket
    public ?int $selectedTicketId = null;
    public string $replyMessage = '';

    public function openForm(): void
    {
        $this->reset(['category', 'subject', 'message', 'priority']);
        $this->showForm    = true;
        $this->selectedTicketId = null;
    }

    public function submitTicket(): void
    {
        $rateLimitKey = 'support-ticket:' . Auth::id();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $secs = RateLimiter::availableIn($rateLimitKey);
            session()->flash('error', "Muitos pedidos enviados. Aguarde {$secs}s.");
            return;
        }
        RateLimiter::hit($rateLimitKey, 600);

        $this->validate([
            'category' => 'required|in:pagamento,projecto,conta,tecnico,outro',
            'subject'  => 'required|string|min:5|max:120',
            'message'  => 'required|string|min:20|max:3000',
            'priority' => 'required|in:normal,alta,urgente',
        ], [
            'category.required' => 'Selecione uma categoria.',
            'subject.required'  => 'Indique o assunto do ticket.',
            'subject.min'       => 'O assunto deve ter pelo menos 5 caracteres.',
            'message.required'  => 'Descreva o problema.',
            'message.min'       => 'Descreva com pelo menos 20 caracteres.',
        ]);

        $ticket = SupportTicket::create([
            'user_id'  => Auth::id(),
            'category' => $this->category,
            'subject'  => $this->subject,
            'message'  => $this->message,
            'priority' => $this->priority,
            'status'   => 'aberto',
        ]);

        // Notify all admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type'    => 'support_ticket_new',
                'title'   => 'Novo ticket de suporte',
                'message' => Auth::user()->name . ' abriu um ticket: "' . $ticket->subject . '".',
            ]);
        }

        $this->reset(['category', 'subject', 'message', 'priority']);
        $this->showForm = false;
        $this->selectedTicketId = $ticket->id;
        session()->flash('success', 'Ticket enviado com sucesso! Responderemos em breve.');
    }

    public function selectTicket(int $id): void
    {
        $ticket = SupportTicket::where('user_id', Auth::id())->findOrFail($id);
        $this->selectedTicketId = $ticket->id;
        $this->showForm = false;
        $this->replyMessage = '';
    }

    public function sendReply(): void
    {
        $this->validate([
            'replyMessage' => 'required|string|min:5|max:3000',
        ], [
            'replyMessage.required' => 'Escreva a sua resposta.',
            'replyMessage.min'      => 'A resposta deve ter pelo menos 5 caracteres.',
        ]);

        $ticket = SupportTicket::where('user_id', Auth::id())->findOrFail($this->selectedTicketId);

        // Reabrir se estava fechado
        if ($ticket->status === 'fechado') {
            $ticket->status = 'aberto';
            $ticket->save();
        }

        SupportTicketReply::create([
            'ticket_id'      => $ticket->id,
            'user_id'        => Auth::id(),
            'message'        => $this->replyMessage,
            'is_admin_reply' => false,
        ]);

        // Notify admins of new reply
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type'    => 'support_ticket_new',
                'title'   => 'Resposta no ticket de suporte',
                'message' => Auth::user()->name . ' respondeu no ticket #' . $ticket->id . ': "' . $ticket->subject . '".',
            ]);
        }

        $this->replyMessage = '';
        $this->dispatch('ticket-updated');
    }

    public function render()
    {
        $query = SupportTicket::where('user_id', Auth::id())
            ->with(['replies', 'user'])
            ->orderByDesc('updated_at');

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $tickets = $query->paginate(10);

        $selected = $this->selectedTicketId
            ? SupportTicket::with(['replies.user'])->where('user_id', Auth::id())->find($this->selectedTicketId)
            : null;

        return view('livewire.support-tickets', compact('tickets', 'selected'))
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Suporte']);
    }
}
