<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // These fields are validated by Api\ServiceController but were missing
            // from the schema, causing store() to silently drop them via $fillable.
            $table->text('descricao')->nullable()->after('titulo');
            $table->string('categoria', 100)->nullable()->after('descricao');
            $table->date('prazo')->nullable()->after('categoria');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['descricao', 'categoria', 'prazo']);
        });
    }
};
