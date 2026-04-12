<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adiciona índices em colunas frequentemente filtradas para melhorar performance.
 * Corrige full-table-scans em services, notifications, service_candidates e messages.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── services ─────────────────────────────────────────────────────────
        // Queries frequentes: WHERE status = ?, WHERE freelancer_id = ?,
        //                     WHERE cliente_id = ?, WHERE status IN (...)
        Schema::table('services', function (Blueprint $table) {
            $table->index('status',       'services_status_idx');
            $table->index('freelancer_id','services_freelancer_idx');
            $table->index('cliente_id',   'services_cliente_idx');
            // Cobre: WHERE freelancer_id = x AND status IN (...)
            $table->index(['freelancer_id', 'status'], 'services_freelancer_status_idx');
            // Cobre: WHERE cliente_id = x AND status = ?
            $table->index(['cliente_id', 'status'], 'services_cliente_status_idx');
        });

        // ── user_notifications ────────────────────────────────────────────────
        // NotificationBell faz COUNT + SELECT WHERE user_id = ? AND read = false
        // (tabela foi renomeada de 'notifications' para 'user_notifications')
        Schema::table('user_notifications', function (Blueprint $table) {
            // Cobre: WHERE user_id = ? (ORDER BY created_at)
            $table->index(['user_id', 'created_at'], 'notifications_user_created_idx');
            // Cobre: WHERE user_id = ? AND read = false (COUNT)
            $table->index(['user_id', 'read'], 'notifications_user_read_idx');
        });

        // ── messages ──────────────────────────────────────────────────────────
        // ServiceChat carrega mensagens WHERE service_id = ? ORDER BY created_at
        Schema::table('messages', function (Blueprint $table) {
            $table->index(['service_id', 'created_at'], 'messages_service_created_idx');
        });

        // ── service_candidates ────────────────────────────────────────────────
        // AvailableProjects conta propostas por service_id e filtra por freelancer_id
        Schema::table('service_candidates', function (Blueprint $table) {
            $table->index(['service_id', 'status'],    'candidates_service_status_idx');
            $table->index(['freelancer_id', 'status'], 'candidates_freelancer_status_idx');
        });

        // ── referrals ─────────────────────────────────────────────────────────
        // Freelancer Dashboard faz WHERE affiliate_id = ?
        Schema::table('referrals', function (Blueprint $table) {
            $table->index('affiliate_id', 'referrals_affiliate_idx');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex('services_status_idx');
            $table->dropIndex('services_freelancer_idx');
            $table->dropIndex('services_cliente_idx');
            $table->dropIndex('services_freelancer_status_idx');
            $table->dropIndex('services_cliente_status_idx');
        });

        Schema::table('user_notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_user_created_idx');
            $table->dropIndex('notifications_user_read_idx');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_service_created_idx');
        });

        Schema::table('service_candidates', function (Blueprint $table) {
            $table->dropIndex('candidates_service_status_idx');
            $table->dropIndex('candidates_freelancer_status_idx');
        });

        Schema::table('referrals', function (Blueprint $table) {
            $table->dropIndex('referrals_affiliate_idx');
        });
    }
};
