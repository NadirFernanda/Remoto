{{-- 
    Partial: post-card
    Variables: $post, $this (Livewire component context for wire:click)
--}}
@php
    $user        = auth()->user();
    $isOwner     = $user && $user->id === $post->user_id;
    $isLiked     = $user ? $post->isLikedBy($user->id) : false;
    $likesCount  = $post->likesCount();
    $commentsCount = $post->commentsCount();
    $showComments  = isset($commentingPostId) && $commentingPostId === $post->id;
@endphp

<article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

    {{-- Creator header --}}
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
            <span class="text-xs text-gray-400">{{ $post->created_at->diffForHumans() }}</span>
            {{-- Options dropdown --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="p-1.5 rounded-full hover:bg-gray-100 transition text-gray-400">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/>
                    </svg>
                </button>
                <div x-show="open" x-transition @click.away="open = false"
                     class="absolute right-0 top-8 bg-white border border-gray-100 rounded-xl shadow-lg w-44 z-10 py-1">
                    @if($isOwner)
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
                </div>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="px-4 pb-3">
        <p class="text-sm text-gray-800 whitespace-pre-line leading-relaxed">{{ $post->content }}</p>
    </div>

    {{-- Images grid --}}
    @if($post->images->isNotEmpty())
        @php $imgs = $post->images; $count = $imgs->count(); @endphp
        <div class="grid gap-0.5 {{ $count === 1 ? 'grid-cols-1' : ($count === 2 ? 'grid-cols-2' : 'grid-cols-3') }} max-h-96">
            @foreach($imgs->take(3) as $i => $img)
                <div class="relative {{ ($count === 1) ? 'col-span-1' : '' }} overflow-hidden bg-gray-100">
                    <img src="{{ Storage::url($img->path) }}"
                         class="w-full h-full object-cover {{ $count === 1 ? 'max-h-96' : 'aspect-square' }}"
                         alt="Imagem {{ $i + 1 }}">
                    @if($i === 2 && $count > 3)
                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                            <span class="text-white text-xl font-bold">+{{ $count - 3 }}</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- Actions bar --}}
    <div class="px-4 py-3 border-t border-gray-50 flex items-center gap-4">
        {{-- Like --}}
        @auth
            <button wire:click="toggleLike({{ $post->id }})"
                class="flex items-center gap-1.5 text-sm transition {{ $isLiked ? 'text-red-500' : 'text-gray-400 hover:text-red-500' }}">
                <svg class="w-5 h-5" fill="{{ $isLiked ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                </svg>
                <span>{{ $likesCount }}</span>
            </button>
        @else
            <span class="flex items-center gap-1.5 text-sm text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                </svg>
                {{ $likesCount }}
            </span>
        @endauth

        {{-- Comment toggle --}}
        <button wire:click="openComments({{ $post->id }})"
            class="flex items-center gap-1.5 text-sm transition {{ $showComments ? 'text-[#00baff]' : 'text-gray-400 hover:text-[#00baff]' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a.375.375 0 01.265-.109c.84-.049 1.67-.12 2.485-.21 1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
            </svg>
            <span>{{ $commentsCount }}</span>
        </button>

        {{-- Follow / spacer --}}
        <div class="ml-auto flex items-center gap-2">
            @auth
                @if(!$isOwner)
                    @php $following = auth()->user()->following()->where('following_id', $post->user_id)->exists(); @endphp
                    <button wire:click="toggleFollow({{ $post->user_id }})"
                        class="text-xs font-semibold px-3 py-1.5 rounded-lg border transition
                            {{ $following ? 'border-gray-200 text-gray-500 hover:border-red-200 hover:text-red-500' : 'border-[#00baff] text-[#00baff] hover:bg-[#00baff] hover:text-white' }}">
                        {{ $following ? 'A seguir' : 'Seguir' }}
                    </button>
                @endif
            @endauth
        </div>
    </div>

    {{-- Comments section --}}
    @if($showComments)
        <div class="border-t border-gray-50 px-4 py-3 space-y-3 bg-gray-50/50">
            @foreach($post->comments as $comment)
                <div class="flex gap-2">
                    <img src="{{ $comment->user->avatarUrl() }}" alt="{{ $comment->user->name }}"
                         class="w-7 h-7 rounded-full object-cover flex-shrink-0"
                         onerror="this.src='{{ asset('img/default-avatar.svg') }}'">
                    <div class="flex-1 bg-white rounded-xl px-3 py-2 shadow-sm">
                        <p class="text-xs font-semibold text-gray-800">{{ $comment->user->name }}</p>
                        <p class="text-xs text-gray-600 mt-0.5">{{ $comment->content }}</p>
                    </div>
                </div>
            @endforeach

            @auth
                <div class="flex gap-2 pt-1">
                    <img src="{{ auth()->user()->avatarUrl() }}" alt=""
                         class="w-7 h-7 rounded-full object-cover flex-shrink-0"
                         onerror="this.src='{{ asset('img/default-avatar.svg') }}'">
                    <div class="flex-1 flex gap-2">
                        <input type="text" wire:model="commentText"
                               wire:keydown.enter="submitComment"
                               placeholder="Escreva um comentário..."
                               class="flex-1 text-sm border border-gray-200 rounded-xl px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-[#00baff]/30">
                        <button wire:click="submitComment"
                            class="text-[#00baff] hover:text-[#009ad6] transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endauth
        </div>
    @endif

</article>
