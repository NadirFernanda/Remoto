<div class="bg-white border border-gray-200 rounded-2xl p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Resumo Financeiro</h2>
            <p class="text-sm text-slate-500">Visao geral do seu saldo e ultimos movimentos.</p>
        </div>
        <a href="#" class="text-[#00baff] hover:underline font-semibold text-sm">Ver extrato completo</a>
    </div>

    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 mb-6">
        <div>
            <span class="text-sm text-slate-500">Saldo disponivel</span>
            <div class="text-2xl font-extrabold text-emerald-600">
                @if(is_null($balance))
                    -
                @else
                    {{ money_aoa($balance) }}
                @endif
            </div>
        </div>
    </div>

    <h3 class="text-sm font-semibold text-slate-600 mb-2">Pagamentos recentes</h3>
    <ul class="divide-y divide-slate-100">
        @forelse($recentPayments as $payment)
            @php $amt = $payment['amount'] ?? null; @endphp
            <li class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 py-3">
                <span class="text-slate-700">{{ $payment['description'] }}</span>
                <div class="flex items-center gap-4">
                    <span class="font-bold {{ (!is_null($amt) && $amt > 0) ? 'text-emerald-600' : ((!is_null($amt) && $amt < 0) ? 'text-red-600' : '') }}">
                        @if(is_null($amt))
                            -
                        @elseif($amt == 0)
                            {{ money_aoa(0) }}
                        @else
                            {{ $amt > 0 ? '+' : '-' }}{{ money_aoa(abs($amt)) }}
                        @endif
                    </span>
                    <span class="text-slate-500 text-xs">{{ \Carbon\Carbon::parse($payment['created_at'])->format('d/m/Y') }}</span>
                </div>
            </li>
        @empty
            <li class="text-slate-500 py-3">Nenhum pagamento recente.</li>
        @endforelse
    </ul>
</div>
