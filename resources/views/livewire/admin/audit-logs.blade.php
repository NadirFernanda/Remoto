<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-sky-50/40 pb-16">

    {{-- ── Header ── --}}
    <div class="bg-white border-b border-slate-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-violet-200/60">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800 leading-tight">Logs de Auditoria</h1>
                    <p class="text-sm text-slate-500">Histórico completo de acções administrativas</p>
                </div>
            </div>
            {{-- Export buttons --}}
            @php
                $exportParams = http_build_query(array_filter([
                    'category'    => $categoryFilter,
                    'action'      => $actionFilter,
                    'entity_type' => $entityFilter,
                    'search'      => $search,
                    'date_start'  => $dateFrom,
                    'date_end'    => $dateTo,
                ]));
            @endphp
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.reports.audit.export') }}?{{ $exportParams }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0-3-3m3 3 3-3M3 17v2a2 2 0 002 2h14a2 2 0 002-2v-2"/></svg>
                    CSV
                </a>
                <a href="{{ route('admin.reports.audit.export.excel') }}?{{ $exportParams }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0-3-3m3 3 3-3M3 17v2a2 2 0 002 2h14a2 2 0 002-2v-2"/></svg>
                    Excel
                </a>
                <a href="{{ route('admin.reports.audit.export.pdf') }}?{{ $exportParams }}" target="_blank"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-700 text-xs font-semibold transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0-3-3m3 3 3-3M3 17v2a2 2 0 002 2h14a2 2 0 002-2v-2"/></svg>
                    PDF
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 pt-8 space-y-5">

        {{-- ── Category Tabs ── --}}
        <div class="flex flex-wrap gap-2">
            @php
                $tabs = [
                    ''           => ['label' => 'Todas',      'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16'],
                    'financeiro' => ['label' => 'Financeiro', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    'suporte'    => ['label' => 'Suporte',    'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z'],
                    'operacoes'  => ['label' => 'Operações',  'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'],
                    'sistema'    => ['label' => 'Sistema',    'icon' => 'M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18'],
                ];
            @endphp
            @foreach($tabs as $val => $tab)
                <button wire:click="$set('categoryFilter', '{{ $val }}')"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold border transition-all
                        {{ $categoryFilter === $val
                            ? 'bg-indigo-600 border-indigo-600 text-white shadow-md shadow-indigo-200/50'
                            : 'bg-white border-slate-200 text-slate-600 hover:border-indigo-400 hover:text-indigo-600' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $tab['icon'] }}"/>
                    </svg>
                    {{ $tab['label'] }}
                </button>
            @endforeach
        </div>

        {{-- ── Filters ── --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <div class="flex flex-wrap gap-3 items-end">

                {{-- Search --}}
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Pesquisar</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                        </svg>
                        <input wire:model.live.debounce.400ms="search" type="text"
                            placeholder="Pesquisar descrição..."
                            class="w-full pl-9 pr-3 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition">
                    </div>
                </div>

                {{-- Action --}}
                <div class="min-w-[160px]">
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Acção</label>
                    <select wire:model.live="actionFilter"
                        class="w-full px-3 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition text-slate-700">
                        <option value="">Todas as acções</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}">{{ str_replace('_', ' ', ucfirst($action)) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Entity --}}
                <div class="min-w-[150px]">
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Entidade</label>
                    <select wire:model.live="entityFilter"
                        class="w-full px-3 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition text-slate-700">
                        <option value="">Todas</option>
                        @foreach($entities as $entity)
                            <option value="{{ $entity }}">{{ $entity }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Date range --}}
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">De</label>
                    <input wire:model.live="dateFrom" type="date"
                        class="px-3 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Até</label>
                    <input wire:model.live="dateTo" type="date"
                        class="px-3 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition">
                </div>

                @if($search || $categoryFilter || $actionFilter || $entityFilter || $dateFrom || $dateTo)
                    <button wire:click="$set('search',''); $set('categoryFilter',''); $set('actionFilter',''); $set('entityFilter',''); $set('dateFrom',''); $set('dateTo','')"
                        class="self-end inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        Limpar
                    </button>
                @endif

                <div class="ml-auto self-end">
                    <span class="text-xs text-slate-400">{{ $logs->total() }} resultado(s)</span>
                </div>
            </div>
        </div>

        {{-- ── Table ── --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="py-3.5 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Data / Hora</th>
                            <th class="py-3.5 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Categoria</th>
                            <th class="py-3.5 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Acção</th>
                            <th class="py-3.5 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Descrição</th>
                            <th class="py-3.5 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Entidade</th>
                            <th class="py-3.5 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Executor</th>
                            <th class="py-3.5 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">IP</th>
                            <th class="py-3.5 px-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($logs as $log)
                            @php
                                $isExpanded = $expandedId === $log->id;
                                $catMeta = match($log->category ?? 'operacoes') {
                                    'financeiro' => ['bg' => 'bg-blue-50 text-[#0055ff] border border-blue-200',   'dot' => 'bg-[#0055ff]'],
                                    'suporte'    => ['bg' => 'bg-sky-50 text-sky-700 border border-sky-200',             'dot' => 'bg-sky-400'],
                                    'sistema'    => ['bg' => 'bg-amber-50 text-amber-700 border border-amber-200',       'dot' => 'bg-amber-400'],
                                    default      => ['bg' => 'bg-slate-100 text-slate-600 border border-slate-200',      'dot' => 'bg-slate-400'],
                                };
                                $catLabel = match($log->category ?? 'operacoes') {
                                    'financeiro' => 'Financeiro',
                                    'suporte'    => 'Suporte',
                                    'sistema'    => 'Sistema',
                                    default      => 'Operações',
                                };
                                $actionMeta = match(true) {
                                    str_contains($log->action, 'suspend') || str_contains($log->action, 'delete') || str_contains($log->action, 'cancel')
                                        => 'bg-red-50 text-red-700 border border-red-200',
                                    str_contains($log->action, 'approv') || str_contains($log->action, 'complet') || str_contains($log->action, 'creat')
                                        => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                    str_contains($log->action, 'kyc') || str_contains($log->action, 'verif')
                                        => 'bg-sky-50 text-sky-700 border border-sky-200',
                                    str_contains($log->action, 'dispute')
                                        => 'bg-orange-50 text-orange-700 border border-orange-200',
                                    str_contains($log->action, 'payment') || str_contains($log->action, 'refund')
                                        => 'bg-violet-50 text-violet-700 border border-violet-200',
                                    str_contains($log->action, 'update') || str_contains($log->action, 'edit')
                                        => 'bg-blue-50 text-blue-700 border border-blue-200',
                                    default
                                        => 'bg-slate-100 text-slate-600 border border-slate-200',
                                };
                                $userName = $log->user->name ?? 'Sistema';
                                $initials = collect(explode(' ', $userName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->implode('');
                            @endphp
                            <tr class="transition-colors {{ $isExpanded ? 'bg-blue-50' : 'hover:bg-slate-50' }}">

                                {{-- Date --}}
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="text-xs font-semibold text-slate-700">{{ $log->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-slate-400 mt-0.5">{{ $log->created_at->format('H:i:s') }}</div>
                                </td>

                                {{-- Category --}}
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $catMeta['bg'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $catMeta['dot'] }}"></span>
                                        {{ $catLabel }}
                                    </span>
                                </td>

                                {{-- Action --}}
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $actionMeta }}">
                                        {{ str_replace('_', ' ', ucfirst($log->action)) }}
                                    </span>
                                </td>

                                {{-- Description --}}
                                <td class="py-3.5 px-4 max-w-xs">
                                    <p class="text-sm text-slate-700 line-clamp-2 leading-snug">{{ $log->description }}</p>
                                </td>

                                {{-- Entity --}}
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($log->entity_type)
                                        <div class="text-xs font-semibold text-slate-600">{{ $log->entity_type }}</div>
                                        @if($log->entity_id)
                                            <div class="text-xs text-slate-400 mt-0.5">#{{ $log->entity_id }}</div>
                                        @endif
                                    @else
                                        <span class="text-slate-300 text-xs">—</span>
                                    @endif
                                </td>

                                {{-- Executor --}}
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                            {{ $initials }}
                                        </div>
                                        <span class="text-xs font-medium text-slate-700">{{ $userName }}</span>
                                    </div>
                                </td>

                                {{-- IP --}}
                                <td class="py-3.5 px-4">
                                    @if($log->ip)
                                        <span class="font-mono text-xs text-slate-500 bg-slate-100 px-2 py-0.5 rounded-lg">{{ $log->ip }}</span>
                                    @else
                                        <span class="text-slate-300 text-xs">—</span>
                                    @endif
                                </td>

                                {{-- Expand --}}
                                <td class="py-3.5 px-4">
                                    @if($log->before || $log->after)
                                        <button wire:click="toggleExpand({{ $log->id }})"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg transition-colors
                                                {{ $isExpanded ? 'bg-indigo-100 text-indigo-600' : 'bg-slate-100 text-slate-500 hover:bg-indigo-100 hover:text-indigo-600' }}">
                                            <svg class="w-3.5 h-3.5 transition-transform {{ $isExpanded ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                    @endif
                                </td>
                            </tr>

                            {{-- Expanded before/after --}}
                            @if($isExpanded && ($log->before || $log->after))
                            <tr class="bg-indigo-50/30">
                                <td colspan="8" class="px-6 py-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @if($log->before)
                                        <div class="bg-white rounded-xl border border-red-100 overflow-hidden">
                                            <div class="flex items-center gap-2 px-4 py-2.5 bg-red-50 border-b border-red-100">
                                                <span class="w-2 h-2 rounded-full bg-red-400"></span>
                                                <span class="text-xs font-semibold text-red-700">Antes</span>
                                            </div>
                                            <pre class="text-xs text-red-800 p-4 overflow-x-auto leading-relaxed">{{ json_encode($log->before, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                        @endif
                                        @if($log->after)
                                        <div class="bg-white rounded-xl border border-emerald-100 overflow-hidden">
                                            <div class="flex items-center gap-2 px-4 py-2.5 bg-emerald-50 border-b border-emerald-100">
                                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                                <span class="text-xs font-semibold text-emerald-700">Depois</span>
                                            </div>
                                            <pre class="text-xs text-emerald-800 p-4 overflow-x-auto leading-relaxed">{{ json_encode($log->after, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endif

                        @empty
                            <tr>
                                <td colspan="8" class="py-16 text-center">
                                    <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-slate-500">Nenhum log encontrado</p>
                                    <p class="text-xs text-slate-400 mt-1">Tente ajustar os filtros acima</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                {{ $logs->links() }}
            </div>
            @endif
        </div>

    </div>
</div>
