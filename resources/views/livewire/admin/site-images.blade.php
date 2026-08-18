<div class="space-y-6">

    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <div class="mb-1">
            <h2 class="text-base font-semibold text-gray-800">Imagens do Site</h2>
        </div>
        <p class="text-xs text-gray-400">
            Carregue um ficheiro para substituir a imagem de imediato — não é preciso mexer em código nem fazer deploy.
            Formatos aceites: JPG, PNG ou WEBP, até 5&nbsp;MB. As imagens são redimensionadas automaticamente.
        </p>
    </div>

    {{-- ── Carrossel da homepage ── --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <div class="mb-5 pb-3 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700">Carrossel da Homepage</h3>
            <p class="text-xs text-gray-400 mt-1">Os 3 banners que giram no topo da página inicial.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @php $banners = [1 => ['banner1', $banner1, $banner1Path], 2 => ['banner2', $banner2, $banner2Path], 3 => ['banner3', $banner3, $banner3Path]]; @endphp
            @foreach($banners as $n => [$property, $tempFile, $currentPath])
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-2">Banner {{ $n }}</label>

                    <div class="aspect-video rounded-xl border border-gray-200 bg-gray-50 overflow-hidden mb-2 flex items-center justify-center">
                        @if($tempFile)
                            <img src="{{ $tempFile->temporaryUrl() }}" alt="Pré-visualização" class="w-full h-full object-cover">
                        @elseif($currentPath)
                            <img src="{{ Storage::url($currentPath) }}" alt="Banner {{ $n }} actual" class="w-full h-full object-cover">
                        @else
                            <span class="text-xs text-gray-400 px-3 text-center">A usar imagem padrão do sistema</span>
                        @endif
                    </div>

                    <input wire:model="{{ $property }}" type="file" accept="image/*"
                        class="block w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-[#0055ff]/10 file:text-[#0055ff] hover:file:bg-[#0055ff]/20 cursor-pointer">

                    <div wire:loading wire:target="{{ $property }}" class="text-xs text-[#0055ff] mt-1">A carregar e optimizar...</div>
                    @error($property) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                    @if($savedSlot === $property)
                        <p class="text-xs text-emerald-600 mt-1">Guardado — já está no ar.</p>
                    @endif

                    @if($currentPath)
                        <button type="button" wire:click="remove('{{ $property }}')" wire:confirm="Remover esta imagem e voltar à imagem padrão?"
                            class="text-xs text-red-500 hover:underline mt-1">
                            Remover e voltar ao padrão
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── Imagem de fundo ── --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <div class="mb-5 pb-3 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700">Imagem de Fundo</h3>
            <p class="text-xs text-gray-400 mt-1">Fundo da secção "Comunidade de Criadores" na página inicial.</p>
        </div>

        <div class="max-w-sm">
            <div class="aspect-video rounded-xl border border-gray-200 bg-gray-50 overflow-hidden mb-2 flex items-center justify-center">
                @if($this->background)
                    <img src="{{ $this->background->temporaryUrl() }}" alt="Pré-visualização" class="w-full h-full object-cover">
                @elseif($backgroundPath)
                    <img src="{{ Storage::url($backgroundPath) }}" alt="Fundo actual" class="w-full h-full object-cover">
                @else
                    <span class="text-xs text-gray-400 px-3 text-center">A usar imagem padrão do sistema</span>
                @endif
            </div>

            <input wire:model="background" type="file" accept="image/*"
                class="block w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-[#0055ff]/10 file:text-[#0055ff] hover:file:bg-[#0055ff]/20 cursor-pointer">

            <div wire:loading wire:target="background" class="text-xs text-[#0055ff] mt-1">A carregar e optimizar...</div>
            @error('background') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

            @if($savedSlot === 'background')
                <p class="text-xs text-emerald-600 mt-1">Guardado — já está no ar.</p>
            @endif

            @if($backgroundPath)
                <button type="button" wire:click="remove('background')" wire:confirm="Remover esta imagem e voltar à imagem padrão?"
                    class="text-xs text-red-500 hover:underline mt-1">
                    Remover e voltar ao padrão
                </button>
            @endif
        </div>
    </div>

    {{-- ── Tela de login ── --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <div class="mb-5 pb-3 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700">Tela de Login</h3>
            <p class="text-xs text-gray-400 mt-1">Imagem do painel direito na página de entrada.</p>
        </div>

        <div class="max-w-sm">
            <div class="aspect-[3/4] rounded-xl border border-gray-200 bg-gray-50 overflow-hidden mb-2 flex items-center justify-center">
                @if($this->login)
                    <img src="{{ $this->login->temporaryUrl() }}" alt="Pré-visualização" class="w-full h-full object-cover">
                @elseif($loginPath)
                    <img src="{{ Storage::url($loginPath) }}" alt="Imagem de login actual" class="w-full h-full object-cover">
                @else
                    <span class="text-xs text-gray-400 px-3 text-center">A usar imagem padrão do sistema</span>
                @endif
            </div>

            <input wire:model="login" type="file" accept="image/*"
                class="block w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-[#0055ff]/10 file:text-[#0055ff] hover:file:bg-[#0055ff]/20 cursor-pointer">

            <div wire:loading wire:target="login" class="text-xs text-[#0055ff] mt-1">A carregar e optimizar...</div>
            @error('login') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

            @if($savedSlot === 'login')
                <p class="text-xs text-emerald-600 mt-1">Guardado — já está no ar.</p>
            @endif

            @if($loginPath)
                <button type="button" wire:click="remove('login')" wire:confirm="Remover esta imagem e voltar à imagem padrão?"
                    class="text-xs text-red-500 hover:underline mt-1">
                    Remover e voltar ao padrão
                </button>
            @endif
        </div>
    </div>

</div>
