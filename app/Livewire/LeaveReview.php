<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Review;
use App\Models\Service;
use App\Events\ReviewSubmitted;
use App\Notifications\ReviewReceivedNotification;
use Illuminate\Support\Facades\RateLimiter;

class LeaveReview extends Component
{
    public Service $service;
    public int $rating = 5;
    public string $comment = '';
    public bool $submitted = false;
    public bool $alreadyReviewed = false;

    // Target user (the one being reviewed)
    public $targetUser;

    public function mount(Service $service)
    {
        $this->service = $service;
        $user = Auth::user();

        // Determine who the current user is reviewing
        if ($user->id === $service->cliente_id) {
            $this->targetUser = $service->freelancer;
        } elseif ($user->id === $service->freelancer_id) {
            $this->targetUser = $service->cliente;
        } else {
            abort(403);
        }

        // Check if already reviewed this service
        $this->alreadyReviewed = Review::where('author_id', $user->id)
            ->where('service_id', $service->id)
            ->exists();
    }

    public function submitReview()
    {
        $user = Auth::user();

        $rateLimitKey = 'submit-review:' . ($user?->id ?? request()->ip());
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            session()->flash('error', "Limite de avaliações atingido. Tente novamente em {$seconds}s.");
            return;
        }
        RateLimiter::hit($rateLimitKey, 3600);

        $this->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($this->alreadyReviewed) {
            session()->flash('error', 'Você já avaliou este projeto.');
            return;
        }

        if ($this->service->status !== 'completed') {
            session()->flash('error', 'Só é possível avaliar projetos concluídos.');
            return;
        }

        $review = Review::create([
            'author_id' => $user->id,
            'target_id' => $this->targetUser->id,
            'service_id'=> $this->service->id,
            'rating'    => $this->rating,
            'comment'   => trim($this->comment),
        ]);

        $profileUrl = $this->targetUser->role === 'freelancer'
            ? route('freelancer.show', $this->targetUser)
            : route('client.dashboard');
        $this->targetUser->notify(new ReviewReceivedNotification($review, $user, $profileUrl));
        ReviewSubmitted::dispatch($review, $user, $this->targetUser);

        $this->submitted = true;
        $this->alreadyReviewed = true;
        session()->flash('success', 'Avaliação enviada com sucesso!');
    }

    public function render()
    {
        return view('livewire.leave-review')
            ->layout('layouts.app');
    }
}
