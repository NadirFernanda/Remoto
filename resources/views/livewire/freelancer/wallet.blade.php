<div class="max-w-5xl mx-auto space-y-6">
    <div class="bg-gradient-to-r from-[#00baff] to-[#0095cc] rounded-2xl p-6 text-white">
        <h2 class="text-2xl font-extrabold">Carteira do Freelancer</h2>
        <p class="text-sm text-white/90 mt-1">Acompanhe saldos e solicite seus saques.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white border border-gray-200 rounded-2xl p-6">
            <p class="text-xs text-gray-500 mb-1">Saldo disponivel</p>
            <p class="text-2xl font-extrabold text-emerald-600">{{ number_format($saldo_disponivel, 2, ',', '.') }} Kz</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl p-6">
            <p class="text-xs text-gray-500 mb-1">Saldo pendente</p>
            <p class="text-2xl font-extrabold text-amber-600">{{ number_format($saldo_pendente, 2, ',', '.') }} Kz</p>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-900 mb-4">Solicitar Saque</h3>
        <form wire:submit.prevent="solicitarSaque" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Valor do saque</label>
                <input type="number" min="1" wire:model="valor_saque" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-[#00baff]/40 focus:border-[#00baff]">
            </div>
            <div class="rounded-xl bg-slate-50 px-4 py-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-600">Valor a receber</span>
                    <span class="text-emerald-600 font-bold">{{ number_format($valor_saque, 2, ',', '.') }} Kz</span>
                </div>
            </div>
            <div class="text-xs text-slate-500">Sem taxa de retirada. As comissoes da plataforma sao descontadas automaticamente.</div>
            <button type="submit" class="w-full bg-[#00baff] hover:bg-[#009ad6] text-white font-bold py-3 rounded-xl transition">Solicitar saque</button>
        </form>
        @if($mensagem)
            <div class="mt-4 rounded-xl bg-emerald-50 border border-emerald-200 px-3 py-2 text-sm text-emerald-700">{{ $mensagem }}</div>
        @endif
    </div>
</div>
