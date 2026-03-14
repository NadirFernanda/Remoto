<?php

namespace App\Modules\Admin\Controllers;

use App\Models\WalletLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FinancialExportController extends Controller
{
    public function exportCsv(Request $request)
    {
        $query = WalletLog::query()->with('user:id,name,email');

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
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
        $filename = 'financeiro_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($logs) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Utilizador', 'Email', 'Valor (AOA)', 'Tipo', 'Descrição', 'Data']);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->id,
                    $log->user->name ?? 'N/A',
                    $log->user->email ?? 'N/A',
                    number_format($log->valor, 2, ',', '.'),
                    $log->tipo,
                    $log->descricao,
                    $log->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
