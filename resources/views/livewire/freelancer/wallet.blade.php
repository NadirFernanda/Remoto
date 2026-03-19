<div class="container mx-auto px-4 pb-8 max-w-xl">
    <h2 class="text-xl font-bold text-cyan-600 mb-4">Carteira do Freelancer</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-gray-700 mb-2">Saldo disponível</div>
            <div class="text-2xl font-bold text-green-600">{{ number_format($saldo_disponivel, 2, ',', '.') }} Kz</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-gray-700 mb-2">Saldo pendente</div>
            <div class="text-2xl font-bold text-yellow-600">{{ number_format($saldo_pendente, 2, ',', '.') }} Kz</div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-cyan-700 mb-4">Solicitar Saque</h3>
        <form wire:submit.prevent="solicitarSaque">
            <div class="mb-4">
                <label class="block font-semibold mb-2">Valor do saque</label>
                <input type="number" min="1" wire:model="valor_saque" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
            </div>
            <div class="mb-4 bg-gray-50 rounded p-4">
                <div class="flex justify-between">
                    <span>Valor a receber</span>
                    <span class="text-green-600 font-bold">{{ number_format($valor_saque, 2, ',', '.') }} Kz</span>
                </div>
            </div>
            <div class="text-sm text-gray-500 mb-4">Sem taxa de retirada — as comissões da plataforma são descontadas automaticamente em cada transação.</div>
            <button type="submit" class="w-full bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-3 rounded-lg transition-all duration-150">Solicitar saque</button>
        </form>
        @if($mensagem)
            <div class="mt-4 p-2 bg-green-100 text-green-700 rounded">{{ $mensagem }}</div>
        @endif
    </div>
</div>
