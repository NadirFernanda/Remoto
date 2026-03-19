<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationBell extends Component
{
    public int $unreadCount = 0;
    public array $recent = [];

    public function mount(): void
    {
        $this->refresh();
    }

    public function refresh(): void
    {
        $user = Auth::user();
        if (!$user) return;

        $this->unreadCount = Notification::where('user_id', $user->id)
            ->where('read', false)
            ->count();

        $this->recent = Notification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(8)
            ->get()
            ->map(fn($n) => [
                'id'         => $n->id,
                'title'      => $n->title ?? '',
                'message'    => $n->message,
                'type'       => $n->type,
                'read'       => (bool) $n->read,
                'service_id' => $n->service_id,
                'created_at' => $n->created_at->diffForHumans(),
            ])
            ->toArray();
    }

    public function markAllRead(): void
    {
        $user = Auth::user();
        if (!$user) return;

        Notification::where('user_id', $user->id)
            ->where('read', false)
            ->update(['read' => true]);

        $this->refresh();
    }

    public function markRead(int $id): void
    {
        $user = Auth::user();
        if (!$user) return;

        Notification::where('id', $id)
            ->where('user_id', $user->id)
            ->update(['read' => true]);

        $this->refresh();
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}
