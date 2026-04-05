<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id', 'service_id', 'type', 'title', 'message', 'read'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Resolve the destination URL for this notification,
     * so clicking it takes the user to the right place.
     */
    public function getUrl(): string
    {
        $sid = $this->service_id;

        try {
            return match($this->type) {
                // ── Freelancer receives ──────────────────────────────
                'service_chosen'       => $sid ? route('freelancer.service.delivery', $sid) : route('freelancer.projects'),
                'revision_requested'   => $sid ? route('freelancer.service.delivery', $sid) : route('freelancer.projects'),
                'project_started'      => $sid ? route('service.chat', $sid) : route('freelancer.projects'),
                'payment_adjustment'   => $sid ? route('service.chat', $sid) : route('freelancer.projects'),
                'delivery_approved'    => route('freelancer.wallet'),
                'payment_released'     => route('freelancer.wallet'),
                'saque_aprovado'       => route('freelancer.wallet'),
                'saque_rejeitado'      => route('freelancer.wallet'),
                'service_rejected'     => route('freelancer.proposals'),
                'project_cancelled'    => route('freelancer.projects'),
                'novo_projeto'         => $sid ? route('freelancer.service.review', $sid) : route('freelancer.available-projects'),
                'project_invite'       => route('freelancer.proposals'),
                'direct_invite'        => route('freelancer.proposals'),
                'nova_mensagem'        => $sid ? route('service.chat', $sid) : route('freelancer.projects'),

                // ── Client receives ──────────────────────────────────
                'proposal_received'    => route('client.projects'),
                'proposal_accepted'    => route('client.projects'),
                'proposal_rejected'    => route('client.projects'),
                'delivery_submitted'   => route('client.projects'),
                'refund_processed'     => route('client.refunds'),
                'refund_approved'      => route('client.refunds'),
                'refund_rejected'      => route('client.refunds'),
                'moderation_requested' => $sid ? route('service.dispute', $sid) : route('admin.disputes'),

                // ── Both sides (dispute) ─────────────────────────────
                'dispute_admin_reply'   => $sid ? route('service.dispute', $sid) : '#',
                'dispute_opened'        => $sid ? route('service.dispute', $sid) : '#',
                'dispute_opened_admin'  => route('admin.disputes'),
                'dispute_resolved'      => $sid ? route('service.dispute', $sid) : '#',

                default => '#',
            };
        } catch (\Throwable) {
            return '#';
        }
    }
}
