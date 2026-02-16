<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop existing check constraint and recreate including 'proposal_sent'
        DB::statement("ALTER TABLE service_candidates DROP CONSTRAINT IF EXISTS service_candidates_status_check;");
        DB::statement("ALTER TABLE service_candidates ADD CONSTRAINT service_candidates_status_check CHECK (status IN ('pending','chosen','rejected','proposal_sent'));");
    }

    public function down(): void
    {
        // Revert to original set
        DB::statement("ALTER TABLE service_candidates DROP CONSTRAINT IF EXISTS service_candidates_status_check;");
        DB::statement("ALTER TABLE service_candidates ADD CONSTRAINT service_candidates_status_check CHECK (status IN ('pending','chosen','rejected'));");
    }
};
