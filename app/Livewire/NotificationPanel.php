<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationPanel extends Component
{
    public $notifications = [];

    public function mount()
    {
        $user = Auth::user();
        if ($user) {
            $this->notifications = Notification::where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->limit(20)
                ->get();
        } else {
            $this->notifications = [];
        }
    }

    public function render()
    {
        return view('livewire.notification-panel')
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Notificações']);
    }
}
