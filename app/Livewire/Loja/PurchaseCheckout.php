<?php

namespace App\Livewire\Loja;

use App\Jobs\InitiateAppyPayInfoprodutoCompraChargeJob;
use App\Jobs\PollAppyPayInfoprodutoCompraCheckoutJob;
use App\Models\Infoproduto;
use App\Models\InfoprodutoCompraCheckout;
use App\Models\Wallet;
use App\Modules\Loja\Services\LojaService;
use App\Modules\Payments\Services\AppyPayGateway;
use App\Modules\Payments\Services\AppyPayReconciliationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class PurchaseCheckout extends Component
{
    public Infoproduto $produto;
    public float $price;

    public string $payment_method = 'wallet'; // wallet | express | bank
    public float $walletBalance   = 0;
    public string $phone_number   = '';

    public string $step         = 'form'; // form | waiting | reference | done
    public ?int $checkout_id    = null;
    public ?string $charge_id   = null;
    public ?string $reference   = null;
    public ?string $entity      = null;
    public string $error        = '';

    public function mount(Infoproduto $produto): void
    {
        if ($produto->status !== 'ativo') {
            abort(404);
        }

        $user = Auth::user();
        if ($produto->freelancer_id === $user->id) {
            session()->flash('error_loja', 'Não pode comprar o seu próprio produto.');
            $this->redirect(route('loja.show', $produto->slug));
            return;
        }

        $this->produto = $produto;
        $this->price   = $produto->preco;

        // Só quem ganha dinheiro na plataforma (freelancer, criador) tem saldo
        // em carteira — o cliente só gasta, nunca gera saldo, por isso nunca
        // tem nada para pagar com "Saldo".
        if ($user->activeRole() === 'cliente') {
            $this->payment_method = 'express';
        } else {
            $this->walletBalance = (float) (Wallet::where('user_id', $user->id)->value('saldo') ?? 0);
        }

        if ($produto->jaCompradoPor($user->id)) {
            session()->flash('success_loja', 'Já adquiriu este produto.');
            $this->redirect(route('loja.show', $produto->slug));
        }
    }

    private function generateMerchantTransactionId(): string
    {
        return strtoupper(Str::random(12));
    }

    // ── Saldo da carteira ──────────────────────────────────────────────────

    public function chargeWallet(): void
    {
        if ($this->step !== 'form') {
            return;
        }

        $user = Auth::user();

        if ($user->activeRole() === 'cliente') {
            $this->error = 'Pagamento com saldo de carteira não está disponível no modo cliente.';
            return;
        }

        try {
            app(LojaService::class)->comprar($user, $this->produto);
        } catch (\RuntimeException $e) {
            $this->error = $e->getMessage();
            return;
        }

        session()->flash('success_loja', 'Compra realizada! Faça o download na página do produto.');
        $this->redirect(route('loja.show', $this->produto->slug));
    }

    // ── AppyPay: Multicaixa Express (telefone) ─────────────────────────────

    public function chargeAppyPayPhone(): void
    {
        if ($this->step !== 'form') {
            return;
        }

        $this->error = '';

        $this->validate([
            'phone_number' => ['required', 'regex:/^9[0-9]{8}$/'],
        ], [
            'phone_number.required' => 'Indique o número de telefone Multicaixa Express.',
            'phone_number.regex'    => 'Número inválido — use 9 dígitos (ex: 923456789).',
        ]);

        $checkout = InfoprodutoCompraCheckout::create([
            'infoproduto_id' => $this->produto->id,
            'comprador_id'   => Auth::id(),
            'amount'         => $this->price,
            'payment_status' => 'initiated',
        ]);

        InitiateAppyPayInfoprodutoCompraChargeJob::dispatch(
            $checkout,
            $this->phone_number,
            $this->price,
            'Compra de "' . $this->produto->titulo . '" #' . $checkout->id,
            $this->generateMerchantTransactionId()
        );

        $this->checkout_id = $checkout->id;
        $this->step         = 'waiting';
    }

    // ── AppyPay: Referência de pagamento ────────────────────────────────────

    public function chargeAppyPayReference(): void
    {
        if ($this->step !== 'form') {
            return;
        }

        $this->error = '';

        $checkout = InfoprodutoCompraCheckout::create([
            'infoproduto_id' => $this->produto->id,
            'comprador_id'   => Auth::id(),
            'amount'         => $this->price,
            'payment_status' => 'initiated',
        ]);

        $result = (new AppyPayGateway())->chargeByReference(
            $this->price,
            'Compra de "' . $this->produto->titulo . '" #' . $checkout->id,
            $this->generateMerchantTransactionId()
        );

        if (empty($result['success'])) {
            $checkout->update(['payment_status' => 'failed']);
            $this->error = $result['message'] ?? 'Não foi possível gerar a referência. Tente novamente.';
            return;
        }

        $checkout->update([
            'payment_method_used' => 'appypay_ref',
            'appypay_charge_id'   => $result['charge_id'],
            'payment_reference'   => $result['reference'],
            'payment_entity'      => $result['entity'],
        ]);

        PollAppyPayInfoprodutoCompraCheckoutJob::dispatch($checkout, $result['charge_id'], 'ref')->delay(now()->addMinutes(5));

        $this->checkout_id = $checkout->id;
        $this->charge_id   = $result['charge_id'];
        $this->reference   = $result['reference'];
        $this->entity      = $result['entity'];
        $this->step         = 'reference';
    }

    /** Apenas sandbox — simula o pagamento da referência gerada. */
    public function mockConfirmAppyPayReference(): void
    {
        if (config('services.appypay.mode') !== 'sandbox' || !$this->reference) {
            return;
        }

        (new AppyPayGateway())->mockReferencePayment($this->reference);
        $this->checkAppyPayStatus();
    }

    /** Chamado via wire:poll no ecrã de espera — confirma o estado directamente na AppyPay. */
    public function checkAppyPayStatus(): void
    {
        if (!$this->checkout_id) {
            return;
        }

        $checkout = InfoprodutoCompraCheckout::find($this->checkout_id);
        if (!$checkout) {
            return;
        }

        if ($checkout->payment_status === 'paid') {
            $this->step = 'done';
            session()->flash('success_loja', 'Pagamento confirmado! Faça o download na página do produto.');
            $this->redirect(route('loja.show', $this->produto->slug));
            return;
        }

        if ($checkout->payment_status === 'failed') {
            $this->error = 'O pagamento não foi confirmado. Tente novamente ou escolha outro método.';
            $this->step  = 'form';
            return;
        }

        // O job em segundo plano ainda pode não ter conseguido o charge_id —
        // sem ele não há nada para consultar ainda; o próximo ciclo tenta de novo.
        $chargeId = $this->charge_id ?: $checkout->appypay_charge_id;
        if (!$chargeId) {
            return;
        }
        $this->charge_id = $chargeId;

        $charge = (new AppyPayGateway())->getCharge($chargeId);
        if ($charge['success'] && in_array(strtolower((string) $charge['status']), ['paid', 'completed', 'success', 'approved'], true)) {
            app(AppyPayReconciliationService::class)->markPaidByChargeId($chargeId);
            $this->step = 'done';
            session()->flash('success_loja', 'Pagamento confirmado! Faça o download na página do produto.');
            $this->redirect(route('loja.show', $this->produto->slug));
        }
    }

    public function render()
    {
        return view('livewire.loja.purchase-checkout')
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Comprar produto']);
    }
}
