<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Nome exactamente como consta no documento de identidade, confirmado
            // pelo admin ao aprovar o KYC — separado de 'name' (que o utilizador
            // pode alterar livremente a qualquer momento) para que a validação da
            // conta bancária continue correcta mesmo que o nome da conta mude depois.
            $table->string('kyc_verified_name')->nullable()->after('kyc_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('kyc_verified_name');
        });
    }
};
