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
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
         style="background:rgba(0,0,0,.9)">
        <button @click.stop="open = false" type="button"
                class="absolute top-4 right-4 text-white/80 hover:text-white transition"
                aria-label="Fechar">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <img @click.stop src="{{ $src }}" alt="{{ $alt }}" class="max-w-full max-h-[90vh] rounded-lg object-contain">
    </div>
</div>
