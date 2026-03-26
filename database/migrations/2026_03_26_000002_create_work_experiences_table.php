<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('titulo');           // Cargo / Título
            $table->string('empresa');          // Empresa
            $table->string('cidade')->nullable();
            $table->string('pais')->nullable();
            $table->unsignedTinyInteger('mes_inicio')->nullable();  // 1–12
            $table->unsignedSmallInteger('ano_inicio')->nullable();
            $table->unsignedTinyInteger('mes_fim')->nullable();     // null = emprego atual
            $table->unsignedSmallInteger('ano_fim')->nullable();
            $table->boolean('atual')->default(false);               // "Trabalho aqui actualmente"
            $table->text('descricao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_experiences');
    }
};
