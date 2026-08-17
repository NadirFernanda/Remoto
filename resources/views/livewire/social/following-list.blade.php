<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-[#00c8ff] to-[#0033cc] rounded-2xl p-6 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold">A Seguir</h2>
            <p class="text-sm text-white/75 mt-1">
                Freelancers e criadores que acompanha na plataforma
            </p>
        </div>
        <div class="flex gap-3">
            <div class="bg-white/15 border border-white/30 rounded-xl px-4 py-2 text-center">
                <div class="text-xl font-extrabold">{{ $totalFollowing }}</div>
                <div class="text-xs text-white/75">A seguir</div>
            </div>
            <div class="bg-white/15 border border-white/30 rounded-xl px-4 py-2 text-center">
                <div class="text-xl font-extrabold">{{ $totalSubscriptions }}</div>
                <div class="text-xs text-white/75">Assinaturas</div>
            </div>
        </div>
    </div>

    {{-- Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- Sidebar --}}
        <aside class="hidden lg:block lg:col-span-3">
            <div class="sticky top-6 space-y-4">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-2">
                    <nav class="space-y-0.5">
                        <a href="{{ route('social.feed') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                            Feed
                        </a>
                        <a href="{{ route('social.following') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium bg-[#0055ff]/10 text-[#0055ff] transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                            A Seguir
                            @if($totalFollowing)
                            <span class="ml-auto text-xs bg-[#0055ff] text-white rounded-full px-1.5 font-semibold">{{ $totalFollowing }}</span>
                            @endif
                        </a>
                        <a href="{{ route('social.bookmarks') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/></svg>
                            Guardados
                        </a>
                        <a href="{{ route('social.creators') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                            Descobrir Criadores
                        </a>
                    </nav>
                </div>
            </div>
        </aside>

        {{-- Main --}}
        <div class="col-span-1 lg:col-span-9 space-y-4">

            {{-- Tabs + pesquisa --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="flex items-center border-b border-gray-100">
                    <button wire:click="$set('tab','seguindo')"
                        class="flex items-center gap-2 px-5 py-3.5 text-sm font-semibold border-b-2 transition
                            {{ $tab === 'seguindo' ? 'border-[#0055ff] text-[#0055ff]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                        A Seguir
                        <span class="text-xs px-1.5 py-0.5 rounded-full font-semibold {{ $tab === 'seguindo' ? 'bg-[#0055ff] text-white' : 'bg-gray-100 text-gray-500' }}">{{ $totalFollowing }}</span>
                    </button>
                    <button wire:click="$set('tab','assinaturas')"
                        class="flex items-center gap-2 px-5 py-3.5 text-sm font-semibold border-b-2 transition
                            {{ $tab === 'assinaturas' ? 'border-[#0055ff] text-[#0055ff]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Assinaturas Activas
                        <span class="text-xs px-1.5 py-0.5 rounded-full font-semibold {{ $tab === 'assinaturas' ? 'bg-[#0055ff] text-white' : 'bg-gray-100 text-gray-500' }}">{{ $totalSubscriptions }}</span>
                    </button>

                    {{-- Search --}}
                    <div class="ml-auto px-3 py-2">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Pesquisar..."
                                class="pl-9 pr-3 py-2 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#0055ff]/30 focus:border-[#0055ff] w-40">
                        </div>
                    </div>
                </div>

                {{-- TAB: A SEGUIR --}}
                @if($tab === 'seguindo')
                    @if($following->isEmpty())
                    <div class="py-16 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                        <p class="font-medium text-sm">{{ $search ? 'Nenhum resultado para "' . $search . '"' : 'Ainda não segue ninguém.' }}</p>
                        @if(!$search)
                        <a href="{{ route('social.creators') }}" class="mt-3 inline-flex items-center gap-1.5 text-sm text-[#0055ff] hover:underline font-medium">
                            Descobrir criadores →
                        </a>
                        @endif
                    </div>
                    @else
                    <div class="divide-y divide-gray-50">
                        @foreach($following as $creator)
                        <div wire:key="following-{{ $creator->id }}" class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition">
                            {{-- Avatar --}}
                            <a href="{{ route('social.creator', $creator) }}" class="flex-shrink-0">
                                <img src="{{ $creator->avatarUrl() }}" alt="{{ $creator->name }}"
                                    class="w-11 h-11 rounded-xl object-cover border border-gray-100 hover:ring-2 hover:ring-[#0055ff]/30 transition"
                                    onerror="this.src='{{ asset('img/default-avatar.svg') }}'">
                            </a>
                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('social.creator', $creator) }}"
                                    class="text-sm font-semibold text-gray-800 hover:text-[#0055ff] transition truncate block">
                                    {{ $creator->name }}
                                </a>
                                @if($creator->freelancerProfile?->headline)
                                    <p class="text-xs text-gray-500 truncate mt-0.5">{{ $creator->freelancerProfile->headline }}</p>
                                @else
                                    <p class="text-xs text-gray-400 mt-0.5 capitalize">{{ $creator->activeRole() }}</p>
                                @endif
                            </div>
                            {{-- Actions --}}
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <a href="{{ route('social.creator', $creator) }}"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                                    Ver perfil
                                </a>
                                <button wire:click="unfollow({{ $creator->id }})"
                                    wire:confirm="Deixar de seguir {{ addslashes($creator->name) }}?"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-100 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                    Deixar de seguir
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if($following->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100">{{ $following->links() }}</div>
                    @endif
                    @endif

                {{-- TAB: ASSINATURAS --}}
                @elseif($tab === 'assinaturas')
                    @if($subscriptions->isEmpty())
                    <div class="py-16 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="font-medium text-sm">{{ $search ? 'Nenhum resultado para "' . $search . '"' : 'Sem assinaturas activas.' }}</p>
                        @if(!$search)
                        <a href="{{ route('social.creators') }}" class="mt-3 inline-flex items-center gap-1.5 text-sm text-[#0055ff] hover:underline font-medium">
                            Descobrir criadores →
                        </a>
                        @endif
                    </div>
                    @else
                    <div class="divide-y divide-gray-50">
                        @foreach($subscriptions as $sub)
                        @php $creator = $sub->creator; @endphp
                        <div class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition">
                            {{-- Avatar --}}
                            <a href="{{ route('social.creator', $creator) }}" class="flex-shrink-0">
                                <img src="{{ $creator->avatarUrl() }}" alt="{{ $creator->name }}"
                                    class="w-11 h-11 rounded-xl object-cover border border-gray-100 hover:ring-2 hover:ring-[#0055ff]/30 transition"
                                    onerror="this.src='{{ asset('img/default-avatar.svg') }}'">
                            </a>
                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('social.creator', $creator) }}"
                                    class="text-sm font-semibold text-gray-800 hover:text-[#0055ff] transition truncate block">
                                    {{ $creator->name }}
                                </a>
                                @if($creator->freelancerProfile?->headline)
                                    <p class="text-xs text-gray-500 truncate mt-0.5">{{ $creator->freelancerProfile->headline }}</p>
                                @endif
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-xs text-emerald-600 font-semibold bg-emerald-50 px-2 py-0.5 rounded-full">
                                        Activa
                                    </span>
                                    @if($sub->expires_at)
                                    <span class="text-xs text-gray-400">
                                        Expira {{ $sub->expires_at->format('d/m/Y') }}
                                    </span>
                                    @endif
                                    <span class="text-xs text-gray-500 font-medium">
                                        {{ number_format($sub->amount, 0, ',', '.') }} Kz/mês
                                    </span>
                                </div>
                            </div>
                            {{-- Action --}}
                            <a href="{{ route('social.creator', $creator) }}"
                                class="flex-shrink-0 inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                                Ver perfil
                            </a>
                        </div>
                        @endforeach
                    </div>
                    @if($subscriptions->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100">{{ $subscriptions->links() }}</div>
                    @endif
                    @endif
                @endif

            </div>
        </div>
    </div>
</div>
