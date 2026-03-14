<?php

namespace App\Modules\Payments\Controllers;

use App\Models\WalletLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class FinanceHistoryExportController extends Controller
{
    public function exportCsv(Request $request)
    {
        $user  = Auth::user();
        $query = WalletLog::where('user_id', $user->id);

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('sinal')) {
            $query->where('valor', $request->sinal === 'entrada' ? '>' : '<', 0);
        }
        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        $rows     = $query->orderByDesc('created_at')->get();
        $filename = 'transacoes_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');
            // BOM para compatibilidade com Excel
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['ID', 'Tipo', 'Valor (AOA)', 'Descrição', 'Data']);
            foreach ($rows as $r) {
                fputcsv($handle, [
                    $r->id,
                    $r->tipo,
                    number_format((float) $r->valor, 2, ',', '.'),
                    $r->descricao,
                    $r->created_at->format('d/m/Y H:i'),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
