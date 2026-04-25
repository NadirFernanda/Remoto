{{--
    Partial: livewire.social.partials.post-card
    Layout: Instagram-style — header · media · actions · likes · caption · comments · timestamp
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
    $needsSubscription = ($post->visibility === 'followers')
                         && !$isOwner
                         && !($authUser && in_array($post->user_id, $subscribedCreatorIds ?? []));
    $isCreator = (bool)($post->user->has_creator_profile ?? false);
@endphp

<article class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200" wire:key="post-{{ $post->id }}">

    {{-- ══ HEADER ══════════════════════════════════════════════════════════════ --}}
    <div class="flex items-center gap-3 px-4 py-3">

        {{-- Avatar com ring de criador --}}
        <a href="{{ route('social.creator', $post->user) }}" class="flex-shrink-0">
            <div class="p-0.5 rounded-full {{ $isCreator ? 'bg-gradient-to-tr from-[#00baff] via-blue-400 to-violet-500' : 'bg-transparent' }}">
                <div class="{{ $isCreator ? 'bg-white rounded-full p-0.5' : '' }}">
                    <img src="{{ $post->user->avatarUrl() }}"
                         alt="{{ $post->user->name }}"
                         class="w-9 h-9 rounded-full object-cover block"
                         onerror="this.src='{{ asset('img/default-avatar.svg') }}'">
                </div>
            </div>
        </a>

        {{-- Name + inline follow --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-1.5 flex-wrap">
                <a href="{{ route('social.creator', $post->user) }}"
                   class="text-sm font-bold text-gray-900 hover:text-[#00baff] transition leading-tight">
                    {{ $post->user->name }}
                </a>
                @auth
                    @if(!$isOwner && !$isFollowing)
                        <span class="text-gray-300 text-xs">·</span>
                        <button wire:click="toggleFollow({{ $post->user_id }})"
                            class="text-xs font-bold text-[#00baff] hover:text-[#009ad6] transition">
                            Seguir
                        </button>
                    @elseif(!$isOwner && $isFollowing)
                        <span class="text-gray-300 text-xs">·</span>
                        <button wire:click="toggleFollow({{ $post->user_id }})"
                            class="text-xs font-medium text-gray-400 hover:text-red-400 transition">
                            A seguir
                        </button>
                    @endif
                @endauth
                @if($post->visibility === 'followers')
                    <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold text-amber-600 bg-amber-50 border border-amber-200 rounded-full px-1.5 py-0.5 leading-none">
                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                        </svg>
                        Assinantes
                    </span>
                @endif
            </div>
            @if($post->user->freelancerProfile?->headline)
                <p class="text-[11px] text-gray-400 truncate leading-tight mt-0.5">{{ $post->user->freelancerProfile->headline }}</p>
            @endif
        </div>

        {{-- Options ⋯ --}}
        <div x-data="{ open: false }" class="relative flex-shrink-0">
            <button @click="open = !open"
                class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-500 transition">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/>
                </svg>
            </button>
            <div x-show="open" x-transition @click.away="open = false"
                 class="absolute right-0 top-9 bg-white border border-gray-100 rounded-2xl shadow-2xl w-52 z-20 py-1.5 overflow-hidden">
                @if($isOwner)
                    <button wire:click="openEditPost({{ $post->id }})" @click="open = false"
                        class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition flex items-center gap-3">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                        </svg>
                        Editar publicação
                    </button>
                    <button wire:click="deletePost({{ $post->id }})" @click="open = false"
                        class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition flex items-center gap-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                        </svg>
                        Eliminar publicação
                    </button>
                @else
                    @auth
                        <button wire:click="openReportPost({{ $post->id }})" @click="open = false"
                            class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                            Denunciar publicação
                        </button>
                        <button wire:click="openReportUser({{ $post->user_id }})" @click="open = false"
                            class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                Denunciar utilizador
                        </button>
                    @endauth
                @endif
                <div class="border-t border-gray-50 mt-1 pt-1">
                    <button @click="navigator.clipboard.writeText('{{ url('/social') }}?post={{ $post->id }}'); open = false"
                        class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition flex items-center gap-3">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/>
                        </svg>
                        Copiar link
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ SUBSCRIPTION GATE ═══════════════════════════════════════════════════ --}}
    @if($needsSubscription)
    <div x-data="{ showModal: false }">

        {{-- Blurred media preview --}}
        @php $firstMedia = $post->media->first() ?? null; @endphp
        <div class="relative w-full overflow-hidden bg-gray-900" style="min-height: 260px;">
            @if($firstMedia)
                @if($firstMedia->type === 'image')
                    <img src="{{ $firstMedia->url() }}" class="w-full object-cover blur-2xl scale-110 opacity-60" style="max-height:340px;" alt="" loading="lazy">
                @elseif($firstMedia->type === 'video' && $firstMedia->thumbnailUrl())
                    <img src="{{ $firstMedia->thumbnailUrl() }}" class="w-full object-cover blur-2xl scale-110 opacity-60" style="max-height:340px;" alt="">
                @else
                    <div class="w-full bg-gradient-to-br from-gray-800 to-gray-700 flex items-center justify-center" style="height:260px;">
                        <svg class="w-20 h-20 text-white/10" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                @endif
            @else
                <div class="w-full bg-gradient-to-br from-gray-800 to-gray-700 flex items-center justify-center" style="height:260px;">
                    @if($post->content)
                        <p class="text-sm text-gray-500 blur-sm px-6 text-center line-clamp-3">{{ Str::limit(strip_tags($post->content), 80) }}</p>
                    @endif
                </div>
            @endif

            {{-- Lock overlay --}}
            <div class="absolute inset-0 flex flex-col items-center justify-end pb-8 cursor-pointer bg-gradient-to-t from-black/80 via-black/30 to-transparent"
                 @click="showModal = true">
                <div class="flex flex-col items-center gap-2">
                    <div class="w-14 h-14 rounded-full bg-white/10 border-2 border-white/20 flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-white drop-shadow">Clique para ver o conteúdo</p>
                    <p class="text-xs text-white/70">Exclusivo para assinantes</p>
                </div>
            </div>
        </div>

        {{-- Subscription modal --}}
        <div x-show="showModal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-sm bg-black/60"
             @click.self="showModal = false">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm p-7 text-center">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-[#00baff]/10 to-blue-100 flex items-center justify-center">
                    <svg class="w-7 h-7 text-[#00baff]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Conteúdo exclusivo</h3>
                <p class="text-sm text-gray-500 mb-1">Para assinantes de</p>
                <p class="text-base font-bold text-gray-900 mb-5">{{ $post->user->name }}</p>
                <a href="{{ route('social.creator', $post->user) }}"
                   class="inline-flex items-center gap-2 bg-gradient-to-r from-[#00baff] to-blue-500 text-white text-sm font-bold px-6 py-3 rounded-xl shadow-md hover:shadow-lg transition w-full justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                    </svg>
                    Assinar — 3.000 Kz/mês
                </a>
                <button @click="showModal = false"
                    class="mt-3 text-xs text-gray-400 hover:text-gray-600 transition">
                    Fechar
                </button>
            </div>
        </div>
    </div>

    @else

    {{-- ══ MEDIA BLOCK — edge-to-edge ══════════════════════════════════════════ --}}

    {{-- IMAGES --}}
    @if(isset($post->type) && in_array($post->type, ['image', 'text']) && $post->media->where('type','image')->isNotEmpty())
        @php $imgs = $post->media->where('type','image')->values(); $count = $imgs->count(); @endphp
        @if($count === 1)
            <div class="w-full overflow-hidden bg-gray-50">
                <img src="{{ $imgs[0]->url() }}"
                     class="w-full object-cover"
                     style="max-height: 600px;"
                     alt="Imagem" loading="lazy">
            </div>
        @else
            <div class="grid grid-cols-2 gap-0.5 overflow-hidden">
                @foreach($imgs->take(4) as $i => $img)
                    <div class="relative overflow-hidden bg-gray-100" style="aspect-ratio: 1 / 1;">
                        <img src="{{ $img->url() }}"
                             class="w-full h-full object-cover"
                             alt="Imagem {{ $i + 1 }}" loading="lazy">
                        @if($i === 3 && $count > 4)
                            <div class="absolute inset-0 bg-black/60 flex items-center justify-center">
                                <span class="text-white text-2xl font-bold">+{{ $count - 4 }}</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

    {{-- VIDEO --}}
    @elseif(isset($post->type) && $post->type === 'video' && $post->media->where('type','video')->isNotEmpty())
        @php $vid = $post->media->where('type','video')->first(); @endphp
        <div class="w-full bg-black overflow-hidden">
            <video controls preload="metadata"
                   class="w-full block"
                   style="max-height: 70vh;"
                   poster="{{ $vid->thumbnailUrl() ?? '' }}">
                <source src="{{ $vid->url() }}" type="{{ $vid->mime_type ?? 'video/mp4' }}">
            </video>
        </div>
        @if($vid->original_name)
            <div class="px-4 pt-2 flex items-center gap-1.5 text-xs text-gray-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/>
                </svg>
                {{ Str::limit($vid->original_name, 40) }}
                @if($vid->file_size) · {{ $vid->formattedSize() }} @endif
            </div>
        @endif

    {{-- AUDIO --}}
    @elseif(isset($post->type) && $post->type === 'audio' && $post->media->where('type','audio')->isNotEmpty())
        @php $aud = $post->media->where('type','audio')->first(); @endphp
        <div class="mx-4 mb-1 bg-gradient-to-r from-[#00baff]/5 to-blue-50/40 border border-[#00baff]/10 rounded-2xl p-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-[#00baff]/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-[#00baff]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a2.25 2.25 0 001.632-2.163zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 01-.99-3.467l2.31-.66A2.25 2.25 0 009 15.553z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">
                        {{ $aud->original_name ? Str::limit($aud->original_name, 40) : 'Audio' }}
                        @if($aud->file_size) <span class="text-gray-400 text-xs font-normal">- {{ $aud->formattedSize() }}</span> @endif
                    </p>
                    <audio controls class="w-full mt-2" style="height:34px;">
                        <source src="{{ $aud->url() }}" type="{{ $aud->mime_type ?? 'audio/mpeg' }}">
                    </audio>
                </div>
            </div>
        </div>

    {{-- LINK PREVIEW --}}
    @elseif(isset($post->type) && $post->type === 'link' && $post->link_url)
        <div class="mx-4 mb-1">
            <a href="{{ $post->link_url }}" target="_blank" rel="noopener noreferrer nofollow"
               class="block border border-gray-100 rounded-2xl overflow-hidden hover:border-[#00baff]/40 transition group">
                @if($post->link_image)
                    <img src="{{ $post->link_image }}" class="w-full max-h-52 object-cover" alt="" loading="lazy"
                         onerror="this.parentElement.removeChild(this)">
                @else
                    <div class="w-full h-20 bg-gradient-to-r from-gray-100 to-gray-200 flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/>
                        </svg>
                    </div>
                @endif
                <div class="px-4 py-3 bg-gray-50/80 group-hover:bg-blue-50/30 transition">
                    <p class="text-sm font-bold text-gray-800 truncate group-hover:text-[#00baff] transition">
                        {{ $post->link_title ?: parse_url($post->link_url, PHP_URL_HOST) }}
                    </p>
                    @if($post->link_description)
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ $post->link_description }}</p>
                    @endif
                    <p class="text-[10px] text-[#00baff] mt-1 truncate uppercase tracking-wide">{{ parse_url($post->link_url, PHP_URL_HOST) }}</p>
                </div>
            </a>
        </div>

    {{-- REPOST --}}
    @elseif(isset($post->type) && $post->type === 'repost' && $post->repost)
        <div class="mx-4 mb-1 border border-gray-100 rounded-2xl overflow-hidden bg-gray-50/50">
            <div class="flex items-center gap-2.5 px-3 py-2.5 border-b border-gray-100">
                <img src="{{ $post->repost->user->avatarUrl() }}" class="w-7 h-7 rounded-full object-cover"
                     onerror="this.src='{{ asset('img/default-avatar.svg') }}'">
                <a href="{{ route('social.creator', $post->repost->user) }}"
                   class="text-sm font-bold text-gray-800 hover:text-[#00baff] transition">
                    {{ $post->repost->user->name }}
                </a>
                <span class="text-xs text-gray-400 ml-auto">{{ $post->repost->created_at->diffForHumans() }}</span>
            </div>
            @if($post->repost->content)
                <p class="px-3 py-2.5 text-sm text-gray-600 whitespace-pre-line line-clamp-4 leading-relaxed">{{ $post->repost->content }}</p>
            @endif
            @if($post->repost->media->isNotEmpty())
                @php $rm = $post->repost->media->first(); @endphp
                @if($rm->type === 'image')
                    <img src="{{ $rm->url() }}" class="w-full max-h-44 object-cover" loading="lazy">
                @elseif($rm->type === 'video')
                    <div class="bg-gray-900 flex items-center justify-center gap-2 py-5 text-gray-400 text-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/>
                        </svg>
                        Video
                    </div>
                @elseif($rm->type === 'audio')
                    <div class="bg-gray-50 px-3 py-2 flex items-center gap-2 text-gray-500 text-xs">
                        <svg class="w-4 h-4 text-[#00baff]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75"/>
                        </svg>
                        {{ $rm->original_name ?? 'Audio' }}
                    </div>
                @endif
            @endif
        </div>

    {{-- LEGACY images --}}
    @elseif($post->images->isNotEmpty())
        @php $imgs = $post->images; $count = $imgs->count(); @endphp
        @if($count === 1)
            <div class="w-full overflow-hidden bg-gray-50">
                <img src="{{ Storage::url($imgs[0]->path) }}"
                     class="w-full object-cover"
                     style="max-height: 600px;"
                     alt="Imagem" loading="lazy">
            </div>
        @else
            <div class="grid grid-cols-2 gap-0.5 overflow-hidden">
                @foreach($imgs->take(4) as $i => $img)
                    <div class="relative overflow-hidden bg-gray-100" style="aspect-ratio: 1 / 1;">
                        <img src="{{ Storage::url($img->path) }}"
                             class="w-full h-full object-cover"
                             alt="Imagem {{ $i + 1 }}" loading="lazy">
                        @if($i === 3 && $count > 4)
                            <div class="absolute inset-0 bg-black/60 flex items-center justify-center">
                                <span class="text-white text-2xl font-bold">+{{ $count - 4 }}</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    @endif

    @endif {{-- end subscription gate --}}

    {{-- ══ ACTIONS BAR — icons only, no pills ══════════════════════════════════ --}}
    <div class="flex items-center px-3 pt-2.5 pb-1">
        <div class="flex items-center gap-4 flex-1">

            {{-- Heart --}}
            @auth
                <button wire:click="toggleLike({{ $post->id }})"
                    class="outline-none transition-transform active:scale-125 {{ $isLiked ? 'text-red-500' : 'text-gray-800 hover:text-red-400' }}">
                    <svg class="w-6 h-6" fill="{{ $isLiked ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                    </svg>
                </button>
            @else
                <span class="text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                    </svg>
                </span>
            @endauth

            {{-- Comment bubble --}}
            <button wire:click="openComments({{ $post->id }})"
                class="outline-none {{ $showComments ? 'text-[#00baff]' : 'text-gray-800 hover:text-[#00baff]' }} transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 01-.923 1.785A5.969 5.969 0 006 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337z"/>
                </svg>
            </button>

            {{-- Share / Repost --}}
            @auth
                @if(!$isOwner && ($post->type ?? 'text') !== 'repost')
                    <a href="{{ route('social.create') }}?repost_id={{ $post->id }}"
                       class="text-gray-800 hover:text-green-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
                        </svg>
                    </a>
                @endif
            @endauth
        </div>

        {{-- Bookmark — right side --}}
        @auth
            <button wire:click="toggleBookmark({{ $post->id }})"
                class="outline-none {{ $isBookmarked ? 'text-[#00baff]' : 'text-gray-800 hover:text-[#00baff]' }} transition"
                title="{{ $isBookmarked ? 'Remover dos guardados' : 'Guardar' }}">
                <svg class="w-6 h-6" fill="{{ $isBookmarked ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
                </svg>
            </button>
        @endauth
    </div>

    {{-- ══ LIKES COUNT ═════════════════════════════════════════════════════════ --}}
    @if($likesCount > 0)
        <div class="px-4 pb-1">
            <p class="text-sm font-bold text-gray-900">
                {{ number_format($likesCount) }} {{ $likesCount === 1 ? 'gosto' : 'gostos' }}
            </p>
        </div>
    @endif

    {{-- ══ CAPTION — username bold + text inline (Instagram style) ════════════ --}}
    @if($post->content && !$needsSubscription)
        <div class="px-4 pb-1" x-data="{ expanded: false }">
            <p class="text-sm text-gray-800 leading-relaxed" x-bind:class="expanded ? '' : 'line-clamp-3'">
                <a href="{{ route('social.creator', $post->user) }}"
                   class="font-bold text-gray-900 hover:underline mr-1.5">{{ $post->user->name }}</a>{!! $post->contentWithHashtags() !!}
            </p>
            <button x-show="!expanded"
                @click="expanded = true"
                x-init="$nextTick(() => { const p = $el.previousElementSibling; if (p.scrollHeight <= p.clientHeight + 2) $el.style.display='none'; })"
                class="text-xs text-gray-400 hover:text-gray-600 transition mt-0.5">
                mais
            </button>
        </div>
    @endif

    {{-- ══ VIEW ALL COMMENTS ═══════════════════════════════════════════════════ --}}
    @if($commentsCount > 0 && !$showComments)
        <button wire:click="openComments({{ $post->id }})"
            class="px-4 pb-1 text-sm text-gray-400 hover:text-gray-600 transition block">
            Ver todos os {{ $commentsCount }} {{ $commentsCount === 1 ? 'comentario' : 'comentarios' }}
        </button>
    @endif

    {{-- ══ COMMENTS ════════════════════════════════════════════════════════════ --}}
    @if($showComments)
        <div class="px-4 pb-1 space-y-2">
            @forelse($post->comments as $comment)
                <div class="flex gap-2.5">
                    <img src="{{ $comment->user->avatarUrl() }}" alt="{{ $comment->user->name }}"
                         class="w-7 h-7 rounded-full object-cover flex-shrink-0 mt-0.5"
                         onerror="this.src='{{ asset('img/default-avatar.svg') }}'">
                    <p class="text-sm text-gray-800 leading-snug flex-1">
                        <a href="#" class="font-bold text-gray-900 hover:underline mr-1">{{ $comment->user->name }}</a>{{ $comment->content }}
                    </p>
                </div>
            @empty
                <p class="text-xs text-gray-400 py-1">Seja o primeiro a comentar!</p>
            @endforelse
        </div>
    @endif

    {{-- ══ TIMESTAMP ════════════════════════════════════════════════════════════ --}}
    <div class="px-4 pb-2 mt-0.5">
        <time class="text-[10px] text-gray-400 uppercase tracking-wider"
              datetime="{{ $post->created_at->toIso8601String() }}">
            {{ $post->created_at->diffForHumans() }}
        </time>
    </div>

    {{-- ══ INLINE COMMENT INPUT — Instagram style ══════════════════════════════ --}}
    @auth
        <div class="border-t border-gray-100 flex items-center gap-3 px-4 py-2.5">
            <img src="{{ auth()->user()->avatarUrl() }}" alt=""
                 class="w-7 h-7 rounded-full object-cover flex-shrink-0"
                 onerror="this.src='{{ asset('img/default-avatar.svg') }}'">
            <input wire:model="commentText"
                   type="text"
                   placeholder="Adicionar comentario..."
                   wire:keydown.enter="submitComment"
                   class="flex-1 text-sm text-gray-800 bg-transparent placeholder-gray-400 focus:outline-none py-1">
            <button wire:click="submitComment"
                class="text-sm font-bold text-[#00baff] hover:text-[#009ad6] transition flex-shrink-0">
                Publicar
            </button>
        </div>
        @error('commentText') <p class="px-4 pb-2 text-red-500 text-xs">{{ $message }}</p> @enderror
    @endauth

</article>
