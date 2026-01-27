<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cliente = \App\Models\User::factory()->create([
            'name' => 'Cliente Teste',
            'email' => 'cliente@teste.com',
            'role' => 'cliente',
        ]);
        $cliente->profile()->create([
            'saldo' => 0,
            'patrocinado' => false,
        ]);
        $cliente->wallet()->create([
            'saldo' => 0,
            'saldo_pendente' => 0,
            'saque_minimo' => 20000,
            'taxa_saque' => 20.00,
        ]);

        $freelancer = \App\Models\User::factory()->create([
            'name' => 'Freelancer Teste',
            'email' => 'freelancer@teste.com',
            'role' => 'freelancer',
        ]);
        $freelancer->profile()->create([
            'saldo' => 10000,
            'patrocinado' => true,
            'patrocinio_expira_em' => now()->addMonth(),
            'codigo_afiliado' => 'FREELA123',
            'ganhos_afiliado' => 540,
        ]);
        $freelancer->wallet()->create([
            'saldo' => 10000,
            'saldo_pendente' => 5000,
            'saque_minimo' => 20000,
            'taxa_saque' => 20.00,
        ]);
        $freelancer->affiliate()->create([
            'codigo' => 'FREELA123',
            'ganhos' => 540,
            'status' => 'ativo',
        ]);
        $freelancer->sponsorships()->create([
            'plano' => '1_mes',
            'status' => 'ativo',
            'data_inicio' => now(),
            'data_fim' => now()->addMonth(),
        ]);
    }
}
