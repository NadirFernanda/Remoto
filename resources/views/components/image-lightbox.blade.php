@props(['src', 'alt' => '', 'triggerClass' => ''])

{{--
    Envolve uma imagem clicável que abre em tamanho maior, num overlay a ecrã
    inteiro — como em qualquer rede social. Uso:

    <x-image-lightbox :src="$user->avatarUrl()" alt="{{ $user->name }}">
        <img src="{{ $user->avatarUrl() }}" class="w-20 h-20 rounded-full ...">
    </x-image-lightbox>
--}}
<div x-data="{ open: false }">
    <div @click="open = true" class="cursor-zoom-in {{ $triggerClass }}">
        {{ $slot }}
    </div>

    <div x-show="open" x-cloak x-transition.opacity
         @click="open = false" @keydown.escape.window="open = false"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-8"
         style="background:rgba(0,0,0,.9)">
        <button @click.stop="open = false" type="button"
                class="absolute top-4 right-4 z-10 w-10 h-10 rounded-full bg-white/10 text-white/80 hover:bg-white/20 hover:text-white transition flex items-center justify-center"
                aria-label="Fechar">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div @click.stop class="flex max-w-[min(90vw,900px)] max-h-[82vh] items-center justify-center rounded-2xl bg-[#0f172a] p-2 shadow-2xl">
            <img src="{{ $src }}" alt="{{ $alt }}" class="block max-h-[78vh] max-w-[min(86vw,860px)] rounded-xl object-contain">
        </div>
    </div>
</div>
