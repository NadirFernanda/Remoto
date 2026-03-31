<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            // Processamento de saques: 'automatic' | 'manual'
            ['key' => 'withdrawal_processing',       'value' => 'manual'],

            // Limite mínimo de saque em Kz: '20000' | '60000' | '0' (sem limite)
            ['key' => 'withdrawal_min_amount',       'value' => '20000'],

            // Alerta de liquidez em Kz: '500000' | '1000000'
            ['key' => 'withdrawal_liquidity_alert',  'value' => '500000'],

            // Métodos de pagamento aceites (JSON array)
            // Options: 'bank_transfer', 'visa', 'other'
            ['key' => 'withdrawal_methods',          'value' => '["bank_transfer"]'],
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
            ->whereIn('key', [
                'withdrawal_processing',
                'withdrawal_min_amount',
                'withdrawal_liquidity_alert',
                'withdrawal_methods',
            ])
            ->delete();
    }
};
