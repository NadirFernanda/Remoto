<div>
    @if($savedMsg)
        <div class="mb-5 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl text-sm">{{ $savedMsg }}</div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-200 p-6 max-w-lg">
        <h2 class="text-base font-semibold text-gray-700 mb-5">Taxas da Plataforma</h2>

        <div class="mb-4">
            <label class="block text-xs text-gray-500 mb-1">Comissão sobre projectos (%)</label>
            <input wire:model="commissionRate" type="number" step="0.1" min="0" max="100"
                class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] @error('commissionRate') border-red-400 @enderror">
            @error('commissionRate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-xs text-gray-500 mb-1">Taxa fixa de saque (AOA)</label>
            <input wire:model="withdrawFeeFixed" type="number" step="0.01" min="0"
                class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] @error('withdrawFeeFixed') border-red-400 @enderror">
            @error('withdrawFeeFixed') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-5">
            <label class="block text-xs text-gray-500 mb-1">Taxa percentual de saque (%)</label>
            <input wire:model="withdrawFeePercent" type="number" step="0.01" min="0" max="100"
                class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] @error('withdrawFeePercent') border-red-400 @enderror">
            @error('withdrawFeePercent') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <button wire:click="save" class="btn-primary w-full">Guardar Taxas</button>
    </div>
</div>
