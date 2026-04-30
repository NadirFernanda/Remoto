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
        // Notificações sem type (NULL ou string vazia) são mensagens de admin
        // enviadas antes do fix. Actualizar para admin_message.
        DB::table('user_notifications')
            ->where(function ($q) {
                $q->whereNull('type')->orWhere('type', '');
            })
            ->update([
                'type'        => 'admin_message',
                'sender_name' => DB::raw("COALESCE(NULLIF(sender_name, ''), 'Administração')"),
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
