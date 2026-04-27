<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('wallet_logs', function (Blueprint $table) {
            // Identifica a origem do lançamento: loja | assinaturas | projetos | geral
            $table->string('fonte', 32)->nullable()->after('tipo');
        });
    }

    public function down(): void
    {
        Schema::table('wallet_logs', function (Blueprint $table) {
            $table->dropColumn('fonte');
        });
    }
};
