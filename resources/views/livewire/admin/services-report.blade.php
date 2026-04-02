<div>
    {{-- ─── Filtros ─────────────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-end gap-3 mb-6">
        {{-- Período rápido --}}
        <div class="flex items-center gap-2">
            <span class="text-xs text-gray-500">Período:</span>
            @foreach(['week' => 'Semana', 'month' => 'Mês', 'year' => 'Ano'] as $val => $lbl)
                <button wire:click="$set('period', '{{ $val }}')"
                    class="px-3 py-1.5 rounded-[10px] text-xs border transition
                        {{ $period === $val ? 'bg-[#00baff] text-white border-[#00baff]' : 'bg-white text-gray-600 border-gray-200 hover:border-[#00baff] hover:text-[#00baff]' }}">
                    {{ $lbl }}</button>
            @endforeach
        </div>
        {{-- Status --}}
        <select wire:model.live="status" class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs text-gray-600 focus:ring-2 focus:ring-[#00baff] focus:outline-none">
            <option value="">Todos os status</option>
            @foreach($statusLabels as $val => $lbl)
                <option value="{{ $val }}">{{ $lbl }}</option>
            @endforeach
        </select>
        {{-- Pesquisa --}}
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Título, cliente ou freelancer…"
            class="border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-600 focus:ring-2 focus:ring-[#00baff] focus:outline-none w-56" />
        {{-- Datas --}}
        <input type="date" wire:model.live="dateStart" class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs text-gray-600 focus:ring-2 focus:ring-[#00baff] focus:outline-none" />
        <span class="text-xs text-gray-400">a</span>
        <input type="date" wire:model.live="dateEnd" class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs text-gray-600 focus:ring-2 focus:ring-[#00baff] focus:outline-none" />
    </div>

    {{-- ─── Resumo por status ────────────────────────────────────────── --}}
    <div class="grid grid-cols-3 sm:grid-cols-5 gap-3 mb-5">
        @foreach($statusLabels as $val => $lbl)
            @php
                $count = $resumo[$val] ?? 0;
                $cor = match($val) {
                    'completed'   => 'bg-green-50 border-green-200 text-green-700',
                    'in_progress' => 'bg-blue-50 border-blue-200 text-blue-700',
                    'delivered'   => 'bg-cyan-50 border-cyan-200 text-cyan-700',
                    'cancelled'   => 'bg-red-50 border-red-200 text-red-700',
                    'published'   => 'bg-yellow-50 border-yellow-200 text-yellow-700',
                    default       => 'bg-gray-50 border-gray-200 text-gray-600',
                };
            @endphp
            @if($count > 0)
            <button wire:click="$set('status', '{{ $val }}')"
                class="rounded-xl border p-3 text-center cursor-pointer hover:shadow-sm transition {{ $cor }} {{ $status === $val ? 'ring-2 ring-offset-1 ring-current' : '' }}">
                <p class="text-lg font-bold">{{ $count }}</p>
                <p class="text-[11px] font-medium mt-0.5">{{ $lbl }}</p>
            </button>
            @endif
        @endforeach
        @if($status)
        <button wire:click="$set('status', '')"
            class="rounded-xl border border-gray-200 p-3 text-center text-xs text-gray-400 hover:text-gray-600 hover:border-gray-300 transition">
            ✕ Limpar filtro
        </button>
        @endif
    </div>

    {{-- ─── Total valor ─────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-4 mb-5 flex items-center justify-between">
        <p class="text-xs text-gray-500">Valor total dos serviços no período</p>
        <p class="text-lg font-bold text-gray-800">{{ money_aoa($totalValor) }}</p>
    </div>

    {{-- ─── Tabela ───────────────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-gray-200 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Título</th>
                    <th class="py-3 px-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Data</th>
                    <th class="py-3 px-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Cliente</th>
                    <th class="py-3 px-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Freelancer</th>
                    <th class="py-3 px-3 text-right text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Valor</th>
                    <th class="py-3 px-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($services as $s)
                    @php
                        $badgeCor = match($s->status) {
                            'completed'   => 'bg-green-100 text-green-700',
                            'in_progress' => 'bg-blue-100 text-blue-700',
                            'delivered'   => 'bg-cyan-100 text-cyan-700',
                            'cancelled'   => 'bg-red-100 text-red-700',
                            'published'   => 'bg-yellow-100 text-yellow-700',
                            'accepted'    => 'bg-indigo-100 text-indigo-700',
                            'negotiating' => 'bg-orange-100 text-orange-700',
                            default       => 'bg-gray-100 text-gray-600',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="py-2.5 px-3 text-xs text-gray-800 max-w-[200px] truncate" title="{{ $s->titulo }}">
                            {{ $s->titulo ?? 'Projecto #' . $s->id }}
                        </td>
                        <td class="py-2.5 px-3 text-xs text-gray-500 whitespace-nowrap">{{ $s->created_at->format('d/m/Y') }}</td>
                        <td class="py-2.5 px-3 text-xs text-gray-700">{{ optional($s->cliente)->name ?? '—' }}</td>
                        <td class="py-2.5 px-3 text-xs text-gray-700">{{ optional($s->freelancer)->name ?? '—' }}</td>
                        <td class="py-2.5 px-3 text-xs text-right text-gray-800 font-medium">
                            {{ $s->valor ? money_aoa($s->valor) : '—' }}
                        </td>
                        <td class="py-2.5 px-3">
                            <span class="text-[11px] px-2 py-0.5 rounded-full font-medium {{ $badgeCor }}">
                                {{ $statusLabels[$s->status] ?? ucfirst($s->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-sm text-gray-400">Nenhum serviço encontrado para o período seleccionado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $services->links() }}</div>
</div>
