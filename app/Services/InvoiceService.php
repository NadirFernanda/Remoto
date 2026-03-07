<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceService
{
    /**
     * Gera e salva a fatura/recibo em PDF para um serviço.
     * Retorna o caminho do arquivo salvo.
     */
    public static function generate(Service $service): string
    {
        $data = [
            'service' => $service,
            'cliente' => $service->cliente,
            'freelancer' => $service->freelancer,
        ];
        $pdf = Pdf::loadView('pdf.invoice', $data);
        $filename = 'invoices/invoice_service_' . $service->id . '.pdf';
        Storage::put('public/' . $filename, $pdf->output());
        return 'storage/' . $filename;
    }
}
