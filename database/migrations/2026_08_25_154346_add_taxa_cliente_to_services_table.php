<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->decimal('taxa_cliente', 12, 2)->default(0)->after('taxa');
            $table->decimal('total_cliente', 12, 2)->nullable()->after('taxa_cliente');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['taxa_cliente', 'total_cliente']);
        });
    }
};
