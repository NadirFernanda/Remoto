<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ServiceRefundController extends Controller
{
    /**
     * Cliente solicita reembolso de um serviço ainda não liberado.
     */
    public function requestRefund(Request $request, Service $service)
    {
        $user = Auth::user();
        if (!$user || $service->cliente_id !== $user->id) {
            return redirect()->back()->with('error', 'Ação não autorizada.');
        }
        if ($service->is_payment_released) {
            return redirect()->back()->with('error', 'Não é possível reembolsar: pagamento já foi liberado ao freelancer.');
        }
        if (!in_array($service->status, ['published', 'accepted', 'in_progress', 'delivered'])) {
            return redirect()->back()->with('error', 'Serviço não está em estado elegível para reembolso.');
        }
        DB::transaction(function () use ($service) {
            $service->status = 'cancelled';
            $service->save();
            // Aqui pode-se adicionar lógica para devolver o valor ao cliente (gateway, wallet, etc)
        });
        return redirect()->back()->with('success', 'Reembolso solicitado e serviço cancelado com sucesso!');
    }
}
