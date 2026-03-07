<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
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
