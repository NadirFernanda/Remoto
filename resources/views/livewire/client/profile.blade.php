<div class="max-w-5xl mx-auto space-y-6">
    <div class="bg-gradient-to-r from-[#00baff] to-[#0095cc] rounded-2xl p-6 text-white">
        <h2 class="text-2xl font-extrabold">Meu Perfil</h2>
        <p class="text-sm text-white/90 mt-1">Atualize os seus dados pessoais e preferencias.</p>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-2xl p-6">

    {{-- Foto de capa + foto de perfil --}}
    <div class="mb-6 rounded-xl border border-gray-200 overflow-hidden">
        {{-- Banner de capa --}}
        <div class="relative h-48 bg-gradient-to-r from-[#00baff] to-[#6a5acd]"
             @if($currentCoverPhoto) style="background-image:url('{{ asset('storage/'.$currentCoverPhoto) }}');background-size:cover;background-position:center;" @endif>
            <label for="cp-cover-input"
                   class="absolute bottom-3 right-3 bg-black/50 hover:bg-black/70 text-white rounded-full p-2 cursor-pointer transition"
                   title="Alterar foto de capa">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0"/>
                </svg>
            </label>
            <input type="file" id="cp-cover-input" wire:model="coverPhoto"
                   accept="image/jpeg,image/png,image/webp"
                   style="position:absolute;width:1px;height:1px;opacity:0;overflow:hidden"
                   onchange="if(this.files[0]){var b=this.closest('.relative');b.style.backgroundImage='url('+URL.createObjectURL(this.files[0])+')';b.style.backgroundSize='cover';b.style.backgroundPosition='center';}">
            <span wire:loading wire:target="coverPhoto"
                  class="absolute inset-0 flex items-center justify-center bg-black/30 text-white text-sm font-medium">
                A carregar capa…
            </span>
            @error('coverPhoto')
                <div class="absolute bottom-0 left-0 right-0 text-xs text-white bg-red-600/90 px-3 py-1">{{ $message }}</div>
            @enderror
        </div>
        {{-- Avatar sobreposto na capa --}}
        <div class="relative px-6 pb-4 bg-white">
            <div class="flex items-end gap-4 -mt-10">
                <div class="relative flex-shrink-0">
                    <div class="w-20 h-20 rounded-full overflow-hidden border-4 border-white shadow bg-gray-100">
                        <img id="cp-avatar-preview" class="w-full h-full object-cover"
                             src="{{ $currentProfilePhoto ? asset('storage/' . $currentProfilePhoto) : asset('img/default-avatar.svg') }}"
                             alt="Foto de perfil">
                    </div>
                    <label for="cp-photo-input"
                           class="absolute bottom-0 right-0 bg-[#00baff] hover:bg-[#009ad6] text-white rounded-full p-1.5 cursor-pointer shadow transition"
                           title="Alterar foto de perfil">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0"/>
                        </svg>
                    </label>
                    <input type="file" id="cp-photo-input" wire:model="profilePhoto"
                           accept="image/jpeg,image/png,image/webp,image/gif"
                           style="position:absolute;width:1px;height:1px;opacity:0;overflow:hidden"
                           onchange="if(this.files[0]){document.getElementById('cp-avatar-preview').src=URL.createObjectURL(this.files[0])}">
                    <span wire:loading wire:target="profilePhoto"
                          class="absolute inset-0 rounded-full flex items-center justify-center bg-black/30 text-white text-xs">…</span>
                </div>
                <div class="pb-1 text-xs text-gray-400">
                    Clique nos ícones de câmara para alterar as fotos · jpg, png ou webp · máx. 8 MB
                    @error('profilePhoto') <div class="pub-field-error">{{ $message }}</div> @enderror
                </div>
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
        <div class="text-xs text-gray-500 mt-1">Use tags separadas por vírgula. Isto ajuda os freelancers a encontrá-lo.</div>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-semibold mb-1">Áreas guardadas</label>
        @if($user->profile && $user->profile->interests)
            <div class="flex flex-wrap gap-2">
                @foreach($user->profile->interests as $tag)
                    <span class="px-3 py-1 rounded-full bg-gray-100 text-sm">{{ $tag }}</span>
                @endforeach
            </div>
        @else
            <div class="text-sm text-gray-500">Nenhuma área guardada.</div>
        @endif
    </div>
    <div class="action-row mt-4" role="toolbar" aria-label="Ações do perfil">
        <button wire:click.prevent="saveInterests" class="btn-eq btn-primary" aria-label="Guardar interesses">
            @include('components.icon', ['name' => 'save', 'class' => 'mr-2'])
            <span>Guardar interesses</span>
        </button>
    </div>
</div>
</div>
