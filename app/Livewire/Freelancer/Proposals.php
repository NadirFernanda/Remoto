<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Proposal;
use App\Models\Service;
use App\Models\Notification;

class Proposals extends Component
{
    use WithPagination;

    public string $tab = 'pending'; // pending | accepted | rejected

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    public function accept(int $proposalId): void
    {
        $user     = Auth::user();
        $proposal = Proposal::where('id', $proposalId)
            ->where('recipient_id', $user->id)
            ->where('status', 'pending')
            ->firstOrFail();

        // Criar o projeto/serviço directamente 
        $service = Service::create([
            'cliente_id'    => $proposal->sender_id,
            'freelancer_id' => $user->id,
            'titulo'        => $proposal->title ?? 'Projecto via proposta',
            'briefing'      => $proposal->message,
            'service_type'  => 'direct_invite',
            'valor'         => $proposal->value ?? 0,
            'taxa'          => $proposal->fee   ?? 0,
            'valor_liquido' => $proposal->net   ?? 0,
            'status'        => 'accepted',
        ]);

        // Marcar proposta como aceite
        $proposal->update(['status' => 'accepted']);

        // Notificar o cliente
        Notification::create([
            'user_id' => $proposal->sender_id,
            'type'    => 'proposal_accepted',
            'title'   => 'Proposta aceite!',
            'message' => $user->name . ' aceitou a sua proposta "' . $proposal->title . '".',
        ]);

        session()->flash('success', 'Proposta aceite. O projeto foi criado e já pode começar a trabalhar.');
    }

    public function decline(int $proposalId): void
    {
        $user     = Auth::user();
        $proposal = Proposal::where('id', $proposalId)
            ->where('recipient_id', $user->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $proposal->update(['status' => 'rejected']);

        // Notificar o cliente
        Notification::create([
            'user_id' => $proposal->sender_id,
            'type'    => 'proposal_rejected',
            'title'   => 'Proposta recusada',
            'message' => $user->name . ' recusou a sua proposta "' . $proposal->title . '".',
        ]);

        session()->flash('info', 'Proposta recusada.');
    }

    public function render()
    {
        $user = Auth::user();

        $proposals = Proposal::where('recipient_id', $user->id)
            ->where('status', $this->tab)
            ->with('sender')
            ->orderByDesc('created_at')
            ->paginate(10);

        $counts = [
            'pending'  => Proposal::where('recipient_id', $user->id)->where('status', 'pending')->count(),
            'accepted' => Proposal::where('recipient_id', $user->id)->where('status', 'accepted')->count(),
            'rejected' => Proposal::where('recipient_id', $user->id)->where('status', 'rejected')->count(),
        ];

        return view('livewire.freelancer.proposals', compact('proposals', 'counts'))
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Propostas recebidas']);
    }
}
