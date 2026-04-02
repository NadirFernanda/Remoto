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

    {{-- ─── A) Receita por Modelo de Negócio ──────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Receita por Modelo de Negócio</h2>
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="rounded-xl bg-blue-50 border border-blue-100 p-4">
                <p class="text-xs text-blue-500 font-medium mb-1 uppercase tracking-wide">Freelancing</p>
                <p class="text-xl font-bold text-blue-700">{{ money_aoa($receitaFreelancing) }}</p>
                <p class="text-[11px] text-blue-400 mt-1">Comissões de projectos</p>
            </div>
            <div class="rounded-xl bg-purple-50 border border-purple-100 p-4">
                <p class="text-xs text-purple-500 font-medium mb-1 uppercase tracking-wide">Criador</p>
                <p class="text-xl font-bold text-purple-700">{{ money_aoa($receitaCreator) }}</p>
                <p class="text-[11px] text-purple-400 mt-1">Taxas de assinaturas</p>
            </div>
            <div class="rounded-xl bg-orange-50 border border-orange-100 p-4">
                <p class="text-xs text-orange-500 font-medium mb-1 uppercase tracking-wide">Infoprodutos</p>
                <p class="text-xl font-bold text-orange-700">{{ money_aoa($receitaInfoprodutos) }}</p>
                <p class="text-[11px] text-orange-400 mt-1">Comissões de vendas</p>
            </div>
            <div class="rounded-xl bg-gray-50 border border-gray-200 p-4">
                <p class="text-xs text-gray-500 font-medium mb-1 uppercase tracking-wide">Total Período</p>
                <p class="text-xl font-bold text-gray-800">{{ money_aoa($receitaTotal) }}</p>
                <p class="text-[11px] text-gray-400 mt-1">Receita combinada</p>
            </div>
        </div>
        {{-- Barras de proporção --}}
        @if($receitaTotal > 0)
        <div class="mt-5">
            <p class="text-xs text-gray-400 mb-2">Distribuição da receita</p>
            <div class="flex rounded-full overflow-hidden h-3 w-full gap-px">
                @php
                    $pFreelancing  = round($receitaFreelancing  / $receitaTotal * 100, 1);
                    $pCreator      = round($receitaCreator      / $receitaTotal * 100, 1);
                    $pInfoprodutos = round($receitaInfoprodutos / $receitaTotal * 100, 1);
                @endphp
                @if($pFreelancing > 0)
                <div class="bg-blue-500 h-full" style="width: {{ $pFreelancing }}%" title="Freelancing {{ $pFreelancing }}%"></div>
                @endif
                @if($pCreator > 0)
                <div class="bg-purple-500 h-full" style="width: {{ $pCreator }}%" title="Criador {{ $pCreator }}%"></div>
                @endif
                @if($pInfoprodutos > 0)
                <div class="bg-orange-400 h-full" style="width: {{ $pInfoprodutos }}%" title="Infoprodutos {{ $pInfoprodutos }}%"></div>
                @endif
            </div>
            <div class="flex gap-4 mt-2">
                <span class="flex items-center gap-1 text-[11px] text-gray-500"><span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span>Freelancing {{ $pFreelancing }}%</span>
                <span class="flex items-center gap-1 text-[11px] text-gray-500"><span class="w-2.5 h-2.5 rounded-full bg-purple-500 inline-block"></span>Criador {{ $pCreator }}%</span>
                <span class="flex items-center gap-1 text-[11px] text-gray-500"><span class="w-2.5 h-2.5 rounded-full bg-orange-400 inline-block"></span>Infoprodutos {{ $pInfoprodutos }}%</span>
            </div>
        </div>
        @endif
    </div>

    {{-- ─── B) Retenção — Pagamento em Garantia (Escrow) ──────── --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Retenção — Pagamento em Garantia (Escrow)</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="rounded-xl bg-yellow-50 border border-yellow-100 p-4">
                <p class="text-xs text-yellow-600 font-medium mb-1 uppercase tracking-wide">Actualmente Retido</p>
                <p class="text-xl font-bold text-yellow-700">{{ money_aoa($escrowEmRetencao) }}</p>
                <p class="text-[11px] text-yellow-500 mt-1">Aguarda aprovação / libertação</p>
            </div>
            <div class="rounded-xl bg-red-50 border border-red-100 p-4">
                <p class="text-xs text-red-500 font-medium mb-1 uppercase tracking-wide">Entrou em Escrow (período)</p>
                <p class="text-xl font-bold text-red-600">{{ money_aoa($escrowRetidoPeriodo) }}</p>
                <p class="text-[11px] text-red-400 mt-1">Pagamentos retidos no período</p>
            </div>
            <div class="rounded-xl bg-green-50 border border-green-100 p-4">
                <p class="text-xs text-green-600 font-medium mb-1 uppercase tracking-wide">Libertado (período)</p>
                <p class="text-xl font-bold text-green-700">{{ money_aoa($escrowLiberadoPeriodo) }}</p>
                <p class="text-[11px] text-green-500 mt-1">Transferido para freelancers</p>
            </div>
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
