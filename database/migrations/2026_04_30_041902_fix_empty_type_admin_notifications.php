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
        // A migration anterior usava whereNull mas o campo type é NOT NULL na BD,
        // por isso as notificações antigas tinham type='' (string vazia).
        // Esta migration corrige exactamente esses registos.
        DB::table('user_notifications')
            ->where(function ($q) {
                $q->whereNull('type')->orWhere('type', '');
            })
            ->update([
                'type'        => 'admin_message',
                'sender_name' => DB::raw("COALESCE(NULLIF(sender_name, ''), 'Administração')"),
            ]);
    }

    public function down(): void {}

};
