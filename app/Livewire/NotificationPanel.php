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

        $isFreelancerMode = $user->activeRole() === 'freelancer';

        $freelancerOnly = ['novo_projeto','service_chosen','revision_requested','project_started',
            'payment_adjustment','delivery_approved','payment_released','saque_aprovado',
            'saque_rejeitado','service_rejected','project_invite','direct_invite'];
        $clientOnly = ['refund_processed','refund_approved','refund_rejected',
            'delivery_submitted','proposal_accepted','proposal_rejected'];

        // Mark all visible notifications as read
        Notification::where('user_id', $user->id)
            ->where('read', false)
            ->when(!$isFreelancerMode, fn($q) => $q->whereNotIn('type', $freelancerOnly))
            ->when($isFreelancerMode,  fn($q) => $q->whereNotIn('type', $clientOnly))
            ->update(['read' => true]);

        $this->notifications = Notification::where('user_id', $user->id)
            ->with('user')
            ->when(!$isFreelancerMode, fn($q) => $q->whereNotIn('type', $freelancerOnly))
            ->when($isFreelancerMode,  fn($q) => $q->whereNotIn('type', $clientOnly))
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
