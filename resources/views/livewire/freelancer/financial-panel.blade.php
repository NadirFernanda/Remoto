@extends('layouts.main')

@section('content')
<div class="light-page min-h-screen pt-8 pb-12">
<div class="max-w-5xl mx-auto px-4">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Painel Financeiro</h1>
            <p class="text-sm text-gray-500 mt-1">Acompanhe seus ganhos, taxas e movimentações</p>
        </div>
        {{-- Period filter --}}
        <div class="flex items-center gap-2">
            <label class="text-sm text-gray-500">Período:</label>
            <select wire:model.live="period" class="border rounded-lg px-3 py-1.5 text-sm focus:ring-1 focus:ring-[#00baff]">
                <option value="month">Este mês</option>
                <option value="last_month">Mês anterior</option>
                <option value="quarter">Últimos 3 meses</option>
                <option value="all">Todo o período</option>
            </select>
        </div>
    </div>

    {{-- ===== KPI CARDS ===== --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl border shadow-sm p-5">
            <div class="text-xs text-gray-500 mb-1 font-medium uppercase tracking-wide">Saldo disponível</div>
            <div class="text-2xl font-bold text-green-600">Kz {{ number_format($wallet->saldo ?? 0, 2, ',', '.') }}</div>
            <div class="text-xs text-gray-400 mt-1">pronto para saque</div>
        </div>
        <div class="bg-white rounded-2xl border shadow-sm p-5">
            <div class="text-xs text-gray-500 mb-1 font-medium uppercase tracking-wide">Pendente</div>
            <div class="text-2xl font-bold text-yellow-500">Kz {{ number_format($wallet->saldo_pendente ?? 0, 2, ',', '.') }}</div>
            <div class="text-xs text-gray-400 mt-1">em processamento</div>
        </div>
        <div class="bg-white rounded-2xl border shadow-sm p-5">
            <div class="text-xs text-gray-500 mb-1 font-medium uppercase tracking-wide">Ganhos no período</div>
            <div class="text-2xl font-bold text-[#00baff]">Kz {{ number_format($ganhos, 2, ',', '.') }}</div>
            <div class="text-xs text-gray-400 mt-1">bruto recebido</div>
        </div>
        <div class="bg-white rounded-2xl border shadow-sm p-5">
            <div class="text-xs text-gray-500 mb-1 font-medium uppercase tracking-wide">Taxas cobradas</div>
            <div class="text-2xl font-bold text-red-400">Kz {{ number_format($taxas, 2, ',', '.') }}</div>
            <div class="text-xs text-gray-400 mt-1">comissão da plataforma</div>
        </div>
    </div>

    {{-- Breakdown bar --}}
    @php
    $total_movimentos = $ganhos + $saques + $reembolsos;
    @endphp
    @if($total_movimentos > 0)
    <div class="bg-white rounded-2xl border shadow-sm p-5 mb-8">
        <h2 class="font-semibold text-gray-700 mb-3 text-sm">Resumo do período</h2>
        <div class="grid grid-cols-3 gap-4 text-center">
            <div>
                <div class="text-lg font-bold text-[#00baff]">Kz {{ number_format($ganhos, 2, ',', '.') }}</div>
                <div class="text-xs text-gray-500">Ganhos</div>
            </div>
            <div>
                <div class="text-lg font-bold text-red-400">Kz {{ number_format($taxas, 2, ',', '.') }}</div>
                <div class="text-xs text-gray-500">Taxas ({{ $ganhos > 0 ? number_format($taxas / $ganhos * 100, 1) : 0 }}%)</div>
            </div>
            <div>
                <div class="text-lg font-bold text-gray-600">Kz {{ number_format($saques, 2, ',', '.') }}</div>
                <div class="text-xs text-gray-500">Saques</div>
            </div>
        </div>
    </div>
    @endif

    {{-- ===== PENDING SERVICES ===== --}}
    @if($pendingServices->count())
    <div class="bg-white rounded-2xl border shadow-sm p-5 mb-8">
        <h2 class="font-semibold text-gray-700 mb-3">
            A receber
            <span class="ml-2 text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">{{ $pendingServices->count() }} serviço(s)</span>
        </h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-400 border-b">
                        <th class="pb-2 font-medium">Serviço</th>
                        <th class="pb-2 font-medium">Status</th>
                        <th class="pb-2 font-medium text-right">Valor líquido</th>
                        <th class="pb-2 font-medium"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($pendingServices as $svc)
                    <tr>
                        <td class="py-2 font-medium text-gray-800 max-w-xs truncate">{{ $svc->titulo ?? 'Serviço #'.$svc->id }}</td>
                        <td class="py-2">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                @if($svc->status === 'accepted') bg-blue-100 text-blue-700
                                @elseif($svc->status === 'in_progress') bg-purple-100 text-purple-700
                                @elseif($svc->status === 'delivered') bg-orange-100 text-orange-700
                                @endif">
                                @php $labels = ['accepted'=>'Aceito','in_progress'=>'Em andamento','delivered'=>'Entregue']; @endphp
                                {{ $labels[$svc->status] ?? $svc->status }}
                            </span>
                        </td>
                        <td class="py-2 text-right font-semibold text-green-600">
                            Kz {{ number_format($svc->valor_liquido ?? 0, 2, ',', '.') }}
                        </td>
                        <td class="py-2 text-right">
                            <a href="{{ route('freelancer.service.review', $svc->id) }}" class="text-xs text-[#00baff] hover:underline">Ver</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t border-gray-200">
                        <td colspan="2" class="pt-2 text-sm font-medium text-gray-600">Total previsto</td>
                        <td class="pt-2 text-right font-bold text-green-600">
                            Kz {{ number_format($pendingServices->sum('valor_liquido'), 2, ',', '.') }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif

    {{-- ===== TRANSACTION LOG ===== --}}
    <div class="bg-white rounded-2xl border shadow-sm p-5">
        <h2 class="font-semibold text-gray-700 mb-3">Extrato de movimentações</h2>

        @if($logs->isEmpty())
            <p class="text-sm text-gray-400 py-4 text-center">Nenhuma movimentação no período selecionado.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-400 border-b">
                        <th class="pb-2 font-medium">Data</th>
                        <th class="pb-2 font-medium">Tipo</th>
                        <th class="pb-2 font-medium">Descrição</th>
                        <th class="pb-2 font-medium text-right">Valor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($logs as $log)
                    @php
                    $tipoCor = match($log->tipo) {
                        'ganho'      => 'text-green-600',
                        'taxa'       => 'text-red-400',
                        'saque'      => 'text-gray-600',
                        'reembolso'  => 'text-blue-500',
                        default      => 'text-gray-500',
                    };
                    $tipoLabel = match($log->tipo) {
                        'ganho'      => 'Ganho',
                        'taxa'       => 'Taxa',
                        'saque'      => 'Saque',
                        'reembolso'  => 'Reembolso',
                        default      => ucfirst($log->tipo),
                    };
                    $sinal = in_array($log->tipo, ['ganho','reembolso']) ? '+' : '-';
                    @endphp
                    <tr>
                        <td class="py-2.5 text-gray-500 text-xs whitespace-nowrap">
                            {{ $log->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="py-2.5">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                @if($log->tipo === 'ganho') bg-green-50 text-green-700
                                @elseif($log->tipo === 'taxa') bg-red-50 text-red-600
                                @elseif($log->tipo === 'saque') bg-gray-100 text-gray-600
                                @else bg-blue-50 text-blue-600 @endif">
                                {{ $tipoLabel }}
                            </span>
                        </td>
                        <td class="py-2.5 text-gray-600 max-w-xs truncate">{{ $log->descricao ?? '—' }}</td>
                        <td class="py-2.5 text-right font-semibold {{ $tipoCor }}">
                            {{ $sinal }} Kz {{ number_format($log->valor, 2, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>
</div>
@endsection
