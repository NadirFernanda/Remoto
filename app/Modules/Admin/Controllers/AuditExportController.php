<?php

namespace App\Modules\Admin\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AuditExportController extends Controller
{
    public function exportCsv(Request $request)
    {
        $query = AuditLog::query()->with('user:id,name,email');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }

        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        $logs     = $query->orderByDesc('created_at')->get();
        $filename = 'auditoria_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($logs) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Utilizador', 'Email', 'Ação', 'Entidade', 'ID Entidade', 'Descrição', 'IP', 'Data']);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->id,
                    $log->user->name ?? 'Sistema',
                    $log->user->email ?? 'N/A',
                    $log->action,
                    $log->entity_type,
                    $log->entity_id,
                    $log->description,
                    $log->ip,
                    $log->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
