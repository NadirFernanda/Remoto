<div>
    <div class="container mx-auto px-4 py-8 flex flex-col items-center">
    <h2 class="text-xl font-bold text-cyan-600 mb-2">O que você precisa?</h2>
    <p class="text-sm text-gray-600 mb-4">Passo 1 de 3 &middot; Descreva seu pedido para que possamos encontrar o freelancer ideal.</p>
    <div class="w-full max-w-md bg-white rounded-lg shadow p-6">
        <form wire:submit.prevent="submitBriefing">
            <div class="mb-6">
                <label class="block font-bold text-lg mb-2 text-cyan-700">Título do pedido <span class="text-red-500">*</span></label>
                <input type="text" wire:model.defer="title1" maxlength="100" required autocomplete="off" class="w-full border-2 border-cyan-500 rounded-lg px-4 py-3 text-lg font-semibold focus:ring-2 focus:ring-cyan-500 focus:outline-none bg-cyan-50" placeholder="Título único do pedido">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-2">Tipo de negócio</label>
                <input type="text" wire:model.defer="business_type1" autocomplete="off" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none" placeholder="Tipo de negócio (ex: Loja)">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-2">Qual é a sua necessidade?</label>
                <input type="text" wire:model.defer="necessity1" autocomplete="off" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none" placeholder="Necessidade específica">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-2">Público-alvo</label>
                <input type="text" wire:model.defer="target_audience1" autocomplete="off" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none" placeholder="Público-alvo único">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-2">Estilo desejado</label>
                <input type="text" wire:model.defer="style1" autocomplete="off" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none" placeholder="Estilo único desejado">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-2">Cores preferidas</label>
                <input type="text" wire:model.defer="colors1" autocomplete="off" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none" placeholder="Cores preferidas únicas">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-2">Onde será utilizado?</label>
                <input type="text" wire:model.defer="usage" autocomplete="off" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none" placeholder="Ex: Website, redes sociais, etc.">
            </div>
            <div class="mb-4">
                <button type="button" wire:click="generateDescription" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded w-full mb-2">Gerar descrição inteligente</button>
            </div>
            @if($generated_description)
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Descrição sugerida</label>
                    <textarea wire:model.defer="generated_description" rows="5" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none"></textarea>
                </div>
            @endif
            <div class="flex justify-end">
                <button type="submit" class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-2 px-4 rounded">Finalizar</button>
            </div>
        </form>
        <div class="mt-4 text-sm text-gray-500 text-center">
            Passo {{$step}} de 5
        </div>
    </div>
</div>
