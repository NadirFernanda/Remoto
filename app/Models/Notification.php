<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Notification extends Model
{
    protected $table = 'user_notifications';

    protected $fillable = [
        'user_id', 'service_id', 'type', 'target_role', 'title', 'message', 'read'
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
     * Resolve the destination URL for this notification.
     *
     * We check the RECIPIENT's role (not the sender's) because some types
     * like proposal_received are sent to both freelancers (client sends
     * direct invite) and clients (freelancer sends proposal), but the
     * correct destination page differs per role.
     */
    public function getUrl(): string
    {
        $sid  = $this->service_id;

        // Determine the recipient's role from the DB user record.
        // We use the stored user_id rather than Auth::user() so this works
        // even when called outside of a web request (e.g. tests, artisan).
        $recipientRole = $this->user?->role ?? (Auth::user()?->role ?? 'cliente');
        $isFreelancer  = $recipientRole === 'freelancer';

        try {
            return match($this->type) {
                // ── Freelancer receives ──────────────────────────────
                'service_chosen'       => $sid ? route('freelancer.service.delivery', $sid) : route('freelancer.projects'),
                'revision_requested'   => $sid ? route('freelancer.service.delivery', $sid) : route('freelancer.projects'),
                'project_started'      => $sid ? route('service.chat', $sid) : route('freelancer.projects'),
                'payment_adjustment'   => $sid ? route('service.chat', $sid) : route('freelancer.projects'),
                'delivery_approved'    => $sid ? route('service.chat', $sid) : route('freelancer.projects'),
                'payment_released'     => route('freelancer.wallet'),
                'saque_aprovado'       => route('freelancer.wallet'),
                'saque_rejeitado'      => route('freelancer.wallet'),
                'service_rejected'     => route('freelancer.proposals'),
                'project_cancelled'    => $isFreelancer ? route('freelancer.projects') : route('client.projects'),
                'novo_projeto'         => $sid ? route('freelancer.service.review', $sid) : route('freelancer.available-projects'),
                'project_invite'       => route('freelancer.proposals'),
                'direct_invite'        => route('freelancer.proposals'),
                'nova_mensagem'        => $sid ? route('service.chat', $sid) : ($isFreelancer ? route('freelancer.projects') : route('client.projects')),

                // ── proposal_received: sent to FREELANCER (client direct invite)
                //    OR to CLIENT (freelancer sends proposal). Route differs per role.
                'proposal_received'    => $isFreelancer
                    ? ($sid ? route('service.chat', $sid) : route('freelancer.proposals'))
                    : route('client.projects'),

                // ── Client receives ──────────────────────────────────
                'proposal_accepted'    => route('client.projects'),
                'proposal_rejected'    => route('client.projects'),
                'delivery_submitted'   => $sid ? route('service.chat', $sid) : route('client.projects'),
                'refund_processed'     => $this->target_role === 'freelancer' ? route('freelancer.wallet') : route('client.refunds'),
                'refund_approved'      => $this->target_role === 'freelancer' ? route('freelancer.wallet') : route('client.refunds'),
                'refund_rejected'      => $this->target_role === 'freelancer' ? route('freelancer.wallet') : route('client.refunds'),
                'moderation_requested' => $sid ? route('service.dispute', $sid) : route('admin.disputes'),

                // ── Both sides (dispute / review) ────────────────────
                'dispute_admin_reply'   => $sid ? route('service.dispute', $sid) : '#',
                'dispute_opened'        => $sid ? route('service.dispute', $sid) : '#',
                'dispute_opened_admin'  => route('admin.disputes'),
                'dispute_resolved'      => $sid ? route('service.dispute', $sid) : '#',
                'review_reminder'       => $sid ? route('service.review.leave', $sid) : '#',

                default => '#',
            };
        } catch (\Throwable) {
            return '#';
        }
    }
}

