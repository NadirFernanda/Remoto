<div class="max-w-xl mx-auto p-6 bg-white rounded shadow mt-8">
    <h2 class="text-2xl font-bold mb-4">Meu Perfil</h2>
    <div class="mb-4">
        <label class="block text-gray-700 font-semibold mb-1">Nome</label>
        <input type="text" class="w-full border rounded px-3 py-2" value="{{ $user->name }}" readonly>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-semibold mb-1">E-mail</label>
        <input type="email" class="w-full border rounded px-3 py-2" value="{{ $user->email }}" readonly>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-semibold mb-1">Tipo de Conta</label>
        <input type="text" class="w-full border rounded px-3 py-2" value="{{ ucfirst($user->role) }}" readonly>
    </div>
    <div class="flex justify-end">
        <a href="#" class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-2 px-4 rounded">Editar Perfil</a>
    </div>
</div>
