<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('audit_logs', 'category')) {
            return;
        }

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('category', 64)->default('geral')->after('action');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('audit_logs', 'category')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
    }
};
