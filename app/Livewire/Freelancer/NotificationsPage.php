<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class NotificationsPage extends Component
{
    use WithPagination;

    public function mount(): void
    {
        $user = Auth::user();
        $role = $user->activeRole();
        $freelancerOnly = ['novo_projeto','service_chosen','revision_requested','project_started',
            'payment_adjustment','delivery_approved','payment_released','saque_aprovado',
            'saque_rejeitado','service_rejected','project_invite','direct_invite'];
        $clientOnly = ['delivery_submitted','proposal_accepted','proposal_rejected'];

        // Mark all visible notifications as read
        Notification::where('user_id', Auth::id())
            ->where('read', false)
            ->when($role === 'cliente', fn($q) => $q->whereNotIn('type', $freelancerOnly))
            ->when($role === 'freelancer', fn($q) => $q->whereNotIn('type', $clientOnly))
            ->where(fn($q) => $q->whereNull('target_role')->orWhere('target_role', $role))
            ->update(['read' => true]);
    }

    public function render()
    {
        $user = Auth::user();
        $role = $user->activeRole();
        $freelancerOnly = ['novo_projeto','service_chosen','revision_requested','project_started',
            'payment_adjustment','delivery_approved','payment_released','saque_aprovado',
            'saque_rejeitado','service_rejected','project_invite','direct_invite'];
        $clientOnly = ['delivery_submitted','proposal_accepted','proposal_rejected'];

        $baseQuery = Notification::where('user_id', $user->id)
            ->when($role === 'cliente', fn($q) => $q->whereNotIn('type', $freelancerOnly))
            ->when($role === 'freelancer', fn($q) => $q->whereNotIn('type', $clientOnly))
            ->where(fn($q) => $q->whereNull('target_role')->orWhere('target_role', $role))
            ->orderByDesc('created_at');

        $recent        = (clone $baseQuery)->take(3)->get();
        $notifications = (clone $baseQuery)->paginate(15);

        return view('livewire.freelancer.notifications-page', [
            'notifications' => $notifications,
            'recent'        => $recent
        ])->layout('layouts.dashboard', ['dashboardTitle' => 'Notificações']);
    }
}
