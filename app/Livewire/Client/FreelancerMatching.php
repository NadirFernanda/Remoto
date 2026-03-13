<?php

namespace App\Livewire\Client;

use Livewire\Component;
use App\Models\Service;
use App\Models\ServiceCandidate;
use App\Modules\Marketplace\Services\FreelancerMatchingService;
use Illuminate\Support\Facades\Auth;

class FreelancerMatching extends Component
{
    public Service $service;
    public int     $limit = 6;

    protected $listeners = ['refreshMatching' => '$refresh'];

    public function mount(Service $service): void
    {
        // Only the owner can access matching for their service
        if ($service->cliente_id !== Auth::id()) {
            abort(403);
        }

        $this->service = $service;
    }

    /**
     * Invite a freelancer directly: create a ServiceCandidate with status "invited".
     */
    public function invite(int $freelancerId): void
    {
        $existing = $this->service->candidates()
            ->where('freelancer_id', $freelancerId)
            ->first();

        if ($existing) {
            session()->flash('info', 'Este freelancer já foi convidado ou candidatou-se.');
            return;
        }

        ServiceCandidate::create([
            'service_id'    => $this->service->id,
            'freelancer_id' => $freelancerId,
            'status'        => 'invited',
        ]);

        \App\Models\Notification::create([
            'user_id'    => $freelancerId,
            'service_id' => $this->service->id,
            'type'       => 'project_invite',
            'title'      => 'Convite de projeto',
            'message'    => 'Você foi convidado para o projeto "' . $this->service->titulo . '".',
        ]);

        session()->flash('success', 'Freelancer convidado com sucesso!');
    }

    public function loadMore(): void
    {
        $this->limit += 6;
    }

    public function render()
    {
        $matcher      = new FreelancerMatchingService();
        $suggestions  = $matcher->recommend($this->service, $this->limit);
        $hasMore      = $suggestions->count() === $this->limit;

        return view('livewire.client.freelancer-matching', [
            'suggestions' => $suggestions,
            'hasMore'     => $hasMore,
        ])->layout('layouts.livewire');
    }
}
