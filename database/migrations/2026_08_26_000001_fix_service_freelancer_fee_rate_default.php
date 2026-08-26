<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * A taxa retida ao freelancer foi seedada como 20% (migration
 * 2026_03_27_000001), mas o cálculo real da comissão (FeeService::
 * calculateServiceFee) sempre usou a taxa do cliente (10%) também do
 * lado do freelancer — a setting de 20% nunca teve efeito nenhum no
 * valor realmente cobrado, só distorcia o relatório Admin > Comissões
 * e o preview em Admin > Taxas. Agora que FeeService usa a taxa do
 * freelancer de forma independente, alinhamos o valor guardado com o
 * que sempre esteve realmente em vigor (10%), evitando que o freelancer
 * passe a receber menos de um dia para o outro.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('platform_settings')
            ->where('key', 'service_freelancer_fee_rate')
            ->where('value', '20')
            ->update(['value' => '10', 'updated_at' => now()]);
    }

    public function down(): void
    {
        DB::table('platform_settings')
            ->where('key', 'service_freelancer_fee_rate')
            ->where('value', '10')
            ->update(['value' => '20', 'updated_at' => now()]);
    }
};
