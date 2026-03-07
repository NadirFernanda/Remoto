<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Traits\UserSessionTrait;
use App\Models\Review;

class ReviewPanel extends Component
{
    use UserSessionTrait;
    // Removed extra opening curly brace
    public $reviewsGiven = [];
    public $reviewsReceived = [];

    public function mount()
    {
        $user = $this->getCurrentUser();
        // Avaliações feitas pelo utilizador (com eager loading)
        $this->reviewsGiven = Review::where('author_id', $user->id)
            ->with(['target', 'service'])
            ->orderByDesc('created_at')
            ->get();
        // Avaliações recebidas pelo utilizador
        $this->reviewsReceived = Review::where('target_id', $user->id)
            ->with(['author', 'service'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function render()
    {
        return view('livewire.client.review-panel')
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Avaliações']);
    }
}
