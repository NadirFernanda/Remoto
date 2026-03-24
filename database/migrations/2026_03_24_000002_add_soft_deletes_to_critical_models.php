<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adiciona soft deletes (deleted_at) aos modelos críticos de negócio.
 *
 * Benefícios:
 * - Auditabilidade: registo histórico completo — nada é perdido permanentemente
 * - Recuperação: dados podem ser restaurados se eliminados por engano
 * - Integridade referencial: FK constraints não se quebram em deletes lógicos
 * - Compliance: conformidade com requisitos de retenção de dados financeiros
 *
 * Modelos afectados:
 *   - services    → nunca perder histórico de transacções
 *   - reviews     → nunca perder avaliações (base de confiança da plataforma)
 *   - disputes    → nunca perder histórico de mediações
 *   - wallet_logs → imutabilidade financeira total
 */
return new class extends Migration
{
    public function up(): void
    {
        // Services — núcleo financeiro da plataforma
        Schema::table('services', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });

        // Reviews — base de reputação e confiança
        Schema::table('reviews', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });

        // Disputes — histórico de mediações (obrigatório para auditoria)
        Schema::table('disputes', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });

        // Wallet logs — registo financeiro imutável (só soft delete, nunca hard)
        Schema::table('wallet_logs', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('disputes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('wallet_logs', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
