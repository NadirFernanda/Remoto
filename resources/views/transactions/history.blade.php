@extends('layouts.main')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-sky-50/40 pb-16">

    {{-- ── Header ── --}}
    <div class="bg-white border-b border-slate-100 shadow-sm">
        <div class="max-w-5xl mx-auto px-6 py-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#00baff] to-blue-600 flex items-center justify-center shadow-lg shadow-sky-200">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800 leading-tight">Histórico de Transações</h1>
                    <p class="text-sm text-slate-500">Movimentos de saldo, compras e comissões</p>
                </div>
            </div>
            <a href="{{ route('client.finance.exportCsv') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#00baff] to-blue-600 hover:opacity-90 text-white text-sm font-semibold transition shadow-md shadow-sky-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16"/></svg>
                Exportar CSV
            </a>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-6 pt-8">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <span class="text-sm font-semibold text-slate-700">Movimentos</span>
                <span class="text-xs text-slate-400">{{ $transactions->total() }} registo(s)</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="bg-slate-50/80">
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Descrição</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Valor (AOA)</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($transactions as $t)
                            <tr class="hover:bg-sky-50/30 transition">
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                        {{ $t->valor > 0 ? 'text-emerald-700 bg-emerald-50 border border-emerald-200' : 'text-red-700 bg-red-50 border border-red-200' }}">
                                        {{ $t->tipo }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $t->descricao }}</td>
                                <td class="px-4 py-3 text-right font-semibold {{ $t->valor > 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $t->valor > 0 ? '+' : '' }}{{ number_format($t->valor, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-slate-500 whitespace-nowrap">
                                    {{ $t->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-slate-400">
                                    Ainda não há movimentos registados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transactions->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

