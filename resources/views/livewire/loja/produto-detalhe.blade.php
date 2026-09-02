<div class="space-y-5">

    {{-- Gradient header --}}
    <div class="bg-gradient-to-r from-[#00c8ff] to-[#0033cc] rounded-2xl p-6 text-white flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <a href="{{ route('loja.index') }}"
                   class="inline-flex items-center gap-1 text-xs text-white/70 hover:text-white transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Loja
                </a>
                <span class="text-white/40 text-xs">/</span>
                <span class="text-xs text-white/70 truncate max-w-[200px]">{{ $produto->titulo }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold leading-tight truncate">{{ $produto->titulo }}</h1>
            <p class="text-sm text-white/75 mt-1">{{ $produto->tipoLabel() }}
                @if($patrocinado)
                    &nbsp;·&nbsp;
                    <span class="inline-flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        Produto Patrocinado
                    </span>
                @endif
            </p>
        </div>
        <div class="text-right flex-shrink-0">
            <div class="text-3xl font-extrabold">Kz {{ number_format($produto->preco, 0, ',', '.') }}</div>
            @if($produto->vendas_count > 0)
            <div class="text-xs text-white/60 mt-0.5">{{ $produto->vendas_count }} venda(s)</div>
            @endif
        </div>
    </div>

    {{-- Feedback --}}
    @if(session('success_loja'))
        <div class="px-4 py-3 rounded-xl bg-green-100 text-green-700 text-sm font-medium">{{ session('success_loja') }}</div>
    @endif
    @if(session('error_loja'))
        <div class="px-4 py-3 rounded-xl bg-red-100 text-red-700 text-sm font-medium flex items-center justify-between gap-3">
            <span>{{ session('error_loja') }}</span>
            @if(str_contains(session('error_loja'), 'Saldo insuficiente'))
                <a href="{{ route('wallet.topup') }}" class="whitespace-nowrap font-semibold underline">Recarregar carteira</a>
            @endif
        </div>
    @endif

    {{-- Main card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-start">

            {{-- Cover image --}}
            <div class="md:w-[22rem] h-72 md:h-[26rem] flex-shrink-0" style="background: linear-gradient(135deg, #00c8ff 0%, #0055ff 60%, #0033cc 100%)">
                @if($produto->capa_path)
                    <x-image-lightbox :src="\Illuminate\Support\Facades\Storage::disk('public')->url($produto->capa_path)" :alt="$produto->titulo" trigger-class="block w-full h-full">
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($produto->capa_path) }}"
                            alt="{{ $produto->titulo }}"
                            class="w-full h-full object-cover">
                    </x-image-lightbox>
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-24 h-24 text-white/30" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $produto->tipoIcon() }}"/>
                        </svg>
                    </div>
                @endif
            </div>

            {{-- Details --}}
            <div class="flex-1 p-6 md:p-8 md:sticky md:top-4">

                {{-- Freelancer info --}}
                <div class="flex items-center gap-2 mb-5">
                    <img src="{{ $produto->freelancer->avatarUrl() }}"
                        class="w-9 h-9 rounded-full object-cover ring-2 ring-[#0055ff]/20"
                        onerror="this.src='/img/default-avatar.svg'">
                    <div>
                        <a href="{{ route('freelancer.show', $produto->freelancer) }}"
                            class="text-sm font-semibold text-gray-800 hover:text-[#0055ff] transition">
                            {{ $produto->freelancer->name }}
                        </a>
                        <p class="text-xs text-gray-400">Freelancer</p>
                    </div>
                </div>

                {{-- Trust badges --}}
                <div class="flex flex-wrap gap-3 mb-5 text-xs text-gray-500">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download imediato
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Pagamento seguro pela carteira
                    </span>
                </div>

                {{-- Purchase area --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-6 p-4 bg-gray-50 rounded-xl">
                    @auth
                        @if($jaComprado)
                        <div class="flex flex-col sm:flex-row gap-2 w-full">
                            <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-green-100 text-green-700 rounded-xl text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Produto adquirido
                            </span>
                            <button wire:click="downloadArquivo"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-white rounded-xl text-sm font-semibold transition"
                                style="background: linear-gradient(135deg, #00c8ff 0%, #0055ff 60%, #0033cc 100%)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Fazer Download
                            </button>
                        </div>
                        @elseif($produto->freelancer_id === auth()->id())
                        <span class="text-sm text-gray-400 italic">Este é o seu produto</span>
                        @else
                        <div class="w-full">
                            <a href="{{ route('loja.purchase', $produto->slug) }}"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 text-white rounded-xl font-semibold text-sm transition shadow hover:shadow-lg"
                                style="background: linear-gradient(135deg, #00c8ff 0%, #0055ff 60%, #0033cc 100%)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                Comprar agora
                            </a>
                        </div>
                        @endif
                    @else
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-white rounded-xl font-semibold text-sm transition shadow"
                        style="background: linear-gradient(135deg, #00c8ff 0%, #0055ff 60%, #0033cc 100%)">
                        Entrar para comprar
                    </a>
                    @endauth
                </div>

                @auth
                @if($produto->freelancer_id === auth()->id())
                <div class="text-xs text-gray-400 mb-6">
                    20% do valor retido como comissão da plataforma. O freelancer recebe 80%.
                </div>
                @endif
                @endauth

                {{-- Share buttons --}}
                <div>
                    <p class="text-sm font-medium text-gray-700 mb-2">Partilhar produto:</p>
                    <div class="flex flex-wrap gap-2">
                        <button x-data
                            x-on:click='navigator.clipboard.writeText("{{ route("loja.show", $produto->slug) }}"); $el.textContent = "Link copiado!"; setTimeout(()=>$el.innerHTML = `<svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z\"/></svg> Copiar Link`, 2000)'
                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            Copiar Link
                        </button>
                        <a href="https://wa.me/?text={{ urlencode($produto->titulo . ' — ' . route('loja.show', $produto->slug)) }}" target="_blank"
                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-green-200 text-sm text-green-700 hover:bg-green-50 transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            WhatsApp
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('loja.show', $produto->slug)) }}" target="_blank"
                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-blue-200 text-sm text-blue-700 hover:bg-blue-50 transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            Facebook
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Description --}}
        <div class="px-6 md:px-8 pb-8 border-t border-gray-100">
            <h2 class="font-bold text-gray-800 text-lg mt-6 mb-3">Sobre este produto</h2>
            <div class="text-gray-600 text-sm whitespace-pre-line leading-relaxed">{{ $produto->descricao }}</div>
        </div>
    </div>

    {{-- ── Relacionados ─────────────────────────────────────────────── --}}
    @if($relacionados->isNotEmpty())
    <div>
        <h3 class="text-white font-bold text-base mb-3">Também pode gostar</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($relacionados as $rel)
            <a href="{{ route('loja.show', $rel->slug) }}"
                class="group bg-white rounded-2xl border border-gray-200 hover:border-[#0055ff]/50 hover:shadow-lg hover:-translate-y-1 transition-all overflow-hidden">
                <div class="relative h-32 overflow-hidden" style="background: linear-gradient(135deg, {{ $rel->tipoColor() }}18, {{ $rel->tipoColor() }}35);">
                    @if($rel->capa_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($rel->capa_path) }}"
                            alt="{{ $rel->titulo }}" loading="lazy" decoding="async" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-10 h-10" style="color: {{ $rel->tipoColor() }}66;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $rel->tipoIcon() }}"/></svg>
                        </div>
                    @endif
                </div>
                <div class="p-3">
                    <h4 class="font-semibold text-gray-900 text-xs line-clamp-2 mb-1.5 group-hover:text-[#0055ff] transition-colors">{{ $rel->titulo }}</h4>
                    <span class="text-sm font-bold text-gray-900">Kz {{ number_format($rel->preco, 0, ',', '.') }}</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

</div>
