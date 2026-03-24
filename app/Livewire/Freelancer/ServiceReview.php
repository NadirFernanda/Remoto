<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use App\Models\Service;
use App\Notifications\ProposalReceivedNotification;
use App\Services\FeeService;
use Illuminate\Support\Facades\Auth;

class ServiceReview extends Component
{
    public Service $service;
    public $proposalModal = false;
    public $proposalMessage = '';
    public $proposalValue = null;

    public function mount(Service $service)
    {
        $this->service = $service;
    }

    public function acceptService()
    {
        $user = Auth::user();
        if (!$user || !$user->can('accept', $this->service)) {
            session()->flash('error', 'Você não tem permissão para aceitar este serviço.');
            return;
        }
        // Não altera status do serviço, apenas cadastra candidatura
        $this->service->save();

        // Cria ServiceCandidate se não existir
        $candidate = $this->service->candidates()->where('freelancer_id', $user->id)->first();
        if (!$candidate) {
            $this->service->candidates()->create([
                'freelancer_id' => $user->id,
                'status' => 'pending',
            ]);
        }

        // Notificar o cliente
        $cliente = $this->service->cliente;
        if ($cliente) {
            $cliente->notify(new ProposalReceivedNotification(
                $this->service,
                $user,
                route('client.projects')
            ));
        }

        session()->flash('success', 'Candidatura registrada com sucesso!');
        return redirect()->route('freelancer.dashboard');
    }

    public function refuseService()
    {
        $user = Auth::user();
        if (!$user || !$user->can('refuse', $this->service)) {
            session()->flash('error', 'Você não tem permissão para recusar este serviço.');
            return;
        }
        $this->service->status = 'published'; // Ou lógica de recusa
        $this->service->freelancer_id = null;
        $this->service->save();
        session()->flash('info', 'Serviço recusado.');
        return redirect()->route('freelancer.dashboard');
    }

    public function sendProposal()
    {
        $user = Auth::user();
        if (!$user || !$user->can('accept', $this->service)) {
            abort(403, 'Não tem permissão para enviar proposta para este serviço.');
        }

        $this->validate([
            'proposalMessage' => 'required|string|max:2000',
            'proposalValue'   => 'required|numeric|min:1000',
        ], [
            'proposalValue.required' => 'Informe o valor que pretende receber pelo projeto.',
            'proposalValue.min'      => 'O valor mínimo de proposta é Kz 1.000.',
        ]);

        // O freelancer informa o VALOR BRUTO do projeto (gross).
        // A plataforma deduz 20% na entrega — o freelancer recebe 80%.
        // FeeService::calculateServiceFee() aplica este modelo de forma consistente.
        $gross = (float) $this->proposalValue;
        $fees  = (new FeeService())->calculateServiceFee($gross);
        $fee   = $fees['taxa'];           // 20% deduzido ao freelancer
        $net   = $fees['valor_liquido'];  // 80% que o freelancer recebe

        // Cria ou atualiza candidatura com status de proposta
        $candidate = $this->service->candidates()->where('freelancer_id', $user->id)->first();
        $proposalData = [
            'status'           => 'proposal_sent',
            'proposal_message' => $this->proposalMessage,
            'proposal_value'   => $gross, // valor bruto proposto (base de cálculo)
            'proposal_fee'     => $fee,   // 20% plataforma (deduzido na entrega)
            'proposal_net'     => $net,   // 80% que o freelancer recebe
        ];
        if (!$candidate) {
            $this->service->candidates()->create(
                array_merge(['freelancer_id' => $user->id], $proposalData)
            );
        } else {
            $candidate->fill($proposalData)->save();
        }

        // Notificar o cliente
        $clienteProposal = $this->service->cliente;
        if ($clienteProposal) {
            $clienteProposal->notify(new ProposalReceivedNotification(
                $this->service,
                $user,
                route('client.projects')
            ));
        }

        session()->flash('success', 'Proposta enviada com sucesso!');
        $this->proposalModal = false;
        return redirect()->route('freelancer.dashboard');
    }

    public function showProposalModal()
    {
        $this->proposalMessage = '';
        $this->proposalValue = null;
        $this->proposalModal = true;
    }

    public function render()
    {
        return view('livewire.freelancer.service-review')->layout('layouts.dashboard', ['dashboardTitle' => 'Revisão do Serviço']);
    }
}
