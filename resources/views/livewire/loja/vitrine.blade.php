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
        <div class="px-4 py-3 rounded-xl bg-red-100 text-red-700 text-sm font-medium">{{ session('error_loja') }}</div>
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
                class="group bg-white rounded-2xl border border-gray-200 hover:border-[#0055ff]/50 hover:shadow-lg hover:-translate-y-0.5 transition-all flex-shrink-0 w-44 overflow-hidden">
                <div class="relative h-32 bg-gradient-to-br from-[#00c8ff]/10 to-[#0055ff]/20 overflow-hidden">
                    @if($produto->capa_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($produto->capa_path) }}"
                            alt="{{ $produto->titulo }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-[#0055ff]/30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $produto->tipoIcon() }}"/></svg>
                        </div>
                    @endif
                    <span class="absolute top-1.5 left-1.5 inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-400 text-white shadow">
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
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            @foreach($produtos as $produto)
            @php $isPatrocinado = $produto->patrocinado ?? $produto->isPatrocinado(); @endphp
            <a href="{{ route('loja.show', $produto->slug) }}"
                class="group bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-xl hover:-translate-y-1 hover:border-[#0055ff]/40 transition-all duration-200 flex flex-col {{ $isPatrocinado ? 'ring-2 ring-amber-300' : '' }}">

                {{-- Cover --}}
                <div class="relative h-48 bg-gradient-to-br from-[#00c8ff]/10 to-[#0055ff]/20 overflow-hidden">
                    @if($produto->capa_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($produto->capa_path) }}"
                            alt="{{ $produto->titulo }}"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-16 h-16 text-[#0055ff]/30" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $produto->tipoIcon() }}"/>
                            </svg>
                        </div>
                    @endif

                    {{-- Badges --}}
                    <div class="absolute top-2 left-2 flex flex-col gap-1">
                        @if($isPatrocinado)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-400 text-white shadow">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            Patrocinado
                        </span>
                        @endif
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-white/90 text-gray-700 shadow-sm">
                            {{ $produto->tipoLabel() }}
                        </span>
                    </div>

                    @if($produto->vendas_count > 0)
                    <span class="absolute bottom-2 right-2 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-black/60 text-white backdrop-blur-sm">
                        {{ $produto->vendas_count }} vendido(s)
                    </span>
                    @endif
                </div>

                <div class="p-4 flex flex-col flex-1">
                    <h3 class="font-semibold text-gray-900 text-sm line-clamp-2 mb-2 group-hover:text-[#0055ff] transition-colors">
                        {{ $produto->titulo }}
                    </h3>

                    {{-- Freelancer --}}
                    <div class="flex items-center gap-1.5 mb-3">
                        <img src="{{ $produto->freelancer->avatarUrl() }}"
                            class="w-5 h-5 rounded-full object-cover"
                            onerror="this.src='/img/default-avatar.svg'">
                        <span class="text-xs text-gray-500 truncate">{{ $produto->freelancer->name }}</span>
                    </div>

                    <div class="mt-auto pt-3 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-lg font-extrabold text-gray-900">
                            Kz {{ number_format($produto->preco, 0, ',', '.') }}
                        </span>
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#0055ff]/10 text-[#0055ff] group-hover:bg-[#0055ff] group-hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $produtos->links() }}
        </div>
        @endif

    </div>
</div>
