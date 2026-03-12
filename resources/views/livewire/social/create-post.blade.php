<div>
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-5">

        {{-- Content textarea --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                Partilhe algo com a sua audiência
            </label>
            <textarea wire:model="content" rows="5"
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-[#00baff]/40"
                placeholder="Escreva sobre o seu trabalho, conquistas, dicas profissionais..."></textarea>
            @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            <p class="text-xs text-gray-400 mt-1">{{ strlen($content) }}/2000 caracteres</p>
        </div>

        {{-- Image upload --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                Imagens
                <span class="font-normal text-gray-400">(opcional · máx. 5 imagens · 4 MB cada)</span>
            </label>
            <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-[#00baff]/50 hover:bg-blue-50/30 transition">
                <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                </svg>
                <p class="text-xs text-gray-400">Clique para selecionar imagens</p>
                <input type="file" wire:model="photos" multiple accept="image/*" class="hidden">
            </label>
            @error('photos') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @error('photos.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

            {{-- Preview thumbnails --}}
            @if(count($photos))
                <div class="grid grid-cols-5 gap-2 mt-3">
                    @foreach($photos as $i => $photo)
                        <div class="relative">
                            <img src="{{ $photo->temporaryUrl() }}" class="w-full aspect-square object-cover rounded-lg">
                            <button type="button" wire:click="removePhoto({{ $i }})"
                                class="absolute -top-1.5 -right-1.5 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600 transition">
                                ×
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Upload progress --}}
            <div wire:loading wire:target="photos" class="mt-2 text-xs text-[#00baff]">
                A fazer upload das imagens...
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex gap-3 pt-2">
            <button type="submit"
                class="flex-1 bg-[#00baff] hover:bg-[#009ad6] text-white text-sm font-semibold py-3 rounded-xl transition"
                wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed">
                <span wire:loading.remove wire:target="save">Publicar</span>
                <span wire:loading wire:target="save">A publicar...</span>
            </button>
            <a href="{{ route('social.feed') }}"
               class="px-6 py-3 border border-gray-200 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition text-center">
                Cancelar
            </a>
        </div>

    </form>
</div>
