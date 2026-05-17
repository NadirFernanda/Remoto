<div>
    {{-- ─── Filters ────────────────────────────────────────────── --}}
    <div class="flex flex-wrap gap-3 mb-5">
        <input wire:model.live.debounce.400ms="search" type="text"
            placeholder="Pesquisar utilizador..."
            class="border border-gray-200 rounded-[10px] px-3 py-2 text-sm w-56 focus:outline-none focus:ring-2 focus:ring-[#0055ff]/30 focus:border-[#0055ff]">
        @foreach(['week' => 'Semana', 'month' => 'Mês', 'year' => 'Ano'] as $val => $label)
            <button wire:click="$set('period', '{{ $val }}')"
                class="px-3 py-1.5 rounded-[10px] text-xs border transition
                    {{ $period === $val
                        ? 'bg-[#0055ff] text-white border-[#0055ff]'
                        : 'bg-white text-gray-600 border-gray-200 hover:border-[#0055ff] hover:text-[#0055ff]' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- ─── KPI ────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-6 inline-flex items-center gap-3">
        <span class="text-xs text-gray-500">Total Comissões:</span>
        <span class="text-xl font-bold text-[#0055ff]">{{ money_aoa($total) }}</span>
    </div>

    {{-- ─── Table ──────────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-gray-200 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Freelancer</th>
                    <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 uppercase">Comissão</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Descrição</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 text-xs text-gray-400 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">{{ $log->user->name ?? '—' }}</td>
                        <td class="py-3 px-4 text-sm text-right text-green-600">{{ money_aoa($log->valor) }}</td>
                        <td class="py-3 px-4 text-sm text-gray-500">{{ $log->descricao ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-10 text-center text-sm text-gray-400">Sem comissões para o período seleccionado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $logs->links() }}</div>
</div>
