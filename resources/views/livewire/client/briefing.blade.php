<div>
    <div class="container mx-auto px-4 py-8 flex flex-col items-center">
    <h2 class="text-xl font-bold text-cyan-600 mb-4">Briefing do Pedido</h2>
    <div class="w-full max-w-md bg-white rounded-lg shadow p-6">
        <form wire:submit.prevent="submitBriefing">
            @if($step === 1)
                <div class="mb-6">
                    <label class="block font-bold text-lg mb-2 text-cyan-700">Título do pedido <span class="text-red-500">*</span></label>
                    <input type="text" wire:model.defer="title" maxlength="100" required class="w-full border-2 border-cyan-500 rounded-lg px-4 py-3 text-lg font-semibold focus:ring-2 focus:ring-cyan-500 focus:outline-none bg-cyan-50" placeholder="Ex: Site institucional para minha empresa">
                </div>
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Qual é a sua necessidade?</label>
                    <input type="text" wire:model.defer="business_type" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none" placeholder="Descreva resumidamente o que você precisa">
                </div>
                <button type="button" wire:click="nextStep" class="w-full bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-3 rounded-lg mb-2">Próximo</button>
            @elseif($step === 2)
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Público-alvo</label>
                    <input type="text" wire:model.defer="target_audience" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                </div>
                <div class="flex justify-between">
                    <button type="button" wire:click="prevStep" class="bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded">Voltar</button>
                    <button type="button" wire:click="nextStep" class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-2 px-4 rounded">Próximo</button>
                </div>
            @elseif($step === 3)
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Estilo desejado</label>
                    <input type="text" wire:model.defer="style" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                </div>
                <div class="flex justify-between">
                    <button type="button" wire:click="prevStep" class="bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded">Voltar</button>
                    <button type="button" wire:click="nextStep" class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-2 px-4 rounded">Próximo</button>
                </div>
            @elseif($step === 4)
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Cores preferidas</label>
                    <input type="text" wire:model.defer="colors" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                </div>
                <div class="flex justify-between">
                    <button type="button" wire:click="prevStep" class="bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded">Voltar</button>
                    <button type="button" wire:click="nextStep" class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-2 px-4 rounded">Próximo</button>
                </div>
            @elseif($step === 5)
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Onde será utilizado?</label>
                    <input type="text" wire:model.defer="usage" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    @error('usage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Título do pedido</label>
                    <input type="text" wire:model.defer="title" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Necessidade</label>
                    <input type="text" wire:model.defer="business_type" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    @error('business_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Público-alvo</label>
                    <input type="text" wire:model.defer="target_audience" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    @error('target_audience') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Estilo desejado</label>
                    <input type="text" wire:model.defer="style" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    @error('style') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Cores preferidas</label>
                    <input type="text" wire:model.defer="colors" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    @error('colors') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-between">
                    <button type="button" wire:click="prevStep" class="bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded">Voltar</button>
                    <button type="submit" class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-2 px-4 rounded">Finalizar</button>
                </div>
            @endif
        </form>
        <div class="mt-4 text-sm text-gray-500 text-center">
            Passo {{$step}} de 5
        </div>
    </div>
</div>
