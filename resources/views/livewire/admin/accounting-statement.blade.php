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
        {{-- Tipo --}}
        <select wire:model.live="tipo" class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs text-gray-600 focus:ring-2 focus:ring-[#00baff] focus:outline-none">
            <option value="">Todos os tipos</option>
            <option value="freelancing">Freelances</option>
            <option value="infoproduto">Infoprodutos</option>
            <option value="creator">Criador</option>
        </select>
        {{-- Datas --}}
        <input type="date" wire:model.live="dateStart" class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs text-gray-600 focus:ring-2 focus:ring-[#00baff] focus:outline-none" />
        <span class="text-xs text-gray-400">a</span>
        <input type="date" wire:model.live="dateEnd" class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs text-gray-600 focus:ring-2 focus:ring-[#00baff] focus:outline-none" />
        {{-- Export --}}
        <div class="flex items-center gap-2 ml-auto">
            <a href="{{ route('admin.reports.accounting.csv', ['period' => $period, 'tipo' => $tipo, 'date_start' => $dateStart, 'date_end' => $dateEnd]) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs border border-gray-200 bg-white text-gray-600 hover:border-[#00baff] hover:text-[#00baff] transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                CSV
            </a>
            <a href="{{ route('admin.reports.accounting.excel', ['period' => $period, 'tipo' => $tipo, 'date_start' => $dateStart, 'date_end' => $dateEnd]) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs border border-gray-200 bg-white text-gray-600 hover:border-green-500 hover:text-green-600 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Excel
            </a>
            <a href="{{ route('admin.reports.accounting.pdf', ['period' => $period, 'tipo' => $tipo, 'date_start' => $dateStart, 'date_end' => $dateEnd]) }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs border border-gray-200 bg-white text-gray-600 hover:border-red-500 hover:text-red-600 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                PDF
            </a>
        </div>
    </div>

    {{-- ─── Totais ───────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-3 gap-4 mb-5">
        <div class="bg-white rounded-2xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Valor Bruto Total</p>
            <p class="text-lg font-bold text-gray-800">{{ money_aoa($totalBruto) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Comissões Total</p>
            <p class="text-lg font-bold text-[#00baff]">{{ money_aoa($totalComissao) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Valor Líquido Total</p>
            <p class="text-lg font-bold text-green-600">{{ money_aoa($totalLiquido) }}</p>
        </div>
    </div>

    {{-- ─── Tabela ───────────────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-gray-200 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Serviço / Produto</th>
                    <th class="py-3 px-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Data</th>
                    <th class="py-3 px-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Tipo</th>
                    <th class="py-3 px-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Utilizador Origem</th>
                    <th class="py-3 px-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Utilizador Destino</th>
                    <th class="py-3 px-3 text-right text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Valor Bruto</th>
                    <th class="py-3 px-3 text-right text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Comissão</th>
                    <th class="py-3 px-3 text-right text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Valor Líquido</th>
                    <th class="py-3 px-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($paginated as $row)
                    <tr class="hover:bg-gray-50">
                        <td class="py-2.5 px-3 text-xs text-gray-800 max-w-[160px] truncate" title="{{ $row['nome'] }}">{{ $row['nome'] }}</td>
                        <td class="py-2.5 px-3 text-xs text-gray-500 whitespace-nowrap">{{ $row['data'] }}</td>
                        <td class="py-2.5 px-3">
                            @php $tipoCor = match($row['tipo']) {
                                'Freelancing' => 'bg-blue-100 text-blue-700',
                                'Infoproduto' => 'bg-orange-100 text-orange-700',
                                'Creator'     => 'bg-purple-100 text-purple-700',
                                default       => 'bg-gray-100 text-gray-600',
                            }; @endphp
                            <span class="text-[11px] px-2 py-0.5 rounded-full font-medium {{ $tipoCor }}">{{ $row['tipo'] }}</span>
                        </td>
                        <td class="py-2.5 px-3 text-xs text-gray-700">{{ $row['user_origem'] }}</td>
                        <td class="py-2.5 px-3 text-xs text-gray-700">{{ $row['user_destino'] }}</td>
                        <td class="py-2.5 px-3 text-xs text-right text-gray-800 font-medium">{{ money_aoa($row['valor_bruto']) }}</td>
                        <td class="py-2.5 px-3 text-xs text-right text-[#00baff]">{{ money_aoa($row['comissao']) }}</td>
                        <td class="py-2.5 px-3 text-xs text-right text-green-600 font-medium">{{ money_aoa($row['valor_liquido']) }}</td>
                        <td class="py-2.5 px-3 text-xs text-gray-500">{{ $row['status'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-10 text-center text-sm text-gray-400">Nenhum registo encontrado para o período seleccionado.</td>
                    </tr>
                @endforelse
                {{-- Linha totais --}}
                @if($paginated->count())
                <tr class="bg-gray-50 font-semibold border-t-2 border-gray-300">
                    <td colspan="5" class="py-2.5 px-3 text-xs text-gray-600 uppercase">Total (página)</td>
                    <td class="py-2.5 px-3 text-xs text-right text-gray-800">{{ money_aoa($paginated->sum('valor_bruto')) }}</td>
                    <td class="py-2.5 px-3 text-xs text-right text-[#00baff]">{{ money_aoa($paginated->sum('comissao')) }}</td>
                    <td class="py-2.5 px-3 text-xs text-right text-green-600">{{ money_aoa($paginated->sum('valor_liquido')) }}</td>
                    <td></td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- Total global --}}
    <div class="mt-3 text-right text-xs text-gray-500">
        Total global do período: <strong class="text-gray-800">{{ money_aoa($totalBruto) }}</strong> bruto
        &nbsp;|&nbsp; <strong class="text-green-600">{{ money_aoa($totalLiquido) }}</strong> líquido
    </div>

    <div class="mt-4">{{ $paginated->links() }}</div>
</div>
