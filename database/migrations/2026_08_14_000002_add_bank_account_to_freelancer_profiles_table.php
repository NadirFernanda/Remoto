<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('freelancer_profiles', function (Blueprint $table) {
            $table->string('bank_name', 100)->nullable()->after('kyc_status');
            // Nome do titular tem de bater com o nome da conta (users.name) —
            // validado na aplicação, não aqui, para permitir avisos claros.
            $table->string('bank_account_holder', 120)->nullable()->after('bank_name');
            $table->string('bank_account_number', 60)->nullable()->after('bank_account_holder');
        });
    }

    public function down(): void
    {
        Schema::table('freelancer_profiles', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'bank_account_holder', 'bank_account_number']);
        });
    }
};
