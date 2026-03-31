<?php

namespace App\Modules\Admin\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AuditExportController extends Controller
{
    // ── Shared query builder ──────────────────────────────────────────────────
    private function buildQuery(Request $request)
    {
        $query = AuditLog::query()->with('user:id,name,email');

        if ($request->filled('category'))    $query->where('category',     $request->category);
        if ($request->filled('action'))      $query->where('action',       $request->action);
        if ($request->filled('entity_type')) $query->where('entity_type',  $request->entity_type);
        if ($request->filled('search'))      $query->where('description', 'like', '%' . $request->search . '%');
        if ($request->filled('user_id'))     $query->where('user_id',      $request->user_id);
        if ($request->filled('date_start'))  $query->whereDate('created_at', '>=', $request->date_start);
        if ($request->filled('date_end'))    $query->whereDate('created_at', '<=', $request->date_end);

        return $query->orderByDesc('created_at');
    }

    private function rows($logs): array
    {
        $rows = [['ID', 'Utilizador', 'Email', 'Categoria', 'Ação', 'Entidade', 'ID Entidade', 'Descrição', 'IP', 'Data']];
        foreach ($logs as $log) {
            $rows[] = [
                $log->id,
                $log->user->name  ?? 'Sistema',
                $log->user->email ?? 'N/A',
                $log->category    ?? '',
                $log->action,
                $log->entity_type ?? '',
                $log->entity_id   ?? '',
                $log->description,
                $log->ip          ?? '',
                $log->created_at->format('d/m/Y H:i'),
            ];
        }
        return $rows;
    }

    // ── CSV ───────────────────────────────────────────────────────────────────
    public function exportCsv(Request $request)
    {
        $logs     = $this->buildQuery($request)->get();
        $filename = 'auditoria_' . now()->format('Ymd_His') . '.csv';

        return response()->stream(function () use ($logs) {
            $handle = fopen('php://output', 'w');
            foreach ($this->rows($logs) as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // ── Excel (tab-separated .xls) ────────────────────────────────────────────
    public function exportExcel(Request $request)
    {
        $logs     = $this->buildQuery($request)->get();
        $filename = 'auditoria_' . now()->format('Ymd_His') . '.xls';
        $rows     = $this->rows($logs);

        $html  = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">';
        $html .= '<head><meta charset="UTF-8"></head><body><table>';
        foreach ($rows as $i => $row) {
            $tag   = $i === 0 ? 'th' : 'td';
            $html .= '<tr>' . implode('', array_map(fn($c) => "<{$tag}>" . htmlspecialchars((string)$c) . "</{$tag}>", $row)) . '</tr>';
        }
        $html .= '</table></body></html>';

        return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // ── PDF (HTML print-ready) ────────────────────────────────────────────────
    public function exportPdf(Request $request)
    {
        $logs     = $this->buildQuery($request)->get();
        $filename = 'auditoria_' . now()->format('Ymd_His') . '.pdf';
        $rows     = $this->rows($logs);
        $headers  = array_shift($rows);
        $category = $request->get('category', 'todas');
        $dateFrom = $request->get('date_start', '—');
        $dateTo   = $request->get('date_end',   '—');

        $html = view('admin.exports.audit-pdf', compact('headers', 'rows', 'category', 'dateFrom', 'dateTo'))->render();

        return response($html, 200, [
            'Content-Type'        => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}

