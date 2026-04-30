<div class="space-y-6">
    {{-- ── Hero Header ── --}}
    <div class="bg-gradient-to-r from-[#00baff] to-[#0095cc] rounded-2xl p-6 text-white">
        <h2 class="text-2xl font-extrabold">Programa de Afiliado</h2>
        <p class="text-sm text-white/75 mt-1">Ganhe comissões por cada freelancer ou cliente que se registe pelo seu link</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 mb-1">Saldo Disponível</p>
            <p class="text-2xl font-extrabold text-[#00b894]">{{ money_aoa($saldoDisponivel, false) }}</p>
            <p class="text-xs text-gray-400 mt-1">Comissões já creditadas</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 mb-1">Total de Afiliados</p>
            <p class="text-2xl font-extrabold text-slate-900">{{ $totalAfiliados }}</p>
            <p class="text-xs text-gray-400 mt-1">Registos via o seu link</p>
        </div>

        <div class="bg-white rounded-2xl border border-[#bfe9dd] p-5">
            <p class="text-xs text-[#0f766e] mb-1">Comissão por Afiliado</p>
            <p class="text-2xl font-extrabold text-[#0f766e]">Kz {{ number_format($comissaoPorAfiliado, 0, ',', '.') }}</p>
            <p class="text-xs text-[#0f766e]/70 mt-1">Valor fixo por cada novo afiliado</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-5" x-data="{ copied: false }">
        <p class="text-sm font-semibold text-slate-800 mb-2">Link de Afiliado</p>

        @if($affiliateLink)
            <div class="flex flex-col sm:flex-row gap-2">
                <input id="affiliateLink" type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-sm" value="{{ $affiliateLink }}" readonly>
                <button
                    x-on:click="navigator.clipboard.writeText(document.getElementById('affiliateLink').value); copied = true; setTimeout(() => copied = false, 2000)"
                    class="px-4 py-2 bg-[#00baff] text-white rounded-lg hover:bg-[#029ed9] transition text-sm font-semibold">
                    Copiar link
                </button>
            </div>
            <p x-show="copied" x-transition class="text-xs text-emerald-600 mt-2">Link copiado com sucesso.</p>
        @else
            <div class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-700">
                Código de afiliado não encontrado. Gere o código no seu perfil para activar o programa.
            </div>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100">
            <p class="text-sm font-semibold text-slate-800">Histórico de Comissões</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="py-3 px-4">Data</th>
                        <th class="py-3 px-4">Valor</th>
                        <th class="py-3 px-4">Descrição</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $item)
                        <tr class="border-t border-gray-100">
                            <td class="py-3 px-4 text-gray-600">{{ \Carbon\Carbon::parse($item['created_at'])->format('d/m/Y') }}</td>
                            <td class="py-3 px-4 font-semibold text-[#00b894]">{{ money_aoa($item['amount'], false) }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ $item['description'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-4 px-4 text-gray-500">Nenhuma comissão registada ainda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
