<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infoproduto_compra_checkouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('infoproduto_id')->constrained('infoprodutos')->onDelete('cascade');
            $table->foreignId('comprador_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method_used', 30)->nullable();
            $table->string('appypay_charge_id', 100)->nullable()->unique();
            $table->string('payment_reference', 50)->nullable();
            $table->string('payment_entity', 20)->nullable();
            $table->string('payment_status', 20)->default('initiated'); // initiated | paid | failed
            $table->foreignId('compra_id')->nullable()->constrained('infoproduto_compras')->nullOnDelete();
            $table->timestamps();

            $table->index(['comprador_id', 'infoproduto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infoproduto_compra_checkouts');
    }
};
