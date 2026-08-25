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
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

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

    /** Projecto do modal de proposta actualmente aberto (ou null). */
    public function getProposalServiceProperty(): ?Service
    {
        return $this->proposalServiceId ? Service::find($this->proposalServiceId) : null;
    }

    /**
     * Decomposição mostrada no modal: o freelancer indica quanto deseja
     * ACRESCENTAR ao valor que o cliente já pagou (não o valor total), para
     * ficar claro que o valor actual do projecto já está em escrow.
     */
    public function getProposalBreakdownProperty(): array
    {
        $service    = $this->proposalService;
        $atual      = $service ? (float) $service->valor : 0.0;
        $extra      = max(0.0, (float) ($this->proposalValue ?? 0));
        $novo       = round($atual + $extra, 2);
        $clientRate = \App\Services\FeeService::serviceClientRate();
        $taxa       = round($novo * $clientRate, 2);

        return [
            'atual'             => $atual,
            'extra'             => $extra,
            'novo'              => $novo,
            'taxa'              => $taxa,
            'valor_liquido'     => round($novo - $taxa, 2),
            'clientRatePercent' => round($clientRate * 100, 1),
        ];
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

            // O campo do formulário é o valor a ACRESCENTAR ao que o cliente já
            // pagou (não um novo total) — aqui convertemos para o total, que é
            // o formato que o resto do sistema espera em proposal_value (ver
            // ServiceChat::abrirModalValor, que pré-preenche o pagamento com
            // este total).
            $valorTotalProposto = ($this->proposalValue !== null && $this->proposalValue !== '')
                ? round((float) $service->valor + (float) $this->proposalValue, 2)
                : null;

            $candidate = $service->candidates()->where('freelancer_id', $user->id)->first();
            if (!$candidate) {
                $service->candidates()->create([
                    'freelancer_id'    => $user->id,
                    'status'           => 'proposal_sent',
                    'proposal_message' => $this->proposalMessage,
                    'proposal_value'   => $valorTotalProposto,
                ]);
            } else {
                $candidate->status           = 'proposal_sent';
                $candidate->proposal_message = $this->proposalMessage;
                $candidate->proposal_value   = $valorTotalProposto;
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
            // Pesquisa "solta" — qualquer palavra da pesquisa que apareça em
            // qualquer um dos campos já conta, em vez de exigir a frase exacta.
            ->when(trim($this->search) !== '', function ($q) {
                $terms = array_filter(preg_split('/\s+/', trim($this->search)));
                $q->where(function ($q2) use ($terms) {
                    foreach ($terms as $term) {
                        // ilike (Postgres) — insensível a maiúsculas/minúsculas
                        $q2->orWhere('titulo', 'ilike', "%{$term}%")
                           ->orWhere('descricao', 'ilike', "%{$term}%")
                           ->orWhere('categoria', 'ilike', "%{$term}%");
                    }
                });
            })
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
            ->layout('layouts.dashboard', ['dashboardTitle' => '']);
    }
}
