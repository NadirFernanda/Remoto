<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE services DROP CONSTRAINT services_status_check;");
        DB::statement("ALTER TABLE services ALTER COLUMN status TYPE VARCHAR(20);");
        DB::statement("ALTER TABLE services ADD CONSTRAINT services_status_check CHECK (status IN ('published', 'accepted', 'in_progress', 'delivered', 'completed', 'cancelled'));");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE services DROP CONSTRAINT services_status_check;");
        DB::statement("ALTER TABLE services ALTER COLUMN status TYPE VARCHAR(20);");
        DB::statement("ALTER TABLE services ADD CONSTRAINT services_status_check CHECK (status IN ('published', 'accepted', 'in_progress', 'delivered', 'completed'));");
    }
};
