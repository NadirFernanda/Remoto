<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Separa a tabela customizada de notificações da tabela padrão do Laravel.
 *
 * Cenário A (base de dados nova / SQLite nos testes):
 *   - 'notifications' existe mas não tem coluna 'data' → é a tabela customizada
 *   - Renomeia para 'user_notifications' e cria a tabela padrão do Laravel
 *
 * Cenário B (base de dados de produção existente):
 *   - Mesmo comportamento: 'notifications' é a tabela customizada antiga
 *   - Faz rename e cria a tabela padrão
 *
 * Cenário C (migração revertida e re-aplicada):
 *   - 'user_notifications' já existe, 'notifications' não existe → cria apenas a padrão
 */
return new class extends Migration {
    public function up(): void
    {
        // Se 'notifications' existir e NÃO tiver a coluna 'data' →
        // é a tabela customizada legada; renomear para 'user_notifications'
        if (Schema::hasTable('notifications') && !Schema::hasColumn('notifications', 'data')) {
            Schema::rename('notifications', 'user_notifications');
        }

        // Criar tabela padrão do Laravel para o canal 'database' de Notifications
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');

        if (Schema::hasTable('user_notifications')) {
            Schema::rename('user_notifications', 'notifications');
        }
    }
};
