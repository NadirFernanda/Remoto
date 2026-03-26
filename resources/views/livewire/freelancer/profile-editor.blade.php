  <div>
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit="saveProfile">
        {{-- User personal fields --}}
        {{-- Foto de capa + foto de perfil --}}
        <div class="mb-6 rounded-xl border border-gray-200 overflow-hidden">
            {{-- Banner de capa --}}
            <div class="relative h-48 bg-gradient-to-r from-[#00baff] to-[#6a5acd]"
                 @if($currentCoverPhoto) style="background-image:url('{{ asset('storage/'.$currentCoverPhoto) }}');background-size:cover;background-position:center;" @endif>
                <label for="pe-cover-input"
                       class="absolute bottom-3 right-3 bg-black/50 hover:bg-black/70 text-white rounded-full p-2 cursor-pointer transition"
                       title="Alterar foto de capa">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0"/>
                    </svg>
                </label>
                <input type="file" id="pe-cover-input" wire:model="coverPhoto"
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
                            <img id="pe-avatar-preview" class="w-full h-full object-cover"
                                 src="{{ $currentProfilePhoto ? asset('storage/' . $currentProfilePhoto) : asset('img/default-avatar.svg') }}"
                                 alt="Foto de perfil">
                        </div>
                        <label for="pe-photo-input"
                               class="absolute bottom-0 right-0 bg-[#00baff] hover:bg-[#009ad6] text-white rounded-full p-1.5 cursor-pointer shadow transition"
                               title="Alterar foto de perfil">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0"/>
                            </svg>
                        </label>
                        <input type="file" id="pe-photo-input" wire:model="profilePhoto"
                               accept="image/jpeg,image/png,image/webp,image/gif"
                               style="position:absolute;width:1px;height:1px;opacity:0;overflow:hidden"
                               onchange="if(this.files[0]){document.getElementById('pe-avatar-preview').src=URL.createObjectURL(this.files[0])}">
                        <span wire:loading wire:target="profilePhoto"
                              class="absolute inset-0 rounded-full flex items-center justify-center bg-black/30 text-white text-xs">…</span>
                    </div>
                    <div class="pb-1 text-xs text-gray-400">
                        Clique nos ícones de câmara para alterar as fotos · jpg, png ou webp · máx. 8 MB
                        @error('profilePhoto') <div class="pub-field-error">{{ $message }}</div> @enderror
                        @if($photoMessage)
                            <div class="mt-1 text-sm font-semibold text-green-600">✓ {{ $photoMessage }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nome</label>
                <input type="text" wire:model.defer="name" id="pe-name" class="pub-input">
                @error('name') <div class="pub-field-error">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">E-mail</label>
                <input type="email" wire:model.defer="email" id="pe-email" class="pub-input">
                @error('email') <div class="pub-field-error">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Telefone</label>
                <input type="text" wire:model.defer="phone" class="pub-input">
                @error('phone') <div class="pub-field-error">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Localização</label>
                <input type="text" wire:model.defer="location" class="pub-input">
                @error('location') <div class="pub-field-error">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- Professional fields --}}
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Título profissional</label>
                <input type="text" wire:model.defer="headline" class="pub-input">
                @error('headline') <div class="pub-field-error">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Taxa por hora</label>
                <input type="text" wire:model.defer="hourly_rate" class="pub-input">
                @error('hourly_rate') <div class="pub-field-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Moeda</label>
                <input type="text" wire:model.defer="currency" class="pub-input">
                @error('currency') <div class="pub-field-error">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Disponibilidade</label>
                <select wire:model.defer="availability_status" class="pub-input">
                    <option value="available">Disponível</option>
                    <option value="busy">Ocupado</option>
                    <option value="unavailable">Indisponível</option>
                </select>
                @error('availability_status') <div class="pub-field-error">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Verificação de identidade (KYC)</label>
                @php
                    $kycLabels = ['pending' => ['Pendente', 'bg-yellow-100 text-yellow-700'], 'verified' => ['Verificado', 'bg-green-100 text-green-700'], 'rejected' => ['Rejeitado', 'bg-red-100 text-red-600']];
                    [$kycLabel, $kycClass] = $kycLabels[$kyc_status ?? 'pending'] ?? ['Pendente', 'bg-yellow-100 text-yellow-700'];
                @endphp
                <div class="flex items-center gap-3 mt-1">
                    <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $kycClass }}">{{ $kycLabel }}</span>
                    @if(($kyc_status ?? 'pending') !== 'verified')
                        <a href="{{ route('kyc.submit') }}" class="text-sm text-[#00baff] hover:underline">Verificar identidade →</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700">Resumo / Bio profissional</label>
            <textarea wire:model.defer="summary" rows="4" class="pub-input"></textarea>
            @error('summary') <div class="pub-field-error">{{ $message }}</div> @enderror
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Skills (vírgula separado)</label>
                <input type="text" wire:model.defer="skills" class="pub-input">
                @error('skills') <div class="pub-field-error">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Idiomas (vírgula separado)</label>
                <input type="text" wire:model.defer="languages" class="pub-input">
                @error('languages') <div class="pub-field-error">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- Metrics inputs --}}
        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Projectos concluídos</label>
                <input type="number" wire:model.defer="metrics_completed_projects" class="pub-input">
                @error('metrics_completed_projects') <div class="pub-field-error">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Avaliação média (0-5)</label>
                <input type="number" step="0.1" wire:model.defer="metrics_rating" class="pub-input">
                @error('metrics_rating') <div class="pub-field-error">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Ganhos totais</label>
                <input type="number" step="0.01" wire:model.defer="metrics_total_earnings" class="pub-input">
                @error('metrics_total_earnings') <div class="pub-field-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-xl flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium text-gray-700">Portfólio</p>
                <p class="text-xs text-gray-500 mt-0.5">Adicione trabalhos, certificações e estudos de caso no gestor de portfólio.</p>
            </div>
            <a href="{{ route('freelancer.portfolio') }}"
               class="inline-flex items-center gap-2 bg-[#00baff] hover:bg-[#009ad6] text-white text-sm font-semibold px-4 py-2 rounded-lg transition flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0l-4-4m4 4l-4 4"/>
                </svg>
                Gerir portfólio
            </a>
        </div>

        <div class="mt-6">
            @if($successMessage)
                <div class="mb-3 p-3 bg-green-100 text-green-700 rounded-lg font-semibold text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    {{ $successMessage }}
                </div>
            @endif
            <div class="flex gap-3 items-center">
                <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center gap-2 bg-[#00baff] hover:bg-[#009ad6] disabled:opacity-60 text-white font-semibold px-5 py-2 rounded-lg transition" aria-label="Guardar perfil">
                    <span wire:loading.remove wire:target="saveProfile">
                        @include('components.icon', ['name' => 'save', 'class' => 'h-4 w-4'])
                        Guardar perfil
                    </span>
                    <span wire:loading wire:target="saveProfile" class="flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                        A guardar…
                    </span>
                </button>
                <a href="{{ route('freelancer.dashboard') }}" class="inline-flex items-center gap-2 border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold px-5 py-2 rounded-lg transition">
                    Cancelar
                </a>
            </div>
        </div>
    </form>
</div>
