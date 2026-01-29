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
            'saldo' => 0,
            'patrocinado' => false,
        ]);
        \App\Models\Wallet::updateOrCreate([
            'user_id' => $freelancer->id
        ], [
            'saldo' => 0,
            'saldo_pendente' => 0,
            'saque_minimo' => 20000,
            'taxa_saque' => 20.00,
        ]);
    }
}
