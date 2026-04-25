<div class="min-h-screen bg-gray-50/70">

    {{-- ── Sub-header fixo com back button ─────────────────────────────────── --}}
    <div class="sticky top-[70px] z-30 bg-white/95 backdrop-blur-md border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-14 flex items-center gap-3">

            {{-- Back button --}}
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-[#00baff] transition group flex-shrink-0">
                <svg class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                <span class="hidden sm:inline">Dashboard</span>
            </a>

            <div class="w-px h-5 bg-gray-200 flex-shrink-0"></div>

            {{-- Title --}}
            <div class="flex items-center gap-2 min-w-0">
                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-[#00baff] to-blue-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h1 class="text-base font-bold text-gray-900 truncate">
                    @if($hashtag) #{{ $hashtag }}
                    @elseif($bookmarkedOnly) Guardados
                    @elseif($myPostsOnly) Minhas Publicações
                    @else Feed Social
                    @endif
                </h1>
            </div>

            {{-- Right actions --}}
            <div class="ml-auto flex items-center gap-2 flex-shrink-0">
                @auth
                    @if(in_array(auth()->user()->activeRole(), ['freelancer', 'creator']))
                        <a href="{{ route('social.create') }}"
                           class="inline-flex items-center gap-1.5 bg-[#00baff] hover:bg-[#009ad6] text-white text-sm font-semibold px-4 py-1.5 rounded-full transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span class="hidden sm:inline">Publicar</span>
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    {{-- ── Main 3-column layout ─────────────────────────────────────────────── --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            {{-- ── LEFT SIDEBAR ─────────────────────────────────────────────── --}}
            <aside class="hidden lg:block lg:col-span-3">
                <div class="sticky top-[130px] space-y-4">

                    {{-- User card --}}
                    @auth
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="h-16 bg-gradient-to-br from-[#00baff]/30 via-blue-100/40 to-indigo-100/20"></div>
                        <div class="px-4 pb-4 -mt-7">
                            <img src="{{ auth()->user()->avatarUrl() }}"
                                 alt="{{ auth()->user()->name }}"
                                 class="w-14 h-14 rounded-xl border-2 border-white shadow-md object-cover"
                                 onerror="this.src='{{ asset('img/default-avatar.svg') }}'">
                            <p class="mt-2 text-sm font-bold text-gray-900 leading-tight">{{ auth()->user()->name }}</p>
                            @if(auth()->user()->freelancerProfile?->headline)
                                <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ auth()->user()->freelancerProfile->headline }}</p>
                            @else
                                <p class="text-xs text-gray-400 mt-0.5 capitalize">{{ auth()->user()->activeRole() }}</p>
                            @endif
                        </div>
                    </div>
                    @endauth

                    {{-- Navigation --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-2">
                        <nav class="space-y-0.5">
                            <a href="{{ route('social.feed') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ !$bookmarkedOnly && !$myPostsOnly && !$hashtag ? 'bg-[#00baff]/10 text-[#00baff]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                                </svg>
                                Feed
                            </a>
                            <a href="{{ route('social.bookmarks') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ $bookmarkedOnly ? 'bg-[#00baff]/10 text-[#00baff]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                <svg class="w-5 h-5" fill="{{ $bookmarkedOnly ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
                                </svg>
                                Guardados
                            </a>
                            @auth
                                @if(in_array(auth()->user()->activeRole(), ['freelancer', 'creator']))
                                <a href="{{ route('social.myposts') }}"
                                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ $myPostsOnly ? 'bg-[#00baff]/10 text-[#00baff]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                                    </svg>
                                    Minhas Publicações
                                </a>
                                @endif
                            @endauth
                            <a href="{{ route('social.creators') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                </svg>
                                Descobrir Criadores
                            </a>
                            <a href="{{ route('freelancers.search') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                </svg>
                                Freelancers
                            </a>
                        </nav>
                    </div>

                    {{-- Create post CTA --}}
                    @auth
                        @if(in_array(auth()->user()->activeRole(), ['freelancer', 'creator']))
                        <div class="bg-gradient-to-br from-[#00baff] to-blue-600 rounded-2xl p-4 text-white shadow-md">
                            <p class="text-sm font-bold mb-1">Partilhe o seu trabalho</p>
                            <p class="text-xs text-white/80 mb-3 leading-relaxed">Publique conteúdo e ganhe visibilidade junto dos clientes.</p>
                            <a href="{{ route('social.create') }}"
                               class="inline-flex items-center gap-1.5 bg-white text-[#00baff] text-xs font-bold px-3 py-1.5 rounded-full hover:bg-white/90 transition shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                </svg>
                                Criar publicação
                            </a>
                        </div>
                        @endif
                    @endauth
                </div>
            </aside>

            {{-- ── CENTER FEED ───────────────────────────────────────────────── --}}
            <main class="lg:col-span-6 min-w-0 space-y-4">

                {{-- Flash message --}}
                @if(session('success'))
                    <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl text-sm font-medium flex items-center gap-2 shadow-sm">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Hashtag breadcrumb --}}
                @if($hashtag)
                    <div class="flex items-center gap-2 bg-white rounded-2xl border border-gray-100 shadow-sm px-4 py-3">
                        <a href="{{ route('social.feed') }}"
                           class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-[#00baff] transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                            </svg>
                            Todos os posts
                        </a>
                        <span class="text-gray-300">/</span>
                        <span class="text-sm font-bold text-[#00baff]">#{{ $hashtag }}</span>
                    </div>
                @endif

                {{-- Stories bar --}}
                @auth
                    <livewire:social.stories />
                @endauth

                {{-- Mobile: filter pills --}}
                <div class="flex items-center gap-2 lg:hidden flex-wrap">
                    <a href="{{ route('social.feed') }}"
                       class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-full border transition {{ !$bookmarkedOnly && !$myPostsOnly ? 'bg-[#00baff] text-white border-[#00baff]' : 'border-gray-200 text-gray-600 hover:border-[#00baff] hover:text-[#00baff]' }}">
                        Feed
                    </a>
                    <a href="{{ route('social.bookmarks') }}"
                       class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-full border transition {{ $bookmarkedOnly ? 'bg-[#00baff] text-white border-[#00baff]' : 'border-gray-200 text-gray-600 hover:border-[#00baff] hover:text-[#00baff]' }}">
                        Guardados
                    </a>
                    @auth
                        @if(in_array(auth()->user()->activeRole(), ['freelancer', 'creator']))
                        <a href="{{ route('social.myposts') }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-full border transition {{ $myPostsOnly ? 'bg-[#00baff] text-white border-[#00baff]' : 'border-gray-200 text-gray-600 hover:border-[#00baff] hover:text-[#00baff]' }}">
                            Minhas
                        </a>
                        @endif
                    @endauth
                </div>

                {{-- Empty state --}}
                @if($isEmpty)
                    <div class="bg-white border border-gray-100 rounded-2xl p-10 text-center shadow-sm">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-[#00baff]/10 to-blue-100/30 flex items-center justify-center">
                            <svg class="w-8 h-8 text-[#00baff]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <p class="text-base font-semibold text-gray-800 mb-1">Ainda não segue nenhum criador</p>
                        <p class="text-sm text-gray-500 mb-5">Explore os criadores e comece a seguir os que mais gosta.</p>
                        <a href="{{ route('freelancers.search') }}"
                           class="inline-flex items-center gap-2 bg-[#00baff] hover:bg-[#009ad6] text-white text-sm font-semibold px-5 py-2.5 rounded-full transition shadow-sm">
                            Descobrir criadores
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                            </svg>
                        </a>
                    </div>
                @endif

                {{-- Posts --}}
                <div class="space-y-4">
                    @forelse($posts as $post)
                        @include('livewire.social.partials.post-card', [
                            'post'                 => $post,
                            'subscribedCreatorIds' => $subscribedCreatorIds ?? [],
                        ])
                    @empty
                        @if(!$isEmpty)
                        <div class="bg-white border border-gray-100 rounded-2xl py-16 text-center shadow-sm">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                            </svg>
                            <p class="text-sm font-medium text-gray-500">
                                @if($bookmarkedOnly) Nenhuma publicação guardada.
                                @elseif($myPostsOnly) Ainda não publicou nada.
                                @elseif($hashtag) Nenhuma publicação com #{{ $hashtag }}.
                                @else Nenhuma publicação encontrada.
                                @endif
                            </p>
                        </div>
                        @endif
                    @endforelse
                </div>

                {{-- Pagination --}}
                <div class="mt-4 pb-4">{{ $posts->links() }}</div>
            </main>

            {{-- ── RIGHT SIDEBAR ─────────────────────────────────────────────── --}}
            <aside class="hidden lg:block lg:col-span-3">
                <div class="sticky top-[130px] space-y-4">

                    {{-- Explore links --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                        <h3 class="text-sm font-bold text-gray-900 mb-3">Explorar</h3>
                        <div class="space-y-1">
                            <a href="{{ route('social.creators') }}"
                               class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 transition group">
                                <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4.5 h-4.5 text-[#00baff]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="width:18px;height:18px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800 group-hover:text-[#00baff] transition">Criadores</p>
                                    <p class="text-xs text-gray-400">Descubra talentos</p>
                                </div>
                            </a>
                            <a href="{{ route('freelancers.search') }}"
                               class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 transition group">
                                <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4.5 h-4.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="width:18px;height:18px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800 group-hover:text-emerald-600 transition">Freelancers</p>
                                    <p class="text-xs text-gray-400">Contratar profissionais</p>
                                </div>
                            </a>
                            <a href="{{ route('dashboard') }}"
                               class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 transition group">
                                <div class="w-9 h-9 rounded-xl bg-violet-50 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4.5 h-4.5 text-violet-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="width:18px;height:18px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800 group-hover:text-violet-600 transition">Dashboard</p>
                                    <p class="text-xs text-gray-400">Voltar ao painel</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    {{-- Trending hashtags --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                        <h3 class="text-sm font-bold text-gray-900 mb-3">Tópicos populares</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['design', 'programacao', 'marketing', 'fotografia', 'videos', 'copywriting', 'branding', 'freelance'] as $tag)
                            <a href="{{ route('social.feed', ['hashtag' => $tag]) }}"
                               class="inline-block px-3 py-1 rounded-full text-xs font-semibold transition {{ $hashtag === $tag ? 'bg-[#00baff] text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-[#00baff]/10 hover:text-[#00baff]' }}">
                                #{{ $tag }}
                            </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Branding badge --}}
                    <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-4 text-white shadow-md">
                        <p class="text-sm font-bold mb-0.5">24h Remoto</p>
                        <p class="text-xs text-white/50 mb-3">Trabalho remoto em Angola</p>
                        <div class="flex flex-wrap gap-1.5">
                            <span class="text-xs bg-white/10 rounded-full px-2.5 py-1">Freelancers</span>
                            <span class="text-xs bg-white/10 rounded-full px-2.5 py-1">Projetos</span>
                            <span class="text-xs bg-white/10 rounded-full px-2.5 py-1">Pagamentos</span>
                        </div>
                    </div>
                </div>
            </aside>

        </div>
    </div>

    {{-- ── Edit modal ────────────────────────────────────────────────────────── --}}
    @if($editingPostId)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-gray-900">Editar publicação</h3>
                    <button wire:click="cancelEditPost" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <textarea wire:model="editContent" rows="5"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-[#00baff]/40"
                    maxlength="3000"
                    placeholder="Escreva o novo conteúdo..."></textarea>
                <p class="text-xs text-gray-400 mt-1 text-right">{{ strlen($editContent) }}/3000</p>
                @error('editContent') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <div class="flex gap-3 mt-4">
                    <button wire:click="saveEditPost"
                        class="flex-1 bg-[#00baff] hover:bg-[#009ad6] text-white text-sm font-semibold py-2 rounded-xl transition">
                        Guardar alterações
                    </button>
                    <button wire:click="cancelEditPost"
                        class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2 rounded-xl hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Report modal --}}
    @if($reportingPostId || $reportingUserId)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                <h3 class="text-lg font-bold mb-1">Denunciar {{ $reportType === 'post' ? 'publicação' : 'utilizador' }}</h3>
                <p class="text-sm text-gray-500 mb-4">Descreva brevemente o motivo da sua denúncia.</p>
                <textarea wire:model="reportReason" rows="4"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-[#00baff]/40"
                    placeholder="Ex: Conteúdo inapropriado, spam, ofensivo..."></textarea>
                @error('reportReason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <div class="flex gap-3 mt-4">
                    <button wire:click="submitReport"
                        class="flex-1 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold py-2 rounded-xl transition">
                        Enviar denúncia
                    </button>
                    <button wire:click="cancelReport"
                        class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2 rounded-xl hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
