<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_candidates', function (Blueprint $table) {
            $table->decimal('proposal_fee', 12, 2)->nullable()->after('proposal_value');
            $table->decimal('proposal_net', 12, 2)->nullable()->after('proposal_fee');
        });
    }

    public function down(): void
    {
        Schema::table('service_candidates', function (Blueprint $table) {
            $table->dropColumn(['proposal_fee', 'proposal_net']);
        });
    }
};
