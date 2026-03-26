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

    public function openChat(int $proposalId): mixed
    {
        $user     = Auth::user();
        $proposal = Proposal::where('id', $proposalId)
            ->where('recipient_id', $user->id)
            ->firstOrFail();

        if (!$proposal->service_id) {
            // Proposta antiga sem Service — criar agora com o status correcto
            $chatStatus = in_array($proposal->status, ['accepted']) ? 'accepted' : 'negotiating';
            $service = Service::create([
                'cliente_id'    => $proposal->sender_id,
                'freelancer_id' => $user->id,
                'titulo'        => $proposal->title ?? 'Projecto via proposta',
                'briefing'      => $proposal->message,
                'service_type'  => 'direct_invite',
                'valor'         => $proposal->value ?? 0,
                'taxa'          => $proposal->fee   ?? 0,
                'valor_liquido' => $proposal->net   ?? 0,
                'status'        => $chatStatus,
            ]);
            $proposal->update(['service_id' => $service->id]);
        }

        return redirect()->route('service.chat', $proposal->service_id);
    }

    public function accept(int $proposalId): void
    {
        $user     = Auth::user();
        $proposal = Proposal::where('id', $proposalId)
            ->where('recipient_id', $user->id)
            ->where('status', 'pending')
            ->firstOrFail();

        if ($proposal->service_id) {
            // Promover o Service existente de negociação para aceite
            Service::where('id', $proposal->service_id)->update(['status' => 'accepted']);
        } else {
            // Fallback: propostas antigas sem service_id — criar Service e guardar o ID
            $newService = Service::create([
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
            $proposal->service_id = $newService->id;
        }

        $proposal->update(['status' => 'accepted', 'service_id' => $proposal->service_id]);

        Notification::create([
            'user_id'    => $proposal->sender_id,
            'service_id' => $proposal->service_id,
            'type'       => 'proposal_accepted',
            'title'      => 'Proposta aceite!',
            'message'    => $user->name . ' aceitou a sua proposta "' . $proposal->title . '".',
        ]);

        session()->flash('success', 'Proposta aceite! Pode continuar a negociação ou começar a trabalhar no chat.');
    }

    public function decline(int $proposalId): void
    {
        $user     = Auth::user();
        $proposal = Proposal::where('id', $proposalId)
            ->where('recipient_id', $user->id)
            ->where('status', 'pending')
            ->firstOrFail();

        if ($proposal->service_id) {
            Service::where('id', $proposal->service_id)->update(['status' => 'cancelled']);
        }

        $proposal->update(['status' => 'rejected']);

        // Notificar o cliente
        Notification::create([
            'user_id'    => $proposal->sender_id,
            'service_id' => $proposal->service_id,
            'type'       => 'proposal_rejected',
            'title'      => 'Proposta recusada',
            'message'    => $user->name . ' recusou a sua proposta "' . $proposal->title . '".',
        ]);

        session()->flash('info', 'Proposta recusada.');
    }

    public function render()
    {
        $user = Auth::user();

        $proposals = Proposal::where('recipient_id', $user->id)
            ->where('status', $this->tab)
            ->with('sender', 'service')
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
