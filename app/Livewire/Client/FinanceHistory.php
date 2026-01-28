<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Service;


use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinanceHistory extends Component
{
    public $transactions = [];
    public $filter_status = '';
    public $filter_type = '';
    public $filter_date_start = '';
    public $filter_date_end = '';

    public function mount()
    {
        $this->loadTransactions();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['filter_status', 'filter_type', 'filter_date_start', 'filter_date_end'])) {
            $this->loadTransactions();
        }
    }

    public function loadTransactions()
    {
        $user = Auth::user();
        $query = Service::where('cliente_id', $user->id);

        if ($this->filter_status) {
            $query->where('status', $this->filter_status);
        }
        if ($this->filter_type) {
            if ($this->filter_type === 'entrada') {
                $query->where('valor', '>', 0);
            } elseif ($this->filter_type === 'saida') {
                $query->where('valor', '<', 0);
            }
        }
        if ($this->filter_date_start) {
            $query->whereDate('created_at', '>=', $this->filter_date_start);
        }
        if ($this->filter_date_end) {
            $query->whereDate('created_at', '<=', $this->filter_date_end);
        }

        $this->transactions = $query->orderByDesc('created_at')->get();
    }

    public function exportCsv()
    {
        $filename = 'historico_transacoes_' . now()->format('Ymd_His') . '.csv';
        $transactions = $this->transactions;
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function() use ($transactions) {
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

    public function render()
    {
        return view('livewire.client.finance-history');
    }
       public function downloadReceipt($id)
    {
        $service = Service::findOrFail($id);
        $user = Auth::user();
        $pdf = app('dompdf.wrapper');
        $html = view('livewire.client.receipt-pdf', compact('service', 'user'))->render();
        $pdf->loadHTML($html);
        return $pdf->download('recibo_transacao_' . $service->id . '.pdf');
    }
}
