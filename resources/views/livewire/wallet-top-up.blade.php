<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-sky-50/40 pb-16">

    {{-- ── Header ── --}}
    <div class="bg-gradient-to-r from-[#00c8ff] to-[#0033cc] rounded-2xl p-6 text-white mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold leading-tight">Recarregar carteira</h1>
                <p class="text-sm text-white/80 mt-0.5">Saldo actual: Kz {{ number_format($saldo, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <div class="max-w-lg mx-auto">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">

            @if($error)
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm">{{ $error }}</div>
            @endif

            @if($step === 'form')
                <div class="flex items-center gap-4 mb-5">
                    <img src="{{ asset('img/payment/multicaixa-express.jpg') }}" alt="Multicaixa Express"
                        class="w-12 h-12 rounded-2xl object-cover flex-shrink-0 shadow-sm">
                    <div>
                        <p class="text-base font-bold text-slate-800">Multicaixa Express</p>
                        <p class="text-xs text-slate-400">Vai receber um pedido de aprovação no seu telemóvel</p>
                    </div>
                </div>

                <form wire:submit.prevent="chargeAppyPayPhone">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Valor a recarregar (Kz) <span class="text-red-500">*</span></label>
                    <input type="number" wire:model.defer="valor" min="5" step="1"
                        class="w-full bg-white text-slate-800 border border-slate-200 rounded-xl px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-sky-200 focus:border-sky-400 outline-none transition @error('valor') border-red-400 @enderror"
                        placeholder="5">
                    @error('valor') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                    <div class="flex gap-2 mt-2">
                        @foreach([5, 50, 500, 5000] as $preset)
                            <button type="button" wire:click="$set('valor', {{ $preset }})"
                                class="flex-1 text-xs font-semibold text-sky-700 bg-sky-50 hover:bg-sky-100 rounded-lg py-2 transition">
                                {{ number_format($preset, 0, ',', '.') }}
                            </button>
                        @endforeach
                    </div>

                    <label class="block text-sm font-semibold text-slate-700 mb-1.5 mt-4">Número de telefone <span class="text-red-500">*</span></label>
                    <input type="tel" wire:model.defer="phone_number" maxlength="9"
                        class="w-full bg-white text-slate-800 border border-slate-200 rounded-xl px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-sky-200 focus:border-sky-400 outline-none transition @error('phone_number') border-red-400 @enderror"
                        placeholder="923456789">
                    @error('phone_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                    <button type="submit" wire:loading.attr="disabled" wire:target="chargeAppyPayPhone"
                        class="w-full mt-4 bg-gradient-to-r from-[#00c8ff] to-[#0055ff] hover:from-sky-400 hover:to-blue-600 disabled:opacity-60 text-white font-bold py-4 rounded-2xl transition-all shadow-md shadow-sky-200/40 flex items-center justify-center gap-2 text-base">
                        <span wire:loading.remove wire:target="chargeAppyPayPhone">Recarregar {{ number_format($valor, 0, ',', '.') }} Kz via Express</span>
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
                    <p class="text-base font-semibold text-slate-700">Aguarde a aprovação no seu telemóvel</p>
                    <p class="text-sm text-slate-400 max-w-xs">Abra a app Multicaixa Express e aprove o pedido de pagamento. Esta página actualiza-se automaticamente.</p>
                </div>
            @endif

            @if($step === 'done')
                <div class="flex flex-col items-center justify-center py-8 text-center gap-3">
                    <div class="w-14 h-14 rounded-full bg-emerald-50 flex items-center justify-center">
                        <svg class="w-7 h-7 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <p class="text-base font-semibold text-slate-700">Recarga confirmada!</p>
                    <p class="text-sm text-slate-400 max-w-xs">O saldo já está disponível na sua carteira.</p>
                    <a href="{{ url()->previous() }}" class="mt-2 text-sm font-semibold text-sky-600 hover:text-sky-700">Voltar</a>
                </div>
            @endif
        </div>
    </div>
</div>
