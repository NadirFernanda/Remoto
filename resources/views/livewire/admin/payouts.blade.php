<div>
    {{-- ─── Filters ────────────────────────────────────────────── --}}
    <div class="flex flex-wrap gap-3 mb-5">
        <input wire:model.live.debounce.400ms="search" type="text"
            placeholder="Pesquisar utilizador..."
            class="border border-gray-200 rounded-[10px] px-3 py-2 text-sm w-56 focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
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

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 text-green-800 rounded-[10px] border border-green-200 text-sm">{{ session('success') }}</div>
    @endif

    {{-- ─── Pendentes ──────────────────────────────────────────── --}}
    @if($pendentes->count() > 0)
    <div class="mb-6">
        <h3 class="text-sm font-bold text-orange-700 mb-2 flex items-center gap-2">
            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-orange-100 text-orange-700 text-xs font-bold">{{ $pendentes->count() }}</span>
            Saques Pendentes — aguardam aprovação
        </h3>
        <div class="rounded-2xl border border-orange-200 overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-orange-50">
                    <tr>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Freelancer</th>
                        <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 uppercase">Valor Solicitado</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Descrição</th>
                        <th class="py-3 px-4 text-center text-xs font-semibold text-gray-500 uppercase">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-orange-100">
                    @foreach($pendentes as $log)
                    <tr class="bg-white hover:bg-orange-50">
                        <td class="py-3 px-4 text-xs text-gray-400 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3 px-4 text-sm font-medium text-gray-700">{{ $log->user->name ?? '—' }}</td>
                        <td class="py-3 px-4 text-sm text-right font-bold text-orange-600">{{ money_aoa(abs($log->valor)) }}</td>
                        <td class="py-3 px-4 text-xs text-gray-500">{{ $log->descricao ?? '—' }}</td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button wire:click="aprovarSaque({{ $log->id }})"
                                    wire:confirm="Aprovar este saque de {{ money_aoa(abs($log->valor)) }}?"
                                    class="px-3 py-1 rounded-[8px] bg-green-100 text-green-700 border border-green-300 hover:bg-green-600 hover:text-white text-xs font-semibold transition">
                                    ✓ Aprovar
                                </button>
                                <button wire:click="rejeitarSaque({{ $log->id }})"
                                    wire:confirm="Rejeitar e devolver o valor ao freelancer?"
                                    class="px-3 py-1 rounded-[8px] bg-red-100 text-red-700 border border-red-300 hover:bg-red-600 hover:text-white text-xs font-semibold transition">
                                    ✕ Rejeitar
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ─── KPI ────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-6 inline-flex items-center gap-3">
        <span class="text-xs text-gray-500">Total Aprovado (período):</span>
        <span class="text-xl font-bold text-red-500">{{ money_aoa($totalAprovado) }}</span>
    </div>

    {{-- ─── Histórico ──────────────────────────────────────────── --}}
    <h3 class="text-sm font-semibold text-gray-600 mb-2">Histórico de Saques</h3>
    <div class="rounded-2xl border border-gray-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Freelancer</th>
                    <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 uppercase">Valor</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Estado</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Descrição</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 text-xs text-gray-400 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">{{ $log->user->name ?? '—' }}</td>
                        <td class="py-3 px-4 text-sm text-right font-medium
                            {{ $log->tipo === 'saque_rejeitado' ? 'text-gray-400 line-through' : 'text-red-500' }}">
                            {{ money_aoa(abs($log->valor)) }}
                        </td>
                        <td class="py-3 px-4">
                            @if($log->tipo === 'saque_aprovado')
                                <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-semibold">Aprovado</span>
                            @elseif($log->tipo === 'saque_rejeitado')
                                <span class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-semibold">Rejeitado</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-xs text-gray-500">{{ $log->descricao ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-sm text-gray-400">Sem saques para o período seleccionado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $logs->links() }}</div>
</div>
