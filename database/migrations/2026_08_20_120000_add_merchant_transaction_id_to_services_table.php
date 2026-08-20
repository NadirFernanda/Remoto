<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Gravado ANTES de chamar a AppyPay (não depois) — se o pedido
            // expirar do nosso lado sem recebermos o charge_id deles, ainda
            // ficamos com este ID para reconciliar manualmente com o suporte
            // da AppyPay em vez de perder o rasto do pagamento por completo.
            $table->string('appypay_merchant_transaction_id', 20)->nullable()->after('appypay_charge_id');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('appypay_merchant_transaction_id');
        });
    }
};
