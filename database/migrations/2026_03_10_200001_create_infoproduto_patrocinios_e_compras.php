<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infoproduto_patrocinios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('infoproduto_id')->constrained('infoprodutos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('data_inicio');
            $table->date('data_fim');
            $table->unsignedSmallInteger('dias');
            $table->decimal('valor_total', 12, 2); // 600 Kz × dias
            $table->enum('status', ['ativo', 'expirado', 'cancelado'])->default('ativo');
            $table->timestamps();
        });

        Schema::create('infoproduto_compras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('infoproduto_id')->constrained('infoprodutos')->onDelete('cascade');
            $table->foreignId('comprador_id')->constrained('users')->onDelete('cascade');
            $table->decimal('valor_pago', 12, 2);
            $table->decimal('comissao_plataforma', 12, 2); // 30%
            $table->decimal('valor_freelancer', 12, 2);    // 70%
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infoproduto_compras');
        Schema::dropIfExists('infoproduto_patrocinios');
    }
};
