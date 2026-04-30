<div>
    {{-- ─── Filtros ─────────────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-end gap-3 mb-5">
        {{-- Período rápido --}}
        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-xs text-gray-500">Período:</span>
            @foreach(['week' => 'Semana', 'month' => 'Mês', 'year' => 'Ano'] as $val => $lbl)
                <button wire:click="$set('period', '{{ $val }}')"
                    class="px-3 py-1.5 rounded-[10px] text-xs border transition
                        {{ $period === $val ? 'bg-[#0070ff] text-white border-[#0070ff]' : 'bg-white text-gray-600 border-gray-200 hover:border-[#0070ff] hover:text-[#0070ff]' }}">
                    {{ $lbl }}
                </button>
            @endforeach
        </div>

        {{-- Tipo --}}
        <select wire:model.live="tipo"
            class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs text-gray-600 focus:ring-2 focus:ring-[#0070ff]/30 focus:outline-none">
            <option value="">Todos os serviços</option>
            <option value="projetos">📁 Meus Projectos</option>
            <option value="infoprodutos">🛒 Infoprodutos</option>
            <option value="assinaturas">⭐ Assinaturas</option>
        </select>

        {{-- Datas --}}
        <input type="date" wire:model.live="dateStart"
               class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs text-gray-600 focus:ring-2 focus:ring-[#0070ff]/30 focus:outline-none">
        <span class="text-xs text-gray-400">a</span>
        <input type="date" wire:model.live="dateEnd"
               class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs text-gray-600 focus:ring-2 focus:ring-[#0070ff]/30 focus:outline-none">

        {{-- Export --}}
        <div class="flex items-center gap-2 ml-auto">
            <a href="{{ route('admin.facturacao.csv', ['period' => $period, 'tipo' => $tipo, 'date_start' => $dateStart, 'date_end' => $dateEnd]) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs border border-gray-200 bg-white text-gray-600 hover:border-[#0070ff] hover:text-[#0070ff] transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Exportar CSV
            </a>
            <a href="{{ route('admin.facturacao.excel', ['period' => $period, 'tipo' => $tipo, 'date_start' => $dateStart, 'date_end' => $dateEnd]) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs border border-gray-200 bg-white text-gray-600 hover:border-green-500 hover:text-green-600 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Excel
            </a>
        </div>
    </div>

    {{-- ─── Cards de resumo ────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs text-gray-500 mb-1">Total Facturas</p>
            <p class="text-2xl font-bold text-gray-900">{{ $total }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs text-gray-500 mb-1">Valor Bruto</p>
            <p class="text-2xl font-bold text-gray-900">{{ money_aoa($totalBruto) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs text-gray-500 mb-1">Comissão Plataforma</p>
            <p class="text-2xl font-bold text-amber-600">{{ money_aoa($totalComissao) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs text-gray-500 mb-1">Valor Líquido</p>
            <p class="text-2xl font-bold text-emerald-600">{{ money_aoa($totalLiquido) }}</p>
        </div>
    </div>

    {{-- ─── Breakdown por tipo ─────────────────────────────────────── --}}
    @if($byTipo->count() > 1)
    <div class="flex flex-wrap gap-3 mb-5">
        @foreach($byTipo as $tipoNome => $dados)
        @php
            $cor = match($tipoNome) {
                'Projectos'    => 'bg-blue-50 border-blue-200 text-blue-700',
                'Infoprodutos' => 'bg-violet-50 border-violet-200 text-violet-700',
                'Assinaturas'  => 'bg-amber-50 border-amber-200 text-amber-700',
                default        => 'bg-gray-50 border-gray-200 text-gray-700',
            };
            $icon = match($tipoNome) {
                'Projectos'    => '📁',
                'Infoprodutos' => '🛒',
                'Assinaturas'  => '⭐',
                default        => '📄',
            };
        @endphp
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border text-sm font-medium {{ $cor }}">
            <span>{{ $icon }}</span>
            <span>{{ $tipoNome }}: <strong>{{ $dados['count'] }}</strong> · {{ money_aoa($dados['total']) }}</span>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ─── Tabela de facturas ──────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        @if($paginator->isEmpty())
            <div class="py-16 text-center text-gray-400">
                <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm">Nenhuma factura para o período seleccionado.</p>
            </div>
        @else
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Nº Factura</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Data</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Tipo</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Descrição</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Cliente</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Prestador</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Bruto (AOA)</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Comissão</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Líquido</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($paginator as $row)
                @php
                    $badge = match($row['tipo']) {
                        'Projectos'    => 'bg-blue-50 text-blue-700',
                        'Infoprodutos' => 'bg-violet-50 text-violet-700',
                        'Assinaturas'  => 'bg-amber-50 text-amber-700',
                        default        => 'bg-gray-100 text-gray-600',
                    };
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-2.5 font-mono text-xs font-bold text-[#0070ff] whitespace-nowrap">{{ $row['fat_numero'] }}</td>
                    <td class="px-4 py-2.5 text-xs text-gray-500 whitespace-nowrap">{{ $row['data'] }}</td>
                    <td class="px-4 py-2.5">
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold {{ $badge }}">{{ $row['tipo'] }}</span>
                    </td>
                    <td class="px-4 py-2.5 text-gray-700 max-w-[200px] truncate" title="{{ $row['descricao'] }}">{{ $row['descricao'] }}</td>
                    <td class="px-4 py-2.5 text-gray-600 whitespace-nowrap">{{ $row['cliente'] }}</td>
                    <td class="px-4 py-2.5 text-gray-600 whitespace-nowrap">{{ $row['prestador'] }}</td>
                    <td class="px-4 py-2.5 text-right font-semibold text-gray-800 whitespace-nowrap">{{ money_aoa($row['valor_bruto']) }}</td>
                    <td class="px-4 py-2.5 text-right text-amber-600 whitespace-nowrap">{{ money_aoa($row['comissao']) }}</td>
                    <td class="px-4 py-2.5 text-right text-emerald-600 font-semibold whitespace-nowrap">{{ money_aoa($row['valor_liquido']) }}</td>
                    <td class="px-4 py-2.5">
                        <span class="text-xs text-gray-500">{{ $row['status'] }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="border-t-2 border-gray-200 bg-gray-50">
                <tr>
                    <td colspan="6" class="px-4 py-3 text-xs font-bold text-gray-600 uppercase">Totais do período</td>
                    <td class="px-4 py-3 text-right font-bold text-gray-900 whitespace-nowrap">{{ money_aoa($totalBruto) }}</td>
                    <td class="px-4 py-3 text-right font-bold text-amber-600 whitespace-nowrap">{{ money_aoa($totalComissao) }}</td>
                    <td class="px-4 py-3 text-right font-bold text-emerald-600 whitespace-nowrap">{{ money_aoa($totalLiquido) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        </div>

        @if($paginator->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $paginator->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
