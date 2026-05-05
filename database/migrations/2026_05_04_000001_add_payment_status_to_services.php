<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Estados: none | initiated | capturing | paid | failed | chargeback
return new class extends Migration {
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('payment_status', 30)->default('none')->after('transaction_id');
            $table->string('paypal_order_id', 100)->nullable()->after('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'paypal_order_id']);
        });
    }
};
