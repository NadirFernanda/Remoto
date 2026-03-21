<div>

    {{-- Search & Filters Bar --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        {{-- Search input --}}
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
            </svg>
            <input
                type="search"
                wire:model.live.debounce.400ms="query"
                placeholder="Procurar criadores por nome ou bio..."
                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]"
            >
        </div>

        {{-- Category filter --}}
        <select wire:model.live="category"
            class="px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] bg-white">
            <option value="">Todas as Categorias</option>
            @foreach($categories as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>

        {{-- Sort --}}
        <select wire:model.live="sort"
            class="px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] bg-white">
            <option value="populares">Mais Populares</option>
            <option value="novos">Mais Recentes</option>
            <option value="preco_asc">Menor Preço</option>
            <option value="preco_desc">Maior Preço</option>
        </select>
    </div>

    {{-- Loading indicator --}}
    <div wire:loading.flex class="justify-center py-6">
        <svg class="w-6 h-6 animate-spin text-[#00baff]" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
    </div>

    {{-- Results --}}
    <div wire:loading.remove>

        @if($creators->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <svg class="w-16 h-16 text-gray-200 mb-4" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
                <p class="text-gray-500 font-medium">Nenhum criador encontrado</p>
                <p class="text-gray-400 text-sm mt-1">Tente ajustar os filtros ou pesquisa</p>
            </div>
        @else
            <p class="text-xs text-gray-400 mb-4">{{ $creators->total() }} criador(es) encontrado(s)</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($creators as $creator)
                    @php
                        $profile  = $creator->creatorProfile;
                        $isSubbed = in_array($creator->id, $subscribedCreatorIds);
                        $catLabel = \App\Models\CreatorProfile::categories()[$profile?->category ?? ''] ?? null;
                    @endphp
                    <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden hover:shadow-md hover:border-[#00baff]/30 transition group">

                        {{-- Cover photo --}}
                        <div class="relative h-24 bg-gradient-to-br from-[#00baff]/10 to-[#e0f7fa]">
                            @if($profile?->cover_photo)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($profile->cover_photo) }}"
                                     alt="capa"
                                     class="absolute inset-0 w-full h-full object-cover">
                            @endif
                            {{-- Avatar --}}
                            <div class="absolute -bottom-6 left-4">
                                <img src="{{ $creator->avatarUrl() }}"
                                     alt="{{ $creator->name }}"
                                     class="w-12 h-12 rounded-full object-cover ring-2 ring-white"
                                     onerror="this.src='{{ asset('img/default-avatar.svg') }}'">
                            </div>
                        </div>

                        <div class="pt-8 pb-4 px-4">
                            {{-- Name & category --}}
                            <div class="flex items-start justify-between gap-2 mb-1">
                                <div class="min-w-0">
                                    <p class="font-semibold text-sm text-gray-900 truncate">{{ $creator->name }}</p>
                                    @if($catLabel)
                                        <span class="inline-block text-xs text-[#00baff] bg-[#e0f7fa] rounded-full px-2 py-0.5 mt-0.5">{{ $catLabel }}</span>
                                    @endif
                                </div>
                                @if($isSubbed)
                                    <span class="flex-shrink-0 text-xs text-green-600 bg-green-50 border border-green-100 rounded-full px-2 py-0.5 font-medium">Assinado</span>
                                @endif
                            </div>

                            {{-- Bio snippet --}}
                            @if($profile?->bio)
                                <p class="text-xs text-gray-400 mt-2 line-clamp-2">{{ $profile->bio }}</p>
                            @endif

                            {{-- Stats --}}
                            <div class="flex items-center gap-4 mt-3 text-xs text-gray-500">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ number_format($profile?->total_subscribers ?? 0) }} assinantes
                                </span>
                                <span class="flex items-center gap-1 font-medium text-gray-700">
                                    <svg class="w-3.5 h-3.5 text-[#00baff]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Kz 3.000/mês
                                </span>
                            </div>

                            {{-- Actions --}}
                            <div class="mt-4 flex gap-2">
                                <a href="{{ route('social.creator', $creator) }}"
                                   class="flex-1 text-center text-xs font-medium px-3 py-2 rounded-xl border border-gray-200 text-gray-600 hover:border-[#00baff] hover:text-[#00baff] transition">
                                    Ver perfil
                                </a>
                                @auth
                                    @if(auth()->id() === $creator->id)
                                        {{-- own profile: no subscribe button --}}
                                    @elseif($isSubbed)
                                        <a href="{{ route('social.creator', $creator) }}"
                                           class="flex-1 text-center text-xs font-medium px-3 py-2 rounded-xl bg-green-50 text-green-600 border border-green-100 hover:bg-green-100 transition">
                                            Ver conteúdo
                                        </a>
                                    @else
                                        <a href="{{ route('social.creator', $creator) }}"
                                           class="flex-1 text-center text-xs font-medium px-3 py-2 rounded-xl bg-[#00baff] text-white hover:bg-[#009ad6] transition">
                                            Assinar
                                        </a>
                                    @endif
                                @endauth
                                @guest
                                    <a href="{{ route('login') }}"
                                       class="flex-1 text-center text-xs font-medium px-3 py-2 rounded-xl bg-[#00baff] text-white hover:bg-[#009ad6] transition">
                                        Assinar
                                    </a>
                                @endguest
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $creators->links() }}
            </div>
        @endif

    </div>

</div>
