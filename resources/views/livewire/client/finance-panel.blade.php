<div class="p-4 bg-white rounded shadow mb-6">
    <h2 class="text-lg font-bold text-cyan-600 mb-2">Resumo Financeiro</h2>
    <div class="flex items-center justify-between mb-4">
        <div>
            <span class="text-gray-700">Saldo disponível:</span>
            <span class="text-2xl font-bold text-green-600">
                @if(is_null($balance))
                    -
                @else
                    {{ money_aoa($balance) }}
                @endif
            </span>
        </div>
        <a href="#" class="text-cyan-600 hover:underline font-semibold">Ver extrato completo</a>
    </div>
    <h3 class="text-md font-semibold mb-2">Pagamentos recentes</h3>
    <ul>
        @forelse($recentPayments as $payment)
            @php $amt = $payment['amount'] ?? null; @endphp
            <li class="flex justify-between items-center border-b py-2">
                <span>{{ $payment['description'] }}</span>
                <span class="font-bold {{ (!is_null($amt) && $amt > 0) ? 'text-green-600' : ((!is_null($amt) && $amt < 0) ? 'text-red-600' : '') }}">
                    @if(is_null($amt))
                        -
                    @elseif($amt == 0)
                        {{ money_aoa(0) }}
                    @else
                        {{ $amt > 0 ? '+' : '-' }}{{ money_aoa(abs($amt)) }}
                    @endif
                </span>
                <span class="text-gray-500 text-sm">{{ \Carbon\Carbon::parse($payment['created_at'])->format('d/m/Y') }}</span>
            </li>
        @empty
            <li class="text-gray-500">Nenhum pagamento recente.</li>
        @endforelse
    </ul>
</div>
