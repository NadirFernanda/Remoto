<div class="max-w-4xl mx-auto">

    {{-- ─── Progress bar ───────────────────────────────────────────── --}}
    <div class="flex items-center gap-2 mb-8">
        @foreach([1 => 'Briefing', 2 => 'Investimento', 3 => 'Pagamento'] as $n => $label)
            <div class="flex items-center {{ $loop->last ? '' : 'flex-1' }}">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-all
                        {{ $n < 2 ? 'bg-[#00baff] text-white' : ($n === 2 ? 'bg-[#00baff] text-white' : 'bg-gray-200 text-gray-500') }}">
                        @if($n < 2)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            {{ $n }}
                        @endif
                    </div>
                    <span class="text-sm font-medium {{ $n <= 2 ? 'text-gray-800' : 'text-gray-400' }}">{{ $label }}</span>
                </div>
                @if(!$loop->last)
                    <div class="flex-1 h-0.5 mx-3 {{ $n < 2 ? 'bg-[#00baff]' : 'bg-gray-200' }}"></div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- ─── Flash errors ────────────────────────────────────────────── --}}
    @if(session()->has('error'))
        <div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ─── Left: value form ───────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Main card --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-1">Quanto deseja investir?</h2>
                <p class="text-sm text-gray-400 mb-6">Defina o orçamento total do projecto. O freelancer receberá o valor líquido após a taxa da plataforma.</p>

                <form wire:submit.prevent="submitValue">

                    {{-- Input de valor --}}
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Valor do projecto <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                <span class="text-gray-400 text-sm font-semibold">Kz</span>
                            </div>
                            <input
                                type="number"
                                wire:model.live.debounce.300ms="valor"
                                min="10000"
                                step="500"
                                class="w-full bg-white text-gray-800 border border-gray-300 rounded-xl pl-10 pr-4 py-3.5 text-base font-semibold focus:ring-2 focus:ring-[#00baff] focus:border-transparent outline-none transition @error('valor') border-red-400 bg-red-50 @enderror"
                                placeholder="10.000"
                            >
                        </div>
                        @error('valor')
                            <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="text-xs text-gray-400 mt-1.5">Mínimo: 10.000 Kz</p>
                    </div>

                    {{-- Breakdown de taxas --}}
                    <div class="bg-gray-50 rounded-2xl border border-gray-100 p-5 mb-6">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">Decomposição do valor</p>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3.5 h-3.5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-700 font-medium">Valor do projecto</p>
                                        <p class="text-[11px] text-gray-400">Total que paga</p>
                                    </div>
                                </div>
                                <span class="text-sm font-bold text-gray-800">
                                    {{ number_format($valor, 0, ',', '.') }} Kz
                                </span>
                            </div>

                            <div class="border-t border-gray-200"></div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-orange-50 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3.5 h-3.5 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-700 font-medium">Taxa da plataforma ({{ $taxa }}%)</p>
                                        <p class="text-[11px] text-gray-400">Serviço de intermediação</p>
                                    </div>
                                </div>
                                <span class="text-sm font-bold text-orange-500">
                                    − {{ number_format($valor * $taxa / 100, 0, ',', '.') }} Kz
                                </span>
                            </div>

                            <div class="border-t border-gray-200"></div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-green-50 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3.5 h-3.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-700 font-medium">Freelancer recebe</p>
                                        <p class="text-[11px] text-gray-400">Valor líquido após taxa</p>
                                    </div>
                                </div>
                                <span class="text-base font-bold text-green-600">
                                    {{ number_format($valor_liquido, 0, ',', '.') }} Kz
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Info box --}}
                    <div class="bg-[#e8f9ff] border border-[#b3ecff] rounded-2xl px-4 py-3 flex gap-3 items-start mb-6">
                        <svg class="w-4 h-4 text-[#00baff] mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs text-[#0077aa]">
                            O valor é retido em garantia (escrow) e só é transferido para o freelancer após a entrega ser aprovada por si. Pode solicitar reembolso em caso de insatisfação.
                        </p>
                    </div>

                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="w-full bg-[#00baff] hover:bg-cyan-500 disabled:opacity-60 text-white font-bold py-3.5 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2">
                        <span wire:loading.remove>
                            Continuar para pagamento
                            <svg class="w-4 h-4 inline ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            A processar...
                        </span>
                    </button>

                </form>
            </div>
        </div>

        {{-- ─── Right: order summary ───────────────────────────────── --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Resumo do pedido --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-xs font-bold text-[#00baff] uppercase tracking-wide mb-4">Resumo do pedido</p>

                @php
                    $order = session('client_order', []);
                    $b     = $order['briefing_raw'] ?? [];
                @endphp

                @if(!empty($order['title']))
                    <div class="mb-3">
                        <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-0.5">Título</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $order['title'] }}</p>
                    </div>
                @endif

                @if(!empty($b['business_type']))
                    <div class="mb-3">
                        <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-0.5">Tipo de serviço</p>
                        <p class="text-sm text-gray-700">{{ $b['business_type'] }}</p>
                    </div>
                @endif

                @if(!empty($b['necessity']))
                    <div>
                        <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-0.5">Descrição</p>
                        <p class="text-xs text-gray-600 leading-relaxed line-clamp-4">{{ $b['necessity'] }}</p>
                    </div>
                @endif

                @if(empty($order['title']) && empty($b['business_type']))
                    <p class="text-xs text-gray-400 italic">Nenhum pedido em sessão.</p>
                @endif
            </div>

            {{-- Garantia --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Protecção garantida</p>
                <div class="space-y-2.5">
                    @foreach([
                        ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'label' => 'Pagamento em escrow seguro'],
                        ['icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'label' => 'Reembolso disponível'],
                        ['icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z', 'label' => 'Suporte 24 horas'],
                    ] as $item)
                        <div class="flex items-center gap-2.5">
                            <div class="w-6 h-6 rounded-full bg-green-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                                </svg>
                            </div>
                            <span class="text-xs text-gray-600">{{ $item['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

</div>
