<div class="p-4 bg-white rounded shadow mb-6">
    <h2 class="text-lg font-bold text-cyan-600 mb-2">Resumo Financeiro</h2>
    <div class="flex items-center justify-between mb-4">
        <div>
            <span class="text-gray-700">Saldo disponível:</span>
            <span class="text-2xl font-bold text-green-600">R$ {{ number_format($balance, 2, ',', '.') }}</span>
        </div>
        <a href="#" class="text-cyan-600 hover:underline font-semibold">Ver extrato completo</a>
    </div>
    <h3 class="text-md font-semibold mb-2">Pagamentos recentes</h3>
    <ul>
        @forelse($recentPayments as $payment)
            <li class="flex justify-between items-center border-b py-2">
                <span>{{ $payment['description'] }}</span>
                <span class="{{ $payment['amount'] > 0 ? 'text-green-600' : 'text-red-600' }} font-bold">
                    {{ $payment['amount'] > 0 ? '+' : '-' }}R$ {{ number_format(abs($payment['amount']), 2, ',', '.') }}
                </span>
                <span class="text-gray-500 text-sm">{{ $payment['date'] }}</span>
            </li>
        @empty
            <li class="text-gray-500">Nenhum pagamento recente.</li>
        @endforelse
    </ul>
</div>
