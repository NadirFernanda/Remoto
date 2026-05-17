<div class="max-w-4xl mx-auto">

    {{-- ─── Progress bar ───────────────────────────────────────────── --}}
    <div class="flex items-center gap-2 mb-8">
        @foreach([1 => 'Briefing', 2 => 'Investimento', 3 => 'Pagamento'] as $n => $label)
            <div class="flex items-center {{ $loop->last ? '' : 'flex-1' }}">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-all
                        {{ $n < 3 ? 'bg-[#00baff] text-white' : 'bg-[#00baff] text-white' }}">
                        @if($n < 3)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            {{ $n }}
                        @endif
                    </div>
                    <span class="text-sm font-medium text-gray-800">{{ $label }}</span>
                </div>
                @if(!$loop->last)
                    <div class="flex-1 h-0.5 mx-3 bg-[#00baff]"></div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- ─── Alertas de sessão ──────────────────────────────────────── --}}
    @if(session('error'))
        <div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="mb-5 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ─── Left: payment form ─────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Selecção do método --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-1">Método de pagamento</h2>
                <p class="text-sm text-gray-400 mb-5">Escolha como pretende efectuar o pagamento</p>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-0">
                    @php
                        $methods = [
                            'card'   => ['label' => 'Cartão', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>'],
                            'paypal' => ['label' => 'PayPal',  'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
                            'express'=> ['label' => 'Express', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>'],
                            'bank'   => ['label' => 'Banco',   'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>'],
                        ];
                    @endphp
                    @foreach($methods as $key => $method)
                        <button type="button" wire:click="$set('payment_method', '{{ $key }}')"
                                class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 transition-all
                                    {{ $payment_method === $key
                                        ? 'border-[#00baff] bg-[#e8f9ff] text-[#00baff]'
                                        : 'border-gray-100 bg-gray-50 text-gray-400 hover:border-[#00baff] hover:bg-[#f0fbff] hover:text-[#00baff]' }}">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                {!! $method['icon'] !!}
                            </svg>
                            <span class="text-xs font-semibold">{{ $method['label'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Formulário por método --}}
            <form wire:submit.prevent="confirmPayment">

                {{-- CARTÃO ──────────────────────────────────────── --}}
                @if($payment_method === 'card')
                <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-4 h-4 text-[#00baff]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <h3 class="text-sm font-bold text-gray-700">Dados do cartão</h3>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Nome no cartão <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model.defer="card_name"
                               class="w-full bg-white text-gray-800 border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#00baff] focus:border-transparent outline-none transition @error('card_name') border-red-400 @enderror"
                               placeholder="Como está impresso no cartão"
                               autocomplete="cc-name">
                        @error('card_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Número do cartão <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" wire:model.defer="card_number" maxlength="19"
                                   class="w-full bg-white text-gray-800 border border-gray-300 rounded-xl px-4 py-3 text-sm font-mono tracking-widest focus:ring-2 focus:ring-[#00baff] focus:border-transparent outline-none transition @error('card_number') border-red-400 @enderror"
                                   placeholder="0000 0000 0000 0000"
                                   autocomplete="cc-number">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 flex gap-1">
                                <span class="text-[10px] font-bold text-blue-700 bg-blue-50 border border-blue-100 px-1.5 py-0.5 rounded">VISA</span>
                                <span class="text-[10px] font-bold text-red-600 bg-red-50 border border-red-100 px-1.5 py-0.5 rounded">MC</span>
                            </div>
                        </div>
                        @error('card_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Validade <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model.defer="card_expiry" maxlength="5"
                                   class="w-full bg-white text-gray-800 border border-gray-300 rounded-xl px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-[#00baff] focus:border-transparent outline-none transition @error('card_expiry') border-red-400 @enderror"
                                   placeholder="MM/AA"
                                   autocomplete="cc-exp">
                            @error('card_expiry') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                CVV <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" wire:model.defer="card_cvv" maxlength="4"
                                       class="w-full bg-white text-gray-800 border border-gray-300 rounded-xl px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-[#00baff] focus:border-transparent outline-none transition @error('card_cvv') border-red-400 @enderror"
                                       placeholder="123"
                                       autocomplete="cc-csc">
                                <div class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-300">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            @error('card_cvv') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    @error('payment_token')
                        <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-xs">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                @endif

                {{-- PAYPAL ──────────────────────────────────────── --}}
                @if($payment_method === 'paypal')
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="#003087">
                                <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.607-.541c1.379 2.879.577 6.397-2.525 8.17-2.484 1.42-5.954 1.338-8.31.132l-.38 2.395c-.082.518.364.959.888.959H13.6c.524 0 .968-.382 1.05-.9l.894-5.67c.082-.518.526-.9 1.05-.9h.665c2.77 0 4.936-.69 5.983-3.645z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-base font-bold text-gray-800">Pagar com PayPal</p>
                            <p class="text-xs text-gray-400">Será redirecionado para o PayPal</p>
                        </div>
                    </div>
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-4">
                        <ul class="space-y-2">
                            @foreach(['Pagamento 100% seguro via PayPal', 'Após aprovação, o pedido é publicado automaticamente', 'Pode usar saldo PayPal ou cartão associado'] as $item)
                                <li class="flex items-center gap-2 text-xs text-blue-700">
                                    <svg class="w-3.5 h-3.5 text-blue-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    {{ $item }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif

                {{-- EXPRESS / BANCO ─────────────────────────────── --}}
                @if(in_array($payment_method, ['express', 'bank']))
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <div class="flex flex-col items-center justify-center py-6 text-center gap-3">
                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-base font-semibold text-gray-600">Em breve</p>
                        <p class="text-sm text-gray-400 max-w-xs">
                            {{ $payment_method === 'express' ? 'O pagamento via Express estará disponível em breve.' : 'A transferência bancária estará disponível em breve.' }}
                            Escolha cartão ou PayPal para continuar.
                        </p>
                    </div>
                </div>
                @endif

                {{-- Botão de submissão --}}
                @if(!in_array($payment_method, ['express', 'bank']))
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="w-full bg-[#00baff] hover:bg-cyan-500 disabled:opacity-60 text-white font-bold py-4 rounded-2xl transition-all shadow-sm flex items-center justify-center gap-2 text-base">
                    <span wire:loading.remove wire:target="confirmPayment">
                        @if($payment_method === 'paypal')
                            Continuar para PayPal
                            <svg class="w-4 h-4 inline ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        @else
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Pagar {{ number_format($valor_total, 0, ',', '.') }} Kz e publicar pedido
                        @endif
                    </span>
                    <span wire:loading wire:target="confirmPayment" class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        A processar...
                    </span>
                </button>
                @endif

            </form>

            {{-- Segurança --}}
            <div class="flex items-center justify-center gap-6 pt-1">
                @foreach(['Pagamento seguro SSL', 'Dados encriptados', 'Escrow protegido'] as $badge)
                    <span class="flex items-center gap-1.5 text-[11px] text-gray-400">
                        <svg class="w-3 h-3 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        {{ $badge }}
                    </span>
                @endforeach
            </div>
        </div>

        {{-- ─── Right: order summary ───────────────────────────────── --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Resumo financeiro --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-xs font-bold text-[#00baff] uppercase tracking-wide mb-4">Resumo financeiro</p>

                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Valor do projecto</span>
                        <span class="text-sm font-semibold text-gray-800">{{ number_format($valor, 0, ',', '.') }} Kz</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Taxa plataforma</span>
                        <span class="text-sm font-semibold text-orange-500">− {{ number_format($taxa, 0, ',', '.') }} Kz</span>
                    </div>
                    <div class="border-t border-gray-100 pt-3 flex justify-between items-center">
                        <span class="text-sm font-bold text-gray-700">Total a pagar</span>
                        <span class="text-lg font-bold text-[#00baff]">{{ number_format($valor_total, 0, ',', '.') }} Kz</span>
                    </div>
                    <div class="bg-green-50 border border-green-100 rounded-xl px-3 py-2 flex justify-between items-center">
                        <span class="text-xs text-green-700">Freelancer recebe</span>
                        <span class="text-sm font-bold text-green-600">{{ number_format($valor_liquido, 0, ',', '.') }} Kz</span>
                    </div>
                </div>
            </div>

            {{-- Resumo do pedido --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">Pedido</p>
                @php
                    $order     = session('client_order', []);
                    $serviceId = $order['service_id'] ?? request()->route('service');
                    $svc       = $serviceId ? \App\Models\Service::find($serviceId) : null;
                    $titulo    = $svc->titulo ?? $order['title'] ?? null;
                    $briefing  = \Illuminate\Support\Str::limit($svc->briefing ?? $order['briefing_text'] ?? null, 150);
                @endphp

                @if($titulo)
                    <div class="mb-3">
                        <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-0.5">Título</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $titulo }}</p>
                    </div>
                @endif

                @if($briefing)
                    <div>
                        <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-0.5">Descrição</p>
                        <p class="text-xs text-gray-600 leading-relaxed">{{ $briefing }}</p>
                    </div>
                @endif

                @if(!$titulo && !$briefing)
                    <p class="text-xs text-gray-400 italic">Dados do pedido não disponíveis.</p>
                @endif
            </div>

            {{-- Política de reembolso --}}
            <div class="bg-amber-50 border border-amber-100 rounded-2xl p-4">
                <p class="text-xs font-bold text-amber-700 uppercase tracking-wide mb-2">Garantia de reembolso</p>
                <p class="text-xs text-amber-700 leading-relaxed">
                    O valor fica em escrow e só é transferido para o freelancer após confirmar a entrega. Pode solicitar reembolso em caso de insatisfação.
                </p>
            </div>

        </div>
    </div>

</div>
