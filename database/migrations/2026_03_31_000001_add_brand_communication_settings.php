<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Widen the value column to support longer texts (e.g. receipt text)
        Schema::table('platform_settings', function (Blueprint $table) {
            $table->text('value')->change();
        });

        // Seed default brand & communication keys
        $defaults = [
            ['key' => 'financial_support_email', 'value' => ''],
            ['key' => 'receipt_text',             'value' => 'Pagamento processado pela 24Horas Remoto.'],
            ['key' => 'brand_logo_path',          'value' => ''],
            ['key' => 'wallet_min_balance',       'value' => '0'],
        ];

        foreach ($defaults as $setting) {
            DB::table('platform_settings')->insertOrIgnore(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        DB::table('platform_settings')
            ->whereIn('key', ['financial_support_email', 'receipt_text', 'brand_logo_path', 'wallet_min_balance'])
            ->delete();
    }
};
