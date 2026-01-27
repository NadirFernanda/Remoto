<div class="container mx-auto px-4 py-8 flex flex-col items-center">
    <h2 class="text-xl font-bold text-cyan-600 mb-4">Defina o valor do serviço</h2>
    <div class="w-full max-w-md bg-white rounded-lg shadow p-6">
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
</div>
