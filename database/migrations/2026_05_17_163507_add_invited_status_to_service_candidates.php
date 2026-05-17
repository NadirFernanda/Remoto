<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE service_candidates MODIFY COLUMN status ENUM('pending','chosen','rejected','proposal_sent','invited') NOT NULL DEFAULT 'pending'");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE service_candidates DROP CONSTRAINT IF EXISTS service_candidates_status_check;");
            DB::statement("ALTER TABLE service_candidates ADD CONSTRAINT service_candidates_status_check CHECK (status IN ('pending','chosen','rejected','proposal_sent','invited'));");
        } elseif ($driver === 'sqlite') {
            Schema::table('service_candidates', function (Blueprint $table) {
                $table->enum('status', ['pending', 'chosen', 'rejected', 'proposal_sent', 'invited'])->default('pending')->change();
            });
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE service_candidates MODIFY COLUMN status ENUM('pending','chosen','rejected','proposal_sent') NOT NULL DEFAULT 'pending'");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE service_candidates DROP CONSTRAINT IF EXISTS service_candidates_status_check;");
            DB::statement("ALTER TABLE service_candidates ADD CONSTRAINT service_candidates_status_check CHECK (status IN ('pending','chosen','rejected','proposal_sent'));");
        } elseif ($driver === 'sqlite') {
            Schema::table('service_candidates', function (Blueprint $table) {
                $table->enum('status', ['pending', 'chosen', 'rejected', 'proposal_sent'])->default('pending')->change();
            });
        }
    }
};
