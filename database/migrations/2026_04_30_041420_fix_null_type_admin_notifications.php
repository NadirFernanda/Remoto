<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Notificações sem type são sempre mensagens de admin enviadas antes do fix.
        // Marcar como admin_message e atribuir sender_name genérico se ainda NULL.
        DB::table('user_notifications')
            ->whereNull('type')
            ->update([
                'type'        => 'admin_message',
                'sender_name' => DB::raw("COALESCE(sender_name, 'Administração')"),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Irreversível — não faz sentido reverter dados de negócio
    }
};
