<?php

namespace App\Repositories\Eloquent;

use App\Models\AuditLog;
use App\Repositories\Contracts\AuditLogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;

class AuditLogRepository implements AuditLogRepositoryInterface
{
    public function logAction(
        int $userId,
        string $action,
        string $description,
        string $entityType = '',
        int $entityId = 0,
        ?array $before = null,
        ?array $after = null
    ): AuditLog {
        return AuditLog::create([
            'user_id'     => $userId,
            'action'      => $action,
            'description' => $description,
            'entity_type' => $entityType,
            'entity_id'   => $entityId ?: null,
            'before'      => $before,
            'after'       => $after,
            'ip'          => Request::ip(),
        ]);
    }

    public function getByEntityType(string $entityType): Collection
    {
        return AuditLog::where('entity_type', $entityType)->orderByDesc('created_at')->get();
    }

    public function getByUser(int $userId): Collection
    {
        return AuditLog::where('user_id', $userId)->orderByDesc('created_at')->get();
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = AuditLog::query();

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (!empty($filters['entity_type'])) {
            $query->where('entity_type', $filters['entity_type']);
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
