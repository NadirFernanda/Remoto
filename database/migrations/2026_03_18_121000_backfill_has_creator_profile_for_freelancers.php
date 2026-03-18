<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Todos os freelancers são também criadores (decisão arquitectural de Mar 2026)
        DB::statement("UPDATE users SET has_creator_profile = TRUE WHERE role = 'freelancer'");
    }

    public function down(): void
    {
        // Não reverter — não sabemos quais tinham o flag antes
    }
};

