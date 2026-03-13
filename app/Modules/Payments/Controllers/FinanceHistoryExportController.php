<?php

namespace App\Modules\Payments\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Service;

class FinanceHistoryExportController
{
    public function exportCsv(Request $request)
    {
        $user  = Auth::user();
        $query = Service::where('cliente_id', $user->id);

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        if ($request->has('type') && $request->type) {
            if ($request->type === 'entrada') {
                $query->where('valor', '>', 0);
            } elseif ($request->type === 'saida') {
                $query->where('valor', '<', 0);
            }
        }
        if ($request->has('date_start') && $request->date_start) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }
        if ($request->has('date_end') && $request->date_end) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        $transactions = $query->orderByDesc('created_at')->get();
        $filename     = 'historico_transacoes_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($transactions) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Título', 'Valor', 'Status', 'Data']);
            foreach ($transactions as $t) {
                fputcsv($handle, [
                    $t->id,
                    $t->titulo,
                    $t->valor,
                    $t->status,
                    $t->created_at->format('d/m/Y'),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
