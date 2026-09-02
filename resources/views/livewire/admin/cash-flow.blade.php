<div>
    @if(session('cashflow_success'))
        <div class="mb-4 px-4 py-3 rounded-xl bg-green-100 text-green-700 text-sm font-medium">{{ session('cashflow_success') }}</div>
    @endif

    {{-- ─── Filtros ─────────────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-end gap-3 mb-6">
        {{-- Período rápido --}}
        <div class="flex items-center gap-2">
            <span class="text-xs text-gray-500">Período:</span>
            @foreach(['week' => 'Semana', 'month' => 'Mês', 'year' => 'Ano'] as $val => $lbl)
                <button wire:click="$set('period', '{{ $val }}')"
                    class="px-3 py-1.5 rounded-[10px] text-xs border transition {{ $period === $val ? 'bg-[#0055ff] text-white border-[#0055ff]' : 'bg-white text-gray-600 border-gray-200 hover:border-[#0055ff] hover:text-[#0055ff]' }}">
                    {{ $lbl }}</button>
            @endforeach
        </div>
        {{-- Datas customizadas --}}
        <div class="flex items-center gap-2 ml-auto">
            <input type="date" wire:model.live="dateStart" class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs text-gray-600 focus:ring-2 focus:ring-[#0055ff] focus:outline-none" />
            <span class="text-xs text-gray-400">a</span>
            <input type="date" wire:model.live="dateEnd"  class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs text-gray-600 focus:ring-2 focus:ring-[#0055ff] focus:outline-none" />
        </div>
        {{-- Export --}}
        <a href="{{ route('admin.reports.cashflow.csv', ['period' => $period, 'date_start' => $dateStart, 'date_end' => $dateEnd]) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs border border-gray-200 bg-white text-gray-600 hover:border-[#0055ff] hover:text-[#0055ff] transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            CSV
        </a>
        <a href="{{ route('admin.reports.cashflow.excel', ['period' => $period, 'date_start' => $dateStart, 'date_end' => $dateEnd]) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs border border-gray-200 bg-white text-gray-600 hover:border-green-500 hover:text-green-600 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Excel
        </a>
        <a href="{{ route('admin.reports.cashflow.pdf', ['period' => $period, 'date_start' => $dateStart, 'date_end' => $dateEnd]) }}" target="_blank"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs border border-gray-200 bg-white text-gray-600 hover:border-red-500 hover:text-red-600 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            PDF
        </a>
    </div>

    {{-- ─── KPI Totais ──────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 mb-1">Total Entradas</p>
            <p class="text-xl font-bold text-green-600">{{ money_aoa($totalEntradas) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 mb-1">Total Saídas</p>
            <p class="text-xl font-bold text-red-500">{{ money_aoa($totalSaidas) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 mb-1">Comissões Plataforma</p>
            <p class="text-xl font-bold text-[#0055ff]">{{ money_aoa($totalComissao) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 mb-1">Saldo Líquido</p>
            <p class="text-xl font-bold {{ $saldoLiquido >= 0 ? 'text-gray-800' : 'text-red-600' }}">{{ money_aoa($saldoLiquido) }}</p>
        </div>
    </div>

    {{-- ─── Tabela por Origem ───────────────────────────────────────── --}}
    @php
        $corMap = [
            'blue'   => ['bg' => 'bg-blue-50',   'border' => 'border-blue-100',   'text' => 'text-blue-700',   'badge' => 'bg-blue-100 text-blue-700'],
            'purple' => ['bg' => 'bg-blue-50',  'border' => 'border-blue-100', 'text' => 'text-[#0055ff]', 'badge' => 'bg-blue-100 text-[#0055ff]'],
            'orange' => ['bg' => 'bg-orange-50',  'border' => 'border-orange-100', 'text' => 'text-orange-700', 'badge' => 'bg-orange-100 text-orange-700'],
            'green'  => ['bg' => 'bg-green-50',   'border' => 'border-green-100',  'text' => 'text-green-700',  'badge' => 'bg-green-100 text-green-700'],
        ];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @foreach($grupos as $g)
            @php $c = $corMap[$g['cor']]; @endphp
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <div class="{{ $c['bg'] }} {{ $c['border'] }} border-b px-5 py-3 flex items-center justify-between">
                    <span class="{{ $c['text'] }} font-semibold text-sm">{{ $g['origem'] }}</span>
                    @if($g['comissao'] > 0)
                        <span class="text-[11px] px-2 py-0.5 rounded-full {{ $c['badge'] }}">
                            Comissão: {{ money_aoa($g['comissao']) }}
                        </span>
                    @endif
                </div>
                <div class="divide-y divide-gray-100">
                    <div class="flex items-center justify-between px-5 py-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                            <span class="text-xs text-gray-500">Entradas</span>
                        </div>
                        <span class="font-semibold text-sm text-green-600">{{ money_aoa($g['entradas']) }}</span>
                    </div>
                    <div class="flex items-center justify-between px-5 py-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-red-400 inline-block"></span>
                            <span class="text-xs text-gray-500">Saídas</span>
                        </div>
                        <span class="font-semibold text-sm text-red-500">{{ money_aoa($g['saidas']) }}</span>
                    </div>
                    <div class="flex items-center justify-between px-5 py-3 bg-gray-50">
                        <span class="text-xs text-gray-400 font-medium">Saldo</span>
                        @php $saldo = $g['entradas'] - $g['saidas']; @endphp
                        <span class="font-bold text-sm {{ $saldo >= 0 ? 'text-gray-700' : 'text-red-600' }}">{{ money_aoa($saldo) }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ─── Fecho diário ────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden mt-6">
        <div class="px-5 py-4 flex items-center justify-between border-b border-gray-100">
            <div>
                <h3 class="text-sm font-bold text-gray-800">Fecho diário de conta</h3>
                <p class="text-xs text-gray-500 mt-0.5">Fecho automático todos os dias às 23:59. Pode fechar hoje manualmente a qualquer momento.</p>
            </div>
            <button wire:click="fecharHoje" wire:loading.attr="disabled" wire:target="fecharHoje" wire:confirm="Registar o fecho de hoje com os dados até agora?"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-semibold bg-[#0055ff] text-white hover:bg-blue-700 transition disabled:opacity-60">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Fechar hoje agora
            </button>
        </div>

        @if($fechos->isEmpty())
            <p class="text-xs text-gray-400 px-5 py-6 text-center">Ainda não há nenhum fecho registado.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="text-left text-gray-400 border-b border-gray-100">
                            <th class="px-5 py-2.5 font-medium">Data</th>
                            <th class="px-5 py-2.5 font-medium">Entradas</th>
                            <th class="px-5 py-2.5 font-medium">Saídas</th>
                            <th class="px-5 py-2.5 font-medium">Comissão</th>
                            <th class="px-5 py-2.5 font-medium">Saldo do dia</th>
                            <th class="px-5 py-2.5 font-medium">Saldo acumulado</th>
                            <th class="px-5 py-2.5 font-medium">Fechado por</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($fechos as $f)
                            <tr>
                                <td class="px-5 py-2.5 font-semibold text-gray-700">{{ $f->data->format('d/m/Y') }}</td>
                                <td class="px-5 py-2.5 text-green-600">{{ money_aoa($f->total_entradas) }}</td>
                                <td class="px-5 py-2.5 text-red-500">{{ money_aoa($f->total_saidas) }}</td>
                                <td class="px-5 py-2.5 text-[#0055ff]">{{ money_aoa($f->total_comissao) }}</td>
                                <td class="px-5 py-2.5 font-semibold {{ $f->saldo_liquido >= 0 ? 'text-gray-700' : 'text-red-600' }}">{{ money_aoa($f->saldo_liquido) }}</td>
                                <td class="px-5 py-2.5 font-semibold text-gray-700">{{ money_aoa($f->saldo_acumulado) }}</td>
                                <td class="px-5 py-2.5 text-gray-400">{{ $f->fechado_por ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
