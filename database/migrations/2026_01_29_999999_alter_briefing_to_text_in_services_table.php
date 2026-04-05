<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Altera a coluna briefing de json para text
            $table->text('briefing_tmp')->nullable();
        });
        // Copia os dados existentes para a nova coluna (compatível com PostgreSQL e SQLite)
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('UPDATE services SET briefing_tmp = briefing::text');
        } else {
            DB::statement('UPDATE services SET briefing_tmp = CAST(briefing AS TEXT)');
        }
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('briefing');
        });
        Schema::table('services', function (Blueprint $table) {
            $table->text('briefing')->nullable();
        });
        // Copia de volta para a coluna correta
        DB::statement('UPDATE services SET briefing = briefing_tmp');
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('briefing_tmp');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->json('briefing')->nullable()->change();
        });
    }
};
