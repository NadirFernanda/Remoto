<div x-data="{ valorSaque: 0, saldo: {{ $wallet->saldo ?? 0 }} }">

    {{-- ── SALDO HEADER ────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 mb-1 uppercase tracking-wide font-medium">Saldo disponível</p>
            <p class="text-2xl font-bold text-green-600">Kz {{ number_format($wallet->saldo ?? 0, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">pronto para saque</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 mb-1 uppercase tracking-wide font-medium">Pendente</p>
            <p class="text-2xl font-bold text-yellow-500">Kz {{ number_format($wallet->saldo_pendente ?? 0, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">em processamento</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 mb-1 uppercase tracking-wide font-medium">Ganhos no período</p>
            <p class="text-2xl font-bold text-[#00baff]">Kz {{ number_format($ganhos, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">recebido no período</p>
        </div>
    </div>

    {{-- ── CORPO PRINCIPAL: SAQUE + RESUMO ────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

        {{-- SAQUE (2/3) --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Solicitar Saque</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Sem taxa de retirada — comissões já descontadas em cada transação</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
                </div>
            </div>

            @if($successMsg)
            <div class="mb-4 flex items-start gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
                <svg class="w-5 h-5 mt-0.5 shrink-0 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $successMsg }}
            </div>
            @endif

            <form wire:submit.prevent="solicitarSaque" x-on:input.debounce.300ms="valorSaque = parseFloat($el.querySelector('[wire\\:model]').value) || 0">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Valor a sacar <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-semibold text-gray-400">Kz</span>
                        <input
                            type="number"
                            wire:model.live="valorSaque"
                            x-model="valorSaque"
                            min="1"
                            step="100"
                            placeholder="1000"
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 focus:border-[#00baff] focus:ring-1 focus:ring-[#00baff] outline-none text-sm transition @error('valorSaque') border-red-400 @enderror"
                        >
                    </div>
                    @error('valorSaque')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Calculadora em tempo real --}}
                <div class="bg-gray-50 rounded-xl p-4 mb-5 text-sm space-y-2.5" x-show="valorSaque > 0">
                    <div class="flex justify-between text-gray-600">
                        <span>Valor solicitado</span>
                        <span class="font-medium text-gray-800">Kz <span x-text="(+valorSaque || 0).toLocaleString('pt-AO', {minimumFractionDigits:2})"></span></span>
                    </div>
                    <div class="border-t border-gray-200 pt-2.5 flex justify-between">
                        <span class="font-semibold text-gray-700">Receberá</span>
                        <span class="font-bold text-green-600 text-base">Kz <span x-text="(+valorSaque || 0).toLocaleString('pt-AO', {minimumFractionDigits:2})"></span></span>
                    </div>
                </div>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full py-3 rounded-xl bg-[#00baff] hover:bg-[#009de0] text-white font-semibold text-sm transition disabled:opacity-60 flex items-center justify-center gap-2">
                    <span wire:loading.remove>Solicitar saque</span>
                    <span wire:loading class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        A processar…
                    </span>
                </button>
            </form>

            <p class="text-xs text-gray-400 mt-3 text-center">Os saques são processados em 1–3 dias úteis após aprovação.</p>
        </div>

        {{-- RESUMO + FILTRO (1/3) --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 flex flex-col gap-5">
            <div>
                <h2 class="text-base font-bold text-gray-800 mb-1">Resumo do período</h2>
                <select wire:model.live="period" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-1 focus:ring-[#00baff] outline-none">
                    <option value="month">Este mês</option>
                    <option value="last_month">Mês anterior</option>
                    <option value="quarter">Últimos 3 meses</option>
                    <option value="all">Todo o período</option>
                </select>
            </div>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 rounded-xl bg-blue-50">
                    <span class="text-xs font-medium text-gray-600">Ganhos</span>
                    <span class="text-sm font-bold text-[#00baff]">Kz {{ number_format($ganhos, 2, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50">
                    <span class="text-xs font-medium text-gray-600">Saques</span>
                    <span class="text-sm font-bold text-gray-700">Kz {{ number_format($saques, 2, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl bg-green-50">
                    <span class="text-xs font-medium text-gray-600">Reembolsos</span>
                    <span class="text-sm font-bold text-green-600">Kz {{ number_format($reembolsos, 2, ',', '.') }}</span>
                </div>
            </div>

            @if($pendingServices->count())
            <div class="border-t border-gray-100 pt-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">A receber</p>
                <div class="space-y-2">
                    @foreach($pendingServices->take(4) as $svc)
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs text-gray-700 truncate max-w-[120px]">{{ $svc->titulo ?? 'Serviço #'.$svc->id }}</span>
                        <span class="text-xs font-semibold text-green-600 shrink-0">Kz {{ number_format($svc->valor_liquido ?? 0, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                    @if($pendingServices->count() > 4)
                        <p class="text-xs text-gray-400">+ {{ $pendingServices->count() - 4 }} mais</p>
                    @endif
                </div>
                <div class="border-t border-gray-100 pt-2 mt-2 flex justify-between">
                    <span class="text-xs font-semibold text-gray-600">Total previsto</span>
                    <span class="text-xs font-bold text-green-600">Kz {{ number_format($pendingServices->sum('valor_liquido'), 0, ',', '.') }}</span>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ── EXTRATO ─────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
        <h2 class="text-base font-bold text-gray-800 mb-4">Extrato de movimentações</h2>

        @if($logs->isEmpty())
            <div class="text-center py-10">
                <svg class="w-10 h-10 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="text-sm text-gray-400">Nenhuma movimentação no período seleccionado.</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-400 border-b border-gray-100">
                        <th class="pb-3 font-medium">Data</th>
                        <th class="pb-3 font-medium">Tipo</th>
                        <th class="pb-3 font-medium hidden sm:table-cell">Descrição</th>
                        <th class="pb-3 font-medium text-right">Valor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($logs as $log)
                    @php
                        if ($log->tipo === 'taxa') continue;
                        $tipoCor   = match($log->tipo) { 'ganho','reembolso' => 'text-green-600', default => 'text-gray-600' };
                        $tipoLabel = match($log->tipo) { 'ganho'=>'Recebido','saque'=>'Saque','saque_solicitado'=>'Saque','reembolso'=>'Reembolso', default=>ucfirst(str_replace('_',' ',$log->tipo)) };
                        $bgLabel   = match($log->tipo) { 'ganho'=>'bg-green-50 text-green-700','saque','saque_solicitado'=>'bg-gray-100 text-gray-600','reembolso'=>'bg-blue-50 text-blue-600', default=>'bg-gray-100 text-gray-500' };
                        $sinal     = in_array($log->tipo, ['ganho','reembolso']) ? '+' : '−';
                    @endphp
                    <tr>
                        <td class="py-3 text-gray-400 text-xs whitespace-nowrap">{{ $log->created_at->format('d/m/Y') }}</td>
                        <td class="py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $bgLabel }}">{{ $tipoLabel }}</span>
                        </td>
                        <td class="py-3 text-gray-500 text-xs max-w-xs truncate hidden sm:table-cell">{{ $log->descricao ?? '—' }}</td>
                        <td class="py-3 text-right font-semibold text-sm {{ $tipoCor }}">{{ $sinal }} Kz {{ number_format(abs($log->valor), 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>
