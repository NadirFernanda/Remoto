<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->string('category', 32)->default('imagem')->after('is_public');
            $table->string('issuer', 150)->nullable()->after('category');
            $table->unsignedSmallInteger('issued_year')->nullable()->after('issuer');
            $table->unsignedSmallInteger('sort_order')->default(0)->after('issued_year');
            $table->boolean('featured')->default(false)->after('sort_order');
        });

        Schema::table('freelancer_profiles', function (Blueprint $table) {
            $table->boolean('onboarding_dismissed')->default(false)->after('kyc_status');
        });
    }

    public function down(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropColumn(['category', 'issuer', 'issued_year', 'sort_order', 'featured']);
        });

        Schema::table('freelancer_profiles', function (Blueprint $table) {
            $table->dropColumn('onboarding_dismissed');
        });
    }
};
