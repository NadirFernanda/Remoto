<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('freelancer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('titulo');
            $table->json('briefing');
            $table->decimal('valor', 12, 2);
            $table->decimal('taxa', 5, 2)->default(10.00); // 10%
            $table->decimal('valor_liquido', 12, 2);
            $table->enum('status', ['published', 'accepted', 'in_progress', 'delivered', 'completed'])->default('published');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
