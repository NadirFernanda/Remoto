<?php

namespace App\Livewire\Client;

use Livewire\Component;
use App\Models\Service;
use App\Modules\Marketplace\Services\FreelancerMatcherService;

class FreelancerRecommendations extends Component
{
    public Service $service;

    public function mount(Service $service): void
    {
        abort_unless($service->cliente_id === auth()->id(), 403);
        $this->service = $service;
    }

    public function render()
    {
        $matches = FreelancerMatcherService::match($this->service, 8);

        return view('livewire.client.freelancer-recommendations', compact('matches'))
            ->layout('layouts.app');
    }
}
