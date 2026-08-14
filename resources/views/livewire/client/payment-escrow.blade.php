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

                @if($appypay_error)
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm">{{ $appypay_error }}</div>
                @endif

                @if($appypay_step === 'form')
                    <div class="flex items-center gap-4 mb-5">
                        <img src="{{ asset('img/payment/multicaixa-express.jpg') }}" alt="Multicaixa Express"
                            class="w-12 h-12 rounded-2xl object-cover flex-shrink-0 shadow-sm">
                        <div>
                            <p class="text-base font-bold text-slate-800">Pagamento via Multicaixa Express</p>
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
