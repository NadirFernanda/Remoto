<div class="container mx-auto px-4 py-8 flex flex-col items-center">
    <h2 class="text-xl font-bold text-cyan-600 mb-2">Confirme e realize o pagamento</h2>
    <p class="text-sm text-gray-600 mb-4">Passo 3 de 3 &middot; Revise os dados e escolha o método de pagamento.</p>
    <div class="w-full max-w-3xl grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 bg-white rounded-lg shadow p-6">
        <div class="mb-4">
            <div class="flex justify-between mb-2">
                <span>Valor do serviço</span>
                <span class="font-bold">{{ number_format($valor, 2, ',', '.') }} Kz</span>
            </div>
            <div class="flex justify-between mb-2">
                <span>Taxa da plataforma (10%)</span>
                <span class="text-yellow-600 font-bold">{{ number_format($valor * $taxa / 100, 2, ',', '.') }} Kz</span>
            </div>
            <div class="flex justify-between mb-2">
                <span>Valor líquido do freelancer</span>
                <span class="text-green-600 font-bold">{{ number_format($valor_liquido, 2, ',', '.') }} Kz</span>
            </div>
        </div>
        <div class="mb-4 p-3 bg-cyan-50 rounded flex items-center">
            @include('components.icon', ['name' => 'wallet', 'class' => 'w-6 h-6 text-cyan-500 mr-2'])
            <span class="text-cyan-700">O seu pagamento ficará seguro até à entrega do serviço.</span>
        </div>
        <form wire:submit.prevent="confirmPayment" class="space-y-4">
            <div>
                <label class="block font-semibold mb-1">Método de pagamento</label>
                <select wire:model="payment_method" wire:change="$refresh" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    <option value="card">Cartão de crédito</option>
                    <option value="paypal">PayPal</option>
                    <option value="express">Express</option>
                    <option value="bank">Transferência bancária</option>
                </select>
            </div>

            @if($payment_method === 'card')
                <div>
                    <label class="block font-semibold mb-1">Nome no cartão</label>
                    <input type="text" wire:model.defer="card_name" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none" placeholder="Como está no cartão">
                    @error('card_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block font-semibold mb-1">Número do cartão</label>
                    <input type="text" wire:model.defer="card_number" maxlength="19" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none" placeholder="0000 0000 0000 0000">
                    @error('card_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="flex space-x-2">
                    <div class="w-1/2">
                        <label class="block font-semibold mb-1">Validade</label>
                        <input type="text" wire:model.defer="card_expiry" maxlength="5" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none" placeholder="MM/AA">
                        @error('card_expiry') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="w-1/2">
                        <label class="block font-semibold mb-1">CVV</label>
                        <input type="text" wire:model.defer="card_cvv" maxlength="4" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none" placeholder="123">
                        @error('card_cvv') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            @elseif($payment_method === 'paypal')
                <div class="p-3 bg-blue-50 rounded text-blue-700">Será redireccionado para o PayPal para finalizar o pagamento.</div>
            @elseif($payment_method === 'express')
                <div class="p-3 bg-yellow-50 rounded text-yellow-700">Pagamento via Express: instruções aparecerão aqui.</div>
            @elseif($payment_method === 'bank')
                <div class="p-3 bg-green-50 rounded text-green-700">Dados para transferência bancária: instruções aparecerão aqui.</div>
            @endif

            <button type="submit" class="w-full bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-3 rounded-lg transition-all duration-150">Pagar e publicar pedido</button>
        </form>
        <div class="text-xs text-gray-500 mt-4 text-center">Política de reembolso e segurança: O valor só será libertado ao freelancer após a entrega confirmada.</div>
        @if(session('success'))
            <div class="mt-4 p-2 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mt-4 p-2 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
        @endif

        @if(session('debug_valor'))
            <div class="mt-4 p-2 bg-yellow-100 text-yellow-700 rounded">{{ session('debug_valor') }}</div>
        @endif
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
