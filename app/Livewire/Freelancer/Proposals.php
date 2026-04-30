<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Proposal;
use App\Models\Service;
use App\Models\Notification;
use App\Models\Wallet;
use App\Models\WalletLog;

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

        $escrowHandled = false;

        \Illuminate\Support\Facades\DB::transaction(function () use ($proposal, $user, &$escrowHandled) {
            // Determinar o Service a usar (existente ou novo)
            if ($proposal->service_id) {
                $service = Service::lockForUpdate()->findOrFail($proposal->service_id);
            } else {
                // Fallback: propostas antigas sem service_id — criar Service
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
                $proposal->service_id = $service->id;
            }

            // Garantir freelancer_id e valores do proposal no service
            $valorProposta = (float) ($proposal->value ?? $service->valor ?? 0);
            $service->freelancer_id = $user->id;
            if ($valorProposta > 0) {
                $service->valor         = $valorProposta;
                $service->taxa          = (float) ($proposal->fee ?? $service->taxa ?? 0);
                $service->valor_liquido = (float) ($proposal->net ?? $service->valor_liquido ?? 0);
            }

            // Tentar reter escrow e mover directamente para in_progress
            if ($valorProposta > 0) {
                $clientWallet = \App\Models\Wallet::where('user_id', $proposal->sender_id)->lockForUpdate()->first();
                if ($clientWallet && (float) $clientWallet->saldo >= $valorProposta) {
                    $clientWallet->decrement('saldo', $valorProposta);
                    $clientWallet->increment('saldo_pendente', $valorProposta);
                    \App\Models\WalletLog::create([
                        'user_id'   => $proposal->sender_id,
                        'wallet_id' => $clientWallet->id,
                        'valor'     => -$valorProposta,
                        'tipo'      => 'escrow_retido',
                        'descricao' => 'Pagamento retido em escrow para o projecto: ' . $service->titulo,
                    ]);
                    $service->status = 'in_progress';
                    $escrowHandled = true;
                } else {
                    // Saldo insuficiente — aguardar confirmação de pagamento do cliente
                    $service->status = 'accepted';
                }
            } else {
                // Sem valor acordado — iniciar directamente
                $service->status = 'in_progress';
                $escrowHandled = true;
            }
            $service->save();

            $proposal->update(['status' => 'accepted', 'service_id' => $service->id]);

            $msgCliente = $escrowHandled
                ? $user->name . ' aceitou a sua proposta "' . $proposal->title . '". O projecto está Em Andamento!'
                : $user->name . ' aceitou a sua proposta "' . $proposal->title . '". Confirme o pagamento para iniciar o projecto.';

            Notification::create([
                'user_id'    => $proposal->sender_id,
                'service_id' => $service->id,
                'type'       => 'proposal_accepted',
                'title'      => 'Proposta aceite!',
                'message'    => $msgCliente,
            ]);
        });

        (new \App\Services\AffiliateService())->creditCommissionForReferredAction($user, 'accept_proposal', $proposal->id);

        $flashMsg = $escrowHandled
            ? 'Proposta aceite! O projecto está agora Em Andamento. Pode entregar quando estiver pronto.'
            : 'Proposta aceite! O cliente será notificado para confirmar o pagamento e iniciar o projecto.';

        session()->flash('success', $flashMsg);
    }

    public function decline(int $proposalId): void
    {
        $user     = Auth::user();
        $proposal = Proposal::where('id', $proposalId)
            ->where('recipient_id', $user->id)
            ->where('status', 'pending')
            ->firstOrFail();

        \Illuminate\Support\Facades\DB::transaction(function () use ($proposal, $user) {
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
        });

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
            ->layout('layouts.dashboard', ['dashboardTitle' => '']);
    }
}
