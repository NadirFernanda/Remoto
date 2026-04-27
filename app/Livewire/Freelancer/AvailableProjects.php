<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Notification;
use App\Models\Service;
use App\Models\ServiceCandidate;
use Illuminate\Support\Facades\RateLimiter;

class AvailableProjects extends Component
{
    use WithPagination;

    public $proposalModal = false;
    public $proposalServiceId = null;
    public $proposalMessage = '';
    public $proposalValue = null;

    public function acceptService($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        $user = auth()->user();
        if (!$user || $user->id === $service->cliente_id) {
            session()->flash('error', 'Você não pode aceitar um projeto que você mesmo criou.');
            return;
        }
        // Não altera status do serviço, apenas cadastra candidatura
        $service->save();

        // Limite de 6 candidatos por projeto
        if ($service->candidates()->count() >= 6) {
            session()->flash('error', 'Este projeto já atingiu o limite de 6 candidatos.');
            return redirect()->route('freelancer.dashboard');
        }

        // Cria ou atualiza ServiceCandidate
        $candidate = $service->candidates()->where('freelancer_id', $user->id)->first();
        if (!$candidate) {
            $service->candidates()->create([
                'freelancer_id' => $user->id,
                'status' => 'pending',
                'proposal_message' => $this->proposalMessage,
                'proposal_value' => $this->proposalValue,
            ]);
        } elseif ($candidate->status === 'invited') {
            // Freelancer está aceitando um convite do cliente
            $candidate->status = 'pending';
            $candidate->proposal_message = $this->proposalMessage;
            $candidate->proposal_value = $this->proposalValue;
            $candidate->save();

            // Notificar o cliente que o freelancer aceitou o convite
            Notification::create([
                'user_id'    => $service->cliente_id,
                'service_id' => $service->id,
                'type'       => 'proposal_received',
                'title'      => 'Freelancer aceitou o convite',
                'message'    => $user->name . ' aceitou o seu convite para o projecto "' . $service->titulo . '".',
            ]);
        }

        session()->flash('success', 'Candidatura registrada com sucesso!');
        return redirect()->route('freelancer.dashboard');
    }

    public function refuseService($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        $user = auth()->user();
        if (!$user || $user->id === $service->cliente_id) {
            session()->flash('error', 'Ação não permitida.');
            return;
        }
        $service->status = 'published';
        $service->freelancer_id = null;
        $service->save();
        session()->flash('info', 'Serviço recusado.');
        return redirect()->route('freelancer.available-projects');
    }

    public function showProposalModal($serviceId)
    {
        $this->proposalServiceId = $serviceId;
        $this->proposalMessage = '';
        $this->proposalValue = null;
        $this->proposalModal = true;
    }

    public function sendProposal($serviceId = null)
    {
        $serviceId = $serviceId ?? $this->proposalServiceId;
        $service = Service::findOrFail($serviceId);
        $user = auth()->user();
        if (!$user || $user->id === $service->cliente_id) {
            session()->flash('error', 'Ação não permitida. Você não pode enviar proposta para este serviço.');
            return;
        }

        $rateLimitKey = 'send-proposal:' . ($user->id ?? request()->ip());
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            session()->flash('error', "Muitas propostas enviadas. Aguarde {$seconds}s antes de tentar novamente.");
            return;
        }
        RateLimiter::hit($rateLimitKey, 600);

        $this->validate([
            'proposalMessage' => 'required|string|max:2000',
            'proposalValue' => 'nullable|numeric|min:0',
        ]);

        // Limite de 6 propostas — verificado dentro de um lock para evitar race conditions
        $created = false;
        \Illuminate\Support\Facades\DB::transaction(function () use ($service, $user, &$created) {
            // Re-verifica dentro do lock
            $service->refresh();
            if ($service->status !== 'published') {
                session()->flash('error', 'Este projecto já não está disponível.');
                return;
            }

            $activeProposals = $service->candidates()
                ->whereNotIn('status', ['rejected'])
                ->lockForUpdate()
                ->count();

            if ($activeProposals >= 6) {
                session()->flash('error', 'Este projecto já atingiu o limite de 6 propostas.');
                return;
            }

            $candidate = $service->candidates()->where('freelancer_id', $user->id)->first();
            if (!$candidate) {
                $service->candidates()->create([
                    'freelancer_id'    => $user->id,
                    'status'           => 'proposal_sent',
                    'proposal_message' => $this->proposalMessage,
                    'proposal_value'   => $this->proposalValue,
                ]);
            } else {
                $candidate->status           = 'proposal_sent';
                $candidate->proposal_message = $this->proposalMessage;
                $candidate->proposal_value   = $this->proposalValue;
                $candidate->save();
            }
            $created = true;

            // Notificar o cliente que recebeu uma nova proposta
            Notification::create([
                'user_id'    => $service->cliente_id,
                'service_id' => $service->id,
                'type'       => 'proposal_received',
                'title'      => 'Nova proposta recebida',
                'message'    => $user->name . ' enviou uma proposta para o seu projecto "' . $service->titulo . '".',
            ]);
        });

        $this->proposalModal = false;

        if ($created) {
            session()->flash('success', 'Proposta enviada com sucesso!');
        }
        return redirect()->route('freelancer.dashboard');
    }



    public function render()
    {
        $userId = auth()->id();

        // Projectos com 6+ propostas activas (não rejeitadas) estão fechados
        $fullProjectIds = \App\Models\ServiceCandidate::selectRaw('service_id')
            ->whereNotIn('status', ['rejected'])
            ->groupBy('service_id')
            ->havingRaw('COUNT(*) >= 6')
            ->pluck('service_id');

        // IDs de projectos onde este freelancer foi rejeitado
        $rejectedProjectIds = \App\Models\ServiceCandidate::where('freelancer_id', $userId)
            ->where('status', 'rejected')
            ->pluck('service_id');

        $projects = Service::with('cliente')
            ->where('status', 'published')
            ->whereNull('freelancer_id')
            ->where('cliente_id', '!=', $userId)
            ->whereNotIn('id', $fullProjectIds)
            ->whereNotIn('id', $rejectedProjectIds)
            ->orderByDesc('created_at')
            ->paginate(12);

        $myCandidacies = ServiceCandidate::where('freelancer_id', $userId)
            ->whereIn('service_id', $projects->pluck('id'))
            ->whereIn('status', ['pending', 'proposal_sent', 'invited'])
            ->pluck('service_id')
            ->all();

        // Count of active (non-rejected) proposals per project to show "X/6" indicator
        $proposalCounts = ServiceCandidate::whereIn('service_id', $projects->pluck('id'))
            ->whereNotIn('status', ['rejected'])
            ->selectRaw('service_id, COUNT(*) as total')
            ->groupBy('service_id')
            ->pluck('total', 'service_id')
            ->all();

        return view('livewire.freelancer.available-projects', compact('projects', 'myCandidacies', 'proposalCounts'))
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Projectos Disponíveis']);
    }
}
