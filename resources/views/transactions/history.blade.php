@extends('layouts.main')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-white">Histórico de Transações</h1>
        <a href="{{ route('client.finance.exportCsv') }}" class="text-sm px-4 py-2 rounded-lg font-medium"
           style="background:#00baff;color:#021018;">
            ⬇ Exportar CSV
        </a>
    </div>

    <div class="rounded-xl overflow-hidden border border-white/10" style="background:#0b1e2d;">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="text-white/50 text-xs uppercase border-b border-white/10">
                    <th class="px-4 py-3">Tipo</th>
                    <th class="px-4 py-3">Descrição</th>
                    <th class="px-4 py-3 text-right">Valor (AOA)</th>
                    <th class="px-4 py-3 text-right">Data</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $t)
                    <tr class="border-b border-white/5 hover:bg-white/5 transition">
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded text-xs font-semibold
                                {{ $t->valor > 0 ? 'text-green-400 bg-green-400/10' : 'text-red-400 bg-red-400/10' }}">
                                {{ $t->tipo }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-white/80">{{ $t->descricao }}</td>
                        <td class="px-4 py-3 text-right font-semibold
                            {{ $t->valor > 0 ? 'text-green-400' : 'text-red-400' }}">
                            {{ $t->valor > 0 ? '+' : '' }}{{ number_format($t->valor, 2, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-right text-white/50 whitespace-nowrap">
                            {{ $t->created_at->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-white/40">
                            Ainda não há movimentos registados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($transactions->hasPages())
        <div class="mt-6">
            {{ $transactions->links() }}
        </div>
    @endif
</div>
@endsection

