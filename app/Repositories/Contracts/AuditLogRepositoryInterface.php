<?php

namespace App\Repositories\Contracts;

use App\Models\AuditLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface AuditLogRepositoryInterface
{
    public function logAction(
        int $userId,
        string $action,
        string $description,
        string $entityType = '',
        int $entityId = 0,
        ?array $before = null,
        ?array $after = null
    ): AuditLog;

    /** @return Collection<int, AuditLog> */
    public function getByEntityType(string $entityType): Collection;

    /** @return Collection<int, AuditLog> */
    public function getByUser(int $userId): Collection;

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator;
}
