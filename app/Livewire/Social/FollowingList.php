<?php

namespace App\Livewire\Social;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SocialFollow;
use App\Modules\Social\Services\SocialInteractionService;
use Illuminate\Support\Facades\Auth;

class FollowingList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $tab = 'seguindo'; // seguindo | assinaturas

    protected $queryString = ['tab', 'search'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTab(): void
    {
        $this->resetPage();
        $this->search = '';
    }

    public function unfollow(int $creatorId): void
    {
        $user = Auth::user();
        if (!$user) return;

        app(SocialInteractionService::class)->toggleFollow($user, $creatorId);
    }

    public function render()
    {
        $user = Auth::user();

        // Freelancers/criadores que o utilizador segue
        $followingQuery = $user->following()
            ->with('freelancerProfile')
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));

        $following = $followingQuery->paginate(12);
        $totalFollowing = $user->following()->count();

        // Assinaturas pagas activas (criadores)
        $subscriptionsQuery = $user->subscriptionsAsSubscriber()
            ->with('creator.freelancerProfile')
            ->where('status', 'active')
            ->when($this->search, fn($q) => $q->whereHas('creator', fn($u) => $u->where('name', 'like', '%' . $this->search . '%')));

        $subscriptions = $subscriptionsQuery->paginate(12, ['*'], 'subPage');
        $totalSubscriptions = $user->subscriptionsAsSubscriber()->where('status', 'active')->count();

        return view('livewire.social.following-list', compact(
            'following', 'totalFollowing',
            'subscriptions', 'totalSubscriptions',
        ))->layout('layouts.dashboard', ['dashboardTitle' => 'A Seguir']);
    }
}
