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
        $cliente = \App\Models\User::updateOrCreate([
            'email' => 'cliente@teste.com'
        ], [
            'name' => 'Cliente Teste',
            'password' => bcrypt('password'),
            'role' => 'cliente',
        ]);
        \App\Models\Profile::updateOrCreate([
            'user_id' => $cliente->id
        ], [
            'saldo' => 0,
            'patrocinado' => false,
            // Do not set codigo_afiliado for cliente
        ]);
        \App\Models\Wallet::updateOrCreate([
            'user_id' => $cliente->id
        ], [
            'saldo' => 0,
            'saldo_pendente' => 0,
            'saque_minimo' => 20000,
            'taxa_saque' => 20.00,
        ]);

        $freelancer = \App\Models\User::updateOrCreate([
            'email' => 'freelancer@teste.com'
        ], [
            'name' => 'Freelancer Teste',
            'password' => bcrypt('password'),
            'role' => 'freelancer',
        ]);
        \App\Models\Profile::updateOrCreate([
            'user_id' => $freelancer->id
        ], [
            'saldo' => 10000,
            'patrocinado' => true,
            'patrocinio_expira_em' => now()->addMonth(),
            'codigo_afiliado' => 'FREELA123',
            'ganhos_afiliado' => 540,
        ]);
        \App\Models\Wallet::updateOrCreate([
            'user_id' => $freelancer->id
        ], [
            'saldo' => 10000,
            'saldo_pendente' => 5000,
            'saque_minimo' => 20000,
            'taxa_saque' => 20.00,
        ]);
        \App\Models\Affiliate::updateOrCreate([
            'user_id' => $freelancer->id
        ], [
            'codigo' => 'FREELA123',
            'ganhos' => 540,
            'status' => 'ativo',
        ]);
        \App\Models\Sponsorship::updateOrCreate([
            'user_id' => $freelancer->id,
            'plano' => '1_mes'
        ], [
            'status' => 'ativo',
            'data_inicio' => now(),
            'data_fim' => now()->addMonth(),
        ]);
    }
}
