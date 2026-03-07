<div>
    {{-- ─── Filters ─────────────────────────────────────────────── --}}
    <div class="flex flex-wrap gap-3 mb-5">
        <input wire:model.live.debounce.400ms="search" type="text"
            placeholder="Pesquisar descrição..."
            class="border border-gray-200 rounded-[10px] px-3 py-2 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">

        <select wire:model.live="actionFilter"
            class="border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
            <option value="">Todas as acções</option>
            @foreach($actions as $action)
                <option value="{{ $action }}">{{ str_replace('_', ' ', ucfirst($action)) }}</option>
            @endforeach
        </select>

        <select wire:model.live="entityFilter"
            class="border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
            <option value="">Todas as entidades</option>
            @foreach($entities as $entity)
                <option value="{{ $entity }}">{{ $entity }}</option>
            @endforeach
        </select>

        <input wire:model.live="dateFrom" type="date"
            class="border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
        <input wire:model.live="dateTo" type="date"
            class="border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">

        @if($search || $actionFilter || $entityFilter || $dateFrom || $dateTo)
            <button wire:click="$set('search',''); $set('actionFilter',''); $set('entityFilter',''); $set('dateFrom',''); $set('dateTo','')"
                class="btn-outline text-xs">
                Limpar
            </button>
        @endif
    </div>

    {{-- ─── Table ────────────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-gray-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Data/Hora</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Acção</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Descrição</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Entidade</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Executor</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">IP</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Detalhe</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 {{ $expandedId === $log->id ? 'bg-blue-50/30' : '' }}">
                        <td class="py-3 px-4 text-xs text-gray-500 whitespace-nowrap">
                            {{ $log->created_at->format('d/m/Y') }}<br>
                            <span class="text-gray-400">{{ $log->created_at->format('H:i:s') }}</span>
                        </td>
                        <td class="py-3 px-4">
                            @php
                                $actionColor = match(true) {
                                    str_contains($log->action, 'suspend')  => 'bg-red-100 text-red-700',
                                    str_contains($log->action, 'approved') => 'bg-green-100 text-green-700',
                                    str_contains($log->action, 'kyc')      => 'bg-blue-100 text-blue-700',
                                    str_contains($log->action, 'dispute')  => 'bg-orange-100 text-orange-700',
                                    str_contains($log->action, 'payment')  => 'bg-purple-100 text-purple-700',
                                    default                                => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $actionColor }}">
                                {{ str_replace('_', ' ', $log->action) }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-800 max-w-xs">
                            <span class="line-clamp-2">{{ $log->description }}</span>
                        </td>
                        <td class="py-3 px-4 text-xs text-gray-500">
                            @if($log->entity_type)
                                <span class="font-medium">{{ $log->entity_type }}</span>
                                @if($log->entity_id) <span class="text-gray-400">#{{ $log->entity_id }}</span> @endif
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-xs text-gray-700">
                            {{ $log->user->name ?? 'Sistema' }}
                        </td>
                        <td class="py-3 px-4 text-xs text-gray-400 font-mono">{{ $log->ip ?? '—' }}</td>
                        <td class="py-3 px-4">
                            @if($log->before || $log->after)
                                <button wire:click="toggleExpand({{ $log->id }})"
                                    class="text-xs text-[#00baff] hover:underline">
                                    {{ $expandedId === $log->id ? '▲ Fechar' : '▼ Ver' }}
                                </button>
                            @endif
                        </td>
                    </tr>
                    {{-- Expanded row with before/after JSON --}}
                    @if($expandedId === $log->id && ($log->before || $log->after))
                    <tr class="bg-blue-50/30">
                        <td colspan="7" class="px-4 pb-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-2">
                                @if($log->before)
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 mb-1">Antes</p>
                                    <pre class="text-xs bg-red-50 border border-red-100 rounded-[10px] p-3 overflow-x-auto text-red-800">{{ json_encode($log->before, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                                @endif
                                @if($log->after)
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 mb-1">Depois</p>
                                    <pre class="text-xs bg-green-50 border border-green-100 rounded-[10px] p-3 overflow-x-auto text-green-800">{{ json_encode($log->after, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-gray-400 text-sm">
                            <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                            </svg>
                            Nenhum log encontrado
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $logs->links() }}</div>
</div>
