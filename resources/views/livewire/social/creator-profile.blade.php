<div class="space-y-5 creator-profile-page">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl text-sm font-medium flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm font-medium flex items-center justify-between gap-2 shadow-sm">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/></svg>
                {{ session('error') }}
            </span>
            @if(str_contains(session('error'), 'Saldo insuficiente'))
                <a href="{{ route('wallet.topup') }}" class="whitespace-nowrap font-semibold underline">Recarregar carteira</a>
            @endif
        </div>
    @endif

    {{-- ── PROFILE CARD ──────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- Cover --}}
        @if($creator->coverPhotoUrl())
            <x-image-lightbox :src="$creator->coverPhotoUrl()" :alt="$creator->name . ' — capa'">
                <div class="h-32 sm:h-44 relative">
                    <img src="{{ $creator->coverPhotoUrl() }}" alt="{{ $creator->name }} — capa" class="w-full h-full object-cover">
                </div>
            </x-image-lightbox>
        @else
            <div class="h-32 sm:h-44 /40 relative">
                <div class="absolute -top-8 -right-8 w-40 h-40 bg-white/10 rounded-full"></div>
                <div class="absolute top-4 right-20 w-20 h-20 bg-white/10 rounded-full"></div>
            </div>
        @endif

        <div class="px-5 sm:px-8 pb-6">
            <div class="flex flex-col sm:flex-row sm:items-end gap-4 -mt-10 sm:-mt-12 mb-4">

                {{-- Avatar --}}
                <div class="relative flex-shrink-0">
                    <x-image-lightbox :src="$creator->avatarUrl()" :alt="$creator->name">
                        <div class="p-1 rounded-full {{ $creator->has_creator_profile ? ' ' : 'bg-white shadow-md' }}">
                            <div class="{{ $creator->has_creator_profile ? 'p-0.5 bg-white rounded-full' : '' }}">
                                <img src="{{ $creator->avatarUrl() }}"
                                     alt="{{ $creator->name }}"
                                     class="w-20 h-20 sm:w-24 sm:h-24 rounded-full object-cover block"
                                     onerror="this.src='{{ asset('img/default-avatar.svg') }}'">
                            </div>
                        </div>
                    </x-image-lightbox>
                    @if($creator->has_creator_profile)
                        <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-[#0055ff] rounded-full flex items-center justify-center shadow-md border-2 border-white">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        </div>
                    @endif
                </div>

                {{-- Stats --}}
                <div class="flex-1 sm:pb-1">
                    <div class="flex items-center gap-6 sm:gap-10">
                        <div class="text-center">
                            <p class="text-lg font-bold text-gray-900 leading-tight">{{ $posts->total() }}</p>
                            <p class="text-xs text-gray-400">publicações</p>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-bold text-gray-900 leading-tight">{{ $followersCount }}</p>
                            <p class="text-xs text-gray-400">seguidores</p>
                        </div>
                        @if($creator->averageRating() > 0)
                            <div class="text-center">
                                <p class="text-lg font-bold text-gray-900 leading-tight flex items-center justify-center gap-0.5">
                                    <svg class="w-4 h-4 text-amber-400 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    {{ $creator->averageRating() }}
                                </p>
                                <p class="text-xs text-gray-400">avaliação</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Action buttons --}}
                <div class="flex flex-wrap gap-2 sm:pb-1">
                    @auth
                        @if(auth()->id() !== $creator->id)
                            <button wire:click="toggleFollow"
                                class="px-5 py-2 text-sm font-bold rounded-xl border-2 transition {{ $isFollowing ? 'border-gray-200 text-gray-600 hover:border-red-300 hover:text-red-600' : 'border-[#0055ff] bg-[#0055ff] text-white hover:bg-[#0033cc] hover:border-[#0033cc]' }}">
                                @if($isFollowing)
                                    <span class="inline-flex items-center gap-1.5"><svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 13l4 4L19 3"/></svg>A seguir</span>
                                @else
                                    Seguir
                                @endif
                            </button>
                            @if($creator->has_creator_profile)
                                @if($isSubscribed)
                                    <span class="px-5 py-2 text-sm font-bold rounded-xl bg-green-50 text-green-700 border-2 border-green-200 inline-flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Assinante
                                    </span>
                                @else
                                    <a href="{{ route('social.creator.subscribe', $creator) }}"
                                        class="px-5 py-2 text-sm font-bold rounded-xl bg-[#0055ff] text-white hover:bg-[#0047d9] transition shadow-sm">
                                        <span class="inline-flex items-center gap-1.5"><svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.5l2.53 5.13 5.66.82-4.1 3.99 0.97 5.62L12 0.25 6.94 18.06l0.97-5.62L3.81 8.45l5.66-.82L12 2.5z"/></svg>Assinar · {{ number_format($subscriptionPrice, 0) }} KZS/mês</span>
                                    </a>
                                @endif
                            @endif
                            @if($creator->role === 'freelancer' || $creator->has_freelancer_profile)
                                <button type="button"
                                    onclick="Livewire.dispatch('openProposal', { recipientId: {{ $creator->id }} })"
                                    class="px-5 py-2 text-sm font-bold rounded-xl border-2 border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition">
                                    Contratar
                                </button>
                            @endif
                        @else
                            @if(in_array(auth()->user()->activeRole(), ['freelancer', 'creator']))
                                <a href="{{ route('social.create') }}"
                                   class="px-5 py-2 text-sm font-bold rounded-xl bg-[#0055ff] text-white hover:bg-[#0033cc] transition inline-flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    Nova publicação
                                </a>
                            @endif
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                           class="px-5 py-2 text-sm font-bold rounded-xl border-2 border-[#0055ff] text-[#0055ff] hover:bg-[#0055ff] hover:text-white transition">
                            Seguir
                        </a>
                        @if($creator->role === 'freelancer' || $creator->has_freelancer_profile)
                            <a href="{{ route('login') }}"
                               class="px-5 py-2 text-sm font-bold rounded-xl border-2 border-gray-200 text-gray-700 hover:bg-gray-50 transition">
                                Contratar
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            {{-- Name + headline + bio + skills --}}
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl font-bold text-gray-900 leading-tight">{{ $creator->name }}</h1>
                    @if($creator->has_creator_profile)
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-[#0055ff] bg-[#0055ff]/10 rounded-full px-2 py-0.5">
                            <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            Criador
                        </span>
                    @endif
                </div>
                @if($creator->freelancerProfile?->headline)
                    <p class="text-sm text-gray-500 mt-0.5">{{ $creator->freelancerProfile->headline }}</p>
                @endif
                @if($creator->location && $creator->isFieldPublic('location'))
                    <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                        </svg>
                        {{ $creator->location }}
                    </p>
                @endif
                @if($creator->freelancerProfile?->summary && $creator->isFieldPublic('summary'))
                    <p class="text-sm text-gray-600 leading-relaxed mt-3">{{ $creator->freelancerProfile->summary }}</p>
                @endif
                @if($creator->freelancerProfile?->skills && $creator->isFieldPublic('skills'))
                    <div class="mt-3 flex flex-wrap gap-1.5">
                        @foreach(array_slice($creator->freelancerProfile->skills, 0, 10) as $skill)
                            <span class="text-xs bg-[#0055ff]/8 text-[#0055ff] border border-[#0055ff]/20 px-2.5 py-1 rounded-full font-medium">{{ $skill }}</span>
                        @endforeach
                    </div>
                @endif

                {{-- Experiência profissional --}}
                @if($creator->workExperiences->isNotEmpty() && $creator->isFieldPublic('work_experience'))
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Experiência profissional</h3>
                        <div class="flex flex-col gap-3">
                            @foreach($creator->workExperiences as $exp)
                                <div>
                                    <p class="text-sm font-bold text-gray-800">{{ $exp->titulo }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $exp->empresa }}@if($exp->cidade || $exp->pais) · {{ trim(($exp->cidade ?? '') . (($exp->cidade && $exp->pais) ? ', ' : '') . ($exp->pais ?? '')) }}@endif
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $exp->mes_inicio ? str_pad($exp->mes_inicio, 2, '0', STR_PAD_LEFT) . '/' : '' }}{{ $exp->ano_inicio }}
                                        —
                                        {{ $exp->atual ? 'Actual' : (($exp->mes_fim ? str_pad($exp->mes_fim, 2, '0', STR_PAD_LEFT) . '/' : '') . $exp->ano_fim) }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Educação --}}
                @if($creator->educations->isNotEmpty() && $creator->isFieldPublic('education'))
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Educação</h3>
                        <div class="flex flex-col gap-3">
                            @foreach($creator->educations as $edu)
                                <div>
                                    <p class="text-sm font-bold text-gray-800">{{ $edu->escola }}</p>
                                    @if($edu->grau || $edu->area_estudo)
                                        <p class="text-xs text-gray-500">{{ trim(($edu->grau ?? '') . (($edu->grau && $edu->area_estudo) ? ', ' : '') . ($edu->area_estudo ?? '')) }}</p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $edu->ano_inicio }} — {{ $edu->atual ? 'Actual' : $edu->ano_fim }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── POSTS ─────────────────────────────────────────────────────────────── --}}
    @if($posts->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm py-20 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-50 flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                </svg>
            </div>
            <p class="text-sm font-semibold text-gray-500">Ainda não há publicações.</p>
            @auth
                @if(auth()->id() === $creator->id && in_array(auth()->user()->activeRole(), ['freelancer', 'creator']))
                    <a href="{{ route('social.create') }}"
                       class="inline-flex items-center gap-2 mt-4 bg-[#0055ff] hover:bg-[#0033cc] text-white text-sm font-bold px-5 py-2.5 rounded-xl transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Criar primeira publicação
                    </a>
                @endif
            @endauth
        </div>
    @else
        <div x-data="{ view: 'list' }">
            {{-- View toggle tabs --}}
            <div class="bg-white rounded-t-2xl border border-gray-100 shadow-sm flex items-center">
                <button @click="view = 'grid'"
                    class="flex-1 flex items-center justify-center gap-2 py-3.5 text-xs font-bold uppercase tracking-widest border-b-2 transition"
                    :class="view === 'grid' ? 'border-[#0055ff] text-[#0055ff]' : 'border-transparent text-gray-400 hover:text-gray-600'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                    </svg>
                    <span class="hidden sm:inline">Publicações</span>
                </button>
                <button @click="view = 'list'"
                    class="flex-1 flex items-center justify-center gap-2 py-3.5 text-xs font-bold uppercase tracking-widest border-b-2 transition"
                    :class="view === 'list' ? 'border-[#0055ff] text-[#0055ff]' : 'border-transparent text-gray-400 hover:text-gray-600'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                    </svg>
                    <span class="hidden sm:inline">Feed</span>
                </button>
            </div>

            {{-- Grid view --}}
            <div x-show="view === 'grid'" class="bg-white rounded-b-2xl border border-gray-100 border-t-0 shadow-sm overflow-hidden">
                <div class="grid grid-cols-3 gap-0.5">
                    @foreach($posts as $post)
                        @php
                            $thumb = $post->media->first();
                            $hasImg = $thumb && in_array($thumb->type, ['image', 'video']);
                        @endphp
                        <a href="{{ url('/social?post=' . $post->id) }}"
                           class="relative group overflow-hidden bg-gray-100"
                           style="aspect-ratio: 1 / 1;">
                            @if($hasImg && $thumb->type === 'image')
                                <img src="{{ $thumb->url() }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                     alt="" loading="lazy">
                            @elseif($hasImg && $thumb->type === 'video')
                                @if($thumb->thumbnailUrl())
                                    <img src="{{ $thumb->thumbnailUrl() }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                         alt="" loading="lazy">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-8 h-8 text-white/50" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    </div>
                                @endif
                                <div class="absolute top-2 right-2">
                                    <svg class="w-4 h-4 text-white drop-shadow" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                            @else
                                <div class="w-full h-full /10 flex items-center justify-center p-3">
                                    <p class="text-xs text-gray-500 line-clamp-4 text-center leading-relaxed">{{ Str::limit(strip_tags($post->content ?? ''), 80) }}</p>
                                </div>
                            @endif
                            @if($post->visibility === 'followers')
                                <div class="absolute top-2 left-2">
                                    <svg class="w-4 h-4 text-amber-400 drop-shadow" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 00-5.25 5.25v3a3 3 0 00-3 3v6.75a3 3 0 003 3h10.5a3 3 0 003-3v-6.75a3 3 0 00-3-3v-3c0-2.9-2.35-5.25-5.25-5.25zm3.75 8.25v-3a3.75 3.75 0 10-7.5 0v3h7.5z" clip-rule="evenodd"/></svg>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                                <div class="flex items-center gap-3 text-white text-sm font-bold">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                                        {{ $post->likesCount() }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M5.337 21.718a6.707 6.707 0 01-.533-.074.75.75 0 01-.44-1.223 3.73 3.73 0 00.814-1.686c.023-.115-.022-.317-.254-.543C3.274 16.587 2.25 14.41 2.25 12c0-5.03 4.428-9 9.75-9s9.75 3.97 9.75 9c0 5.03-4.428 9-9.75 9-.833 0-1.643-.097-2.417-.279a6.721 6.721 0 01-4.246.997z" clip-rule="evenodd"/></svg>
                                        {{ $post->commentsCount() }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- List/feed view --}}
            <div x-show="view === 'list'" class="bg-white rounded-b-2xl border border-gray-100 border-t-0 shadow-sm overflow-hidden">
                <div class="divide-y divide-gray-50">
                    @foreach($posts as $post)
                        <div class="py-1">
                            @include('livewire.social.partials.post-card', [
                                'post'                 => $post,
                                'subscribedCreatorIds' => $subscribedCreatorIds ?? [],
                            ])
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-4 pb-4">
            {{ $posts->links() }}
        </div>
    @endif

    {{-- Report modal --}}
    @if($reportingPostId)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-gray-900">Denunciar publicação</h3>
                    <button wire:click="cancelReport" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <p class="text-sm text-gray-500 mb-4">Descreva o motivo da sua denúncia.</p>
                <textarea wire:model="reportReason" rows="4"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-[#0055ff]/40"
                    placeholder="Ex: Conteúdo inapropriado, spam..."></textarea>
                @error('reportReason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <div class="flex gap-3 mt-4">
                    <button wire:click="submitReport"
                        class="flex-1 bg-red-500 hover:bg-red-600 text-white text-sm font-bold py-2.5 rounded-xl transition">
                        Enviar denúncia
                    </button>
                    <button wire:click="cancelReport"
                        class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2.5 rounded-xl hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    @endif

    @auth
        @livewire('client.send-proposal')
    @endauth

</div>
