<?php

namespace App\Modules\Payments\Controllers;

use App\Models\Refund;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceRefundController extends Controller
{
    /**
     * Cliente solicita reembolso de um serviço ainda não liberado.
     * O valor NÃO é creditado imediatamente — o admin revê e decide o montante final.
     * Isso evita double-credit com RefundsAdminPanel::approve().
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

        // Impede pedido duplicado
        $existing = Refund::where('service_id', $service->id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'aprovado'])
            ->exists();

        if ($existing) {
            return redirect()->back()->with('error', 'Já existe um pedido de reembolso activo para este serviço.');
        }

        $validated = $request->validate([
            'reason'           => 'required|string|max:255',
            'details'          => 'required|string|max:2000',
            'proposta_cliente' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($service, $user, $validated) {
            Refund::create([
                'service_id'       => $service->id,
                'user_id'          => $user->id,
                'reason'           => $validated['reason'],
                'details'          => $validated['details'],
                'status'           => 'pending',
                'proposta_cliente' => $validated['proposta_cliente'] ?? $service->valor,
                // valor_reembolso definido pelo admin ao aprovar
            ]);

            $service->status = 'cancelled';
            $service->save();
        });

        return redirect()->route('client.refunds')
            ->with('success', 'Pedido de reembolso registado. O serviço foi cancelado e aguarda análise do admin.');
    }
}
