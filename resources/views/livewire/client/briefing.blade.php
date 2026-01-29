<div>
    <div>DEBUG: Entrou no briefing</div>
    <div class="container mx-auto px-4 py-8 flex flex-col items-center">
    <h2 class="text-xl font-bold text-cyan-600 mb-4">Briefing do Pedido</h2>
    <div class="w-full max-w-md bg-white rounded-lg shadow p-6">
        <form wire:submit.prevent="submitBriefing">
            @if($step === 1)
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Tipo de negócio</label>
                    <input type="text" wire:model.defer="business_type" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
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
