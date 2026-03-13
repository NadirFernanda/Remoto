<?php

namespace App\Modules\Admin\Services;

use App\Models\AuditLog;

class AuditLogger
{
    public static function log(
        string  $action,
        string  $description,
        ?string $entityType = null,
        ?int    $entityId   = null,
        ?array  $before     = null,
        ?array  $after      = null
    ): void {
        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'description' => $description,
            'before'      => $before,
            'after'       => $after,
            'ip'          => request()->ip(),
        ]);
    }
}

