<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            // Projetos / Serviços
            ['key' => 'service_client_fee_rate',      'value' => '10'],   // % adicionado ao cliente
            ['key' => 'service_freelancer_fee_rate',  'value' => '20'],   // % retido ao freelancer
            // Loja (infoprodutos)
            ['key' => 'loja_fee_rate',                'value' => '20'],   // % comissão plataforma
            // Assinaturas de Criadores
            ['key' => 'subscription_fee_rate',        'value' => '25'],   // % comissão plataforma
            // Patrocínio de Infoprodutos
            ['key' => 'patrocinio_diario',            'value' => '600'],  // AOA por dia
            // Afiliados
            ['key' => 'affiliate_signup_commission',  'value' => '200'],  // AOA por registo indicado
        ];

        foreach ($settings as $setting) {
            DB::table('platform_settings')->insertOrIgnore(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        $keys = [
            'service_client_fee_rate',
            'service_freelancer_fee_rate',
            'loja_fee_rate',
            'subscription_fee_rate',
            'patrocinio_diario',
            'affiliate_signup_commission',
        ];
        DB::table('platform_settings')->whereIn('key', $keys)->delete();
    }
};
