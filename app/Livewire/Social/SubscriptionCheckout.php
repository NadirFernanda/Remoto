<?php

namespace App\Livewire\Social;

use App\Jobs\PollAppyPaySubscriptionCheckoutJob;
use App\Models\CreatorProfile;
use App\Models\CreatorSubscription;
use App\Models\CreatorSubscriptionCheckout;
use App\Models\User;
use App\Modules\Payments\Services\AppyPayGateway;
use App\Modules\Payments\Services\AppyPayReconciliationService;
use App\Services\CreatorSubscriptionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class SubscriptionCheckout extends Component
{
    public User $creator;
    public float $price;

    public string $payment_method = 'wallet'; // wallet | express | bank
    public string $phone_number   = '';

    public string $step         = 'form'; // form | waiting | reference | done
    public ?int $checkout_id    = null;
    public ?string $charge_id   = null;
    public ?string $reference   = null;
    public ?string $entity      = null;
    public string $error        = '';

    public function mount(User $user): void
    {
        if (!$user->has_creator_profile || ($user->creator_suspended ?? false)) {
            abort(404);
        }

        $me = Auth::user();
        if ($me->id === $user->id) {
            abort(403);
        }

        $this->creator = $user;
        $this->price   = $user->creatorProfile?->subscription_price ?? CreatorProfile::MIN_SUBSCRIPTION_PRICE;

        // Só quem ganha dinheiro na plataforma (freelancer, criador) tem saldo
        // em carteira — o cliente só gasta, nunca gera saldo, por isso nunca
        // tem nada para pagar com "Saldo".
        if ($me->activeRole() === 'cliente') {
            $this->payment_method = 'express';
        }

        $alreadyActive = CreatorSubscription::where('subscriber_id', $me->id)
            ->where('creator_id', $user->id)
            ->active()
            ->exists();

        if ($alreadyActive) {
            session()->flash('success', 'Já é assinante deste criador.');
            $this->redirect(route('social.creator', ['user' => $user]));
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
            $subscription = app(CreatorSubscriptionService::class)->subscribe($user, $this->creator);
        } catch (\RuntimeException $e) {
            $this->error = $e->getMessage();
            return;
        }

        (new \App\Services\AffiliateService())->creditCommissionForReferredAction($user, 'subscribe_creator', $subscription->id);

        session()->flash('success', 'Assinatura activada! Agora tem acesso ao conteúdo exclusivo de ' . $this->creator->name . '.');
        $this->redirect(route('social.creator', ['user' => $this->creator]));
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

        $checkout = CreatorSubscriptionCheckout::create([
            'subscriber_id'  => Auth::id(),
            'creator_id'     => $this->creator->id,
            'amount'         => $this->price,
            'payment_status' => 'initiated',
        ]);

        $result = (new AppyPayGateway())->chargeByPhone(
            $this->phone_number,
            $this->price,
            'Assinatura de ' . $this->creator->name . ' #' . $checkout->id,
            $this->generateMerchantTransactionId()
        );

        if (empty($result['success'])) {
            $checkout->update(['payment_status' => 'failed']);
            $this->error = $result['message'] ?? 'Não foi possível iniciar o pagamento. Tente novamente.';
            return;
        }

        $checkout->update([
            'payment_method_used' => 'appypay_gpo',
            'appypay_charge_id'   => $result['charge_id'],
        ]);

        PollAppyPaySubscriptionCheckoutJob::dispatch($checkout, $result['charge_id'], 'gpo')->delay(now()->addSeconds(30));

        $this->checkout_id = $checkout->id;
        $this->charge_id   = $result['charge_id'];
        $this->step         = 'waiting';
    }

    // ── AppyPay: Referência de pagamento ────────────────────────────────────

    public function chargeAppyPayReference(): void
    {
        if ($this->step !== 'form') {
            return;
        }

        $this->error = '';

        $checkout = CreatorSubscriptionCheckout::create([
            'subscriber_id'  => Auth::id(),
            'creator_id'     => $this->creator->id,
            'amount'         => $this->price,
            'payment_status' => 'initiated',
        ]);

        $result = (new AppyPayGateway())->chargeByReference(
            $this->price,
            'Assinatura de ' . $this->creator->name . ' #' . $checkout->id,
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

        PollAppyPaySubscriptionCheckoutJob::dispatch($checkout, $result['charge_id'], 'ref')->delay(now()->addMinutes(5));

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
        if (!$this->checkout_id || !$this->charge_id) {
            return;
        }

        $checkout = CreatorSubscriptionCheckout::find($this->checkout_id);
        if (!$checkout) {
            return;
        }

        if ($checkout->payment_status === 'paid') {
            $this->step = 'done';
            session()->flash('success', 'Pagamento confirmado! Agora tem acesso ao conteúdo exclusivo de ' . $this->creator->name . '.');
            $this->redirect(route('social.creator', ['user' => $this->creator]));
            return;
        }

        if ($checkout->payment_status === 'failed') {
            $this->error = 'O pagamento não foi confirmado. Tente novamente ou escolha outro método.';
            $this->step  = 'form';
            return;
        }

        $charge = (new AppyPayGateway())->getCharge($this->charge_id);
        if ($charge['success'] && in_array(strtolower((string) $charge['status']), ['paid', 'completed', 'success', 'approved'], true)) {
            app(AppyPayReconciliationService::class)->markPaidByChargeId($this->charge_id);
            $this->step = 'done';
            session()->flash('success', 'Pagamento confirmado! Agora tem acesso ao conteúdo exclusivo de ' . $this->creator->name . '.');
            $this->redirect(route('social.creator', ['user' => $this->creator]));
        }
    }

    public function render()
    {
        return view('livewire.social.subscription-checkout')
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Assinar ' . $this->creator->name]);
    }
}
