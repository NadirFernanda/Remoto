<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE service_candidates DROP CONSTRAINT IF EXISTS service_candidates_status_check;");
            DB::statement("ALTER TABLE service_candidates ADD CONSTRAINT service_candidates_status_check CHECK (status IN ('pending','chosen','rejected','proposal_sent'));");
        } elseif (DB::getDriverName() === 'sqlite') {
            // SQLite implementa enum como CHECK constraint — precisamos recriar a coluna
            Schema::table('service_candidates', function (Blueprint $table) {
                $table->enum('status', ['pending', 'chosen', 'rejected', 'proposal_sent'])->default('pending')->change();
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE service_candidates DROP CONSTRAINT IF EXISTS service_candidates_status_check;");
            DB::statement("ALTER TABLE service_candidates ADD CONSTRAINT service_candidates_status_check CHECK (status IN ('pending','chosen','rejected'));");
        } elseif (DB::getDriverName() === 'sqlite') {
            Schema::table('service_candidates', function (Blueprint $table) {
                $table->enum('status', ['pending', 'chosen', 'rejected'])->default('pending')->change();
            });
        }
    }
};
