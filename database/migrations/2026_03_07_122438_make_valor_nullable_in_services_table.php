<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->decimal('valor', 12, 2)->nullable()->default(null)->change();
            $table->decimal('taxa', 5, 2)->default(10.00)->change();
            $table->decimal('valor_liquido', 12, 2)->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->decimal('valor', 12, 2)->nullable(false)->change();
            $table->decimal('valor_liquido', 12, 2)->nullable(false)->change();
        });
    }
};
