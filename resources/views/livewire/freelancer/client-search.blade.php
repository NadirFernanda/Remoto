<div>

    {{-- Search & Sort Bar --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        {{-- Search input --}}
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
            </svg>
            <input
                type="search"
                wire:model.live.debounce.400ms="query"
                placeholder="Procurar clientes por nome..."
                class="w-full pl-10 pr-4 py-2.5 rounded-xl text-sm text-gray-800 bg-white border border-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#0055ff]/30 focus:border-[#0055ff]"
            >
        </div>

        {{-- Sort --}}
        <select wire:model.live="sort"
            class="px-3 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#0055ff]/30 focus:border-[#0055ff]">
            <option value="recentes">Mais Recentes</option>
            <option value="projetos">Mais Projectos Publicados</option>
        </select>
    </div>

    {{-- Loading indicator --}}
    <div wire:loading.flex class="justify-center py-6">
        <svg class="w-6 h-6 animate-spin text-[#0055ff]" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
    </div>

    {{-- Results --}}
    <div wire:loading.remove>

        @if($clients->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <svg class="w-16 h-16 text-gray-200 mb-4" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
                <p class="text-gray-500 font-medium">Nenhum cliente encontrado</p>
                <p class="text-gray-400 text-sm mt-1">Tente ajustar a pesquisa</p>
            </div>
        @else
            <p class="text-xs text-gray-400 mb-4">{{ $clients->total() }} cliente(s) encontrado(s)</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($clients as $client)
                    <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden hover:shadow-lg hover:border-[#0055ff]/50 transition group">

                        {{-- Cover photo --}}
                        <div class="relative h-24" style="background: linear-gradient(135deg, #00c8ff 0%, #0055ff 60%, #0033cc 100%)">
                            @if($client->coverPhotoUrl())
                                <img src="{{ $client->coverPhotoUrl() }}"
                                     alt="capa"
                                     loading="lazy" decoding="async"
                                     class="absolute inset-0 w-full h-full object-cover">
                            @endif
                            {{-- Avatar --}}
                            <div class="absolute -bottom-6 left-4">
                                <img src="{{ $client->avatarUrl() }}"
                                     alt="{{ $client->name }}"
                                     loading="lazy" decoding="async"
                                     class="w-12 h-12 rounded-full object-cover ring-2 ring-white"
                                     onerror="this.src='{{ asset('img/default-avatar.svg') }}'">
                            </div>
                        </div>

                        <div class="pt-8 pb-4 px-4">
                            {{-- Name --}}
                            <p class="font-semibold text-sm text-gray-900 truncate">{{ $client->name }}</p>
                            <span class="inline-block text-xs text-[#0055ff] bg-[#e0f7fa] rounded-full px-2 py-0.5 mt-0.5">Cliente</span>

                            {{-- Location --}}
                            @if($client->location && $client->isFieldPublic('location'))
                                <p class="text-xs text-gray-400 mt-2 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span class="truncate">{{ $client->location }}</span>
                                </p>
                            @endif

                            {{-- Stats --}}
                            <div class="flex items-center gap-4 mt-3 text-xs text-gray-500">
                                <span class="flex items-center gap-1 font-medium text-gray-700">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    {{ $client->published_projects_count }} {{ $client->published_projects_count === 1 ? 'projecto publicado' : 'projectos publicados' }}
                                </span>
                            </div>
                            @if($client->completed_projects_count > 0)
                                <div class="flex items-center gap-1.5 mt-1.5 text-xs text-emerald-600">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $client->completed_projects_count }} {{ $client->completed_projects_count === 1 ? 'concluído' : 'concluídos' }}
                                </div>
                            @endif

                            {{-- Actions --}}
                            <div class="mt-4">
                                <a href="{{ route('client.public', $client) }}"
                                   class="block text-center text-xs font-medium px-3 py-2 rounded-xl border border-gray-200 text-gray-600 hover:border-[#0055ff] hover:text-[#0055ff] transition">
                                    Ver perfil
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $clients->links() }}
            </div>
        @endif

    </div>

</div>
