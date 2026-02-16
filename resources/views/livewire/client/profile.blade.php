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
    <div class="mb-4">
        <label class="block text-gray-700 font-semibold mb-1">Áreas de interesse (ex.: Marketing, WordPress)</label>
        <input type="text" wire:model.defer="interests_input" class="w-full border rounded px-3 py-2" placeholder="Adicione até 10 tags separadas por vírgula">
        <div class="text-xs text-gray-500 mt-1">Use tags separadas por vírgula. Isso ajuda freelancers a encontrar você.</div>
    </div>
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded">{{ session('success') }}</div>
    @endif
    <div class="mb-4">
        <label class="block text-gray-700 font-semibold mb-1">Áreas salvas</label>
        @if($user->profile && $user->profile->interests)
            <div class="flex flex-wrap gap-2">
                @foreach($user->profile->interests as $tag)
                    <span class="px-3 py-1 rounded-full bg-gray-100 text-sm">{{ $tag }}</span>
                @endforeach
            </div>
        @else
            <div class="text-sm text-gray-500">Nenhuma área salva.</div>
        @endif
    </div>
    <div class="action-row mt-4" role="toolbar" aria-label="Ações do perfil">
        <button wire:click.prevent="saveInterests" class="btn-eq btn-primary" aria-label="Salvar interesses">
            @include('components.icon', ['name' => 'save', 'class' => 'mr-2'])
            <span>Salvar</span>
        </button>
        <a href="#" class="btn-eq btn-outline" aria-label="Editar perfil">
            @include('components.icon', ['name' => 'edit', 'class' => 'mr-2'])
            Editar Perfil
        </a>
    </div>
</div>
