<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_candidates', function (Blueprint $table) {
            $table->text('proposal_message')->nullable()->after('status');
            $table->decimal('proposal_value', 12, 2)->nullable()->after('proposal_message');
        });
    }

    public function down(): void
    {
        Schema::table('service_candidates', function (Blueprint $table) {
            $table->dropColumn(['proposal_message', 'proposal_value']);
        });
    }
};
