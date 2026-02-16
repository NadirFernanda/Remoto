<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use App\Models\Service;

class AvailableProjects extends Component
{
    public $projects;
    public $proposalModal = false;
    public $proposalServiceId = null;
    public $proposalMessage = '';
    public $proposalValue = null;

    public function mount()
    {
        // Exibe apenas projetos publicados e ainda não aceitos por nenhum freelancer
        $this->projects = Service::where('status', 'published')
            ->whereNull('freelancer_id')
            ->orderByDesc('created_at')
            ->get();
    }

    public function acceptService($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        $user = auth()->user();
        if (!$user || $user->id === $service->cliente_id) {
            throw new \Exception('Ação não permitida. Você não pode aceitar este serviço.');
        }
        // Não altera status do serviço, apenas cadastra candidatura
        $service->save();

        // Cria ServiceCandidate se não existir
        $candidate = $service->candidates()->where('freelancer_id', $user->id)->first();
        if (!$candidate) {
            $service->candidates()->create([
                'freelancer_id' => $user->id,
                'status' => 'pending',
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
            throw new \Exception('Ação não permitida. Você não pode recusar este serviço.');
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

        // Cria candidatura com status de proposta, se ainda não existir
        $candidate = $service->candidates()->where('freelancer_id', $user->id)->first();
        if (!$candidate) {
            $service->candidates()->create([
                'freelancer_id' => $user->id,
                'status' => 'proposal_sent',
            ]);
        } else {
            $candidate->status = 'proposal_sent';
            $candidate->save();
        }

        // Log da proposta (conteúdo armazenado em log — alterar para persistência se desejar)
        \Log::info('Proposta enviada', [
            'service_id' => $service->id,
            'freelancer_id' => $user->id,
            'message' => $this->proposalMessage,
            'value' => $this->proposalValue,
        ]);

        session()->flash('success', 'Proposta enviada com sucesso!');
        $this->proposalModal = false;
        return redirect()->route('freelancer.dashboard');
    }



    public function render()
    {
        return view('livewire.freelancer.available-projects')->layout('layouts.livewire');
    }
}
