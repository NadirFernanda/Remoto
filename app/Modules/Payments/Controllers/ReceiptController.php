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

        // Apenas o cliente ou o freelancer do serviço pode ver o recibo
        if ($service->cliente_id !== $user->id && $service->freelancer_id !== $user->id) {
            abort(403, 'Acesso não autorizado a este recibo.');
        }

        // Só serviços concluídos ou cancelados têm recibo válido
        if (!in_array($service->status, ['completed', 'cancelled', 'delivered'])) {
            return redirect()->back()->with('error', 'Recibo disponível apenas para serviços concluídos ou cancelados.');
        }

        return response()
            ->view('livewire.client.receipt-pdf', compact('service', 'user'))
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }
}
