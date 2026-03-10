<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infoprodutos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('freelancer_id')->constrained('users')->onDelete('cascade');
            $table->string('titulo', 200);
            $table->text('descricao');
            $table->enum('tipo', ['ebook', 'audio', 'literatura_digital', 'outro'])->default('ebook');
            $table->decimal('preco', 12, 2); // mínimo 5000 KZS
            $table->string('capa_path')->nullable();
            $table->string('arquivo_path')->nullable();
            $table->string('slug', 220)->unique();
            $table->enum('status', ['rascunho', 'em_moderacao', 'ativo', 'inativo'])->default('em_moderacao');
            $table->unsignedInteger('vendas_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infoprodutos');
    }
};
