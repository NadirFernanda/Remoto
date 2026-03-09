<div class="max-w-xl mx-auto p-6 bg-white rounded shadow mt-8">
    <h2 class="text-2xl font-bold mb-4">Meu Perfil</h2>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded">{{ session('error') }}</div>
    @endif

    {{-- Foto de perfil --}}
    <div class="mb-6">
        <label class="block text-gray-700 font-semibold mb-2">Foto de perfil</label>
        <div class="flex items-center gap-4">
            <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100 flex-shrink-0">
                @if($currentProfilePhoto)
                    <img class="w-full h-full object-cover" src="{{ Storage::url($currentProfilePhoto) }}" alt="Avatar">
                @else
                    <img class="w-full h-full object-cover" src="{{ asset('img/default-avatar.svg') }}" alt="Avatar">
                @endif
            </div>
            <div class="flex-1">
                <x-file-input wire:model="profilePhoto" accept="image/*" label="📷 Escolher nova foto" loading-target="profilePhoto">
                    @error('profilePhoto') <span class="text-red-600 text-sm block mt-1">{{ $message }}</span> @enderror
                    <p class="text-xs text-gray-500">Máx. 8 MB · jpg, png ou webp</p>
                </x-file-input>
                @error('profilePhoto') <span class="text-red-600 text-sm block mt-1">{{ $message }}</span> @enderror
                <p class="text-xs text-gray-500 mt-1">A foto é atualizada automaticamente ao selecionar o ficheiro. Máx. 8 MB — jpg, png ou webp.</p>
            </div>
        </div>
    </div>

    {{-- Dados pessoais editáveis --}}
    <div class="pub-field">
        <label for="cp-name">Nome</label>
        <input type="text" id="cp-name" wire:model.defer="name" class="pub-input" placeholder="O seu nome completo">
        @error('name') <div class="pub-field-error">{{ $message }}</div> @enderror
    </div>
    <div class="pub-field">
        <label>E-mail</label>
        <input type="email" class="pub-input" value="{{ $user->email }}" readonly style="opacity:.7;cursor:not-allowed;">
        <div style="font-size:.75rem;color:#64748b;margin-top:.25rem;">O e-mail não pode ser alterado aqui.</div>
    </div>
    <div class="pub-field">
        <label>Tipo de Conta</label>
        <input type="text" class="pub-input" value="{{ ucfirst($user->role) }}" readonly style="opacity:.7;cursor:not-allowed;">
    </div>
    <div class="pub-field">
        <label for="cp-phone">Telefone</label>
        <input type="text" id="cp-phone" wire:model.defer="phone" class="pub-input" placeholder="+244 900 000 000">
        @error('phone') <div class="pub-field-error">{{ $message }}</div> @enderror
    </div>
    <div class="pub-field">
        <label for="cp-location">Localização</label>
        <input type="text" id="cp-location" wire:model.defer="location" class="pub-input" placeholder="Ex.: Luanda, Angola">
        @error('location') <div class="pub-field-error">{{ $message }}</div> @enderror
    </div>
    <div class="action-row mb-6" role="toolbar" aria-label="Guardar dados pessoais">
        <button wire:click.prevent="saveProfile" class="btn-eq btn-primary" aria-label="Guardar perfil">
            @include('components.icon', ['name' => 'save', 'class' => 'mr-2'])
            <span>Guardar dados</span>
        </button>
    </div>

    <hr class="my-6">

    <div class="pub-field">
        <label for="cp-interests">Áreas de interesse (ex.: Marketing, WordPress)</label>
        <input type="text" id="cp-interests" wire:model.defer="interests_input" class="pub-input" placeholder="Adicione até 10 tags separadas por vírgula">
        <div class="text-xs text-gray-500 mt-1">Use tags separadas por vírgula. Isso ajuda freelancers a encontrar você.</div>
    </div>
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
            <span>Salvar interesses</span>
        </button>
    </div>
</div>
