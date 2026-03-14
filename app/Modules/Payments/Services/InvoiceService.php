<?php

namespace App\Modules\Payments\Services;

use App\Models\Service;
use Illuminate\Support\Facades\Storage;
class InvoiceService
{
    /**
     * Gera e guarda o recibo HTML de um servico concluido.
     * Sem dependencias externas — o utilizador pode imprimir/guardar como PDF via Ctrl+P.
     *
     * @return string  Caminho relativo ao public storage (ex: "invoices/invoice_service_12.html")
     */
    public static function generate(Service $service): string
    {
        $service->loadMissing(['cliente', 'freelancer']);

        $html = view('livewire.client.receipt-pdf', [
            'service' => $service,
            'user'    => $service->cliente,
        ])->render();

        $filename = 'invoices/invoice_service_' . $service->id . '.html';
        Storage::disk('public')->put($filename, $html);

        return 'storage/' . $filename;
    }
}

