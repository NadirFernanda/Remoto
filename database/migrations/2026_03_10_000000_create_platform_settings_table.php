<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');
            $table->timestamps();
        });

        // Seed default values
        DB::table('platform_settings')->insert([
            ['key' => 'commission_rate',      'value' => '10',  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'withdraw_fee_fixed',   'value' => '2',   'created_at' => now(), 'updated_at' => now()],
            ['key' => 'withdraw_fee_percent', 'value' => '1.5', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_settings');
    }
};
