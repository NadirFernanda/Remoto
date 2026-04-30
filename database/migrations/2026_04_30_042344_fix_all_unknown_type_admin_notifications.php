<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $knownTypes = [
            'novo_projeto','service_chosen','revision_requested','project_started',
            'payment_adjustment','delivery_approved','payment_released','saque_aprovado',
            'saque_rejeitado','service_rejected','project_invite','direct_invite',
            'proposal_received','nova_mensagem','project_cancelled','proposal_accepted',
            'proposal_rejected','delivery_submitted','refund_processed','refund_approved',
            'refund_rejected','moderation_requested','dispute_admin_reply','dispute_opened',
            'dispute_opened_admin','dispute_resolved','review_reminder','support_ticket_new',
            'support_ticket_reply','admin_message',
            'review_received','milestone_completed','affiliate_commission',
            'kyc_status','service_delivered','service_accepted','service_cancelled',
            'payment_received','post_liked','post_commented','new_project','proposal_created',
        ];

        // Qualquer type que não seja conhecido é uma mensagem de admin
        DB::table('user_notifications')
            ->whereNotIn('type', $knownTypes)
            ->update([
                'type'        => 'admin_message',
                'sender_name' => DB::raw("COALESCE(NULLIF(sender_name, ''), 'Administração')"),
            ]);
    }

    public function down(): void {}

};
