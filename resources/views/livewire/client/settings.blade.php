<div class="max-w-xl mx-auto p-6 bg-white rounded shadow mt-8">
    <h2 class="text-2xl font-bold mb-4">Configurações da Conta</h2>
    <div class="mb-4">
        <label class="block text-gray-700 font-semibold mb-1">Alterar senha</label>
        <input type="password" class="w-full border rounded px-3 py-2" placeholder="Nova senha">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-semibold mb-1">Receber e-mails de novos projetos</label>
        <select wire:model="notify_new_project_email" class="w-full border rounded px-3 py-2">
            <option value="1">Ativado</option>
            <option value="0">Desativado</option>
        </select>
        @if (session('success'))
            <div class="mt-2 p-2 bg-green-100 text-green-700 rounded text-center text-sm">{{ session('success') }}</div>
        @endif
    </div>
    <div class="flex justify-end">
        <button class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-2 px-4 rounded">Salvar Alterações</button>
    </div>
</div>
