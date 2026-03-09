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
        <div class="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
            <label class="block text-sm font-medium text-gray-700 mb-3">Foto de perfil</label>
            <div class="flex items-center gap-4">
                <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100 flex-shrink-0 border-2 border-gray-200">
                    <img id="pe-avatar-preview" class="w-full h-full object-cover" src="{{ $currentProfilePhoto ? Storage::url($currentProfilePhoto) : asset('img/default-avatar.svg') }}" alt="Avatar">
                </div>
                <div class="flex-1">
                    <input
                        type="file"
                        wire:model="profilePhoto"
                        accept="image/jpeg,image/png,image/webp,image/gif"
                        class="block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border file:border-[#00baff] file:bg-white file:text-[#00baff] file:font-medium file:cursor-pointer hover:file:bg-[#00baff]/5 cursor-pointer"
                        onchange="if(this.files[0]){document.getElementById('pe-avatar-preview').src=URL.createObjectURL(this.files[0])}"
                    >
                    @error('profilePhoto') <div class="pub-field-error mt-1">{{ $message }}</div> @enderror
                    <p class="text-xs text-gray-400 mt-1">jpg, png ou webp · máx. 8 MB</p>
                    @if($photoMessage)
                        <div class="mt-1 text-sm font-semibold text-green-600">✓ {{ $photoMessage }}</div>
                    @endif
                </div>
                <div>
                    <button type="button" wire:click="savePhoto"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-1 bg-[#00baff] hover:bg-[#009ad6] disabled:opacity-60 text-white text-sm font-semibold px-4 py-2 rounded-lg transition whitespace-nowrap">
                        <span wire:loading.remove wire:target="savePhoto">💾 Guardar foto</span>
                        <span wire:loading wire:target="savePhoto">A guardar…</span>
                    </button>
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
                <label class="block text-sm font-medium text-gray-700">Projetos concluídos</label>
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

        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Adicionar ao portfólio (múltiplo)</label>
            <x-file-input wire:model="portfolioFiles" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" multiple label="Carregar ficheiros" loading-target="portfolioFiles">
                @error('portfolioFiles.*') <span class="text-red-600 text-sm block">{{ $message }}</span> @enderror
            </x-file-input>
        </div>

        <script>
            function bytesToMB(bytes) { return bytes / 1024 / 1024; }
            function validateProfilePhoto(input) {
                var errEl = document.getElementById('profilePhotoError');
                errEl.textContent = '';
                if (!input.files || !input.files[0]) return;
                var f = input.files[0];
                var maxBytes = 50 * 1024 * 1024; // 50MB
                if (f.size > maxBytes) {
                    errEl.textContent = 'Arquivo muito grande. Máximo permitido: 50MB.';
                    input.value = '';
                    return false;
                }
                return true;
            }
            function validatePortfolioFiles(input) {
                var errEl = document.getElementById('portfolioFilesError');
                errEl.textContent = '';
                if (!input.files || input.files.length === 0) return;
                var maxBytes = 50 * 1024 * 1024; // 50MB per file
                for (var i = 0; i < input.files.length; i++) {
                    var f = input.files[i];
                    if (f.size > maxBytes) {
                        errEl.textContent = 'Um dos arquivos é maior que 50MB. Remova arquivos grandes.';
                        input.value = '';
                        return false;
                    }
                }
                return true;
            }
        </script>

        <div class="mt-6">
            @if($successMessage)
                <div class="mb-3 p-3 bg-green-100 text-green-700 rounded-lg font-semibold text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    {{ $successMessage }}
                </div>
            @endif
            <div class="flex gap-3 items-center">
                <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center gap-2 bg-[#00baff] hover:bg-[#009ad6] disabled:opacity-60 text-white font-semibold px-5 py-2 rounded-lg transition" aria-label="Salvar perfil">
                    <span wire:loading.remove wire:target="saveProfile">
                        @include('components.icon', ['name' => 'save', 'class' => 'h-4 w-4'])
                        Salvar perfil
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
