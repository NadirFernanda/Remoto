  <div>
    @if($successMessage)
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg font-semibold text-sm">
            {{ $successMessage }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit.prevent="saveProfile">
        {{-- User personal fields --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Foto de perfil</label>
            <div class="flex items-center gap-4">
                <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100">
                    <img class="w-full h-full object-cover" src="{{ $currentProfilePhoto ? Storage::url($currentProfilePhoto) : asset('img/default-avatar.svg') }}" alt="Avatar">
                </div>
                <div class="flex-1">
                    <x-file-input wire:model="profilePhoto" accept="image/*" label="📷 Escolher nova foto" loading-target="profilePhoto">
                        @error('profilePhoto') <span class="text-red-600 text-sm block">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500">Rosto visível, sem logos · mínimo 200×200 · jpg/png/webp · máx. 8 MB</p>
                    </x-file-input>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nome</label>
                <input type="text" wire:model.defer="name" class="block w-full rounded-lg border border-gray-200 py-2 px-3">
                @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">E-mail</label>
                <input type="email" wire:model.defer="email" class="block w-full rounded-lg border border-gray-200 py-2 px-3">
                @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Telefone</label>
                <input type="text" wire:model.defer="phone" class="block w-full rounded-lg border border-gray-200 py-2 px-3">
                @error('phone') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Localização</label>
                <input type="text" wire:model.defer="location" class="block w-full rounded-lg border border-gray-200 py-2 px-3">
                @error('location') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Professional fields --}}
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Título profissional</label>
                <input type="text" wire:model.defer="headline" class="block w-full rounded-lg border border-gray-200 py-2 px-3">
                @error('headline') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Taxa por hora</label>
                <input type="text" wire:model.defer="hourly_rate" class="block w-full rounded-lg border border-gray-200 py-2 px-3">
                @error('hourly_rate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Moeda</label>
                <input type="text" wire:model.defer="currency" class="block w-full rounded-lg border border-gray-200 py-2 px-3">
                @error('currency') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Disponibilidade</label>
                <select wire:model.defer="availability_status" class="block w-full rounded-lg border border-gray-200 py-2 px-3">
                    <option value="available">Disponível</option>
                    <option value="busy">Ocupado</option>
                    <option value="unavailable">Indisponível</option>
                </select>
                @error('availability_status') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
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
            <textarea wire:model.defer="summary" rows="4" class="block w-full rounded-lg border border-gray-200 py-2 px-3"></textarea>
            @error('summary') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Skills (vírgula separado)</label>
                <input type="text" wire:model.defer="skills" class="block w-full rounded-lg border border-gray-200 py-2 px-3">
                @error('skills') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Idiomas (vírgula separado)</label>
                <input type="text" wire:model.defer="languages" class="block w-full rounded-lg border border-gray-200 py-2 px-3">
                @error('languages') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Metrics inputs --}}
        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Projetos concluídos</label>
                <input type="number" wire:model.defer="metrics_completed_projects" class="block w-full rounded-lg border border-gray-200 py-2 px-3">
                @error('metrics_completed_projects') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Avaliação média (0-5)</label>
                <input type="number" step="0.1" wire:model.defer="metrics_rating" class="block w-full rounded-lg border border-gray-200 py-2 px-3">
                @error('metrics_rating') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Ganhos totais</label>
                <input type="number" step="0.01" wire:model.defer="metrics_total_earnings" class="block w-full rounded-lg border border-gray-200 py-2 px-3">
                @error('metrics_total_earnings') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
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

        <div class="mt-6 flex gap-3">
            <button type="submit" class="inline-flex items-center gap-2 bg-[#00baff] hover:bg-[#009ad6] text-white font-semibold px-5 py-2 rounded-lg transition" aria-label="Salvar perfil">
                @include('components.icon', ['name' => 'save', 'class' => 'h-4 w-4'])
                Salvar perfil
            </button>
            <a href="{{ route('freelancer.dashboard') }}" class="inline-flex items-center gap-2 border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold px-5 py-2 rounded-lg transition">
                Cancelar
            </a>
        </div>
    </form>
</div>
