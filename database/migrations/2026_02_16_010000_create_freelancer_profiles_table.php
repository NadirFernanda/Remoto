<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('freelancer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('headline')->nullable();
            $table->text('summary')->nullable();
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->string('currency', 8)->nullable()->default('USD');
            $table->string('availability_status')->nullable()->default('available');
            $table->json('skills')->nullable();
            $table->json('languages')->nullable();
            $table->json('metrics')->nullable();
            $table->string('kyc_status')->nullable()->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('freelancer_profiles');
    }
};
