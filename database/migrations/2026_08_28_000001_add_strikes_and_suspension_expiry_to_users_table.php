<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Contagem de advertências por incumprimento (disputas). Ao atingir 3,
            // a conta é suspensa automaticamente e a contagem reinicia.
            $table->unsignedTinyInteger('strikes_count')->default(0)->after('is_suspended');
            // Data até quando a suspensão automática (por advertências) vigora.
            // Null = suspensão manual do admin, sem prazo automático.
            $table->timestamp('suspended_until')->nullable()->after('strikes_count');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['strikes_count', 'suspended_until']);
        });
    }
};
