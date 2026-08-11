<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A migração 2026_05_04_000001_add_payment_status_to_services.php usa
 * ->after('transaction_id'), mas nenhuma migração chegou a criar essa coluna —
 * ->after() é ignorado silenciosamente pelo Postgres (só o MySQL o respeita),
 * por isso nunca deu erro, mas a coluna pode não existir. Testado localmente
 * com uma base de dados nova: sem esta migração, qualquer pagamento
 * (cartão, PayPal ou AppyPay) falha ao gravar transaction_id.
 */
return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('services', 'transaction_id')) {
            Schema::table('services', function (Blueprint $table) {
                $table->string('transaction_id', 100)->nullable()->after('valor_liquido');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('services', 'transaction_id')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('transaction_id');
            });
        }
    }
};
