<div class="container mx-auto px-4 py-8 flex flex-col items-center">
    <h2 class="text-xl font-bold text-cyan-600 mb-4">Confirme e realize o pagamento</h2>
    <div class="w-full max-w-md bg-white rounded-lg shadow p-6">
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
            <svg class="w-6 h-6 text-cyan-500 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0-1.104.896-2 2-2s2 .896 2 2-.896 2-2 2-2-.896-2-2zm0 0V7m0 4v4m0 0h4m-4 0H8"/></svg>
            <span class="text-cyan-700">Seu pagamento ficará seguro até a entrega do serviço.</span>
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
                <div class="p-3 bg-blue-50 rounded text-blue-700">Você será redirecionado para o PayPal para finalizar o pagamento.</div>
            @elseif($payment_method === 'express')
                <div class="p-3 bg-yellow-50 rounded text-yellow-700">Pagamento via Express: instruções aparecerão aqui.</div>
            @elseif($payment_method === 'bank')
                <div class="p-3 bg-green-50 rounded text-green-700">Dados para transferência bancária: instruções aparecerão aqui.</div>
            @endif

            <button type="submit" class="w-full bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-3 rounded-lg transition-all duration-150">Pagar e publicar pedido</button>
        </form>
        <div class="text-xs text-gray-500 mt-4 text-center">Política de reembolso e segurança: O valor só será liberado ao freelancer após a entrega confirmada.</div>
        @if(session('success'))
            <div class="mt-4 p-2 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif

        @if(session('debug_valor'))
            <div class="mt-4 p-2 bg-yellow-100 text-yellow-700 rounded">{{ session('debug_valor') }}</div>
        @endif
    </div>
</div>
