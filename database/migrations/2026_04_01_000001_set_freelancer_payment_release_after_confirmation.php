<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('platform_settings')
            ->where('key', 'freelancer_payment_release')
            ->update(['value' => 'after_confirmation', 'updated_at' => now()]);
    }

    public function down(): void
    {
        DB::table('platform_settings')
            ->where('key', 'freelancer_payment_release')
            ->update(['value' => 'immediate', 'updated_at' => now()]);
    }
};
