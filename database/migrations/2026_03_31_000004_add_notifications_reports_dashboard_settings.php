<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            // ── Alertas para o Admin ─────────────────────────────────────────
            // JSON array booleans: alert_pending_withdrawals_24h, alert_suspicious_withdrawal,
            //   alert_config_risk, alert_change_history, alert_help_tooltips
            ['key' => 'admin_alerts',               'value' => '["pending_withdrawals_24h","config_risk","change_history","help_tooltips"]'],

            // Canal de alerta: JSON array — email | sms | system
            ['key' => 'admin_alert_channels',       'value' => '["email","system"]'],

            // ── Notificações para Usuários ───────────────────────────────────
            // JSON array — withdrawal_processed | value_retained | dispute | weekly_earnings | fee_change_notice
            ['key' => 'user_notifications',         'value' => '["withdrawal_processed","value_retained","dispute","fee_change_notice"]'],

            // ── Relatórios & Exportações ─────────────────────────────────────
            // Frequência: withdrawal_report_daily (0|1), commission_report_monthly (0|1), tax_report (0|1)
            ['key' => 'report_withdrawal_daily',    'value' => '1'],
            ['key' => 'report_commission_monthly',  'value' => '1'],
            ['key' => 'report_tax',                 'value' => '0'],
            // Email destino dos relatórios
            ['key' => 'report_email',               'value' => 'contabilidade@24horas.ao'],
            // Formatos: JSON array — csv | excel | pdf
            ['key' => 'report_formats',             'value' => '["csv","excel","pdf"]'],

            // ── Dashboard Personalizado ──────────────────────────────────────
            // JSON array — top_freelancers | top_creators | top_products | withdrawal_heatmap
            ['key' => 'dashboard_widgets',          'value' => '["top_freelancers","top_creators","top_products","withdrawal_heatmap"]'],
        ];

        foreach ($defaults as $setting) {
            DB::table('platform_settings')->insertOrIgnore(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        DB::table('platform_settings')
            ->whereIn('key', [
                'admin_alerts',
                'admin_alert_channels',
                'user_notifications',
                'report_withdrawal_daily',
                'report_commission_monthly',
                'report_tax',
                'report_email',
                'report_formats',
                'dashboard_widgets',
            ])
            ->delete();
    }
};
