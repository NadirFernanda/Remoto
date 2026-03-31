<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            // Freelancers — liberação após conclusão do projecto
            // Values: 'immediate' | 'after_confirmation'
            ['key' => 'freelancer_payment_release', 'value' => 'immediate'],

            // Criadores — liberação de pagamento
            // Values: 'immediate' | 'day_26'
            ['key' => 'creator_payment_release', 'value' => 'day_26'],

            // Infoprodutos — liberação ao produtor após venda
            // Values: 'immediate' | '7_days' | '14_days'
            ['key' => 'infoproduto_payment_release', 'value' => '7_days'],
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
                'freelancer_payment_release',
                'creator_payment_release',
                'infoproduto_payment_release',
            ])
            ->delete();
    }
};
