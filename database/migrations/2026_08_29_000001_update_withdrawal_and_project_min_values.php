<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('platform_settings')->updateOrInsert(
            ['key' => 'withdrawal_min_amount'],
            ['value' => '500', 'updated_at' => now(), 'created_at' => now()]
        );

        DB::table('platform_settings')->updateOrInsert(
            ['key' => 'project_min_value'],
            ['value' => '10000', 'updated_at' => now(), 'created_at' => now()]
        );
    }

    public function down(): void
    {
        DB::table('platform_settings')->where('key', 'withdrawal_min_amount')->update(['value' => '20000']);
        DB::table('platform_settings')->where('key', 'project_min_value')->update(['value' => '5']);
    }
};
