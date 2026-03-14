<?php

namespace App\Repositories\Contracts;

use App\Models\Wallet;
use App\Models\WalletLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface WalletRepositoryInterface
{
    public function findByUserId(int $userId): ?Wallet;

    public function credit(int $userId, float $amount, string $tipo, string $descricao): WalletLog;

    public function debit(int $userId, float $amount, string $tipo, string $descricao): WalletLog;

    /** @return Collection<int, WalletLog> */
    public function getLogsForUser(int $userId): Collection;

    public function paginateLogs(array $filters = [], int $perPage = 20): LengthAwarePaginator;
}
