<?php

namespace App\Repositories\Eloquent;

use App\Models\Wallet;
use App\Models\WalletLog;
use App\Repositories\Contracts\WalletRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WalletRepository implements WalletRepositoryInterface
{
    public function findByUserId(int $userId): ?Wallet
    {
        return Wallet::where('user_id', $userId)->first();
    }

    public function credit(int $userId, float $amount, string $tipo, string $descricao): WalletLog
    {
        return DB::transaction(function () use ($userId, $amount, $tipo, $descricao) {
            $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->firstOrFail();
            $wallet->increment('saldo', $amount);

            return WalletLog::create([
                'user_id'   => $userId,
                'wallet_id' => $wallet->id,
                'valor'     => $amount,
                'tipo'      => $tipo,
                'descricao' => $descricao,
            ]);
        });
    }

    public function debit(int $userId, float $amount, string $tipo, string $descricao): WalletLog
    {
        return DB::transaction(function () use ($userId, $amount, $tipo, $descricao) {
            $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->firstOrFail();

            if ($wallet->saldo < $amount) {
                throw new \RuntimeException('Saldo insuficiente.');
            }

            $wallet->decrement('saldo', $amount);

            return WalletLog::create([
                'user_id'   => $userId,
                'wallet_id' => $wallet->id,
                'valor'     => -$amount,
                'tipo'      => $tipo,
                'descricao' => $descricao,
            ]);
        });
    }

    public function getLogsForUser(int $userId): Collection
    {
        return WalletLog::where('user_id', $userId)->orderByDesc('created_at')->get();
    }

    public function paginateLogs(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = WalletLog::query();

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (!empty($filters['date_start'])) {
            $query->whereDate('created_at', '>=', $filters['date_start']);
        }

        if (!empty($filters['date_end'])) {
            $query->whereDate('created_at', '<=', $filters['date_end']);
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }
}
