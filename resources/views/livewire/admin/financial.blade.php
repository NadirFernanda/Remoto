<div>
    {{-- ─── Period filter ─────────────────────────────────────── --}}
    <div class="flex items-center gap-3 mb-6">
        <span class="text-sm text-gray-600">Período:</span>
        @foreach(['week' => 'Semana', 'month' => 'Mês', 'year' => 'Ano'] as $val => $label)
            <button wire:click="$set('period', '{{ $val }}')"
                class="px-3 py-1.5 rounded-[10px] text-xs border transition
                    {{ $period === $val
                        ? 'bg-[#00baff] text-white border-[#00baff]'
                        : 'bg-white text-gray-600 border-gray-200 hover:border-[#00baff] hover:text-[#00baff]' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- ─── KPI cards ──────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 mb-1">Total Entradas</p>
            <p class="text-2xl font-bold text-green-600">{{ money_aoa($totalEntradas) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 mb-1">Total Saídas</p>
            <p class="text-2xl font-bold text-red-500">{{ money_aoa($totalSaidas) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 mb-1">Comissões</p>
            <p class="text-2xl font-bold text-[#00baff]">{{ money_aoa($totalComissoes) }}</p>
        </div>
    </div>

    {{-- ─── Transactions table ─────────────────────────────────── --}}
    <div class="rounded-2xl border border-gray-200 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Utilizador</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                    <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 uppercase">Valor</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 text-xs text-gray-400 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">{{ $log->user->name ?? '—' }}</td>
                        <td class="py-3 px-4 text-sm text-gray-500">{{ ucfirst($log->tipo) }}</td>
                        <td class="py-3 px-4 text-sm text-right {{ $log->valor >= 0 ? 'text-green-600' : 'text-red-500' }}">
                            {{ money_aoa($log->valor) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-10 text-center text-sm text-gray-400">Sem movimentações para o período seleccionado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $logs->links() }}</div>
</div>
