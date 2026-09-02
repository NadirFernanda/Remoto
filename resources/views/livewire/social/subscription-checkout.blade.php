<div class="max-w-2xl mx-auto space-y-5">

    {{-- ── Header ── --}}
    <div class="rounded-2xl p-6 text-white">
        <div class="flex items-center gap-4">
            <img src="{{ $creator->avatarUrl() }}" alt="{{ $creator->name }}"
                class="w-14 h-14 rounded-2xl object-cover ring-2 ring-white/30 flex-shrink-0"
                onerror="this.src='{{ asset('img/default-avatar.svg') }}'">
            <div>
                <p class="text-xs text-white/70 uppercase tracking-wide font-semibold">Assinar</p>
                <h1 class="text-xl font-bold leading-tight">{{ $creator->name }}</h1>
                <p class="text-sm text-white/80 mt-0.5">Kz {{ number_format($price, 0, ',', '.') }}/mês</p>
            </div>
        </div>
    </div>

    {{-- ── Alertas ── --}}
    @if($error)
        <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            {{ $error }}
            @if(str_contains($error, 'Saldo insuficiente'))
                <a href="{{ route('wallet.topup') }}" class="whitespace-nowrap font-semibold underline">Recarregar carteira</a>
            @endif
        </div>
    @endif

    {{-- ── Método de pagamento ── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-base font-bold text-gray-800 mb-1">Método de pagamento</h2>
        <p class="text-sm text-gray-500 mb-5">Escolha como pretende pagar a assinatura</p>

        <div class="grid {{ auth()->user()->activeRole() === 'cliente' ? 'grid-cols-1' : 'grid-cols-2' }} gap-3 mb-6">
            @if(auth()->user()->activeRole() !== 'cliente')
            <button type="button" wire:click="$set('payment_method', 'wallet')"
                class="flex flex-col items-center gap-2 p-4 rounded-2xl border-2 transition-all {{ $payment_method === 'wallet' ? 'border-[#0055ff] bg-[#0055ff]/5 text-[#0055ff] shadow-sm' : 'border-gray-100 bg-gray-50 text-gray-400 hover:border-[#0055ff]/40 hover:bg-[#0055ff]/5 hover:text-[#0055ff]' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <span class="text-xs font-semibold">Saldo</span>
            </button>
            @endif
            <button type="button" wire:click="$set('payment_method', 'express')"
                class="flex flex-col items-center gap-2 p-4 rounded-2xl border-2 transition-all {{ $payment_method === 'express' ? 'border-[#0055ff] bg-[#0055ff]/5 text-[#0055ff] shadow-sm' : 'border-gray-100 bg-gray-50 text-gray-400 hover:border-[#0055ff]/40 hover:bg-[#0055ff]/5 hover:text-[#0055ff]' }}">
                <img src="{{ asset('img/payment/multicaixa-express.jpg') }}" alt="Multicaixa Express" class="w-5 h-5 rounded object-cover">
                <span class="text-xs font-semibold">Express</span>
            </button>
        </div>

        {{-- SALDO --}}
        @if($payment_method === 'wallet')
            <div class="flex items-center justify-between px-1 mb-3 text-sm">
                <span class="text-gray-500">Saldo disponível</span>
                <span class="font-bold {{ $walletBalance < $price ? 'text-red-500' : 'text-gray-800' }}">
                    Kz {{ number_format($walletBalance, 0, ',', '.') }}
                </span>
            </div>
            <button type="button" wire:click="chargeWallet" wire:loading.attr="disabled" wire:target="chargeWallet"
                class="w-full hover: hover: disabled:opacity-60 text-white font-bold py-4 rounded-2xl transition-all shadow-md flex items-center justify-center gap-2 text-base">
                <span wire:loading.remove wire:target="chargeWallet">Pagar {{ number_format($price, 0, ',', '.') }} Kz com saldo da carteira</span>
                <span wire:loading wire:target="chargeWallet" class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    A processar...
                </span>
            </button>
        @endif

        {{-- EXPRESS (Multicaixa Express — telefone) --}}
        @if($payment_method === 'express')
            @if($step === 'form')
                <form wire:submit.prevent="chargeAppyPayPhone">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Número de telefone <span class="text-red-500">*</span></label>
                    <input type="tel" wire:model.defer="phone_number" maxlength="9"
                        class="w-full bg-white text-gray-800 border border-gray-200 rounded-xl px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-sky-200 focus:border-sky-400 outline-none transition @error('phone_number') border-red-400 @enderror"
                        placeholder="923456789">
                    @error('phone_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                    <button type="submit" wire:loading.attr="disabled" wire:target="chargeAppyPayPhone"
                        class="w-full mt-4 hover: hover: disabled:opacity-60 text-white font-bold py-4 rounded-2xl transition-all shadow-md flex items-center justify-center gap-2 text-base">
                        <span wire:loading.remove wire:target="chargeAppyPayPhone">Pagar {{ number_format($price, 0, ',', '.') }} Kz via Express</span>
                        <span wire:loading wire:target="chargeAppyPayPhone" class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            A processar...
                        </span>
                    </button>
                </form>
            @endif

            @if($step === 'waiting')
                <div wire:poll.3s="checkAppyPayStatus" class="flex flex-col items-center justify-center py-8 text-center gap-3">
                    <svg class="animate-spin w-10 h-10 text-sky-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <p class="text-base font-semibold text-gray-700">Aguarde a aprovação no seu telemóvel</p>
                    <p class="text-sm text-gray-400 max-w-xs">Abra a app Multicaixa Express e aprove o pedido de pagamento. Esta página actualiza-se automaticamente.</p>
                </div>
            @endif
        @endif
    </div>

</div>
