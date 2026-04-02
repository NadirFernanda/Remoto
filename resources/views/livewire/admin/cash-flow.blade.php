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
        {{-- Datas customizadas --}}
        <div class="flex items-center gap-2 ml-auto">
            <input type="date" wire:model.live="dateStart" class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs text-gray-600 focus:ring-2 focus:ring-[#00baff] focus:outline-none" />
            <span class="text-xs text-gray-400">a</span>
            <input type="date" wire:model.live="dateEnd"  class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs text-gray-600 focus:ring-2 focus:ring-[#00baff] focus:outline-none" />
        </div>
        {{-- Export --}}
        <a href="{{ route('admin.reports.cashflow.csv', ['period' => $period, 'date_start' => $dateStart, 'date_end' => $dateEnd]) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs border border-gray-200 bg-white text-gray-600 hover:border-[#00baff] hover:text-[#00baff] transition">
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
            <p class="text-xl font-bold text-[#00baff]">{{ money_aoa($totalComissao) }}</p>
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
            'purple' => ['bg' => 'bg-purple-50',  'border' => 'border-purple-100', 'text' => 'text-purple-700', 'badge' => 'bg-purple-100 text-purple-700'],
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
</div>
