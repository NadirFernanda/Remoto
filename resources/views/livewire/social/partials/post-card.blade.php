{{--
    Partial: livewire.social.partials.post-card
    Variables: $post (SocialPost with media, likes, comments.user, repost.user, repost.media)
    Context: inside a Livewire component with toggleLike, toggleBookmark, openComments,
             toggleFollow, deletePost, openReportPost, openReportUser, $commentingPostId
--}}
@php
    $authUser      = auth()->user();
    $isOwner       = $authUser && $authUser->id === $post->user_id;
    $isLiked       = $authUser ? $post->isLikedBy($authUser->id) : false;
    $isBookmarked  = $authUser ? $post->isBookmarkedBy($authUser->id) : false;
    $likesCount    = $post->likesCount();
    $commentsCount = $post->commentsCount();
    $repostsCount  = $post->repostsCount();
    $showComments  = isset($commentingPostId) && $commentingPostId === $post->id;
    $isFollowing   = $authUser && !$isOwner
                     ? $authUser->following()->where('following_id', $post->user_id)->exists()
                     : false;
    // Creator gating — só bloqueia posts com visibility = 'followers', nunca os públicos
    $isCreatorPost = ($post->user->has_creator_profile ?? false);
    $isSubscribed  = $isOwner
                     || !$isCreatorPost
                     || ($post->visibility ?? 'public') !== 'followers'
                     || ($authUser && in_array($post->user_id, $subscribedCreatorIds ?? []));
@endphp

