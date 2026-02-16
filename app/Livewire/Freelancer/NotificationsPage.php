<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class NotificationsPage extends Component
{
    use WithPagination;

    public function render()
    {
        $user = Auth::user();
        $recent = Notification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(3)
            ->get();
        $notifications = Notification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(15);
        return view('livewire.freelancer.notifications-page', [
            'notifications' => $notifications,
            'recent' => $recent
        ]);
    }
}
