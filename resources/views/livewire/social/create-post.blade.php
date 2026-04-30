<div class="max-w-3xl mx-auto space-y-6" x-data="{ tab: @entangle('postType') }">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-[#00baff] to-[#0095cc] rounded-2xl p-6 text-white">
        <h2 class="text-2xl font-extrabold">Nova Publicação</h2>
        <p class="text-sm text-white/75 mt-1">Partilhe conteúdo com a comunidade 24Horas</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">

    @if(session('success'))
        <div class="p-3 bg-green-100 text-green-700 rounded-lg text-sm font-medium">{{ session('success') }}</div>
    @endif

    {{-- Type tabs --}}
    <div class="flex gap-1 bg-gray-100 rounded-2xl p-1 mb-6 overflow-x-auto">
        @foreach([
            ['type' => 'text',   'icon' => 'M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12', 'label' => 'Texto'],
            ['type' => 'image',  'icon' => 'M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 19.5h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm1.5-10.5h.008v.008H5.25V9zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z', 'label' => 'Imagem'],
            ['type' => 'video',  'icon' => 'M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9A2.25 2.25 0 0013.5 5.25h-9A2.25 2.25 0 002.25 7.5v9A2.25 2.25 0 004.5 18.75z', 'label' => 'Vídeo'],
            ['type' => 'audio',  'icon' => 'M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a2.25 2.25 0 001.632-2.163zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 01-.99-3.467l2.31-.66A2.25 2.25 0 009 15.553z', 'label' => 'Áudio'],
            ['type' => 'link',   'icon' => 'M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244', 'label' => 'Link'],
            ['type' => 'repost', 'icon' => 'M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3', 'label' => 'Repost'],
        ] as $t)
            <button type="button" wire:click="setType('{{ $t['type'] }}')"
                :class="tab === '{{ $t['type'] }}' ? 'bg-white text-[#00baff] shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold transition whitespace-nowrap flex-shrink-0">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $t['icon'] }}"/>
                </svg>
                {{ $t['label'] }}
            </button>
        @endforeach
    </div>

    <form wire:submit.prevent="save" class="space-y-5">

        {{-- Content textarea (all types) --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                @if($postType === 'repost') Adicione um comentário (opcional)
                @elseif($postType === 'link') Descrição (opcional)
                @else Escreva algo para partilhar
                @endif
            </label>
            <textarea wire:model.live="content" rows="4"
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-[#00baff]/40"
                placeholder="{{ $postType === 'text' ? 'Partilhe as suas conquistas, dicas, novidades... Use #hashtags para categorizar!' : ($postType === 'repost' ? 'O que acha desta publicação?' : 'Escreva uma legenda ou contexto...') }}"></textarea>
            @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            <p class="text-xs text-gray-400 mt-1">{{ strlen($content) }}/{{ $postType === 'repost' ? '1000' : '3000' }} caracteres</p>
        </div>

        {{-- IMAGE TYPE --}}
        @if($postType === 'image')
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Imagens <span class="font-normal text-gray-400">(máx. 5 · 8 MB cada)</span>
                </label>
                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-[#00baff]/60 hover:bg-blue-50/20 transition">
                    <svg class="w-8 h-8 text-gray-300 mb-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.338-2.32 5.75 5.75 0 011.423 7.55A4.5 4.5 0 0117.25 19.5H6.75z"/>
                    </svg>
                    <p class="text-xs text-gray-400">Clique para selecionar imagens</p>
                    <input type="file" wire:model="photos" multiple accept="image/*" class="hidden">
                </label>
                @error('photos') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                @error('photos.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <div wire:loading wire:target="photos" class="mt-2 text-xs text-[#00baff]">A processar imagens...</div>
                @if(count($photos))
                    <div class="grid grid-cols-5 gap-2 mt-3">
                        @foreach($photos as $i => $photo)
                            <div class="relative">
                                <img src="{{ $photo->temporaryUrl() }}" class="w-full aspect-square object-cover rounded-lg">
                                <button type="button" wire:click="removePhoto({{ $i }})"
                                    class="absolute -top-1.5 -right-1.5 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">×</button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        {{-- VIDEO TYPE --}}
        @if($postType === 'video')
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Vídeo <span class="font-normal text-gray-400">(MP4, WebM, MOV · máx. 200 MB)</span>
                </label>
                @if(!$video)
                    <label class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-[#00baff]/60 hover:bg-blue-50/20 transition">
                        <svg class="w-10 h-10 text-gray-300 mb-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9A2.25 2.25 0 0013.5 5.25h-9A2.25 2.25 0 002.25 7.5v9A2.25 2.25 0 004.5 18.75z"/>
                        </svg>
                        <p class="text-xs text-gray-400">Clique para selecionar o vídeo</p>
                        <input type="file" wire:model="video" accept="video/mp4,video/webm,video/quicktime,video/x-msvideo" class="hidden">
                    </label>
                @else
                    <div class="relative bg-gray-900 rounded-xl overflow-hidden">
                        <video src="{{ $video->temporaryUrl() }}" controls class="w-full max-h-64"></video>
                        <button type="button" wire:click="removeVideo"
                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full px-3 py-1 text-xs font-semibold">Remover</button>
                    </div>
                @endif
                @error('video') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <div wire:loading wire:target="video" class="mt-2 text-xs text-[#00baff]">
                    A carregar vídeo... Ficheiros grandes podem demorar alguns segundos.
                </div>
            </div>
        @endif

        {{-- AUDIO TYPE --}}
        @if($postType === 'audio')
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Áudio <span class="font-normal text-gray-400">(MP3, M4A, OGG, WAV · máx. 50 MB)</span>
                </label>
                @if(!$audio)
                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-[#00baff]/60 hover:bg-blue-50/20 transition">
                        <svg class="w-9 h-9 text-gray-300 mb-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a2.25 2.25 0 001.632-2.163zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 01-.99-3.467l2.31-.66A2.25 2.25 0 009 15.553z"/>
                        </svg>
                        <p class="text-xs text-gray-400">Clique para selecionar o áudio</p>
                        <input type="file" wire:model="audio" accept="audio/mpeg,audio/mp4,audio/ogg,audio/wav,audio/x-wav" class="hidden">
                    </label>
                @else
                    <div class="bg-gradient-to-r from-gray-50 to-blue-50/40 rounded-xl p-4 flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-[#00baff]/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-[#00baff]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a2.25 2.25 0 001.632-2.163z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $audio->getClientOriginalName() }}</p>
                            <audio controls class="w-full mt-1 h-8">
                                <source src="{{ $audio->temporaryUrl() }}" type="{{ $audio->getMimeType() }}">
                            </audio>
                        </div>
                        <button type="button" wire:click="removeAudio"
                            class="text-xs text-red-500 hover:text-red-700 font-semibold flex-shrink-0">Remover</button>
                    </div>
                @endif
                @error('audio') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <div wire:loading wire:target="audio" class="mt-2 text-xs text-[#00baff]">A carregar áudio...</div>
            </div>
        @endif

        {{-- LINK TYPE --}}
        @if($postType === 'link')
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">URL *</label>
                    <input wire:model="linkUrl" type="url" placeholder="https://..."
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/40">
                    @error('linkUrl') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Título do link (opcional)</label>
                    <input wire:model="linkTitle" type="text" placeholder="Ex: O meu novo artigo sobre design..."
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/40">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">URL de imagem de capa (opcional)</label>
                    <input wire:model="linkImage" type="url" placeholder="https://... (imagem da pré-visualização)"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/40">
                </div>
                {{-- Live preview --}}
                @if($linkUrl)
                    <div class="border border-gray-100 rounded-xl overflow-hidden bg-gray-50">
                        @if($linkImage)
                            <img src="{{ $linkImage }}" class="w-full h-32 object-cover" alt="" onerror="this.remove()">
                        @endif
                        <div class="p-3">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $linkTitle ?: $linkUrl }}</p>
                            @if($content) <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ $content }}</p> @endif
                            <p class="text-xs text-[#00baff] mt-1 truncate">{{ $linkUrl }}</p>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- REPOST TYPE --}}
        @if($postType === 'repost')
            @if($repostPost)
                <div class="border border-gray-100 rounded-xl bg-gray-50/50 overflow-hidden">
                    <div class="flex items-center gap-2 p-3 border-b border-gray-100">
                        <img src="{{ $repostPost->user->avatarUrl() }}" class="w-7 h-7 rounded-full object-cover"
                             onerror="this.src='{{ asset('img/default-avatar.svg') }}'">
                        <span class="text-sm font-semibold text-gray-800">{{ $repostPost->user->name }}</span>
                        <span class="text-xs text-gray-400 ml-auto">{{ $repostPost->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="p-3">
                        @if($repostPost->content)
                            <p class="text-sm text-gray-600 line-clamp-3">{{ $repostPost->content }}</p>
                        @endif
                        @if($repostPost->media->isNotEmpty())
                            @php $firstMedia = $repostPost->media->first(); @endphp
                            @if($firstMedia->type === 'image')
                                <img src="{{ $firstMedia->url() }}" class="mt-2 w-full max-h-32 object-cover rounded-lg">
                            @elseif($firstMedia->type === 'video')
                                <div class="mt-2 bg-gray-900 rounded-lg flex items-center justify-center h-20 text-gray-400 text-xs">
                                    <svg class="w-6 h-6 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72"/>
                                    </svg> Vídeo
                                </div>
                            @elseif($firstMedia->type === 'audio')
                                <div class="mt-2 flex items-center gap-2 text-gray-500 text-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 9l10.5-3m0 6.553v3.75"/>
                                    </svg> Áudio
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            @else
                <div class="p-4 border border-dashed border-gray-200 rounded-xl text-center text-sm text-gray-400">
                    Para repostar, clique em "Repost" numa publicação do feed.
                </div>
            @endif
            @error('repostId') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
        @endif

        {{-- Visibility --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Visibilidade</label>
            <div class="flex flex-col gap-2">
                <label class="flex items-start gap-2.5 cursor-pointer group">
                    <input type="radio" wire:model="visibility" value="public" class="text-[#00baff] mt-0.5">
                    <div>
                        <span class="text-sm font-medium text-gray-800">Público — qualquer pessoa pode ver</span>
                        <p class="text-xs text-gray-400 mt-0.5">Aparece no feed de todos os utilizadores da plataforma.</p>
                    </div>
                </label>
                <label class="flex items-start gap-2.5 cursor-pointer group">
                    <input type="radio" wire:model="visibility" value="followers" class="text-[#00baff] mt-0.5">
                    <div>
                        <span class="text-sm font-medium text-gray-800">Apenas assinantes</span>
                        <p class="text-xs text-gray-400 mt-0.5">Só quem paga a tua subscrição mensal pode ver este conteúdo.</p>
                    </div>
                </label>
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
</div>

