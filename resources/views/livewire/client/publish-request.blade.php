<div class="container mx-auto px-4 py-8 flex flex-col items-center">
    <h1 class="text-2xl font-bold text-sky-600 mb-6">O que precisa?</h1>
    <form wire:submit.prevent="submit" class="w-full max-w-md">
        <label for="title" class="block text-lg font-bold text-sky-700 mb-2">Título do pedido <span class="text-red-500">*</span></label>
        <input type="text" id="title" wire:model.defer="title" maxlength="100" required
            class="w-full border-2 border-sky-500 rounded-lg px-4 py-3 text-lg font-semibold text-gray-900 focus:ring-2 focus:ring-sky-500 focus:outline-none mb-4 bg-sky-50"
            placeholder="Ex: Site institucional para minha empresa">
        <label for="need" class="block text-lg font-semibold text-gray-800 mb-2">Descreva a sua necessidade</label>
        <input type="text" id="need" wire:model.defer="need"
            class="w-full border border-sky-500 rounded-lg px-4 py-3 text-gray-900 focus:ring-2 focus:ring-sky-500 focus:outline-none mb-4"
            placeholder="Ex: Preciso de um logotipo" required>
        <button type="submit"
            class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-3 rounded-lg transition-all duration-150">
            Continuar
        </button>
    </form>
</div>
