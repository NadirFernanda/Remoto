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

        $isFreelancerMode = $user->activeRole() === 'freelancer';

        // Types that only make sense in one mode
        $freelancerOnly = ['novo_projeto','service_chosen','revision_requested','project_started',
            'payment_adjustment','delivery_approved','payment_released','saque_aprovado',
            'saque_rejeitado','service_rejected','project_invite','direct_invite'];
        $clientOnly = ['refund_processed','refund_approved','refund_rejected',
            'delivery_submitted','proposal_accepted','proposal_rejected'];

        $this->unreadCount = Notification::where('user_id', $user->id)
            ->where('read', false)
            ->when(!$isFreelancerMode, fn($q) => $q->whereNotIn('type', $freelancerOnly))
            ->when($isFreelancerMode,  fn($q) => $q->whereNotIn('type', $clientOnly))
            ->count();

        $this->recent = Notification::where('user_id', $user->id)
            ->with('user')
            ->when(!$isFreelancerMode, fn($q) => $q->whereNotIn('type', $freelancerOnly))
            ->when($isFreelancerMode,  fn($q) => $q->whereNotIn('type', $clientOnly))
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
                'url'        => $n->getUrl(),
                'created_at' => $n->created_at->diffForHumans(),
            ])
            ->toArray();
    }

    public function markAllRead(): void
    {
        $user = Auth::user();
        if (!$user) return;

        $isFreelancerMode = $user->activeRole() === 'freelancer';
        $freelancerOnly = ['novo_projeto','service_chosen','revision_requested','project_started',
            'payment_adjustment','delivery_approved','payment_released','saque_aprovado',
            'saque_rejeitado','service_rejected','project_invite','direct_invite'];
        $clientOnly = ['refund_processed','refund_approved','refund_rejected',
            'delivery_submitted','proposal_accepted','proposal_rejected'];

        Notification::where('user_id', $user->id)
            ->where('read', false)
            ->when(!$isFreelancerMode, fn($q) => $q->whereNotIn('type', $freelancerOnly))
            ->when($isFreelancerMode,  fn($q) => $q->whereNotIn('type', $clientOnly))
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
