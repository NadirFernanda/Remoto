<?php

namespace App\Modules\Payments\Controllers;

use App\Models\Refund;
use App\Models\Service;
use App\Modules\Wallet\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceRefundController extends Controller
{
    public function __construct(private WalletService $walletService) {}

    /**
     * Cliente solicita reembolso de um serviço ainda não liberado.
     */
    public function requestRefund(Request $request, Service $service)
    {
        $user = Auth::user();

        if (!$user || $service->cliente_id !== $user->id) {
            abort(403, 'Ação não autorizada.');
        }
        if ($service->is_payment_released) {
            return redirect()->back()->with('error', 'Não é possível reembolsar: pagamento já foi liberado ao freelancer.');
        }
        if (!in_array($service->status, ['published', 'accepted', 'in_progress', 'delivered', 'negotiating'])) {
            return redirect()->back()->with('error', 'Serviço não está em estado elegível para reembolso.');
        }

        $validated = $request->validate([
            'reason'  => 'required|string|max:255',
            'details' => 'required|string|max:2000',
        ]);

        DB::transaction(function () use ($service, $user, $validated) {
            // Registar o pedido de reembolso
            Refund::create([
                'service_id' => $service->id,
                'user_id'    => $user->id,
                'reason'     => $validated['reason'],
                'details'    => $validated['details'],
                'status'     => 'pending',
            ]);

            // Cancelar o serviço
            $service->status = 'cancelled';
            $service->save();

            // Devolver o valor ao cliente se já tinha sido debitado
            if ($service->valor && $service->valor > 0) {
                $this->walletService->credit(
                    $user,
                    (float) $service->valor,
                    'reembolso',
                    "Reembolso do serviço #{$service->id}: {$service->titulo}"
                );
            }
        });

        return redirect()->route('client.refunds')
            ->with('success', 'Pedido de reembolso registado. O serviço foi cancelado e o valor devolvido à sua carteira.');
    }
}
