<?php

namespace App\Modules\Payments\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Modules\Payments\Services\InvoiceService;

class ServiceEscrowController extends Controller
{
    /**
     * Cliente confirma entrega e libera pagamento para o freelancer.
     */
    public function releasePayment(Request $request, Service $service)
    {
        $user = Auth::user();

        if (!$user || $service->cliente_id !== $user->id) {
            return redirect()->back()->with('error', 'Ação não autorizada.');
        }
        if ($service->is_payment_released) {
            return redirect()->back()->with('info', 'Pagamento já foi liberado anteriormente.');
        }
        if (!in_array($service->status, ['delivered', 'completed'])) {
            return redirect()->back()->with('error', 'O serviço ainda não foi entregue.');
        }

        $invoicePath = null;

        DB::transaction(function () use ($service, &$invoicePath) {
            $service->is_payment_released = true;
            $service->payment_released_at = Carbon::now();
            $service->status = 'completed';
            $service->save();

            $invoicePath = InvoiceService::generate($service);
        });

        return redirect()->back()->with(
            'success',
            'Pagamento liberado para o freelancer com sucesso!'
        )->with('invoice_path', $invoicePath);
    }
}
