<div class="space-y-6">

    {{-- Search & Filters --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
            </svg>
            <input
                type="text"
                wire:model.debounce.400ms="search"
                placeholder="Buscar por nome, especialidade ou descrição…"
                class="w-full pl-10 pr-4 py-2.5 rounded-[10px] border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/40 focus:border-[#00baff]"
            >
        </div>
        <div class="relative sm:w-52">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10M7 11h6M7 15h4"/>
            </svg>
            <input
                type="text"
                wire:model.debounce.400ms="skill"
                placeholder="Filtrar por skill"
                class="w-full pl-10 pr-4 py-2.5 rounded-[10px] border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/40 focus:border-[#00baff]"
            >
        </div>
        <button
            wire:click="$refresh"
            class="btn-primary"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 01.707 1.707L13 12.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-8.586L3.293 4.707A1 1 0 013 4z"/>
            </svg>
            Filtrar
        </button>
    </div>

    {{-- Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($freelancers as $freelancer)
            @php
                $fp          = $freelancer->freelancerProfile;
                $metrics     = $fp ? (is_array($fp->metrics) ? $fp->metrics : json_decode($fp->metrics, true)) : [];
                $rating      = (float) ($metrics['rating']            ?? 0);
                $projects    = (int)   ($metrics['completed_projects'] ?? 0);
                $availability = $fp->availability_status ?? 'unavailable';
                $availLabel  = match($availability) {
                    'available'   => ['Disponível',   'bg-green-100 text-green-700'],
                    'busy'        => ['Ocupado',       'bg-yellow-100 text-yellow-700'],
                    default       => ['Indisponível',  'bg-gray-100 text-gray-500'],
                };
            @endphp
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col overflow-hidden">

                {{-- Top accent bar --}}
                <div class="h-1 bg-gradient-to-r from-[#00baff] to-[#0099d6]"></div>

                <div class="p-5 flex flex-col gap-4 flex-1">
                    {{-- Avatar + identity --}}
                    <div class="flex items-start gap-3">
                        <a href="{{ route('freelancer.show', $freelancer->id) }}" class="relative flex-shrink-0">
                            <img
                                src="{{ $freelancer->avatarUrl() }}"
                                alt="{{ $freelancer->name }}"
                                class="w-14 h-14 rounded-full object-cover ring-2 ring-[#00baff]/20"
                            >
                            {{-- Online dot based on availability --}}
                            <span class="absolute bottom-0.5 right-0.5 w-3 h-3 rounded-full border-2 border-white
                                {{ $availability === 'available' ? 'bg-green-400' : ($availability === 'busy' ? 'bg-yellow-400' : 'bg-gray-300') }}">
                            </span>
                        </a>

                        <div class="flex-1 min-w-0">
                            <a href="{{ route('freelancer.show', $freelancer->id) }}" class="text-base font-semibold text-gray-900 hover:text-[#00baff] transition truncate block">
                                {{ $freelancer->name }}
                            </a>
                            @if($fp && $fp->headline)
                                <p class="text-xs text-gray-500 truncate">{{ $fp->headline }}</p>
                            @endif

                            {{-- Rating & projects --}}
                            <div class="flex items-center gap-2 mt-1">
                                <div class="flex items-center gap-0.5">
                                    @for($s = 1; $s <= 5; $s++)
                                        <svg class="w-3 h-3 {{ $s <= round($rating) ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                                @if($rating > 0)
                                    <span class="text-xs text-gray-500">{{ number_format($rating,1) }}</span>
                                @endif
                                @if($projects > 0)
                                    <span class="text-xs text-gray-400">· {{ $projects }} projetos</span>
                                @endif
                            </div>
                        </div>

                        {{-- Hourly rate --}}
                        @if($fp && $fp->hourly_rate)
                            <div class="text-right flex-shrink-0">
                                <span class="text-sm font-bold text-gray-800">{{ number_format($fp->hourly_rate, 0) }}</span>
                                <span class="text-xs text-gray-400 block -mt-0.5">{{ $fp->currency ?? 'AOA' }}/h</span>
                            </div>
                        @endif
                    </div>

                    {{-- Summary --}}
                    @if($fp && $fp->summary)
                        <p class="text-xs text-gray-500 leading-relaxed line-clamp-2">
                            {{ Str::limit($fp->summary, 130) }}
                        </p>
                    @endif

                    {{-- Skills --}}
                    @if($fp && !empty($fp->skills))
                        <div class="flex flex-wrap gap-1.5">
                            @foreach(array_slice((array) $fp->skills, 0, 5) as $tag)
                                <span class="text-xs bg-[#00baff]/10 text-[#0099d6] px-2.5 py-0.5 rounded-full font-medium">{{ $tag }}</span>
                            @endforeach
                            @if(count((array) $fp->skills) > 5)
                                <span class="text-xs text-gray-400 px-1">+{{ count((array) $fp->skills) - 5 }}</span>
                            @endif
                        </div>
                    @endif

                    {{-- Footer: availability badge + CTA buttons --}}
                    <div class="mt-auto pt-3 border-t border-gray-50 flex items-center justify-between gap-2">
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $availLabel[1] }}">
                            {{ $availLabel[0] }}
                        </span>

                        <div class="flex items-center gap-2">
                            <a
                                href="{{ route('freelancer.show', $freelancer->id) }}"
                                class="btn-outline text-xs"
                            >
                                Ver perfil
                            </a>
                            @auth
                                <button
                                    type="button"
                                    wire:click="openProposal({{ $freelancer->id }})"
                                    class="btn-primary text-xs"
                                >
                                    Enviar proposta
                                </button>
                            @else
                                <a
                                    href="{{ route('login') }}"
                                    class="btn-primary text-xs"
                                >
                                    Enviar proposta
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 flex flex-col items-center gap-3 text-gray-400">
                <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318A4.486 4.486 0 0 0 12.016 15a4.486 4.486 0 0 0-3.198 1.318M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z"/>
                </svg>
                <p class="text-sm font-medium">Nenhum freelancer encontrado</p>
                <p class="text-xs">Tente outros termos de pesquisa</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="flex justify-center">
        {{ $freelancers->links() }}
    </div>

</div>
