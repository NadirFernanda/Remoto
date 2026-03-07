<div class="container mx-auto px-4 py-8 flex flex-col items-center">
    <h2 class="text-xl font-bold text-cyan-600 mb-2">Defina o valor do serviço</h2>
    <p class="text-sm text-gray-600 mb-4">Passo 2 de 3 &middot; Escolha quanto deseja investir neste pedido.</p>
    <div class="w-full max-w-3xl grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 bg-white rounded-lg shadow p-6">
        <form wire:submit.prevent="submitValue">
            <div class="mb-4">
                <label class="block font-semibold mb-2">Valor do serviço (mínimo 10.000 Kz)</label>
                <input type="number" wire:model="valor" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                @error('valor') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4 bg-gray-50 rounded p-4">
                <div class="flex justify-between mb-2">
                    <span>Taxa da plataforma (10%)</span>
                    <span class="text-yellow-600 font-bold">{{ number_format($valor * $taxa / 100, 2, ',', '.') }} Kz</span>
                </div>
                <div class="flex justify-between">
                    <span>Valor líquido do freelancer</span>
                    <span class="text-green-600 font-bold">{{ number_format($valor_liquido, 2, ',', '.') }} Kz</span>
                </div>
            </div>
            <div class="text-sm text-gray-500 mb-4">A taxa de 10% será descontada do valor total.</div>
            <button type="submit" class="w-full bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-3 rounded-lg transition-all duration-150">Continuar</button>
        </form>
        @error('valor')
            <div class="mt-2 p-2 bg-red-100 text-red-700 rounded text-center">
                O valor deve ser maior ou igual a 10.000 Kz.
            </div>
        @enderror
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-sm">
            <h3 class="font-semibold text-cyan-700 mb-2">Resumo do pedido</h3>
            @php($order = session('client_order', []))
            <p class="mb-1"><span class="font-semibold">Título:</span> {{ $order['title'] ?? '-' }}</p>
            @php($b = $order['briefing_raw'] ?? [])
            <p class="mb-1"><span class="font-semibold">Tipo de negócio:</span> {{ $b['business_type'] ?? '-' }}</p>
            <p class="mb-1"><span class="font-semibold">Descrição do serviço:</span> {{ $b['necessity'] ?? '-' }}</p>
        </div>
    </div>
</div>
