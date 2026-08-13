<?php

namespace App\Livewire;

use App\Jobs\PollAppyPayWalletTopUpJob;
use App\Models\WalletTopUp as WalletTopUpModel;
use App\Modules\Payments\Services\AppyPayGateway;
use App\Modules\Payments\Services\AppyPayReconciliationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class WalletTopUp extends Component
{
    public float $valor = 5;
    public string $phone_number = '';

    public string $step = 'form'; // form | waiting | done
    public ?int $top_up_id = null;
    public ?string $charge_id = null;
    public string $error = '';

    private function generateMerchantTransactionId(): string
    {
        return strtoupper(Str::random(12));
    }

    public function chargeAppyPayPhone()
    {
        $this->error = '';

        $this->validate([
            'valor'        => ['required', 'numeric', 'min:5'],
            'phone_number' => ['required', 'regex:/^9[0-9]{8}$/'],
        ], [
            'valor.required'         => 'Indique o valor a recarregar.',
            'valor.min'              => 'O valor mínimo de recarga é Kz 5.',
            'phone_number.required'  => 'Indique o número de telefone Multicaixa Express.',
            'phone_number.regex'     => 'Número inválido — use 9 dígitos (ex: 923456789).',
        ]);

        $user = Auth::user();

        $topUp = WalletTopUpModel::create([
            'user_id'        => $user->id,
            'valor'          => $this->valor,
            'payment_status' => 'initiated',
        ]);

        $result = (new AppyPayGateway())->chargeByPhone(
            $this->phone_number,
            $this->valor,
            'Recarga de carteira #' . $topUp->id,
            $this->generateMerchantTransactionId()
        );

        if (empty($result['success'])) {
            $topUp->payment_status = 'failed';
            $topUp->save();
            $this->error = $result['message'] ?? 'Não foi possível iniciar o pagamento. Tente novamente.';
            return;
        }

        $topUp->payment_method_used = 'appypay_gpo';
        $topUp->appypay_charge_id   = $result['charge_id'];
        $topUp->save();

        PollAppyPayWalletTopUpJob::dispatch($topUp, $result['charge_id'])->delay(now()->addSeconds(30));

        $this->top_up_id = $topUp->id;
        $this->charge_id = $result['charge_id'];
        $this->step      = 'waiting';
    }

    /** Chamado via wire:poll no ecrã de espera — confirma o estado directamente na AppyPay. */
    public function checkAppyPayStatus()
    {
        if (!$this->top_up_id || !$this->charge_id) {
            return;
        }

        $topUp = WalletTopUpModel::find($this->top_up_id);
        if (!$topUp) {
            return;
        }

        if ($topUp->payment_status === 'paid') {
            $this->step = 'done';
            return;
        }

        if ($topUp->payment_status === 'failed') {
            $this->error = 'O pagamento não foi confirmado. Tente novamente.';
            $this->step  = 'form';
            return;
        }

        // Consulta directa (não depende só do job em fila) — mais responsivo para o utilizador à espera.
        $charge = (new AppyPayGateway())->getCharge($this->charge_id);
        if ($charge['success'] && in_array(strtolower((string) $charge['status']), ['paid', 'completed', 'success', 'approved'], true)) {
            app(AppyPayReconciliationService::class)->markPaidByChargeId($this->charge_id);
            $this->step = 'done';
        }
    }

    public function render()
    {
        $saldo = Auth::user()->wallet->saldo ?? 0;

        return view('livewire.wallet-top-up', ['saldo' => $saldo])
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Recarregar carteira']);
    }
}
