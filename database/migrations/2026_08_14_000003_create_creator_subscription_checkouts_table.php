<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creator_subscription_checkouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscriber_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method_used', 30)->nullable();
            $table->string('appypay_charge_id', 100)->nullable()->unique();
            $table->string('payment_reference', 50)->nullable();
            $table->string('payment_entity', 20)->nullable();
            $table->string('payment_status', 20)->default('initiated'); // initiated | paid | failed
            $table->foreignId('subscription_id')->nullable()->constrained('creator_subscriptions')->nullOnDelete();
            $table->timestamps();

            $table->index(['subscriber_id', 'creator_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creator_subscription_checkouts');
    }
};
