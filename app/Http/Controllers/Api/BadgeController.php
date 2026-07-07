<?php

namespace App\Http\Controllers\Api;

use App\Models\ChatRead;
use App\Models\Notification;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BadgeController extends Controller
{
    /** GET /api/v1/badges — unread chat + notification counts, for the browser extension icon badge. */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $serviceIds = Service::where('cliente_id', $user->id)
            ->orWhere('freelancer_id', $user->id)
            ->pluck('id');

        $chatUnread = 0;
        foreach ($serviceIds as $serviceId) {
            $chatUnread += ChatRead::unreadCount($serviceId, $user->id);
        }

        $isFreelancer = $user->activeRole() === 'freelancer';
        $freelancerOnly = ['novo_projeto', 'service_chosen', 'revision_requested', 'project_started',
            'payment_adjustment', 'delivery_approved', 'payment_released', 'saque_aprovado',
            'saque_rejeitado', 'service_rejected', 'project_invite', 'direct_invite'];
        $clientOnly = ['refund_processed', 'refund_approved', 'refund_rejected',
            'delivery_submitted', 'proposal_accepted', 'proposal_rejected'];

        $notificationsUnread = Notification::where('user_id', $user->id)
            ->where('read', false)
            ->when(!$isFreelancer, fn ($q) => $q->whereNotIn('type', $freelancerOnly))
            ->when($isFreelancer, fn ($q) => $q->whereNotIn('type', $clientOnly))
            ->count();

        return response()->json([
            'chat_unread'          => $chatUnread,
            'notifications_unread' => $notificationsUnread,
            'total'                => $chatUnread + $notificationsUnread,
        ]);
    }
}
