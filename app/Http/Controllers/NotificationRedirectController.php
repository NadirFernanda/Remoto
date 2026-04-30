<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationRedirectController extends Controller
{
    /**
     * Mark the notification as read and redirect to its specific destination.
     *
     * URL resolution is done HERE — not via Notification::getUrl() — so this
     * always executes from fresh bytecode, fully immune to PHP OPcache staleness.
     */
    public function __invoke(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$notification->read) {
            $notification->update(['read' => true]);
        }

        $sid  = $notification->service_id;
        // Use activeRole (session-based) so a client switching to freelancer mode
        // is still sent to the correct client pages when clicking client-only notifications.
        $activeRole = Auth::user()->activeRole();
        $role       = Auth::user()->role; // DB column — used for types that depend on who RECEIVED the notif

        $url = match ($notification->type) {
            // ── Freelancer-bound ─────────────────────────────────────────────
            'novo_projeto'         => $sid ? route('public.project.show', $sid)
                                           : route('freelancer.available-projects'),
            'service_chosen'       => $sid ? route('freelancer.service.delivery', $sid)
                                           : route('freelancer.projects'),
            'revision_requested'   => $sid ? route('freelancer.service.delivery', $sid)
                                           : route('freelancer.projects'),
            'project_started'      => $sid ? route('service.chat', $sid) : route('freelancer.projects'),
            'payment_adjustment'   => $sid ? route('service.chat', $sid) : route('freelancer.projects'),
            'delivery_approved'    => $sid ? route('service.chat', $sid) : route('freelancer.projects'),
            'payment_released'     => route('freelancer.wallet'),
            'saque_aprovado'       => route('freelancer.wallet'),
            'saque_rejeitado'      => route('freelancer.wallet'),
            'service_rejected'     => route('freelancer.proposals'),
            'project_invite'       => route('freelancer.proposals'),
            'direct_invite'        => route('freelancer.proposals'),

            // proposal_received: sent to FREELANCER (client invited) OR to CLIENT (freelancer proposed)
            'proposal_received'    => $role === 'freelancer'
                ? ($sid ? route('service.chat', $sid) : route('freelancer.proposals'))
                : route('client.projects'),

            // nova_mensagem: both roles use service chat
            'nova_mensagem'        => $sid ? route('service.chat', $sid)
                : ($role === 'freelancer' ? route('freelancer.projects') : route('client.projects')),

            // project_cancelled: both roles
            'project_cancelled'    => $role === 'freelancer' ? route('freelancer.projects') : route('client.projects'),

            // ── Client-bound ─────────────────────────────────────────────────
            'proposal_accepted'    => route('client.projects'),
            'proposal_rejected'    => route('client.projects'),
            'delivery_submitted'   => route('client.projects'),
            // Refund pages live under cliente/ — force client mode switch via session
            'refund_processed'     => $this->clientRefundRedirect(),
            'refund_approved'      => $this->clientRefundRedirect(),
            'refund_rejected'      => $this->clientRefundRedirect(),

            // ── Dispute / shared ─────────────────────────────────────────────
            'moderation_requested' => $sid ? route('service.dispute', $sid) : route('dashboard'),
            'dispute_admin_reply'  => $sid ? route('service.dispute', $sid) : route('dashboard'),
            'dispute_opened'       => $sid ? route('service.dispute', $sid) : route('dashboard'),
            'dispute_opened_admin' => route('admin.disputes'),
            'dispute_resolved'     => $sid ? route('service.dispute', $sid) : route('dashboard'),
            'review_reminder'      => $sid ? route('service.review.leave', $sid) : route('dashboard'),

            // ── Support Tickets ───────────────────────────────────────────────
            'support_ticket_new'   => route('admin.support'),
            'support_ticket_reply' => $role === 'freelancer' ? route('freelancer.support') : route('client.support'),

            // ── Admin messages ────────────────────────────────────────────────
            'admin_message'        => route('notification.show', $notification->id),

            default                => $role === 'freelancer'
                ? route('freelancer.notifications')
                : route('notifications'),
        };

        return redirect($url);
    }

    /**
     * Redirect to the client refunds page, switching active role to 'cliente'
     * if the user is currently in freelancer mode — prevents the role:cliente
     * middleware from bouncing them to the dashboard.
     */
    private function clientRefundRedirect(): string
    {
        if (Auth::user()->activeRole() !== 'cliente') {
            session(['active_role' => 'cliente']);
        }
        return route('client.refunds');
    }
}
