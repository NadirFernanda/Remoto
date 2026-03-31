<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Extend users table with new admin role values and corporate email
        Schema::table('users', function (Blueprint $table) {
            // admin_role now supports: master | financeiro | gestor | suporte | analista | null
            // We simply ensure the column allows the new values (it's already varchar)
            // Add corporate email for admin users
            if (! Schema::hasColumn('users', 'admin_corporate_email')) {
                $table->string('admin_corporate_email')->nullable()->after('admin_role');
            }
            if (! Schema::hasColumn('users', 'admin_cargo')) {
                $table->string('admin_cargo', 100)->nullable()->after('admin_corporate_email');
            }
            if (! Schema::hasColumn('users', 'admin_phone')) {
                $table->string('admin_phone', 30)->nullable()->after('admin_cargo');
            }
        });

        // Per-module permissions for admin users
        Schema::create('admin_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Module identifier: dashboard | users | financial | disputes | audit | social | refunds
            //                     commissions | payouts | categories | fees | settings | suporte | loja
            $table->string('module', 60);
            // access level: none | read | write | full
            $table->enum('access', ['none', 'read', 'write', 'full'])->default('none');
            $table->timestamps();
            $table->unique(['user_id', 'module']);
        });

        // Admin security settings per user
        Schema::create('admin_security', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('two_factor_required')->default(false);
            $table->boolean('ip_restriction')->default(false);
            $table->text('allowed_ips')->nullable(); // JSON array of IPs
            $table->boolean('session_timeout_enabled')->default(true);
            $table->unsignedSmallInteger('session_timeout_minutes')->default(60);
            $table->boolean('force_password_change')->default(false);
            $table->timestamp('password_changed_at')->nullable();
            $table->timestamps();
        });

        // Admin notification preferences per user
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('notify_new_user')->default(true);
            $table->boolean('notify_new_dispute')->default(true);
            $table->boolean('notify_kyc_pending')->default(true);
            $table->boolean('notify_payout_request')->default(true);
            $table->boolean('notify_high_value_transaction')->default(false);
            $table->boolean('notify_system_error')->default(true);
            $table->boolean('notify_daily_report')->default(false);
            $table->enum('channel', ['email', 'system', 'both'])->default('both');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
        Schema::dropIfExists('admin_security');
        Schema::dropIfExists('admin_permissions');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['admin_corporate_email', 'admin_cargo', 'admin_phone']);
        });
    }
};
