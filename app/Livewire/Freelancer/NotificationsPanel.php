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
        $this->unreadCount = Notification::where('user_id', Auth::id())
            ->where('read', false)
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
