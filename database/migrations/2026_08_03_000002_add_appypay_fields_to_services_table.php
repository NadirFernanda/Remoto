<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('payment_method_used', 30)->nullable()->after('paypal_order_id');
            $table->string('appypay_charge_id', 100)->nullable()->after('payment_method_used');
            $table->string('payment_reference', 50)->nullable()->after('appypay_charge_id');
            $table->string('payment_entity', 20)->nullable()->after('payment_reference');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['payment_method_used', 'appypay_charge_id', 'payment_reference', 'payment_entity']);
        });
    }
};
