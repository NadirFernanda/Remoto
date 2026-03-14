<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Adds the admin_role sub-role COLUMN — distinct from the migration at
    // 2026_02_02_000000 which only adds 'admin' to the role CHECK constraint.
    public function up(): void
    {
        if (Schema::hasColumn('users', 'admin_role')) {
            return; // already added (idempotency guard for migrate:fresh)
        }
        Schema::table('users', function (Blueprint $table) {
            // master = Administrador total | gestor = Gestor/Moderador | financeiro = Financeira
            $table->string('admin_role', 20)->nullable()->after('role')
                ->comment('master | gestor | financeiro — apenas para utilizadores com role=admin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('admin_role');
        });
    }
};
