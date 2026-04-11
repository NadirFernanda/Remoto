<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Após o rename (migração 2026_01_28_000002) a tabela customizada
     * passa-se a chamar 'user_notifications'. Adicionamos service_id à
     * tabela correcta, com guard para evitar duplicate column.
     */
    public function up(): void
    {
        $tbl = Schema::hasTable('user_notifications') ? 'user_notifications' : 'notifications';

        if (!Schema::hasColumn($tbl, 'service_id')) {
            Schema::table($tbl, function (Blueprint $table) {
                $table->unsignedBigInteger('service_id')->nullable()->after('user_id');
                $table->foreign('service_id')->references('id')->on('services')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tbl = Schema::hasTable('user_notifications') ? 'user_notifications' : 'notifications';

        Schema::table($tbl, function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
        });
    }
};
