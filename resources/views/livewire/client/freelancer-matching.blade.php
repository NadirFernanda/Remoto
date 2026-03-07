<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <a href="{{ route('client.projects') }}" class="btn-outline text-xs inline-flex items-center gap-1 mb-3">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Voltar
            </a>
            <h1 class="text-xl font-bold text-gray-900">Freelancers Sugeridos</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Para o projeto <span class="font-medium text-gray-700">"{{ $service->titulo }}"</span>
            </p>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-[10px] px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @elseif(session('info'))
        <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-[10px] px-4 py-3 text-sm">
            {{ session('info') }}
        </div>
    @elseif(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-[10px] px-4 py-3 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Project briefing context --}}
    <div class="bg-[#00baff]/5 border border-[#00baff]/20 rounded-2xl p-4 flex gap-3 items-start">
        <div class="w-8 h-8 rounded-lg bg-[#00baff]/20 flex items-center justify-center flex-shrink-0 mt-0.5">
            <svg class="w-4 h-4 text-[#00baff]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-[#0099d6] font-semibold uppercase tracking-wide">Como funciona o matching</p>
            <p class="text-sm text-gray-600 mt-0.5">
                Os freelancers são classificados por compatibilidade de skills com o briefing do projeto,
                disponibilidade, avaliação, portfólio e histórico de colaboração consigo.
            </p>
        </div>
    </div>

    {{-- Suggestions grid --}}
    @if($suggestions->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
            <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/>
            </svg>
            <p class="text-sm font-medium text-gray-500">Nenhum freelancer disponível no momento.</p>
            <a href="{{ route('freelancers.index') }}" class="btn-primary mt-4 text-xs">Ver todos os freelancers</a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($suggestions as $item)
                @php
                    $fl       = $item['freelancer'];
                    $fp       = $fl->freelancerProfile;
                    $rating   = $item['rating'];
                    $metrics  = $item['metrics'];
                    $projects = (int) ($metrics['completed_projects'] ?? 0);
                    $avail    = $fp->availability_status ?? 'unavailable';
                    $availLabel = match($avail) {
                        'available'   => ['Disponível',  'bg-green-100 text-green-700'],
                        'busy'        => ['Ocupado',      'bg-yellow-100 text-yellow-700'],
                        default       => ['Indisponível', 'bg-gray-100 text-gray-500'],
                    };
                    $score = $item['score'];
                    // Normalize score to 0-100% for display (max theoretical ~30)
                    $pct   = min(100, round($score / 28 * 100));
                @endphp
                <div class="bg-white rounded-2xl border border-gray-100 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 overflow-hidden flex flex-col">

                    {{-- Match score bar --}}
                    <div class="h-1 bg-gray-100">
                        <div class="h-full bg-gradient-to-r from-[#00baff] to-[#0099d6] transition-all" style="width: {{ $pct }}%"></div>
                    </div>

                    <div class="p-5 flex flex-col gap-3 flex-1">

                        {{-- Match score badge --}}
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-[#0099d6] flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                {{ $pct }}% compatível
                            </span>
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $availLabel[1] }}">{{ $availLabel[0] }}</span>
                        </div>

                        {{-- Identity --}}
                        <div class="flex items-center gap-3">
                            <a href="{{ route('freelancer.show', $fl->id) }}" class="relative flex-shrink-0">
                                <img src="{{ $fl->avatarUrl() }}" alt="{{ $fl->name }}"
                                    width="48" height="48" loading="lazy"
                                    class="w-12 h-12 rounded-full object-cover ring-2 ring-[#00baff]/20">
                            </a>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('freelancer.show', $fl->id) }}"
                                    class="text-sm font-semibold text-gray-900 hover:text-[#00baff] transition truncate block">
                                    {{ $fl->name }}
                                </a>
                                @if($fp && $fp->headline)
                                    <p class="text-xs text-gray-500 truncate">{{ $fp->headline }}</p>
                                @endif
                                {{-- Stars --}}
                                <div class="flex items-center gap-1 mt-0.5">
                                    @for($s = 1; $s <= 5; $s++)
                                        <svg class="w-3 h-3 {{ $s <= round($rating) ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                    @if($rating > 0)
                                        <span class="text-xs text-gray-400 ml-0.5">{{ number_format($rating,1) }}</span>
                                    @endif
                                    @if($projects > 0)
                                        <span class="text-xs text-gray-400 ml-1">· {{ $projects }} projetos</span>
                                    @endif
                                </div>
                            </div>
                            @if($fp && $fp->hourly_rate)
                                <div class="text-right flex-shrink-0">
                                    <span class="text-sm font-bold text-gray-800">{{ number_format($fp->hourly_rate, 0) }}</span>
                                    <span class="text-xs text-gray-400 block -mt-0.5">{{ $fp->currency ?? 'AOA' }}/h</span>
                                </div>
                            @endif
                        </div>

                        {{-- Skill matches --}}
                        @if(!empty($item['skill_matches']))
                            <div>
                                <p class="text-xs text-gray-500 mb-1.5 font-medium">Skills compatíveis:</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach(array_slice($item['skill_matches'], 0, 5) as $matchSkill)
                                        <span class="text-xs bg-[#00baff]/15 text-[#0088bb] px-2.5 py-0.5 rounded-full font-semibold ring-1 ring-[#00baff]/20">
                                            {{ $matchSkill }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @elseif($fp && !empty($fp->skills))
                            <div class="flex flex-wrap gap-1.5">
                                @foreach(array_slice((array) $fp->skills, 0, 4) as $tag)
                                    <span class="text-xs bg-gray-100 text-gray-500 px-2.5 py-0.5 rounded-full">{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="mt-auto pt-3 border-t border-gray-50 flex items-center justify-between gap-2">
                            <a href="{{ route('freelancer.show', $fl->id) }}" class="btn-outline text-xs">Ver perfil</a>
                            @if($item['is_candidate'])
                                <span class="text-xs text-gray-400 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                    </svg>
                                    Já convidado
                                </span>
                            @else
                                <button wire:click="invite({{ $fl->id }})" class="btn-primary text-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"/>
                                    </svg>
                                    Convidar
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Load more --}}
        @if($hasMore)
            <div class="flex justify-center">
                <button wire:click="loadMore" class="btn-outline">
                    <div wire:loading wire:target="loadMore" class="w-4 h-4 border-2 border-[#00baff] border-t-transparent rounded-full animate-spin"></div>
                    <span wire:loading.remove wire:target="loadMore">Ver mais sugestões</span>
                </button>
            </div>
        @endif

        {{-- Browse all --}}
        <div class="text-center">
            <a href="{{ route('freelancers.index') }}" class="text-sm text-[#00baff] hover:underline">
                Ver todos os freelancers disponíveis →
            </a>
        </div>
    @endif
</div>
