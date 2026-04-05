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
        $role = Auth::user()->role; // DB column — registered role

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
            'delivery_submitted'   => $sid ? route('service.chat', $sid) : route('client.projects'),
            'refund_processed'     => route('client.refunds'),
            'refund_approved'      => route('client.refunds'),
            'refund_rejected'      => route('client.refunds'),

            // ── Dispute / shared ─────────────────────────────────────────────
            'moderation_requested' => $sid ? route('service.dispute', $sid) : route('dashboard'),
            'dispute_admin_reply'  => $sid ? route('service.dispute', $sid) : route('dashboard'),
            'dispute_opened'       => $sid ? route('service.dispute', $sid) : route('dashboard'),
            'dispute_opened_admin' => route('admin.disputes'),
            'dispute_resolved'     => $sid ? route('service.dispute', $sid) : route('dashboard'),
            'review_reminder'      => $sid ? route('service.review.leave', $sid) : route('dashboard'),

            default                => $role === 'freelancer'
                ? route('freelancer.dashboard')
                : route('client.dashboard'),
        };

        return redirect($url);
    }
}
