<div class="bg-white border border-gray-200 rounded-2xl p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Resumo Financeiro</h2>
            <p class="text-sm text-slate-500">Visao geral do seu saldo e ultimos movimentos.</p>
        </div>
        <a href="#" class="text-[#0055ff] hover:underline font-semibold text-sm">Ver extrato completo</a>
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
        @if(!is_null($balance) && $balance > 0 && auth()->user()->canSwitchRole())
            <form method="POST" action="{{ route('switch.role') }}">
                @csrf
                <input type="hidden" name="redirect_after" value="/freelancer/financeiro">
                <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold bg-gradient-to-r from-emerald-500 to-teal-500 hover:opacity-90 text-white transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Sacar no Modo Freelancer
                </button>
            </form>
        @endif
    </div>

    @if(!is_null($balance) && $balance > 0)
        <div class="mb-6 px-4 py-3 rounded-xl bg-sky-50 border border-sky-100 text-sm text-sky-800">
            Como cliente não é possível sacar directamente. Este saldo (ex: reembolsos) fica associado à sua conta e pode ser sacado a qualquer momento no <strong>Modo Freelancer</strong>, no Painel Financeiro.
        </div>
    @endif

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
