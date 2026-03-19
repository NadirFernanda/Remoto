<?php

namespace App\Modules\Payments\Controllers;

use App\Models\Service;
use App\Models\Wallet;
use App\Models\WalletLog;
use App\Services\FeeService;
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
     * Distribui automaticamente: freelancer recebe o valor líquido,
     * plataforma retém a taxa de serviço (10% por defeito).
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

            // ── Distribute payment to freelancer ──────────────────────────
            if ($service->freelancer_id && $service->valor > 0) {
                $feeService = app(FeeService::class);
                $fee        = $feeService->calculateServiceFee((float) $service->valor);
                $taxa       = $fee['taxa'];
                $liquido    = $fee['valor_liquido'];

                // Update service record with calculated values
                $service->taxa          = $taxa;
                $service->valor_liquido = $liquido;
                $service->save();

                // Credit freelancer wallet
                $freelancerWallet = Wallet::firstOrCreate(
                    ['user_id' => $service->freelancer_id],
                    ['saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => 0, 'taxa_saque' => 0]
                );
                $freelancerWallet->increment('saldo', $liquido);

                $feeRate = $feeService->getServiceFeeRate();
                WalletLog::create([
                    'user_id'   => $service->freelancer_id,
                    'wallet_id' => $freelancerWallet->id,
                    'valor'     => $liquido,
                    'tipo'      => 'ganho_servico',
                    'descricao' => "Pagamento do serviço \"{$service->titulo}\" — comissão de {$feeRate}% retida pela plataforma.",
                ]);
            }

            $invoicePath = InvoiceService::generate($service);
        });

        return redirect()->back()->with(
            'success',
            'Pagamento liberado para o freelancer com sucesso!'
        )->with('invoice_path', $invoicePath);
    }
}