<article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" wire:key="post-{{ $post->id }}">

    {{-- ── Creator header ──────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between p-4">
        <a href="{{ route('social.creator', $post->user) }}" class="flex items-center gap-3 group">
            <img src="{{ $post->user->avatarUrl() }}"
                 alt="{{ $post->user->name }}"
                 class="w-10 h-10 rounded-full object-cover group-hover:ring-2 group-hover:ring-[#00baff]/40 transition"
                 onerror="this.src='{{ asset('img/default-avatar.svg') }}'">
            <div>
                <p class="text-sm font-semibold text-gray-900 group-hover:text-[#00baff] transition leading-tight">
                    {{ $post->user->name }}
                </p>
                @if($post->user->freelancerProfile?->headline)
                    <p class="text-xs text-gray-400 leading-tight">{{ $post->user->freelancerProfile->headline }}</p>
                @endif
            </div>
        </a>
        <div class="flex items-center gap-2">
            @auth
                @if(!$isOwner)
                    <button wire:click="toggleFollow({{ $post->user_id }})"
                        class="text-xs font-semibold px-3 py-1 rounded-lg border transition
                            {{ $isFollowing ? 'border-gray-200 text-gray-400 hover:border-red-200 hover:text-red-400' : 'border-[#00baff] text-[#00baff] hover:bg-[#00baff] hover:text-white' }}">
                        {{ $isFollowing ? 'A seguir' : '+ Seguir' }}
                    </button>
                @endif
            @endauth
            @if(isset($post->visibility) && $post->visibility === 'followers')
                <span class="inline-flex items-center gap-1 text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded-full px-2 py-0.5">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                    Assinantes
                </span>
            @endif
            <span class="text-xs text-gray-400">{{ $post->created_at->diffForHumans() }}</span>
            {{-- Options dropdown --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="p-1.5 rounded-full hover:bg-gray-100 transition text-gray-400">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/>
                    </svg>
                </button>
                <div x-show="open" x-transition @click.away="open = false"
                     class="absolute right-0 top-8 bg-white border border-gray-100 rounded-xl shadow-lg w-48 z-10 py-1">
                    @if($isOwner)
                        <button wire:click="openEditPost({{ $post->id }})" @click="open = false"
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                            Editar publicação
                        </button>
                        <button wire:click="deletePost({{ $post->id }})" @click="open = false"
                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                            Eliminar publicação
                        </button>
                    @else
                        @auth
                            <button wire:click="openReportPost({{ $post->id }})" @click="open = false"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                Denunciar publicação
                            </button>
                            <button wire:click="openReportUser({{ $post->user_id }})" @click="open = false"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                Denunciar utilizador
                            </button>
                        @endauth
                    @endif
                    <button
                        @click="navigator.clipboard.writeText('{{ url('/social') }}?post={{ $post->id }}'); open = false"
                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition border-t border-gray-50 mt-1">
                        Copiar link
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Text content ─────────────────────────────────────────────────────── --}}

    @if($isCreatorPost && !$isSubscribed)
    {{-- ── PREVIEW BLOQUEADO: mostra pré-visualização desfocada + overlay de subscrição ── --}}
    <div class="relative mx-0 mb-0 overflow-hidden"
         x-data="{ showModal: false }">

        {{-- Pré-visualização desfocada (texto + thumbnail) --}}
        <div class="select-none pointer-events-none">
            @if($post->content)
                <div class="px-4 pb-3">
                    <p class="text-sm text-gray-800 whitespace-pre-line leading-relaxed blur-sm">{!! Str::limit(strip_tags($post->content), 120) !!}...</p>
                </div>
            @endif
            @php $firstMedia = $post->media->first(); @endphp
            @if($firstMedia)
                <div class="relative bg-gray-100 overflow-hidden" style="max-height:220px;">
                    @if(in_array($firstMedia->type, ['image']))
                        <img src="{{ $firstMedia->url() }}" class="w-full object-cover blur-md scale-105" style="max-height:220px;" alt="" loading="lazy">
                    @elseif($firstMedia->type === 'video')
                        @if($firstMedia->thumbnailUrl())
                            <img src="{{ $firstMedia->thumbnailUrl() }}" class="w-full object-cover blur-md scale-105" style="max-height:220px;" alt="">
                        @else
                            <div class="w-full bg-gradient-to-br from-gray-800 to-gray-600 flex items-center justify-center" style="height:220px;">
                                <svg class="w-16 h-16 text-white/30" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </div>
                        @endif
                    @elseif($firstMedia->type === 'audio')
                        <div class="w-full bg-gradient-to-r from-[#00baff]/10 to-blue-50 flex items-center justify-center" style="height:80px;">
                            <svg class="w-8 h-8 text-[#00baff]/40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a2.25 2.25 0 001.632-2.163zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 01-.99-3.467l2.31-.66A2.25 2.25 0 009 15.553z"/></svg>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Overlay clicável sobre a pré-visualização --}}
        <div class="absolute inset-0 bg-gradient-to-t from-white/95 via-white/60 to-transparent flex flex-col items-center justify-end pb-5 cursor-pointer"
             @click="showModal = true">
            <div class="flex flex-col items-center gap-2">
                <div class="w-10 h-10 rounded-full bg-[#00baff]/10 border border-[#00baff]/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-[#00baff]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-gray-700">Clique para ver o conteúdo</span>
            </div>
        </div>

        {{-- Modal de subscrição (Alpine) --}}
        <div x-show="showModal" x-cloak
             style="position:fixed;inset:0;z-index:60;display:flex;align-items:center;justify-content:center;background:rgba(15,23,42,.65);backdrop-filter:blur(4px);"
             @click.self="showModal = false">
            <div style="background:#fff;border-radius:1.25rem;padding:2rem 1.75rem;max-width:380px;width:calc(100% - 2rem);box-shadow:0 24px 64px rgba(0,0,0,.25);text-align:center;">
                <div style="width:56px;height:56px;background:#e0f7ff;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                    <svg style="width:28px;height:28px;color:#00baff;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                    </svg>
                </div>
                <h3 style="font-size:1.05rem;font-weight:700;color:#0f172a;margin-bottom:.5rem;">Conteúdo exclusivo</h3>
                <p style="font-size:.85rem;color:#64748b;margin-bottom:.25rem;">Este conteúdo é exclusivo para assinantes de</p>
                <p style="font-size:.95rem;font-weight:700;color:#0f172a;margin-bottom:1.25rem;">{{ $post->user->name }}</p>
                <a href="{{ route('social.creator', $post->user) }}"
                   style="display:inline-flex;align-items:center;gap:.5rem;background:#00baff;color:#fff;font-size:.9rem;font-weight:700;padding:.7rem 1.5rem;border-radius:.75rem;text-decoration:none;margin-bottom:.75rem;">
                    <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                    </svg>
                    Assinar por 3.000 Kz/mês
                </a>
                <br>
                <button @click="showModal = false"
                        style="font-size:.8rem;color:#94a3b8;background:none;border:none;cursor:pointer;margin-top:.25rem;">
                    Fechar
                </button>
            </div>
        </div>
    </div>

    @else

    @if($post->content)
        <div class="px-4 pb-3">
            <p class="text-sm text-gray-800 whitespace-pre-line leading-relaxed">{!! $post->contentWithHashtags() !!}</p>
        </div>
    @endif

    {{-- ── Media block ─────────────────────────────────────────────────────── --}}

    {{-- IMAGES (new media table) --}}
    @if(isset($post->type) && in_array($post->type, ['image', 'text']) && $post->media->where('type','image')->isNotEmpty())
        @php $imgs = $post->media->where('type','image')->values(); $count = $imgs->count(); @endphp
        <div class="grid gap-0.5 {{ $count === 1 ? 'grid-cols-1' : ($count === 2 ? 'grid-cols-2' : 'grid-cols-3') }}">
            @foreach($imgs->take(3) as $i => $img)
                <div class="relative overflow-hidden bg-gray-100">
                    <img src="{{ $img->url() }}"
                         class="w-full h-auto object-contain {{ $count > 1 ? 'max-h-72' : '' }}"
                         alt="Imagem {{ $i + 1 }}" loading="lazy">
                    @if($i === 2 && $count > 3)
                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                            <span class="text-white text-xl font-bold">+{{ $count - 3 }}</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

    {{-- VIDEO --}}
    @elseif(isset($post->type) && $post->type === 'video' && $post->media->where('type','video')->isNotEmpty())
        @php $vid = $post->media->where('type','video')->first(); @endphp
        <div class="bg-black">
            <video controls preload="metadata" class="w-full h-auto max-h-[80vh] mx-auto block"
                   poster="{{ $vid->thumbnailUrl() ?? '' }}">
                <source src="{{ $vid->url() }}" type="{{ $vid->mime_type ?? 'video/mp4' }}">
            </video>
        </div>
        @if($vid->original_name || $vid->file_size)
            <div class="px-4 py-1.5 flex items-center gap-1.5 text-xs text-gray-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72"/>
                </svg>
                {{ $vid->original_name ? Str::limit($vid->original_name, 40) : 'Vídeo' }}
                @if($vid->file_size) · {{ $vid->formattedSize() }} @endif
            </div>
        @endif

    {{-- AUDIO --}}
    @elseif(isset($post->type) && $post->type === 'audio' && $post->media->where('type','audio')->isNotEmpty())
        @php $aud = $post->media->where('type','audio')->first(); @endphp
        <div class="mx-4 mb-3 bg-gradient-to-r from-gray-50 to-blue-50/40 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-[#00baff]/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-[#00baff]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a2.25 2.25 0 001.632-2.163zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 01-.99-3.467l2.31-.66A2.25 2.25 0 009 15.553z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">
                        {{ $aud->original_name ? Str::limit($aud->original_name, 40) : 'Áudio' }}
                        @if($aud->file_size) <span class="text-gray-400 text-xs font-normal">· {{ $aud->formattedSize() }}</span> @endif
                    </p>
                    <audio controls class="w-full mt-1.5" style="height:32px;">
                        <source src="{{ $aud->url() }}" type="{{ $aud->mime_type ?? 'audio/mpeg' }}">
                    </audio>
                </div>
            </div>
        </div>

    {{-- LINK PREVIEW --}}
    @elseif(isset($post->type) && $post->type === 'link' && $post->link_url)
        <div class="mx-4 mb-3">
            <a href="{{ $post->link_url }}" target="_blank" rel="noopener noreferrer nofollow"
               class="block border border-gray-100 rounded-xl overflow-hidden hover:border-[#00baff]/40 transition group">
                @if($post->link_image)
                    <img src="{{ $post->link_image }}" class="w-full max-h-48 object-cover" alt="" loading="lazy"
                         onerror="this.parentElement.removeChild(this)">
                @else
                    <div class="w-full h-16 bg-gradient-to-r from-gray-100 to-gray-200 flex items-center justify-center">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/>
                        </svg>
                    </div>
                @endif
                <div class="px-4 py-3 bg-gray-50 group-hover:bg-blue-50/20 transition">
                    <p class="text-sm font-semibold text-gray-800 truncate group-hover:text-[#00baff] transition">
                        {{ $post->link_title ?: parse_url($post->link_url, PHP_URL_HOST) }}
                    </p>
                    @if($post->link_description)
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ $post->link_description }}</p>
                    @endif
                    <p class="text-xs text-[#00baff] mt-1 truncate">{{ $post->link_url }}</p>
                </div>
            </a>
        </div>

    {{-- REPOST --}}
    @elseif(isset($post->type) && $post->type === 'repost' && $post->repost)
        <div class="mx-4 mb-3 border border-gray-100 rounded-xl overflow-hidden bg-gray-50/50">
            <div class="flex items-center gap-2 px-3 pt-3 pb-2 border-b border-gray-100">
                <img src="{{ $post->repost->user->avatarUrl() }}" class="w-7 h-7 rounded-full object-cover"
                     onerror="this.src='{{ asset('img/default-avatar.svg') }}'">
                <a href="{{ route('social.creator', $post->repost->user) }}"
                   class="text-sm font-semibold text-gray-700 hover:text-[#00baff] transition">
                    {{ $post->repost->user->name }}
                </a>
                <span class="text-xs text-gray-400 ml-auto">{{ $post->repost->created_at->diffForHumans() }}</span>
            </div>
            @if($post->repost->content)
                <p class="px-3 py-2 text-sm text-gray-600 whitespace-pre-line line-clamp-4">{{ $post->repost->content }}</p>
            @endif
            @if($post->repost->media->isNotEmpty())
                @php $rm = $post->repost->media->first(); @endphp
                @if($rm->type === 'image')
                    <img src="{{ $rm->url() }}" class="w-full max-h-40 object-cover" loading="lazy">
                @elseif($rm->type === 'video')
                    <div class="bg-gray-900 flex items-center justify-center h-24 text-gray-400 text-xs gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72"/>
                        </svg>
                        Vídeo
                    </div>
                @elseif($rm->type === 'audio')
                    <div class="bg-gray-50 px-3 py-2 flex items-center gap-2 text-gray-500 text-xs">
                        <svg class="w-4 h-4 text-[#00baff]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75"/>
                        </svg>
                        {{ $rm->original_name ?? 'Áudio' }}
                    </div>
                @endif
            @endif
        </div>

    {{-- LEGACY images (backward compat with posts predating media table) --}}
    @elseif($post->images->isNotEmpty())
        @php $imgs = $post->images; $count = $imgs->count(); @endphp
        <div class="grid gap-0.5 {{ $count === 1 ? 'grid-cols-1' : ($count === 2 ? 'grid-cols-2' : 'grid-cols-3') }}">
            @foreach($imgs->take(3) as $i => $img)
                <div class="relative overflow-hidden bg-gray-100">
                    <img src="{{ Storage::url($img->path) }}"
                         class="w-full object-cover {{ $count === 1 ? 'max-h-96' : 'aspect-square' }}"
                         alt="Imagem {{ $i + 1 }}" loading="lazy">
                    @if($i === 2 && $count > 3)
                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                            <span class="text-white text-xl font-bold">+{{ $count - 3 }}</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @endif
    {{-- end creator gate --}}

    {{-- ── Actions bar ──────────────────────────────────────────────────────── --}}
    <div class="px-4 py-3 border-t border-gray-50 flex items-center gap-1 flex-wrap">

        {{-- Like --}}
        @auth
            <button wire:click="toggleLike({{ $post->id }})"
                class="flex items-center gap-1.5 text-sm px-2 py-1 rounded-lg transition {{ $isLiked ? 'text-red-500 bg-red-50' : 'text-gray-400 hover:text-red-500 hover:bg-red-50/50' }}">
                <svg class="w-5 h-5" fill="{{ $isLiked ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                </svg>
                <span>{{ $likesCount }}</span>
            </button>
        @else
            <span class="flex items-center gap-1.5 text-sm text-gray-400 px-2 py-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                </svg>
                {{ $likesCount }}
            </span>
        @endauth

        {{-- Comment --}}
        <button wire:click="openComments({{ $post->id }})"
            class="flex items-center gap-1.5 text-sm px-2 py-1 rounded-lg transition {{ $showComments ? 'text-[#00baff] bg-blue-50' : 'text-gray-400 hover:text-[#00baff] hover:bg-blue-50/50' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a.375.375 0 01.265-.109c.84-.049 1.67-.12 2.485-.21 1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
            </svg>
            <span>{{ $commentsCount }}</span>
        </button>

        {{-- Repost --}}
        @auth
            @if(!$isOwner && ($post->type ?? 'text') !== 'repost')
                <a href="{{ route('social.create') }}?repost_id={{ $post->id }}"
                   class="flex items-center gap-1.5 text-sm px-2 py-1 rounded-lg text-gray-400 hover:text-green-600 hover:bg-green-50/50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3"/>
                    </svg>
                    @if($repostsCount > 0)<span>{{ $repostsCount }}</span>@endif
                </a>
            @endif
        @endauth

        {{-- Bookmark --}}
        @auth
            <button wire:click="toggleBookmark({{ $post->id }})"
                class="flex items-center gap-1 text-sm px-2 py-1 rounded-lg transition {{ $isBookmarked ? 'text-[#00baff] bg-blue-50' : 'text-gray-400 hover:text-[#00baff] hover:bg-blue-50/50' }}"
                title="{{ $isBookmarked ? 'Remover dos guardados' : 'Guardar' }}">
                <svg class="w-5 h-5" fill="{{ $isBookmarked ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
                </svg>
            </button>
        @endauth

        {{-- Follow (right side) --}}
        @auth
            @if(!$isOwner)
                <button wire:click="toggleFollow({{ $post->user_id }})"
                    class="ml-auto text-xs font-semibold px-3 py-1.5 rounded-lg border transition
                        {{ $isFollowing ? 'border-gray-200 text-gray-500 hover:border-red-200 hover:text-red-500' : 'border-[#00baff] text-[#00baff] hover:bg-[#00baff] hover:text-white' }}">
                    {{ $isFollowing ? 'A seguir' : '+ Seguir' }}
                </button>
            @endif
        @endauth

    </div>

    {{-- ── Comments section ────────────────────────────────────────────────── --}}
    @if($showComments)
        <div class="border-t border-gray-50 px-4 py-3 space-y-3 bg-gray-50/50">
            @forelse($post->comments as $comment)
                <div class="flex gap-2">
                    <img src="{{ $comment->user->avatarUrl() }}" alt="{{ $comment->user->name }}"
                         class="w-7 h-7 rounded-full object-cover flex-shrink-0"
                         onerror="this.src='{{ asset('img/default-avatar.svg') }}'">
                    <div class="flex-1 bg-white rounded-xl px-3 py-2 shadow-sm">
                        <p class="text-xs font-semibold text-gray-800">{{ $comment->user->name }}</p>
                        <p class="text-xs text-gray-600 mt-0.5 whitespace-pre-line">{{ $comment->content }}</p>
                    </div>
                </div>
            @empty
                <p class="text-xs text-gray-400 text-center py-1">Seja o primeiro a comentar!</p>
            @endforelse

            @auth
                <div class="flex gap-2 pt-1">
                    <img src="{{ auth()->user()->avatarUrl() }}" alt=""
                         class="w-7 h-7 rounded-full object-cover flex-shrink-0"
                         onerror="this.src='{{ asset('img/default-avatar.svg') }}'">
                    <div class="flex-1 flex gap-2">
                        <input wire:model="commentText" type="text"
                               placeholder="Escreva um comentário..."
                               wire:keydown.enter="submitComment"
                               class="flex-1 border border-gray-200 rounded-xl px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-[#00baff]/40">
                        <button wire:click="submitComment"
                            class="px-3 py-1.5 bg-[#00baff] text-white text-xs font-semibold rounded-xl hover:bg-[#009ad6] transition">
                            Enviar
                        </button>
                    </div>
                </div>
                @error('commentText') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            @endauth
        </div>
    @endif

</article>
