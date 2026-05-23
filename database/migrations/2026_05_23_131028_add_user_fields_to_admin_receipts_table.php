<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_receipts', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->after('id');
            $table->decimal('valor', 15, 2)->nullable()->after('user_id');
            $table->string('email')->nullable()->after('telefone');
        });
    }

    public function down(): void
    {
        Schema::table('admin_receipts', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'valor', 'email']);
        });
    }
};
