<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Elimina a taxa de saque e o valor mínimo de saque de todas as carteiras.
     * As comissões da plataforma são agora cobradas no momento de cada transação:
     *   - Loja (infoprodutos): 20%
     *   - Assinaturas (criadores): 15%
     *   - Serviços (freelancers): 10%
     */
    public function up(): void
    {
        DB::table('wallets')->update([
            'taxa_saque'   => 0,
            'saque_minimo' => 0,
        ]);

        Schema::table('wallets', function (Blueprint $table) {
            $table->decimal('taxa_saque', 5, 2)->default(0)->change();
            $table->decimal('saque_minimo', 12, 2)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->decimal('taxa_saque', 5, 2)->default(20.00)->change();
            $table->decimal('saque_minimo', 12, 2)->default(20000)->change();
        });
    }
};
