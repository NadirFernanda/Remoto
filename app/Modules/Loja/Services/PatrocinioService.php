<?php

namespace App\Modules\Loja\Services;

use App\Models\Infoproduto;
use App\Models\InfoprodutoPatrocinio;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PatrocinioService
{
    /**
     * Caminho da carteira: debita o freelancer e activa o patrocínio.
     *
     * @throws \RuntimeException on validation failure
     */
    public function patrocinar(User $user, Infoproduto $produto, int $dias, float $valor): InfoprodutoPatrocinio
    {
        return DB::transaction(function () use ($user, $produto, $dias, $valor) {
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
            if (!$wallet || $wallet->saldo < $valor) {
                throw new \RuntimeException('Saldo insuficiente. Recarregue a sua carteira ou pague via Multicaixa Express.');
            }

            $wallet->decrement('saldo', $valor);
            WalletLog::create([
                'user_id'   => $user->id,
                'wallet_id' => $wallet->id,
                'valor'     => -$valor,
                'tipo'      => 'patrocinio',
                'descricao' => "Patrocínio do infoproduto \"{$produto->titulo}\" por {$dias} dia(s) — Kz " . number_format($valor, 0, ',', '.') . '.',
            ]);

            return $this->activate($user, $produto, $dias, $valor);
        });
    }

    /**
     * Regista o patrocínio — partilhado entre patrocinar() (após débito da
     * carteira) e a reconciliação AppyPay (após confirmação do pagamento).
     * Nunca debita o utilizador.
     */
    public function activate(User $user, Infoproduto $produto, int $dias, float $valor): InfoprodutoPatrocinio
    {
        // Cancela qualquer patrocínio activo deste produto antes de criar o novo
        InfoprodutoPatrocinio::where('infoproduto_id', $produto->id)
            ->where('status', 'ativo')
            ->update(['status' => 'cancelado']);

        $inicio = Carbon::today();
        $fim    = $inicio->copy()->addDays($dias - 1);

        return InfoprodutoPatrocinio::create([
            'infoproduto_id' => $produto->id,
            'user_id'        => $user->id,
            'data_inicio'    => $inicio,
            'data_fim'       => $fim,
            'dias'           => $dias,
            'valor_total'    => $valor,
            'status'         => 'ativo',
        ]);
    }
}
