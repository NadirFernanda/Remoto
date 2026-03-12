<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add multi-profile flags + per-profile suspension to users
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('has_freelancer_profile')->default(false)->after('role');
            $table->boolean('has_cliente_profile')->default(false)->after('has_freelancer_profile');
            $table->boolean('has_creator_profile')->default(false)->after('has_cliente_profile');
            $table->boolean('freelancer_suspended')->default(false)->after('has_creator_profile');
            $table->boolean('cliente_suspended')->default(false)->after('freelancer_suspended');
            $table->boolean('creator_suspended')->default(false)->after('cliente_suspended');
        });

        // Seed flags for existing users based on their current role
        DB::statement("UPDATE users SET has_freelancer_profile = TRUE WHERE role = 'freelancer'");
        DB::statement("UPDATE users SET has_cliente_profile = TRUE WHERE role = 'cliente'");
        DB::statement("UPDATE users SET has_creator_profile = TRUE WHERE role = 'creator'");

        // 2. Creator profiles table
        Schema::create('creator_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('category')->default('geral');
            $table->text('bio')->nullable();
            $table->string('cover_photo')->nullable();
            $table->decimal('subscription_price', 10, 2)->default(3000.00);
            $table->boolean('is_public')->default(true);
            $table->unsignedInteger('total_subscribers')->default(0);
            $table->decimal('total_earnings', 14, 2)->default(0.00);
            $table->timestamps();
        });

        // 3. Creator subscriptions table
        Schema::create('creator_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscriber_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('creator_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 10, 2)->default(3000.00);      // total paid
            $table->decimal('platform_fee', 10, 2)->default(900.00); // 30%
            $table->decimal('net_amount', 10, 2)->default(2100.00);  // 70%
            $table->enum('status', ['active', 'cancelled', 'expired'])->default('active');
            $table->timestamp('starts_at');
            $table->timestamp('expires_at');
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->unique(['subscriber_id', 'creator_id']);
            $table->index(['creator_id', 'status', 'expires_at']);
            $table->index(['subscriber_id', 'status', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creator_subscriptions');
        Schema::dropIfExists('creator_profiles');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'has_freelancer_profile',
                'has_cliente_profile',
                'has_creator_profile',
                'freelancer_suspended',
                'cliente_suspended',
                'creator_suspended',
            ]);
        });
    }
};
