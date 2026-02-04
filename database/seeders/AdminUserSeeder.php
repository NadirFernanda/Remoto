<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@sitefreelancer.test'],
            [
                'name' => 'Admin SITE FREELANCER',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );
    }
}
