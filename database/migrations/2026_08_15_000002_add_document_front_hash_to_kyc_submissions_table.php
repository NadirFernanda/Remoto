<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kyc_submissions', function (Blueprint $table) {
            $table->string('document_front_hash', 64)->nullable()->after('document_front_path');
            $table->unique('document_front_hash');
        });
    }

    public function down(): void
    {
        Schema::table('kyc_submissions', function (Blueprint $table) {
            $table->dropUnique(['document_front_hash']);
            $table->dropColumn('document_front_hash');
        });
    }
};
