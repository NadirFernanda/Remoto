<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Adiciona índices GIN (Generalized Inverted Index) nas colunas JSON
 * da tabela freelancer_profiles para acelerar pesquisas com
 * whereJsonContains('skills', ...) e whereJsonContains('languages', ...).
 *
 * Sem índices GIN, estas queries fazem full table scan — O(n).
 * Com índices GIN, as pesquisas passam a O(log n).
 *
 * NOTA: GIN é específico do PostgreSQL e não tem equivalente directo em MySQL.
 * Em MySQL/MariaDB, considerar colunas FULLTEXT ou tabelas pivot normalizadas.
 */
return new class extends Migration
{
    public function up(): void
    {
        // As colunas skills, languages e metrics podem ser do tipo json (não jsonb).
        // jsonb_path_ops requer jsonb — convertemos primeiro via USING cast.
        // O cast json::jsonb é seguro e sem perda de dados.
        DB::statement("ALTER TABLE freelancer_profiles
            ALTER COLUMN skills    TYPE jsonb USING skills::jsonb,
            ALTER COLUMN languages TYPE jsonb USING languages::jsonb");

        // metrics pode não existir em todos os ambientes — converter só se existir
        $hasMetrics = DB::selectOne("
            SELECT 1 FROM information_schema.columns
            WHERE table_name = 'freelancer_profiles' AND column_name = 'metrics'
        ");
        if ($hasMetrics) {
            DB::statement("ALTER TABLE freelancer_profiles
                ALTER COLUMN metrics TYPE jsonb USING metrics::jsonb");
        }

        DB::statement(
            'CREATE INDEX IF NOT EXISTS idx_fp_skills_gin
             ON freelancer_profiles USING GIN (skills jsonb_path_ops)'
        );

        DB::statement(
            'CREATE INDEX IF NOT EXISTS idx_fp_languages_gin
             ON freelancer_profiles USING GIN (languages jsonb_path_ops)'
        );

        if ($hasMetrics) {
            DB::statement(
                'CREATE INDEX IF NOT EXISTS idx_fp_metrics_gin
                 ON freelancer_profiles USING GIN (metrics jsonb_path_ops)'
            );
        }

        // Índice B-tree composto para filtros de disponibilidade + taxa horária
        DB::statement(
            'CREATE INDEX IF NOT EXISTS idx_fp_availability_hourly
             ON freelancer_profiles (availability_status, hourly_rate)'
        );

        // Índice no kyc_status para o middleware KycVerified e admin filters
        DB::statement(
            'CREATE INDEX IF NOT EXISTS idx_fp_kyc_status
             ON freelancer_profiles (kyc_status)'
        );
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS idx_fp_skills_gin');
        DB::statement('DROP INDEX IF EXISTS idx_fp_languages_gin');
        DB::statement('DROP INDEX IF EXISTS idx_fp_metrics_gin');
        DB::statement('DROP INDEX IF EXISTS idx_fp_availability_hourly');
        DB::statement('DROP INDEX IF EXISTS idx_fp_kyc_status');
    }
};
