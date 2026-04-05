<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationPanel extends Component
{
    public $notifications = [];
    public int $unreadCount = 0;

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications(): void
    {
        $user = Auth::user();
        if (!$user) { $this->notifications = []; return; }

        // Mark all as read when the panel page is opened
        Notification::where('user_id', $user->id)
            ->where('read', false)
            ->update(['read' => true]);

        $this->notifications = Notification::where('user_id', $user->id)
            ->with('user')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        $this->unreadCount = 0; // all marked read
    }

    public function refresh(): void
    {
        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.notification-panel', [
            'notifications' => $this->notifications,
            'unreadCount'   => $this->unreadCount,
        ])->layout('layouts.dashboard', ['dashboardTitle' => 'Notificações']);
    }
}
