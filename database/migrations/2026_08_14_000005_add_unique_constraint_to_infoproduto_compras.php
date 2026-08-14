<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * infoproduto_compras nunca teve uma constraint única em (infoproduto_id,
     * comprador_id) — ao contrário de creator_subscriptions — o que permite um
     * double-click/duas-abas debitar a carteira do comprador duas vezes pelo
     * mesmo produto.
     */
    public function up(): void
    {
        Schema::table('infoproduto_compras', function (Blueprint $table) {
            $table->unique(['infoproduto_id', 'comprador_id'], 'infoproduto_compras_unique_purchase');
        });
    }

    public function down(): void
    {
        Schema::table('infoproduto_compras', function (Blueprint $table) {
            $table->dropUnique('infoproduto_compras_unique_purchase');
        });
    }
};
