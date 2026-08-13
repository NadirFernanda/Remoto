<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_flow_closings', function (Blueprint $table) {
            $table->id();
            $table->date('data')->unique();
            $table->json('grupos'); // breakdown por origem (Freelancing, Criador, Infoprodutos, Afiliados)
            $table->decimal('total_entradas', 14, 2)->default(0);
            $table->decimal('total_saidas', 14, 2)->default(0);
            $table->decimal('total_comissao', 14, 2)->default(0);
            $table->decimal('saldo_liquido', 14, 2)->default(0);
            $table->decimal('saldo_acumulado', 14, 2)->default(0);
            $table->string('fechado_por', 100)->nullable(); // 'automatico' ou nome do admin
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_flow_closings');
    }
};
