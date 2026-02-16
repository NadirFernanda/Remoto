<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use App\Models\Service;
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
            throw new \Exception('Ação não permitida. Você não pode aceitar este serviço.');
        }
        // Não altera status do serviço, apenas cadastra candidatura
        \Log::debug('CANDIDATURA SERVIÇO: freelancer_id', [
            'service_id' => $this->service->id,
            'freelancer_id' => $user->id
        ]);
        $this->service->save();

        // Cria ServiceCandidate se não existir
        $candidate = $this->service->candidates()->where('freelancer_id', $user->id)->first();
        if (!$candidate) {
            $this->service->candidates()->create([
                'freelancer_id' => $user->id,
                'status' => 'pending',
            ]);
        }

        session()->flash('success', 'Candidatura registrada com sucesso!');
        return redirect()->route('freelancer.dashboard');
    }

    public function refuseService()
    {
        $user = Auth::user();
        if (!$user || !$user->can('refuse', $this->service)) {
            throw new \Exception('Ação não permitida. Você não pode recusar este serviço.');
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
            throw new \Exception('Ação não permitida. Você não pode enviar proposta para este serviço.');
        }

        $this->validate([
            'proposalMessage' => 'required|string|max:2000',
            'proposalValue' => 'nullable|numeric|min:0',
        ]);

        // Agora: o freelancer informa o VALOR LÍQUIDO (o que ele receberá).
        // A taxa de plataforma (10% sobre o valor bruto) é calculada internamente.
        // Dado net = 0.9 * gross  => gross = net / 0.9  => fee = gross * 0.10 = net / 9
        $net = $this->proposalValue !== null ? (float)$this->proposalValue : 0.0;
        $fee = $net > 0 ? round($net / 9, 2) : 0.0; // aproximado: fee = net * (1/9)
        $gross = round($net + $fee, 2);

        // Cria ou atualiza candidatura com status de proposta
        $candidate = $this->service->candidates()->where('freelancer_id', $user->id)->first();
        if (!$candidate) {
            $this->service->candidates()->create([
                'freelancer_id' => $user->id,
                'status' => 'proposal_sent',
                'proposal_message' => $this->proposalMessage,
                'proposal_value' => $net, // stored as líquido (freelancer receives)
                'proposal_fee' => $fee,
                'proposal_net' => $net,
            ]);
        } else {
            $candidate->status = 'proposal_sent';
            $candidate->proposal_message = $this->proposalMessage;
            $candidate->proposal_value = $net; // stored as líquido (freelancer receives)
            $candidate->proposal_fee = $fee;
            $candidate->proposal_net = $net;
            $candidate->save();
        }

        \Log::info('Proposta enviada (detalhes)', [
            'service_id' => $this->service->id,
            'freelancer_id' => $user->id,
            'message' => $this->proposalMessage,
            'value' => $this->proposalValue,
        ]);

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
        return view('livewire.freelancer.service-review')->layout('layouts.livewire');
    }
}
