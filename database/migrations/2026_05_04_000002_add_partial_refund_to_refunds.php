<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            // Valor final que o admin vai devolver (pode ser < valor total = reembolso parcial)
            $table->decimal('valor_reembolso', 12, 2)->nullable()->after('details');
            // Proposta do cliente (percentagem 0-100 ou valor absoluto)
            $table->decimal('proposta_cliente', 12, 2)->nullable()->after('valor_reembolso');
            // Contra-proposta do freelancer
            $table->decimal('proposta_freelancer', 12, 2)->nullable()->after('proposta_cliente');
            $table->text('resposta_freelancer')->nullable()->after('proposta_freelancer');
        });
    }

    public function down(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            $table->dropColumn(['valor_reembolso', 'proposta_cliente', 'proposta_freelancer', 'resposta_freelancer']);
        });
    }
};
