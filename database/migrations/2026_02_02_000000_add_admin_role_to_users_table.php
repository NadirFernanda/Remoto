<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            // Em PostgreSQL, o enum de role foi criado como CHECK constraint (users_role_check)
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('cliente', 'freelancer', 'admin'))");
        } elseif (in_array($driver, ['mysql', 'mariadb'])) {
            // Ajuste específico para MySQL/MariaDB: adiciona 'admin' ao ENUM existente
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('cliente', 'freelancer', 'admin') NOT NULL DEFAULT 'cliente'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('cliente', 'freelancer'))");
        } elseif (in_array($driver, ['mysql', 'mariadb'])) {
            // Reverte para o ENUM anterior sem 'admin'
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('cliente', 'freelancer') NOT NULL DEFAULT 'cliente'");
        }
    }
};
