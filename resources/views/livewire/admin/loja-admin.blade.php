<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-sky-50/30 pb-20">

    {{-- ── Hero Header ── --}}
    <div class="bg-white border-b border-slate-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#00baff] to-blue-600 flex items-center justify-center shadow-lg shadow-sky-200">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.35 2.7A1 1 0 007 17h11M7 13L5.4 5M9 21a1 1 0 102 0 1 1 0 00-2 0m8 0a1 1 0 102 0 1 1 0 00-2 0"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800 leading-tight">Gestão da Loja</h1>
                    <p class="text-sm text-slate-500">Moderação, aprovação e estatísticas de infoprodutos</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium transition-all duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('loja.index') }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-[#00baff] to-blue-600 hover:opacity-90 text-white text-sm font-medium transition-all duration-150 shadow-md shadow-sky-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Ver Loja
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 pt-8">

        {{-- ── Stats Grid ── --}}
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">

            {{-- Total Produtos --}}
            <div class="group relative bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden p-5">
                <div class="absolute top-0 right-0 w-20 h-20 bg-slate-50 rounded-full -translate-y-6 translate-x-6 group-hover:scale-110 transition-transform duration-300"></div>
                <div class="relative">
                    <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center mb-3">
                        <svg class="w-4.5 h-4.5 text-slate-600" style="width:18px;height:18px" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0v10l-8 4m0-14L4 17m8-10v14"/></svg>
                    </div>
                    <div class="text-2xl font-extrabold text-slate-800 leading-none">{{ $stats['total'] }}</div>
                    <div class="text-xs text-slate-400 mt-1 font-medium">Total de produtos</div>
                </div>
            </div>

            {{-- Em Moderação --}}
            <div class="group relative bg-white rounded-2xl border border-amber-100 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden p-5">
                <div class="absolute top-0 right-0 w-20 h-20 bg-amber-50 rounded-full -translate-y-6 translate-x-6 group-hover:scale-110 transition-transform duration-300"></div>
                <div class="relative">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center mb-3">
                        <svg style="width:18px;height:18px" class="text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div class="text-2xl font-extrabold text-amber-600 leading-none">{{ $stats['em_moderacao'] }}</div>
                    <div class="text-xs text-slate-400 mt-1 font-medium">Em moderação</div>
                </div>
            </div>

            {{-- Ativos --}}
            <div class="group relative bg-white rounded-2xl border border-emerald-100 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden p-5">
                <div class="absolute top-0 right-0 w-20 h-20 bg-emerald-50 rounded-full -translate-y-6 translate-x-6 group-hover:scale-110 transition-transform duration-300"></div>
                <div class="relative">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center mb-3">
                        <svg style="width:18px;height:18px" class="text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="text-2xl font-extrabold text-emerald-600 leading-none">{{ $stats['ativos'] }}</div>
                    <div class="text-xs text-slate-400 mt-1 font-medium">Ativos</div>
                </div>
            </div>

            {{-- Vendas Hoje --}}
            <div class="group relative bg-white rounded-2xl border border-sky-100 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden p-5">
                <div class="absolute top-0 right-0 w-20 h-20 bg-sky-50 rounded-full -translate-y-6 translate-x-6 group-hover:scale-110 transition-transform duration-300"></div>
                <div class="relative">
                    <div class="w-9 h-9 rounded-xl bg-sky-100 flex items-center justify-center mb-3">
                        <svg style="width:18px;height:18px" class="text-sky-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <div class="text-2xl font-extrabold text-sky-600 leading-none">{{ $stats['vendas_hoje'] }}</div>
                    <div class="text-xs text-slate-400 mt-1 font-medium">Vendas hoje</div>
                </div>
            </div>

            {{-- Receita Total --}}
            <div class="group relative bg-white rounded-2xl border border-purple-100 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden p-5">
                <div class="absolute top-0 right-0 w-20 h-20 bg-purple-50 rounded-full -translate-y-6 translate-x-6 group-hover:scale-110 transition-transform duration-300"></div>
                <div class="relative">
                    <div class="w-9 h-9 rounded-xl bg-purple-100 flex items-center justify-center mb-3">
                        <svg style="width:18px;height:18px" class="text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="text-lg font-extrabold text-purple-700 leading-none">Kz {{ number_format($stats['receita_total'], 0, ',', '.') }}</div>
                    <div class="text-xs text-slate-400 mt-1 font-medium">Receita (comissões)</div>
                </div>
            </div>

            {{-- Patrocínios ativos --}}
            <div class="group relative bg-white rounded-2xl border border-rose-100 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden p-5">
                <div class="absolute top-0 right-0 w-20 h-20 bg-rose-50 rounded-full -translate-y-6 translate-x-6 group-hover:scale-110 transition-transform duration-300"></div>
                <div class="relative">
                    <div class="w-9 h-9 rounded-xl bg-rose-100 flex items-center justify-center mb-3">
                        <svg style="width:18px;height:18px" class="text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    </div>
                    <div class="text-2xl font-extrabold text-rose-600 leading-none">{{ $stats['patrocinios_ativos'] }}</div>
                    <div class="text-xs text-slate-400 mt-1 font-medium">Patrocínios ativos</div>
                </div>
            </div>

        </div>

        {{-- ── Feedback ── --}}
        @if($feedback)
        <div class="mb-6 flex items-center gap-3 px-5 py-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium shadow-sm">
            <svg class="w-5 h-5 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ $feedback }}
        </div>
        @endif

        {{-- ── Filters ── --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4 mb-6">
            <div class="flex flex-wrap gap-3 items-center">
                <div class="relative flex-1 min-w-[220px]">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/></svg>
                    <input wire:model.live.debounce.400ms="busca" type="text"
                        placeholder="Buscar por título ou freelancer..."
                        class="w-full pl-9 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition">
                </div>
                <select wire:model.live="filtroStatus"
                    class="px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition text-slate-700 min-w-[160px]">
                    <option value="">Todos os status</option>
                    <option value="em_moderacao">Em Moderação</option>
                    <option value="ativo">Ativo</option>
                    <option value="inativo">Inativo</option>
                    <option value="rascunho">Rascunho</option>
                </select>
                <select wire:model.live="filtroTipo"
                    class="px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition text-slate-700 min-w-[160px]">
                    <option value="">Todos os tipos</option>
                    <option value="ebook">E-book</option>
                    <option value="audio">Áudio</option>
                    <option value="literatura_digital">Literatura Digital</option>
                    <option value="outro">Outro</option>
                </select>
            </div>
        </div>

        {{-- ── Table Card ── --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

            {{-- Table Header Label --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <span class="text-sm font-semibold text-slate-700">Lista de Infoprodutos</span>
                <span class="text-xs text-slate-400">{{ $produtos->total() }} resultado(s)</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50/80">
                            <th class="text-left px-6 py-3.5 font-semibold text-slate-500 text-xs uppercase tracking-wider">Produto</th>
                            <th class="text-left px-4 py-3.5 font-semibold text-slate-500 text-xs uppercase tracking-wider">Freelancer</th>
                            <th class="text-left px-4 py-3.5 font-semibold text-slate-500 text-xs uppercase tracking-wider">Tipo</th>
                            <th class="text-right px-4 py-3.5 font-semibold text-slate-500 text-xs uppercase tracking-wider">Preço</th>
                            <th class="text-center px-4 py-3.5 font-semibold text-slate-500 text-xs uppercase tracking-wider">Vendas</th>
                            <th class="text-center px-4 py-3.5 font-semibold text-slate-500 text-xs uppercase tracking-wider">Status</th>
                            <th class="text-center px-4 py-3.5 font-semibold text-slate-500 text-xs uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($produtos as $produto)
                        <tr class="group hover:bg-sky-50/30 transition-colors duration-100">

                            {{-- Produto --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center flex-shrink-0">
                                        @if($produto->tipo === 'ebook')
                                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                        @elseif($produto->tipo === 'audio')
                                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                                        @else
                                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-semibold text-slate-800 truncate max-w-[200px]">{{ $produto->titulo }}</div>
                                        <div class="text-xs text-slate-400 mt-0.5">{{ $produto->created_at->format('d/m/Y') }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Freelancer --}}
                            <td class="px-4 py-4">
                                <div class="font-medium text-slate-700">{{ $produto->freelancer->name }}</div>
                                <div class="text-xs text-slate-400 truncate max-w-[160px]">{{ $produto->freelancer->email }}</div>
                            </td>

                            {{-- Tipo --}}
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-100">
                                    {{ $produto->tipoLabel() }}
                                </span>
                            </td>

                            {{-- Preço --}}
                            <td class="px-4 py-4 text-right">
                                <span class="font-bold text-slate-800">Kz {{ number_format($produto->preco, 0, ',', '.') }}</span>
                            </td>

                            {{-- Vendas --}}
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-semibold text-xs">
                                    {{ $produto->compras_count }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-4 text-center">
                                @if($produto->status === 'ativo')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        {{ $produto->statusLabel() }}
                                    </span>
                                @elseif($produto->status === 'em_moderacao')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        {{ $produto->statusLabel() }}
                                    </span>
                                @elseif($produto->status === 'inativo')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        {{ $produto->statusLabel() }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        {{ $produto->statusLabel() }}
                                    </span>
                                @endif
                            </td>

                            {{-- Ações --}}
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Inspecionar (always visible) --}}
                                    <button wire:click="inspecionar({{ $produto->id }})"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 transition-all duration-150"
                                        title="Inspecionar produto">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                        Inspecionar
                                    </button>

                                    @if($produto->status === 'em_moderacao')
                                        <button wire:click="aprovar({{ $produto->id }})"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition-all duration-150 shadow-sm shadow-emerald-200">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            Aprovar
                                        </button>
                                        <button wire:click="rejeitar({{ $produto->id }})"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-100 hover:bg-red-200 text-red-700 transition-all duration-150">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Rejeitar
                                        </button>
                                    @elseif($produto->status === 'ativo')
                                        <button wire:click="rejeitar({{ $produto->id }})"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 transition-all duration-150">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            Desativar
                                        </button>
                                        <a href="{{ route('loja.show', $produto->slug) }}" target="_blank"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition-all duration-150">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Ver
                                        </a>
                                    @elseif($produto->status === 'inativo')
                                        <button wire:click="aprovar({{ $produto->id }})"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 transition-all duration-150">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            Reativar
                                        </button>
                                    @endif
                                </div>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center">
                                        <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0v10l-8 4m0-14L4 17m8-10v14"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-slate-500 font-medium text-sm">Nenhum produto encontrado</p>
                                        <p class="text-slate-400 text-xs mt-0.5">Tente ajustar os filtros de busca</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($produtos->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $produtos->links() }}
            </div>
            @endif

        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{--  MODAL: Inspeção do Produto (Admin)                          --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    @if($showInspecao && $produtoInspecao)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data x-on:keydown.escape.window="$wire.fecharInspecao()">

        {{-- Overlay --}}
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="fecharInspecao"></div>

        {{-- Modal Content --}}
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto border border-slate-200">

            {{-- Header --}}
            <div class="sticky top-0 bg-white z-10 px-6 py-4 border-b border-slate-100 flex items-center justify-between rounded-t-2xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">Inspeção do Produto</h2>
                        <p class="text-xs text-slate-400">Verifique a imagem e o conteúdo real do produto</p>
                    </div>
                </div>
                <button wire:click="fecharInspecao" class="p-2 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 space-y-6">

                {{-- Status badge --}}
                <div class="flex items-center gap-3">
                    @if($produtoInspecao->status === 'em_moderacao')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            Aguardando verificação
                        </span>
                    @elseif($produtoInspecao->status === 'ativo')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Ativo
                        </span>
                    @elseif($produtoInspecao->status === 'inativo')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            Rejeitado / Inativo
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                            {{ $produtoInspecao->statusLabel() }}
                        </span>
                    @endif

                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-100">
                        {{ $produtoInspecao->tipoLabel() }}
                    </span>
                </div>

                {{-- Product title & freelancer --}}
                <div>
                    <h3 class="text-xl font-bold text-slate-800">{{ $produtoInspecao->titulo }}</h3>
                    <div class="flex items-center gap-2 mt-2 text-sm text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span><strong>{{ $produtoInspecao->freelancer->name }}</strong> ({{ $produtoInspecao->freelancer->email }})</span>
                    </div>
                    <div class="flex items-center gap-2 mt-1 text-sm text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Publicado em {{ $produtoInspecao->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="mt-2">
                        <span class="text-2xl font-bold text-slate-800">Kz {{ number_format($produtoInspecao->preco, 0, ',', '.') }}</span>
                        <span class="text-sm text-slate-400 ml-2">·</span>
                        <span class="text-sm text-slate-500 ml-2">{{ $produtoInspecao->vendas_count }} venda(s)</span>
                    </div>
                </div>

                {{-- ── SECTION 1: Cover Image ── --}}
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <div class="bg-slate-50 px-4 py-2.5 border-b border-slate-200">
                        <h4 class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Imagem de Capa (o que o cliente vê)
                        </h4>
                    </div>
                    <div class="p-4 bg-white">
                        @if($produtoInspecao->capa_path)
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($produtoInspecao->capa_path) }}"
                                alt="Capa: {{ $produtoInspecao->titulo }}"
                                class="w-full max-h-80 object-contain rounded-lg border border-slate-100">
                        @else
                            <div class="w-full h-40 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 text-sm">
                                Sem imagem de capa
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ── SECTION 2: Product File (the real content) ── --}}
                <div class="border border-indigo-200 rounded-xl overflow-hidden">
                    <div class="bg-indigo-50 px-4 py-2.5 border-b border-indigo-200">
                        <h4 class="text-sm font-semibold text-indigo-700 flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Ficheiro do Produto (conteúdo real vendido)
                        </h4>
                    </div>
                    <div class="p-4 bg-white">
                        @if($produtoInspecao->arquivo_path)
                            <div class="flex items-center justify-between p-4 bg-indigo-50/50 rounded-lg border border-indigo-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-slate-700">{{ basename($produtoInspecao->arquivo_path) }}</div>
                                        <div class="text-xs text-slate-400">Ficheiro armazenado no disco privado</div>
                                    </div>
                                </div>
                                <a href="{{ route('admin.loja.download', $produtoInspecao->id) }}"
                                    target="_blank"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Fazer Download para Verificação
                                </a>
                            </div>
                            <p class="mt-2 text-xs text-indigo-600/70">
                                <svg class="w-3.5 h-3.5 inline -mt-0.5 mr-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Descarregue o ficheiro e verifique se corresponde ao título, descrição e imagem de capa. Rejeite caso o conteúdo seja fraudulento.
                            </p>
                        @else
                            <div class="flex items-center gap-3 p-4 bg-red-50 rounded-lg border border-red-200">
                                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span class="text-sm text-red-700 font-medium">Nenhum ficheiro de produto encontrado. Este produto não possui conteúdo para entregar.</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ── SECTION 3: Description ── --}}
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <div class="bg-slate-50 px-4 py-2.5 border-b border-slate-200">
                        <h4 class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/></svg>
                            Descrição do Produto
                        </h4>
                    </div>
                    <div class="p-4 bg-white">
                        <div class="text-sm text-slate-600 whitespace-pre-line leading-relaxed max-h-60 overflow-y-auto">{{ $produtoInspecao->descricao }}</div>
                    </div>
                </div>

            </div>

            {{-- Footer actions --}}
            <div class="sticky bottom-0 bg-white z-10 px-6 py-4 border-t border-slate-100 rounded-b-2xl flex items-center justify-between gap-3">
                <button wire:click="fecharInspecao"
                    class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition">
                    Fechar
                </button>
                <div class="flex items-center gap-2">
                    @if($produtoInspecao->status === 'em_moderacao' || $produtoInspecao->status === 'inativo')
                        <button wire:click="aprovar({{ $produtoInspecao->id }})"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Aprovar Produto
                        </button>
                    @endif
                    @if($produtoInspecao->status === 'em_moderacao' || $produtoInspecao->status === 'ativo')
                        <button wire:click="rejeitar({{ $produtoInspecao->id }})"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg bg-red-100 hover:bg-red-200 text-red-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Rejeitar Produto
                        </button>
                    @endif
                </div>
            </div>

        </div>
    </div>
    @endif

</div>
