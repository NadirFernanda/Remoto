<div class="min-h-screen bg-gray-50">

    {{-- ══ Hero banner ═══════════════════════════════════════════════════════ --}}
    <div class="bg-gradient-to-r from-[#00baff] to-[#005f8a] py-12">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <h1 class="text-3xl font-bold text-white mb-2">Loja de Infoprodutos</h1>
            <p class="text-white/80 text-base mb-6">E-books, áudios, literatura digital e muito mais dos melhores freelancers</p>
            <div class="max-w-lg mx-auto">
                <div class="relative">
                    <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input wire:model.live.debounce.400ms="busca" type="text"
                        class="w-full pl-10 pr-4 py-3 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-white/50"
                        placeholder="Buscar e-books, áudios, literatura...">
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 py-8">

        {{-- Feedback --}}
        @if(session('success_loja'))
            <div class="mb-6 px-4 py-3 rounded-xl bg-green-100 text-green-700 text-sm font-medium">
                {{ session('success_loja') }}
            </div>
        @endif
        @if(session('error_loja'))
            <div class="mb-6 px-4 py-3 rounded-xl bg-red-100 text-red-700 text-sm font-medium">
                {{ session('error_loja') }}
            </div>
        @endif

        {{-- ── Filters ─────────────────────────────────────────────────── --}}
        <div class="flex flex-wrap gap-3 mb-8 items-center">
            <div>
                <label class="text-xs text-gray-500 block mb-1">Categoria</label>
                <select wire:model.live="tipo" class="border rounded-lg px-3 py-1.5 text-sm focus:ring-1 focus:ring-[#00baff]">
                    <option value="">Todos</option>
                    <option value="ebook">E-book</option>
                    <option value="audio">Áudio</option>
                    <option value="literatura_digital">Literatura Digital</option>
                    <option value="outro">Outro</option>
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-500 block mb-1">Ordenar</label>
                <select wire:model.live="ordenar" class="border rounded-lg px-3 py-1.5 text-sm focus:ring-1 focus:ring-[#00baff]">
                    <option value="recente">Mais recentes</option>
                    <option value="mais_vendidos">Mais vendidos</option>
                    <option value="preco_asc">Menor preço</option>
                    <option value="preco_desc">Maior preço</option>
                </select>
            </div>
            <div class="ml-auto text-sm text-gray-400">
                {{ $produtos->total() }} produto(s) encontrado(s)
            </div>
        </div>

        {{-- ── Products grid ───────────────────────────────────────────── --}}
        @if($produtos->isEmpty())
        <div class="text-center py-20">
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
                class="group bg-white rounded-2xl border shadow-sm overflow-hidden hover:shadow-md transition-shadow flex flex-col {{ $isPatrocinado ? 'ring-2 ring-amber-300' : '' }}">

                {{-- Cover --}}
                <div class="relative h-44 bg-gradient-to-br from-[#00baff]/10 to-[#00baff]/20 overflow-hidden">
                    @if($produto->capa_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($produto->capa_path) }}"
                            alt="{{ $produto->titulo }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-16 h-16 text-[#00baff]/30" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
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
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-white/90 text-gray-700">
                            {{ $produto->tipoLabel() }}
                        </span>
                    </div>
                </div>

                <div class="p-4 flex flex-col flex-1">
                    <h3 class="font-semibold text-gray-900 text-sm line-clamp-2 mb-1 group-hover:text-[#00baff] transition-colors">
                        {{ $produto->titulo }}
                    </h3>

                    {{-- Freelancer --}}
                    <div class="flex items-center gap-1.5 mb-3">
                        <img src="{{ $produto->freelancer->avatarUrl() }}"
                            class="w-5 h-5 rounded-full object-cover"
                            onerror="this.src='/img/default-avatar.svg'">
                        <span class="text-xs text-gray-500 truncate">{{ $produto->freelancer->name }}</span>
                    </div>

                    <div class="mt-auto flex items-center justify-between">
                        <span class="text-base font-bold text-gray-900">
                            Kz {{ number_format($produto->preco, 0, ',', '.') }}
                        </span>
                        <span class="text-xs text-gray-400">{{ $produto->vendas_count }} vendas</span>
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
