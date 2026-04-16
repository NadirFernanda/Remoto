<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationsPanel extends Component
{
    public int $unreadCount = 0;

    public function refresh(): void
    {
        $user = Auth::user();
        if (!$user) { $this->unreadCount = 0; return; }
        $role = $user->activeRole();
        $clientOnly = ['delivery_submitted','proposal_accepted','proposal_rejected'];

        $this->unreadCount = Notification::where('user_id', $user->id)
            ->where('read', false)
            ->when($role === 'freelancer', fn($q) => $q->whereNotIn('type', $clientOnly))
            ->where(fn($q) => $q->whereNull('target_role')->orWhere('target_role', $role))
            ->count();
    }

    public function mount(): void
    {
        $this->refresh();
    }

    public function render()
    {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();
        return view('livewire.freelancer.notifications-panel', [
            'notifications' => $notifications,
            'unreadCount'   => $this->unreadCount,
        ]);
    }
}
