<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use App\Models\Service;
use App\Models\ServiceCandidate;

class AvailableProjects extends Component
{
    public $projects;
    public $myCandidacies = []; // IDs dos projetos onde já me candidatei
    public $proposalModal = false;
    public $proposalServiceId = null;
    public $proposalMessage = '';
    public $proposalValue = null;

    public function mount()
    {
        $userId = auth()->id();
        // Exibe apenas projetos publicados, sem freelancer, que o próprio usuário NÃO criou
        $this->projects = Service::where('status', 'published')
            ->whereNull('freelancer_id')
            ->where('cliente_id', '!=', $userId)
            ->orderByDesc('created_at')
            ->get();

        // IDs dos projetos onde este freelancer já tem candidatura ativa
        $serviceIds = $this->projects->pluck('id')->all();
        $this->myCandidacies = ServiceCandidate::where('freelancer_id', $userId)
            ->whereIn('service_id', $serviceIds)
            ->whereIn('status', ['pending', 'proposal_sent', 'invited'])
            ->pluck('service_id')
            ->all();
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
            throw new \Exception('Ação não permitida. Você não pode enviar proposta para este serviço.');
        }

        $this->validate([
            'proposalMessage' => 'required|string|max:2000',
            'proposalValue' => 'nullable|numeric|min:0',
        ]);


        // Limite de 6 candidatos por projeto
        if ($service->candidates()->count() >= 6) {
            session()->flash('error', 'Este projeto já atingiu o limite de 6 candidatos.');
            $this->proposalModal = false;
            return redirect()->route('freelancer.dashboard');
        }

        // Cria candidatura com status de proposta, se ainda não existir
        $candidate = $service->candidates()->where('freelancer_id', $user->id)->first();
        if (!$candidate) {
            $service->candidates()->create([
                'freelancer_id' => $user->id,
                'status' => 'proposal_sent',
                'proposal_message' => $this->proposalMessage,
                'proposal_value' => $this->proposalValue,
            ]);
        } else {
            $candidate->status = 'proposal_sent';
            $candidate->proposal_message = $this->proposalMessage;
            $candidate->proposal_value = $this->proposalValue;
            $candidate->save();
        }

        session()->flash('success', 'Proposta enviada com sucesso!');
        $this->proposalModal = false;
        return redirect()->route('freelancer.dashboard');
    }



    public function render()
    {
        return view('livewire.freelancer.available-projects')->layout('layouts.livewire');
    }
}
