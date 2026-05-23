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
                <h1 class="text-xl font-bold leading-tight">Definir Investimento</h1>
                <p class="text-sm text-white/80 mt-0.5">Indique o orçamento para o seu projecto</p>
            </div>
        </div>
    </div>

    {{-- ── Progress bar ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm px-6 py-5 mb-8">
        <div class="flex items-center gap-2">
            @foreach([1 => 'Briefing', 2 => 'Investimento', 3 => 'Pagamento'] as $n => $label)
                <div class="flex items-center {{ $loop->last ? '' : 'flex-1' }}">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold shadow-sm transition-all
                            {{ $n < 2  ? 'bg-gradient-to-br from-emerald-400 to-emerald-500 text-white' :
                               ($n === 2 ? 'bg-gradient-to-br from-[#00c8ff] to-[#0055ff] text-white shadow-sky-200/60' :
                                          'bg-slate-100 text-slate-400') }}">
                            @if($n < 2)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            @else
                                {{ $n }}
                            @endif
                        </div>
                        <span class="text-sm font-semibold {{ $n <= 2 ? 'text-slate-800' : 'text-slate-400' }} hidden sm:inline">{{ $label }}</span>
                    </div>
                    @if(!$loop->last)
                        <div class="flex-1 h-1 mx-3 rounded-full {{ $n < 2 ? 'bg-gradient-to-r from-[#00c8ff] to-[#0055ff]' : 'bg-slate-100' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── Alertas ── --}}
    @if(session()->has('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Coluna principal ── --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <h2 class="text-lg font-bold text-slate-800 mb-1">Quanto deseja investir?</h2>
                <p class="text-sm text-slate-500 mb-6">Defina o orçamento total. O freelancer recebe o valor líquido após a taxa da plataforma.</p>

                <form wire:submit.prevent="submitValue">

                    {{-- Input de valor --}}
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Valor do projecto <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                <span class="text-slate-400 text-sm font-semibold">Kz</span>
                            </div>
                            <input type="number" wire:model.live.debounce.300ms="valor" min="10000" step="500"
                                class="w-full bg-white text-slate-800 border border-slate-200 rounded-xl pl-10 pr-4 py-3.5 text-lg font-bold focus:ring-2 focus:ring-sky-200 focus:border-sky-400 outline-none transition @error('valor') border-red-400 bg-red-50 @enderror"
                                placeholder="10.000">
                        </div>
                        @error('valor')
                            <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="text-xs text-slate-400 mt-1.5">Mínimo: 10.000 Kz</p>
                    </div>

                    {{-- Breakdown --}}
                    <div class="rounded-2xl bg-slate-50 border border-slate-100 p-5 mb-6 space-y-4">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Decomposição do valor</p>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-sky-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-700 font-medium">Valor do projecto</p>
                                    <p class="text-xs text-slate-400">Total que paga</p>
                                </div>
                            </div>
                            <span class="text-sm font-bold text-slate-800">{{ number_format($valor, 0, ',', '.') }} Kz</span>
                        </div>

                        <div class="border-t border-slate-200"></div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-700 font-medium">Taxa da plataforma ({{ $taxa }}%)</p>
                                    <p class="text-xs text-slate-400">Serviço de intermediação</p>
                                </div>
                            </div>
                            <span class="text-sm font-bold text-orange-500">− {{ number_format($valor * $taxa / 100, 0, ',', '.') }} Kz</span>
                        </div>

                        <div class="border-t border-slate-200"></div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-700 font-medium">Freelancer recebe</p>
                                    <p class="text-xs text-slate-400">Valor líquido após taxa</p>
                                </div>
                            </div>
                            <span class="text-base font-bold text-emerald-600">{{ number_format($valor_liquido, 0, ',', '.') }} Kz</span>
                        </div>
                    </div>

                    {{-- Info box --}}
                    <div class="bg-sky-50 border border-sky-200 rounded-2xl px-4 py-3 flex gap-3 items-start mb-6">
                        <svg class="w-4 h-4 text-sky-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs text-sky-800 leading-relaxed">O valor é retido em garantia (escrow) e só é transferido para o freelancer após a entrega ser aprovada por si. Pode solicitar reembolso em caso de insatisfação.</p>
                    </div>

                    <button type="submit" wire:loading.attr="disabled"
                        class="w-full bg-gradient-to-r from-[#00c8ff] to-[#0055ff] hover:from-sky-400 hover:to-blue-600 disabled:opacity-60 text-white font-bold py-4 rounded-2xl transition-all shadow-md shadow-sky-200/40 flex items-center justify-center gap-2 text-base">
                        <span wire:loading.remove>
                            Continuar para pagamento
                            <svg class="w-4 h-4 inline ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            A processar...
                        </span>
                    </button>
                </form>
            </div>
        </div>

        {{-- ── Sidebar ── --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Resumo do pedido --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                    <p class="text-xs font-bold text-sky-700 uppercase tracking-wide">Resumo do pedido</p>
                </div>
                @php
                    $order = session('client_order', []);
                    $b     = $order['briefing_raw'] ?? [];
                @endphp
                @if(!empty($order['title']))
                    <div class="mb-3">
                        <p class="text-[11px] text-slate-400 uppercase tracking-wide mb-0.5">Título</p>
                        <p class="text-sm font-semibold text-slate-800">{{ $order['title'] }}</p>
                    </div>
                @endif
                @if(!empty($b['business_type']))
                    <div class="mb-3">
                        <p class="text-[11px] text-slate-400 uppercase tracking-wide mb-0.5">Tipo de serviço</p>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-sky-50 text-sky-700 text-xs font-semibold">{{ $b['business_type'] }}</span>
                    </div>
                @endif
                @if(!empty($b['necessity']))
                    <div>
                        <p class="text-[11px] text-slate-400 uppercase tracking-wide mb-0.5">Descrição</p>
                        <p class="text-xs text-slate-600 leading-relaxed line-clamp-4">{{ $b['necessity'] }}</p>
                    </div>
                @endif
                @if(empty($order['title']) && empty($b['business_type']))
                    <p class="text-xs text-slate-400 italic">Nenhum pedido em sessão.</p>
                @endif
            </div>

            {{-- Protecção --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Protecção garantida</p>
                </div>
                <div class="space-y-3">
                    @foreach([
                        ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'label' => 'Pagamento em escrow seguro'],
                        ['icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'label' => 'Reembolso disponível'],
                        ['icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z', 'label' => 'Suporte 24 horas'],
                    ] as $item)
                        <div class="flex items-center gap-2.5">
                            <div class="w-6 h-6 rounded-full bg-emerald-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/></svg>
                            </div>
                            <span class="text-xs text-slate-600">{{ $item['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>
