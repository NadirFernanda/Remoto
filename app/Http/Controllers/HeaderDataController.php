<?php

namespace App\Http\Controllers;

use App\Models\ChatRead;
use App\Models\Notification;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class HeaderDataController extends Controller
{
    /** GET /user/chat-badge — unread chat count for the header badge */
    public function chatBadge(): JsonResponse
    {
        $user = Auth::user();
        $unread = 0;

        $serviceIds = Service::where('cliente_id', $user->id)
            ->orWhere('freelancer_id', $user->id)
            ->pluck('id');

        foreach ($serviceIds as $serviceId) {
            $unread += ChatRead::unreadCount($serviceId, $user->id);
        }

        return response()->json(['unread' => $unread]);
    }

    /** GET /user/notification-data — unread count + recent notifications */
    public function notificationData(): JsonResponse
    {
        $user = Auth::user();
        $isFreelancer = $user->activeRole() === 'freelancer';

        $freelancerOnly = ['novo_projeto','service_chosen','revision_requested','project_started',
            'payment_adjustment','delivery_approved','payment_released','saque_aprovado',
            'saque_rejeitado','service_rejected','project_invite','direct_invite'];
        $clientOnly = ['refund_processed','refund_approved','refund_rejected',
            'delivery_submitted','proposal_accepted','proposal_rejected'];

        $query = fn ($q) => $q->where('user_id', $user->id)
            ->when(!$isFreelancer, fn ($q) => $q->whereNotIn('type', $freelancerOnly))
            ->when($isFreelancer,  fn ($q) => $q->whereNotIn('type', $clientOnly));

        $unreadCount = $query(Notification::query())->where('read', false)->count();

        $items = $query(Notification::query())
            ->orderByDesc('created_at')
            ->limit(8)
            ->get()
            ->map(fn ($n) => [
                'id'          => $n->id,
                'title'       => $n->title ?? '',
                'message'     => $n->message,
                'type'        => $n->type,
                'read'        => (bool) $n->read,
                'sender_name' => $n->sender_name,
                'url'         => route('notification.open', $n->id),
                'created_at'  => $n->created_at->diffForHumans(),
            ])
            ->values()
            ->toArray();

        return response()->json(['unread_count' => $unreadCount, 'items' => $items]);
    }

    /** POST /user/notifications/mark-all-read */
    public function markAllRead(): JsonResponse
    {
        $user = Auth::user();
        $isFreelancer = $user->activeRole() === 'freelancer';

        $freelancerOnly = ['novo_projeto','service_chosen','revision_requested','project_started',
            'payment_adjustment','delivery_approved','payment_released','saque_aprovado',
            'saque_rejeitado','service_rejected','project_invite','direct_invite'];
        $clientOnly = ['refund_processed','refund_approved','refund_rejected',
            'delivery_submitted','proposal_accepted','proposal_rejected'];

        Notification::where('user_id', $user->id)
            ->where('read', false)
            ->when(!$isFreelancer, fn ($q) => $q->whereNotIn('type', $freelancerOnly))
            ->when($isFreelancer,  fn ($q) => $q->whereNotIn('type', $clientOnly))
            ->update(['read' => true]);

        return response()->json(['ok' => true]);
    }
}
