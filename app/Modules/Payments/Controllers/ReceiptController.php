<?php

namespace App\Modules\Payments\Controllers;

use App\Models\Service;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    /**
     * Exibe o recibo de uma transação em formato imprimível.
     * O utilizador pode usar Ctrl+P / Imprimir → Guardar como PDF no browser.
     */
    public function download(Service $service)
    {
        $user = Auth::user();

        // Apenas o cliente ou o freelancer do serviço pode ver a factura
        if ($service->cliente_id !== $user->id && $service->freelancer_id !== $user->id) {
            abort(403, 'Acesso não autorizado a esta factura.');
        }

        // Disponível assim que o cliente efectuou o pagamento (escrow bloqueado)
        $paid = ['in_progress', 'em_andamento', 'em andamento', 'delivered', 'completed', 'concluido', 'cancelled', 'cancelado'];
        if (!in_array($service->status, $paid)) {
            return redirect()->back()->with('error', 'Factura disponível apenas após o pagamento ter sido efectuado.');
        }

        $service->loadMissing('freelancer');

        return response()
            ->view('livewire.client.receipt-pdf', compact('service', 'user'))
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }
}
