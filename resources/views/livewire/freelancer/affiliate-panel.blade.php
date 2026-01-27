<div class="p-6 bg-white rounded shadow max-w-2xl mx-auto mt-8">
    <h2 class="text-2xl font-bold mb-4">Programa de Afiliados</h2>
    <div class="mb-4">
        <label class="block text-gray-700 font-semibold">Seu código de afiliado:</label>
        <div class="flex items-center mt-2">
            <input id="affiliateCode" type="text" class="w-full px-3 py-2 border rounded-l bg-gray-100" value="{{ $affiliateCode }}" readonly>
            <button x-data x-on:click="navigator.clipboard.writeText(document.getElementById('affiliateCode').value); $dispatch('copied')" class="px-4 py-2 bg-blue-600 text-white rounded-r hover:bg-blue-700 focus:outline-none">Copiar</button>
        </div>
        <span x-data="{ show: false }" x-on:copied.window="show = true; setTimeout(() => show = false, 2000)" x-show="show" class="text-green-600 text-sm mt-2 block">Copiado!</span>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-semibold">Ganhos acumulados:</label>
        <div class="text-lg text-green-700 font-bold mt-1">R$ {{ number_format($earnings, 2, ',', '.') }}</div>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-semibold">Status do afiliado:</label>
        <span class="inline-block px-3 py-1 rounded-full text-white {{ $status === 'ativo' ? 'bg-green-500' : 'bg-gray-400' }}">
            {{ ucfirst($status) }}
        </span>
    </div>
    <div>
        <label class="block text-gray-700 font-semibold mb-2">Histórico de ganhos:</label>
        <table class="w-full text-left border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="py-2 px-3">Data</th>
                    <th class="py-2 px-3">Valor</th>
                    <th class="py-2 px-3">Descrição</th>
                </tr>
            </thead>
            <tbody>
                @forelse($history as $item)
                    <tr>
                        <td class="py-2 px-3">{{ $item['date'] }}</td>
                        <td class="py-2 px-3">R$ {{ number_format($item['amount'], 2, ',', '.') }}</td>
                        <td class="py-2 px-3">{{ $item['description'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-2 px-3 text-gray-500">Nenhum histórico encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
