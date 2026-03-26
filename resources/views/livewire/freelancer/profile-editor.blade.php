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

    {{-- ══════════════════════════════════════════════════════════════════
         HISTÓRICO PROFISSIONAL
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="mt-8">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#00baff]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Histórico Profissional
            </h3>
            <button type="button" wire:click="openExpForm()"
                    class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#00baff] hover:text-[#009ad6] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Adicionar
            </button>
        </div>

        @if(count($experiences) === 0)
            <div class="text-sm text-gray-400 italic py-3 px-4 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                Nenhuma experiência adicionada ainda.
            </div>
        @else
            <div class="space-y-3">
                @foreach($experiences as $exp)
                    @php
                        $meses = ['','Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
                        $inicio = trim(($meses[$exp['mes_inicio'] ?? 0] ?? '') . ' ' . ($exp['ano_inicio'] ?? ''));
                        $fim    = $exp['atual'] ? 'Actualmente' : trim(($meses[$exp['mes_fim'] ?? 0] ?? '') . ' ' . ($exp['ano_fim'] ?? ''));
                        $periodo = $inicio || $fim ? trim("$inicio – $fim") : '';
                    @endphp
                    <div class="flex gap-4 p-4 bg-white border border-gray-200 rounded-xl hover:border-[#00baff]/40 transition group">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#00baff]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-800 text-sm">{{ $exp['titulo'] }}</p>
                            <p class="text-sm text-gray-600">{{ $exp['empresa'] }}{{ $exp['cidade'] ? ' · ' . $exp['cidade'] : '' }}{{ $exp['pais'] ? ', ' . $exp['pais'] : '' }}</p>
                            @if($periodo)
                                <p class="text-xs text-gray-400 mt-0.5">{{ $periodo }}</p>
                            @endif
                            @if($exp['descricao'])
                                <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $exp['descricao'] }}</p>
                            @endif
                        </div>
                        <div class="flex-shrink-0 flex gap-2 opacity-0 group-hover:opacity-100 transition">
                            <button type="button" wire:click="openExpForm({{ $exp['id'] }})"
                                    class="p-1.5 text-gray-400 hover:text-[#00baff] transition" title="Editar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </button>
                            <button type="button" wire:click="deleteExperience({{ $exp['id'] }})"
                                    wire:confirm="Remover esta experiência?"
                                    class="p-1.5 text-gray-400 hover:text-red-500 transition" title="Remover">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         EDUCAÇÃO
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="mt-8">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#00baff]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
                Educação
            </h3>
            <button type="button" wire:click="openEduForm()"
                    class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#00baff] hover:text-[#009ad6] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Adicionar
            </button>
        </div>

        @if(count($educations) === 0)
            <div class="text-sm text-gray-400 italic py-3 px-4 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                Nenhuma formação adicionada ainda.
            </div>
        @else
            <div class="space-y-3">
                @foreach($educations as $edu)
                    @php
                        $periodo = trim(($edu['ano_inicio'] ?? '') . ($edu['ano_fim'] || $edu['atual'] ? ' – ' . ($edu['atual'] ? 'A frequentar' : $edu['ano_fim']) : ''));
                    @endphp
                    <div class="flex gap-4 p-4 bg-white border border-gray-200 rounded-xl hover:border-[#00baff]/40 transition group">
                        <div class="flex-shrink-0 w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-800 text-sm">{{ $edu['escola'] }}</p>
                            <p class="text-sm text-gray-600">{{ implode(' · ', array_filter([$edu['grau'], $edu['area_estudo']])) }}</p>
                            @if($periodo)
                                <p class="text-xs text-gray-400 mt-0.5">{{ $periodo }}</p>
                            @endif
                            @if($edu['descricao'])
                                <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $edu['descricao'] }}</p>
                            @endif
                        </div>
                        <div class="flex-shrink-0 flex gap-2 opacity-0 group-hover:opacity-100 transition">
                            <button type="button" wire:click="openEduForm({{ $edu['id'] }})"
                                    class="p-1.5 text-gray-400 hover:text-[#00baff] transition" title="Editar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </button>
                            <button type="button" wire:click="deleteEducation({{ $edu['id'] }})"
                                    wire:confirm="Remover esta formação?"
                                    class="p-1.5 text-gray-400 hover:text-red-500 transition" title="Remover">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         MODAL — Experiência
    ══════════════════════════════════════════════════════════════════ --}}
    @if($showExpForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4" wire:click.self="closeExpForm">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h4 class="text-base font-bold text-gray-800">{{ $expForm['id'] ? 'Editar experiência' : 'Adicionar experiência' }}</h4>
                    <button type="button" wire:click="closeExpForm" class="text-gray-400 hover:text-gray-600 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    {{-- Título e Empresa --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cargo / Título <span class="text-red-500">*</span></label>
                            <input type="text" wire:model.defer="expForm.titulo" class="pub-input" placeholder="Ex.: Desenvolvedor Web">
                            @error('expForm.titulo') <div class="pub-field-error">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Empresa <span class="text-red-500">*</span></label>
                            <input type="text" wire:model.defer="expForm.empresa" class="pub-input" placeholder="Nome da empresa">
                            @error('expForm.empresa') <div class="pub-field-error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    {{-- Cidade e País --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                            <input type="text" wire:model.defer="expForm.cidade" class="pub-input" placeholder="Ex.: Luanda">
                            @error('expForm.cidade') <div class="pub-field-error">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">País</label>
                            <input type="text" wire:model.defer="expForm.pais" class="pub-input" placeholder="Ex.: Angola">
                            @error('expForm.pais') <div class="pub-field-error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    {{-- Período --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mês início</label>
                            <select wire:model.defer="expForm.mes_inicio" class="pub-input">
                                <option value="">—</option>
                                @foreach(['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'] as $i => $m)
                                    <option value="{{ $i+1 }}">{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ano início</label>
                            <input type="number" wire:model.defer="expForm.ano_inicio" class="pub-input" placeholder="2020" min="1950" max="2100">
                            @error('expForm.ano_inicio') <div class="pub-field-error">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mês fim</label>
                            <select wire:model.defer="expForm.mes_fim" class="pub-input" @if($expForm['atual']) disabled @endif>
                                <option value="">—</option>
                                @foreach(['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'] as $i => $m)
                                    <option value="{{ $i+1 }}">{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ano fim</label>
                            <input type="number" wire:model.defer="expForm.ano_fim" class="pub-input" placeholder="2023" min="1950" max="2100" @if($expForm['atual']) disabled @endif>
                            @error('expForm.ano_fim') <div class="pub-field-error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    {{-- Actualmente --}}
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="exp-atual" wire:model="expForm.atual" class="w-4 h-4 accent-[#00baff]">
                        <label for="exp-atual" class="text-sm text-gray-700">Trabalho aqui actualmente</label>
                    </div>
                    {{-- Descrição --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                        <textarea wire:model.defer="expForm.descricao" rows="3" class="pub-input" placeholder="Descreva as suas responsabilidades e conquistas…"></textarea>
                        @error('expForm.descricao') <div class="pub-field-error">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="px-6 py-4 border-t flex justify-end gap-3">
                    <button type="button" wire:click="closeExpForm" class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancelar</button>
                    <button type="button" wire:click="saveExperience" wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-[#00baff] hover:bg-[#009ad6] disabled:opacity-60 rounded-lg transition">
                        <span wire:loading.remove wire:target="saveExperience">Guardar</span>
                        <span wire:loading wire:target="saveExperience" class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                            A guardar…
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         MODAL — Educação
    ══════════════════════════════════════════════════════════════════ --}}
    @if($showEduForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4" wire:click.self="closeEduForm">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h4 class="text-base font-bold text-gray-800">{{ $eduForm['id'] ? 'Editar formação' : 'Adicionar formação' }}</h4>
                    <button type="button" wire:click="closeEduForm" class="text-gray-400 hover:text-gray-600 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Escola / Instituição <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.defer="eduForm.escola" class="pub-input" placeholder="Ex.: Universidade Agostinho Neto">
                        @error('eduForm.escola') <div class="pub-field-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Grau</label>
                            <select wire:model.defer="eduForm.grau" class="pub-input">
                                <option value="">— Selecionar —</option>
                                <option value="Ensino Secundário">Ensino Secundário</option>
                                <option value="Técnico Médio">Técnico Médio</option>
                                <option value="Bacharelato">Bacharelato</option>
                                <option value="Licenciatura">Licenciatura</option>
                                <option value="Pós-Graduação">Pós-Graduação</option>
                                <option value="Mestrado">Mestrado</option>
                                <option value="Doutoramento">Doutoramento</option>
                                <option value="Certificação">Certificação</option>
                                <option value="Curso Profissional">Curso Profissional</option>
                                <option value="Outro">Outro</option>
                            </select>
                            @error('eduForm.grau') <div class="pub-field-error">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Área de estudo</label>
                            <input type="text" wire:model.defer="eduForm.area_estudo" class="pub-input" placeholder="Ex.: Engenharia Informática">
                            @error('eduForm.area_estudo') <div class="pub-field-error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ano início</label>
                            <input type="number" wire:model.defer="eduForm.ano_inicio" class="pub-input" placeholder="2018" min="1950" max="2100">
                            @error('eduForm.ano_inicio') <div class="pub-field-error">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ano fim</label>
                            <input type="number" wire:model.defer="eduForm.ano_fim" class="pub-input" placeholder="2022" min="1950" max="2100" @if($eduForm['atual']) disabled @endif>
                            @error('eduForm.ano_fim') <div class="pub-field-error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="edu-atual" wire:model="eduForm.atual" class="w-4 h-4 accent-[#00baff]">
                        <label for="edu-atual" class="text-sm text-gray-700">A frequentar actualmente</label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição (opcional)</label>
                        <textarea wire:model.defer="eduForm.descricao" rows="3" class="pub-input" placeholder="Actividades, conquistas, notas relevantes…"></textarea>
                        @error('eduForm.descricao') <div class="pub-field-error">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="px-6 py-4 border-t flex justify-end gap-3">
                    <button type="button" wire:click="closeEduForm" class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancelar</button>
                    <button type="button" wire:click="saveEducation" wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-[#00baff] hover:bg-[#009ad6] disabled:opacity-60 rounded-lg transition">
                        <span wire:loading.remove wire:target="saveEducation">Guardar</span>
                        <span wire:loading wire:target="saveEducation" class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                            A guardar…
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
