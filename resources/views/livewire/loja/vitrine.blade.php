<div class="space-y-8">

    {{-- Hero --}}
    <div class="relative overflow-hidden rounded-2xl p-7 sm:p-9 text-white" style="background: linear-gradient(135deg, #00c8ff 0%, #0055ff 55%, #0033cc 100%);">
        <div class="absolute -right-10 -top-10 w-56 h-56 rounded-full bg-white/10"></div>
        <div class="absolute right-16 bottom-[-3rem] w-40 h-40 rounded-full bg-white/10"></div>
        <div class="relative">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/15 backdrop-blur-sm mb-3">
                🛍️ Loja Oficial
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold">Loja de Infoprodutos</h2>
            <p class="text-sm text-white/80 mt-1.5 max-w-md">E-books, áudios, literatura digital e muito mais, feitos pelos melhores freelancers da plataforma.</p>

            <div class="max-w-lg mt-5">
                <div class="relative">
                    <svg class="absolute left-3.5 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input wire:model.live.debounce.400ms="busca" type="text"
                        class="w-full pl-11 pr-4 py-3.5 rounded-xl text-sm text-gray-800 shadow-lg focus:outline-none focus:ring-2 focus:ring-white/60"
                        placeholder="Buscar e-books, áudios, literatura...">
                </div>
            </div>
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

    {{-- ── Mais vendidos ────────────────────────────────────────────── --}}
    @if($maisVendidos->isNotEmpty())
    <div>
        <div class="flex items-center gap-2 mb-3">
            <span class="text-lg">🔥</span>
            <h3 class="text-white font-bold text-base">Mais vendidos</h3>
        </div>
        <div class="flex gap-4 overflow-x-auto pb-2 -mx-1 px-1" style="scrollbar-width:thin;">
            @foreach($maisVendidos as $produto)
            <a href="{{ route('loja.show', $produto->slug) }}"
                class="group bg-white rounded-2xl border border-gray-200 hover:border-[#0055ff]/50 hover:shadow-lg hover:-translate-y-1 transition-all flex-shrink-0 w-44 overflow-hidden">
                <div class="relative h-32 overflow-hidden" style="background: linear-gradient(135deg, {{ $produto->tipoColor() }}18, {{ $produto->tipoColor() }}35);">
                    @if($produto->capa_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($produto->capa_path) }}"
                            alt="{{ $produto->titulo }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-10 h-10" style="color: {{ $produto->tipoColor() }}66;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $produto->tipoIcon() }}"/></svg>
                        </div>
                    @endif
                    <span class="absolute top-1.5 left-1.5 inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-[#0055ff] text-white shadow">
                        #{{ $loop->iteration }} mais vendido
                    </span>
                </div>
                <div class="p-3">
                    <h4 class="font-semibold text-gray-900 text-xs line-clamp-2 mb-1.5 group-hover:text-[#0055ff] transition-colors">{{ $produto->titulo }}</h4>
                    <span class="text-sm font-bold text-gray-900">Kz {{ number_format($produto->preco, 0, ',', '.') }}</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <div>
        {{-- ── Category pills + sort ───────────────────────────────────── --}}
        <div class="flex flex-wrap items-center gap-2 mb-6">
            @php
                $tipos = ['' => 'Todos', 'ebook' => 'E-book', 'audio' => 'Áudio', 'literatura_digital' => 'Literatura Digital', 'outro' => 'Outro'];
            @endphp
            @foreach($tipos as $value => $label)
                <button type="button" wire:click="$set('tipo', '{{ $value }}')"
                    class="px-4 py-2 rounded-full text-sm font-semibold transition border
                        {{ $tipo === $value ? 'bg-[#0055ff] text-white border-[#0055ff] shadow' : 'bg-white text-gray-600 border-gray-200 hover:border-[#0055ff]/50 hover:text-[#0055ff]' }}">
                    {{ $label }}
                </button>
            @endforeach

            <div class="ml-auto flex items-center gap-2">
                <select wire:model.live="ordenar" class="border border-gray-200 bg-white rounded-full px-3.5 py-2 text-sm text-gray-600 focus:ring-1 focus:ring-[#0055ff] focus:outline-none">
                    <option value="recente">Mais recentes</option>
                    <option value="mais_vendidos">Mais vendidos</option>
                    <option value="preco_asc">Menor preço</option>
                    <option value="preco_desc">Maior preço</option>
                </select>
                <span class="text-sm text-white/60 whitespace-nowrap">{{ $produtos->total() }} produto(s)</span>
            </div>
        </div>

        {{-- ── Products grid ───────────────────────────────────────────── --}}
        @if($produtos->isEmpty())
        <div class="text-center py-20 bg-white rounded-2xl border border-gray-200">
            <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <p class="text-gray-500 font-medium">Nenhum produto encontrado</p>
            <p class="text-sm text-gray-400 mt-1">Tente outros termos ou categorias</p>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($produtos as $produto)
            @php $isPatrocinado = $produto->patrocinado ?? $produto->isPatrocinado(); @endphp
            <div wire:key="produto-{{ $produto->id }}"
                style="animation: loja-card-in .45s ease-out backwards; animation-delay: {{ min($loop->index * 45, 300) }}ms; {{ $isPatrocinado ? 'background: linear-gradient(160deg, #0055ff 0%, #0033cc 100%);' : '' }}"
                class="group relative rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl hover:shadow-[#0055ff]/20 hover:-translate-y-1.5 transition-all duration-300 flex flex-col
                    {{ $isPatrocinado ? 'text-white' : 'bg-white border border-gray-200/70 text-gray-900 hover:border-[#0055ff]/40' }}">

                @if($isPatrocinado)
                <span class="absolute top-0 right-0 z-10 px-4 py-1.5 text-[11px] font-extrabold tracking-wide bg-amber-400 text-white rounded-bl-2xl shadow">
                    ★ PATROCINADO
                </span>
                @endif

                <a href="{{ route('loja.show', $produto->slug) }}" class="flex flex-col flex-1">

                {{-- Cover --}}
                <div class="relative h-48 overflow-hidden" style="background: linear-gradient(135deg, {{ $produto->tipoColor() }}18, {{ $produto->tipoColor() }}35);">
                    @if($produto->capa_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($produto->capa_path) }}"
                            alt="{{ $produto->titulo }}"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-16 h-16 transition-transform duration-500 group-hover:scale-110" style="color: {{ $produto->tipoColor() }}66;" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $produto->tipoIcon() }}"/>
                            </svg>
                        </div>
                    @endif

                    @if($produto->vendas_count > 0)
                    <span class="absolute bottom-2.5 right-2.5 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#0055ff] text-white shadow">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M13 7H7v6h6V7z"/><path fill-rule="evenodd" d="M7 2a1 1 0 012 0v1h2V2a1 1 0 112 0v1h1a2 2 0 012 2v1h-1a1 1 0 100 2h1v2h-1a1 1 0 100 2h1v1a2 2 0 01-2 2h-1v1a1 1 0 11-2 0v-1H9v1a1 1 0 11-2 0v-1H6a2 2 0 01-2-2v-1h1a1 1 0 100-2H4v-2h1a1 1 0 100-2H4V5a2 2 0 012-2h1V2z" clip-rule="evenodd"/></svg>
                        {{ $produto->vendas_count }} vendido(s)
                    </span>
                    @endif
                </div>

                <div class="p-5 flex flex-col flex-1">
                    {{-- Icon chip + type --}}
                    <div class="flex items-center gap-2 mb-3">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl flex-shrink-0"
                            style="background: {{ $isPatrocinado ? 'rgba(255,255,255,.15)' : $produto->tipoColor().'18' }}; color: {{ $isPatrocinado ? '#fff' : $produto->tipoColor() }};">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $produto->tipoIcon() }}"/></svg>
                        </span>
                        <span class="text-xs font-bold uppercase tracking-wide {{ $isPatrocinado ? 'text-white/70' : 'text-gray-400' }}">
                            {{ $produto->tipoLabel() }}
                        </span>
                    </div>

                    <h3 class="font-bold text-base leading-snug line-clamp-2 mb-3 transition-colors {{ $isPatrocinado ? '' : 'text-gray-900 group-hover:text-[#0055ff]' }}">
                        {{ $produto->titulo }}
                    </h3>

                    {{-- Freelancer --}}
                    <div class="flex items-center gap-2 mb-4">
                        <img src="{{ $produto->freelancer->avatarUrl() }}"
                            class="w-6 h-6 rounded-full object-cover ring-2 {{ $isPatrocinado ? 'ring-white/20' : 'ring-gray-100' }}"
                            onerror="this.src='/img/default-avatar.svg'">
                        <span class="text-xs truncate font-medium {{ $isPatrocinado ? 'text-white/80' : 'text-gray-500' }}">{{ $produto->freelancer->name }}</span>
                    </div>

                    <div class="mt-auto {{ $isPatrocinado ? '' : 'pt-4 border-t border-gray-100' }}">
                        <div class="flex items-baseline gap-1 mb-3">
                            <span class="text-2xl font-extrabold tracking-tight">{{ number_format($produto->preco, 0, ',', '.') }}</span>
                            <span class="text-xs font-bold {{ $isPatrocinado ? 'text-white/70' : 'text-gray-400' }}">Kz</span>
                        </div>
                        <span class="block w-full text-center py-2.5 rounded-xl font-bold text-sm transition-colors
                            {{ $isPatrocinado
                                ? 'bg-white text-[#0033cc] group-hover:bg-amber-400 group-hover:text-white'
                                : 'bg-[#0055ff]/10 text-[#0055ff] group-hover:bg-[#0055ff] group-hover:text-white' }}">
                            Ver produto
                        </span>
                    </div>
                </div>
                </a>

                <div class="px-5 pb-5 {{ $isPatrocinado ? '' : '-mt-1' }}">
                    <a href="{{ route('loja.purchase', $produto->slug) }}"
                        class="block w-full text-center py-2.5 rounded-xl font-bold text-sm transition-colors
                            {{ $isPatrocinado
                                ? 'bg-amber-400 text-white hover:bg-amber-300'
                                : 'bg-[#0055ff] text-white hover:bg-[#0033cc]' }}">
                        Comprar
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <style>
            @keyframes loja-card-in {
                from { opacity: 0; transform: translateY(10px); }
                to   { opacity: 1; transform: translateY(0); }
            }
        </style>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $produtos->links() }}
        </div>
        @endif

    </div>
</div>
