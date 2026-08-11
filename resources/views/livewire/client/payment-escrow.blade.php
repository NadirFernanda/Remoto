<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-sky-50/40 pb-16">

    {{-- ── Header ── --}}
    <div class="bg-gradient-to-r from-[#00c8ff] to-[#0033cc] rounded-2xl p-6 text-white mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold leading-tight">Pagamento Seguro</h1>
                <p class="text-sm text-white/80 mt-0.5">O valor fica em escrow até à entrega aprovada</p>
            </div>
        </div>
    </div>

    {{-- ── Progress bar ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm px-6 py-5 mb-8">
        <div class="flex items-center gap-2">
            @foreach([1 => 'Briefing', 2 => 'Investimento', 3 => 'Pagamento'] as $n => $label)
                <div class="flex items-center {{ $loop->last ? '' : 'flex-1' }}">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold shadow-sm
                            {{ $n < 3  ? 'bg-gradient-to-br from-emerald-400 to-emerald-500 text-white' :
                               'bg-gradient-to-br from-[#00c8ff] to-[#0055ff] text-white shadow-sky-200/60' }}">
                            @if($n < 3)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            @else
                                {{ $n }}
                            @endif
                        </div>
                        <span class="text-sm font-semibold text-slate-800 hidden sm:inline">{{ $label }}</span>
                    </div>
                    @if(!$loop->last)
                        <div class="flex-1 h-1 mx-3 rounded-full bg-gradient-to-r from-[#00c8ff] to-[#0055ff]"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── Alertas ── --}}
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Coluna principal ── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Método de pagamento --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <h2 class="text-lg font-bold text-slate-800 mb-1">Método de pagamento</h2>
                <p class="text-sm text-slate-500 mb-5">Escolha como pretende efectuar o pagamento</p>

                @php
                    $methods = [
                        'card'    => ['label' => 'Cartão',  'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>'],
                        'paypal'  => ['label' => 'PayPal',  'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
                        'express' => ['label' => 'Express', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>'],
                        'bank'    => ['label' => 'Banco',   'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>'],
                    ];
                @endphp

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach($methods as $key => $method)
                        <button type="button" wire:click="$set('payment_method', '{{ $key }}')"
                            class="flex flex-col items-center gap-2 p-4 rounded-2xl border-2 transition-all
                                {{ $payment_method === $key
                                    ? 'border-sky-400 bg-sky-50 text-sky-700 shadow-sm shadow-sky-100'
                                    : 'border-slate-100 bg-slate-50/60 text-slate-400 hover:border-sky-300 hover:bg-sky-50/60 hover:text-sky-600' }}">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                {!! $method['icon'] !!}
                            </svg>
                            <span class="text-xs font-semibold">{{ $method['label'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <form wire:submit.prevent="confirmPayment">

                {{-- CARTÃO --}}
                @if($payment_method === 'card')
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-4 h-4 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <h3 class="text-sm font-bold text-slate-700">Dados do cartão</h3>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nome no cartão <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.defer="card_name"
                            class="w-full bg-white text-slate-800 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-sky-200 focus:border-sky-400 outline-none transition @error('card_name') border-red-400 @enderror"
                            placeholder="Como está impresso no cartão" autocomplete="cc-name">
                        @error('card_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Número do cartão <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="text" wire:model.defer="card_number" maxlength="19"
                                class="w-full bg-white text-slate-800 border border-slate-200 rounded-xl px-4 py-3 text-sm font-mono tracking-widest focus:ring-2 focus:ring-sky-200 focus:border-sky-400 outline-none transition @error('card_number') border-red-400 @enderror"
                                placeholder="0000 0000 0000 0000" autocomplete="cc-number">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 flex gap-1">
                                <span class="text-[10px] font-bold text-blue-700 bg-blue-50 border border-blue-100 px-1.5 py-0.5 rounded">VISA</span>
                                <span class="text-[10px] font-bold text-red-600 bg-red-50 border border-red-100 px-1.5 py-0.5 rounded">MC</span>
                            </div>
                        </div>
                        @error('card_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Validade <span class="text-red-500">*</span></label>
                            <input type="text" wire:model.defer="card_expiry" maxlength="5"
                                class="w-full bg-white text-slate-800 border border-slate-200 rounded-xl px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-sky-200 focus:border-sky-400 outline-none transition @error('card_expiry') border-red-400 @enderror"
                                placeholder="MM/AA" autocomplete="cc-exp">
                            @error('card_expiry') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">CVV <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="text" wire:model.defer="card_cvv" maxlength="4"
                                    class="w-full bg-white text-slate-800 border border-slate-200 rounded-xl px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-sky-200 focus:border-sky-400 outline-none transition @error('card_cvv') border-red-400 @enderror"
                                    placeholder="123" autocomplete="cc-csc">
                                <div class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-300">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                            </div>
                            @error('card_cvv') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    @error('payment_token')
                        <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-xs">{{ $message }}</div>
                    @enderror
                </div>
                @endif

                {{-- PAYPAL --}}
                @if($payment_method === 'paypal')
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <div class="flex items-center gap-4 mb-5">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="#003087">
                                <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.607-.541c1.379 2.879.577 6.397-2.525 8.17-2.484 1.42-5.954 1.338-8.31.132l-.38 2.395c-.082.518.364.959.888.959H13.6c.524 0 .968-.382 1.05-.9l.894-5.67c.082-.518.526-.9 1.05-.9h.665c2.77 0 4.936-.69 5.983-3.645z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-base font-bold text-slate-800">Pagar com PayPal</p>
                            <p class="text-xs text-slate-400">Será redirecionado para o PayPal</p>
                        </div>
                    </div>
                    <div class="bg-sky-50 border border-sky-100 rounded-xl p-4">
                        <ul class="space-y-2.5">
                            @foreach(['Pagamento 100% seguro via PayPal', 'Após aprovação, o pedido é publicado automaticamente', 'Pode usar saldo PayPal ou cartão associado'] as $item)
                                <li class="flex items-center gap-2 text-xs text-sky-700">
                                    <svg class="w-3.5 h-3.5 text-sky-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    {{ $item }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif

                {{-- Botão de pagamento --}}
                @if(!in_array($payment_method, ['express', 'bank']))
                <button type="submit" wire:loading.attr="disabled"
                    class="w-full bg-gradient-to-r from-[#00c8ff] to-[#0055ff] hover:from-sky-400 hover:to-blue-600 disabled:opacity-60 text-white font-bold py-4 rounded-2xl transition-all shadow-md shadow-sky-200/40 flex items-center justify-center gap-2 text-base">
                    <span wire:loading.remove wire:target="confirmPayment">
                        @if($payment_method === 'paypal')
                            Continuar para PayPal
                            <svg class="w-4 h-4 inline ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        @else
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Pagar {{ number_format($valor_total, 0, ',', '.') }} Kz e publicar pedido
                        @endif
                    </span>
                    <span wire:loading wire:target="confirmPayment" class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        A processar...
                    </span>
                </button>
                @endif
            </form>

            {{-- EXPRESS (Multicaixa Express — telefone) --}}
            @if($payment_method === 'express')
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">

                @if($appypay_error)
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm">{{ $appypay_error }}</div>
                @endif

                @if($appypay_step === 'form')
                    <div class="flex items-center gap-4 mb-5">
                        <div class="w-12 h-12 rounded-2xl bg-orange-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div>
                            <p class="text-base font-bold text-slate-800">Multicaixa Express</p>
                            <p class="text-xs text-slate-400">Vai receber um pedido de aprovação no seu telemóvel</p>
                        </div>
                    </div>

                    <form wire:submit.prevent="chargeAppyPayPhone">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Número de telefone <span class="text-red-500">*</span></label>
                        <input type="tel" wire:model.defer="phone_number" maxlength="9"
                            class="w-full bg-white text-slate-800 border border-slate-200 rounded-xl px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-sky-200 focus:border-sky-400 outline-none transition @error('phone_number') border-red-400 @enderror"
                            placeholder="923456789">
                        @error('phone_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                        <button type="submit" wire:loading.attr="disabled" wire:target="chargeAppyPayPhone"
                            class="w-full mt-4 bg-gradient-to-r from-[#00c8ff] to-[#0055ff] hover:from-sky-400 hover:to-blue-600 disabled:opacity-60 text-white font-bold py-4 rounded-2xl transition-all shadow-md shadow-sky-200/40 flex items-center justify-center gap-2 text-base">
                            <span wire:loading.remove wire:target="chargeAppyPayPhone">Pagar {{ number_format($valor_total, 0, ',', '.') }} Kz via Express</span>
                            <span wire:loading wire:target="chargeAppyPayPhone" class="flex items-center gap-2">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                A processar...
                            </span>
                        </button>
                    </form>
                @endif

                @if($appypay_step === 'waiting')
                    <div wire:poll.3s="checkAppyPayStatus" class="flex flex-col items-center justify-center py-8 text-center gap-3">
                        <svg class="animate-spin w-10 h-10 text-sky-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <p class="text-base font-semibold text-slate-700">Aguarde a aprovação no seu telemóvel</p>
                        <p class="text-sm text-slate-400 max-w-xs">Abra a app Multicaixa Express e aprove o pedido de pagamento. Esta página actualiza-se automaticamente.</p>
                    </div>
                @endif
            </div>
            @endif

            {{-- BANCO (Referência de pagamento) --}}
            @if($payment_method === 'bank')
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">

                @if($appypay_error)
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm">{{ $appypay_error }}</div>
                @endif

                @if($appypay_step === 'form')
                    <div class="flex items-center gap-4 mb-5">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                        </div>
                        <div>
                            <p class="text-base font-bold text-slate-800">Referência de pagamento</p>
                            <p class="text-xs text-slate-400">Pague depois num ATM, Multicaixa ou Internet Banking</p>
                        </div>
                    </div>

                    <button type="button" wire:click="chargeAppyPayReference" wire:loading.attr="disabled" wire:target="chargeAppyPayReference"
                        class="w-full bg-gradient-to-r from-[#00c8ff] to-[#0055ff] hover:from-sky-400 hover:to-blue-600 disabled:opacity-60 text-white font-bold py-4 rounded-2xl transition-all shadow-md shadow-sky-200/40 flex items-center justify-center gap-2 text-base">
                        <span wire:loading.remove wire:target="chargeAppyPayReference">Gerar referência de {{ number_format($valor_total, 0, ',', '.') }} Kz</span>
                        <span wire:loading wire:target="chargeAppyPayReference" class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            A gerar...
                        </span>
                    </button>
                @endif

                @if($appypay_step === 'reference')
                    <div wire:poll.30s="checkAppyPayStatus" class="text-center py-4">
                        <p class="text-sm text-slate-500 mb-4">Use estes dados para pagar num ATM, Multicaixa ou Internet Banking:</p>
                        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Entidade</p>
                                <p class="text-lg font-bold text-slate-800 font-mono">{{ $appypay_entity }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Referência</p>
                                <p class="text-lg font-bold text-slate-800 font-mono">{{ $appypay_reference }}</p>
                            </div>
                            <div class="col-span-2 border-t border-slate-200 pt-3">
                                <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Valor</p>
                                <p class="text-lg font-bold text-sky-700">{{ number_format($valor_total, 0, ',', '.') }} Kz</p>
                            </div>
                        </div>
                        <p class="text-xs text-slate-400">Assim que o pagamento for confirmado, o pedido é publicado automaticamente. Pode fechar esta página — receberá uma notificação.</p>

                        @if(config('services.appypay.mode') === 'sandbox')
                        <button type="button" wire:click="mockConfirmAppyPayReference" wire:confirm="[Sandbox] Simular pagamento desta referência agora?"
                            class="mt-4 text-xs text-slate-400 underline">Simular confirmação (apenas sandbox)</button>
                        @endif
                    </div>
                @endif
            </div>
            @endif

            {{-- Selos de segurança --}}
            <div class="flex items-center justify-center gap-6">
                @foreach(['Pagamento seguro SSL', 'Dados encriptados', 'Escrow protegido'] as $badge)
                    <span class="flex items-center gap-1.5 text-[11px] text-slate-400">
                        <svg class="w-3 h-3 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        {{ $badge }}
                    </span>
                @endforeach
            </div>
        </div>

        {{-- ── Sidebar ── --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Resumo financeiro --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                    <p class="text-xs font-bold text-sky-700 uppercase tracking-wide">Resumo financeiro</p>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Valor do projecto</span>
                        <span class="text-sm font-semibold text-slate-800">{{ number_format($valor, 0, ',', '.') }} Kz</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Taxa plataforma</span>
                        <span class="text-sm font-semibold text-orange-500">− {{ number_format($taxa, 0, ',', '.') }} Kz</span>
                    </div>
                    <div class="border-t border-slate-100 pt-3 flex justify-between items-center">
                        <span class="text-sm font-bold text-slate-700">Total a pagar</span>
                        <span class="text-lg font-bold text-sky-700">{{ number_format($valor_total, 0, ',', '.') }} Kz</span>
                    </div>
                    <div class="bg-emerald-50 border border-emerald-100 rounded-xl px-3 py-2 flex justify-between items-center">
                        <span class="text-xs text-emerald-700">Freelancer recebe</span>
                        <span class="text-sm font-bold text-emerald-600">{{ number_format($valor_liquido, 0, ',', '.') }} Kz</span>
                    </div>
                </div>
            </div>

            {{-- Resumo do pedido --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Pedido</p>
                </div>
                @php
                    $order     = session('client_order', []);
                    $serviceId = $order['service_id'] ?? request()->route('service');
                    $svc       = $serviceId ? \App\Models\Service::find($serviceId) : null;
                    $titulo    = $svc->titulo ?? $order['title'] ?? null;
                    $briefing  = \Illuminate\Support\Str::limit($svc->briefing ?? $order['briefing_text'] ?? null, 150);
                @endphp
                @if($titulo)
                    <div class="mb-3">
                        <p class="text-[11px] text-slate-400 uppercase tracking-wide mb-0.5">Título</p>
                        <p class="text-sm font-semibold text-slate-800">{{ $titulo }}</p>
                    </div>
                @endif
                @if($briefing)
                    <div>
                        <p class="text-[11px] text-slate-400 uppercase tracking-wide mb-0.5">Descrição</p>
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $briefing }}</p>
                    </div>
                @endif
                @if(!$titulo && !$briefing)
                    <p class="text-xs text-slate-400 italic">Dados do pedido não disponíveis.</p>
                @endif
            </div>

            {{-- Garantia de reembolso --}}
            <div class="bg-amber-50 border border-amber-100 rounded-2xl p-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    <p class="text-xs font-bold text-amber-700 uppercase tracking-wide">Garantia de reembolso</p>
                </div>
                <p class="text-xs text-amber-700 leading-relaxed">
                    O valor fica em escrow e só é transferido para o freelancer após confirmar a entrega. Pode solicitar reembolso em caso de insatisfação.
                </p>
            </div>
        </div>
    </div>

</div>
