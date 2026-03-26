<div class="max-w-2xl mx-auto p-6 bg-white rounded shadow mt-8 space-y-6">
    <h2 class="text-2xl font-bold mb-4">Configurações da Conta</h2>

    <div class="mb-4 border-b pb-5">
        <label class="block text-gray-700 font-semibold mb-1">Alterar palavra-passe</label>
        <input type="password" class="w-full border rounded px-3 py-2" placeholder="Nova palavra-passe">
    </div>

    <div class="mb-4 border-b pb-5">
        <label class="block text-gray-700 font-semibold mb-1">Receber e-mails de novos projectos</label>
        <select wire:model="notify_new_project_email" class="w-full border rounded px-3 py-2">
            <option value="1">Activado</option>
            <option value="0">Desactivado</option>
        </select>
        @if (session('success'))
            <div class="mt-2 p-2 bg-green-100 text-green-700 rounded text-center text-sm">{{ session('success') }}</div>
        @endif
    </div>
    <div class="border-b pb-5">
        <h3 class="text-lg font-semibold text-slate-800 mb-2">Desativar Conta</h3>
        <p class="text-sm text-gray-500 mb-3">A conta fica suspensa e pode ser reativada pelo suporte.</p>
        <input wire:model.defer="deactivatePassword" type="password" class="w-full border rounded px-3 py-2 mb-2" placeholder="Confirme a palavra-passe">
        @error('deactivatePassword')
            <p class="text-sm text-red-600 mb-2">{{ $message }}</p>
        @enderror
        <button wire:click="deactivateAccount" class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 px-4 rounded">
            Desativar conta
        </button>
    </div>

    <div>
        <h3 class="text-lg font-semibold text-red-700 mb-2">Remover Conta</h3>
        <p class="text-sm text-gray-500 mb-3">Esta ação é permanente. Digite REMOVER para confirmar.</p>
        <input wire:model.defer="deletePassword" type="password" class="w-full border rounded px-3 py-2 mb-2" placeholder="Confirme a palavra-passe">
        @error('deletePassword')
            <p class="text-sm text-red-600 mb-2">{{ $message }}</p>
        @enderror

        <input wire:model.defer="deleteConfirmation" type="text" class="w-full border rounded px-3 py-2 mb-2" placeholder="Digite REMOVER">
        @error('deleteConfirmation')
            <p class="text-sm text-red-600 mb-2">{{ $message }}</p>
        @enderror

        <button wire:click="deleteAccount" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
            Remover conta
        </button>
    </div>
</div>
