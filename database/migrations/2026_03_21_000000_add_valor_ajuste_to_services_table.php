<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->decimal('valor_ajuste', 12, 2)->nullable()->after('valor_liquido');
            $table->decimal('valor_ajuste_taxa', 12, 2)->nullable()->after('valor_ajuste');
            $table->boolean('valor_ajuste_pago')->default(false)->after('valor_ajuste_taxa');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['valor_ajuste', 'valor_ajuste_taxa', 'valor_ajuste_pago']);
        });
    }
};
