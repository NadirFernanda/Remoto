<div class="max-w-6xl mx-auto space-y-6">

    @php
        $statusLabels = [
            'negotiating'        => 'Em Negociação',
            'published'          => 'Publicado',
            'accepted'           => 'Aceite',
            'in_progress'        => 'Em Andamento',
            'revision_requested' => 'Revisão Pedida',
            'delivered'          => 'Entregue',
            'completed'          => 'Concluído',
            'cancelled'          => 'Cancelado',
            'em_moderacao'       => 'Em Moderação',
        ];
        $statusColors = [
            'negotiating'        => 'bg-amber-100 text-amber-700 border-amber-200',
            'published'          => 'bg-blue-100 text-blue-700 border-blue-200',
            'accepted'           => 'bg-indigo-100 text-indigo-700 border-indigo-200',
            'in_progress'        => 'bg-yellow-100 text-yellow-700 border-yellow-200',
            'revision_requested' => 'bg-red-100 text-red-700 border-red-200',
            'delivered'          => 'bg-orange-100 text-orange-700 border-orange-200',
            'completed'          => 'bg-green-100 text-green-700 border-green-200',
            'cancelled'          => 'bg-gray-100 text-gray-500 border-gray-200',
            'em_moderacao'       => 'bg-blue-100 text-[#0055ff] border-blue-200',
        ];
        $statusDots = [
            'negotiating'        => 'bg-amber-400',
            'published'          => 'bg-blue-400',
            'accepted'           => 'bg-indigo-400',
            'in_progress'        => 'bg-yellow-400',
            'revision_requested' => 'bg-red-500',
            'delivered'          => 'bg-orange-400',
            'completed'          => 'bg-green-500',
            'cancelled'          => 'bg-gray-400',
            'em_moderacao'       => 'bg-[#0055ff]',
        ];
    @endphp

    {{-- ─── Gradient Header ──────────────────────────────────── --}}
    <div class="rounded-2xl p-6 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold">Os Meus Projectos</h2>
            <p class="text-sm text-white/75 mt-1">Acompanhe o estado e actue sobre os seus projectos activos.</p>
        </div>
    </div>

    {{-- ─── Ganhos e comissão ────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex flex-wrap gap-6 items-center">
        <div>
            <div class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Ganhos dos projectos</div>
            <div class="text-2xl font-bold text-green-600">Kz {{ number_format($totalGanhoProjetos, 2, ',', '.') }}</div>
            <div class="text-xs text-gray-400 mt-0.5">total ganho</div>
        </div>
        <div class="text-gray-200 hidden sm:block">|</div>
        <div>
            <div class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Comissão da plataforma</div>
            <div class="text-lg font-semibold text-gray-700">{{ $comissaoLabel }} <span class="text-xs text-gray-400 font-normal">por projecto</span></div>
        </div>
        <div class="text-gray-200 hidden sm:block">|</div>
        <div>
            <div class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Valor mínimo</div>
            <div class="text-lg font-semibold text-gray-700">Kz {{ number_format($valorMinimo, 2, ',', '.') }}</div>
        </div>
        <div class="flex items-center gap-3 ml-auto flex-wrap">
            <a href="{{ route('freelancer.financial') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold hover:opacity-90 text-white transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Sacar no Painel Financeiro
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    {{-- ─── Contadores de status ──────────────────────────────── --}}
    <div class="flex flex-wrap gap-2">
        @foreach(['accepted' => 'Aceite', 'negotiating' => 'Em Negociação', 'in_progress' => 'Em Andamento', 'revision_requested' => 'Revisão Pedida', 'delivered' => 'Entregue', 'completed' => 'Concluído'] as $key => $label)
            <button wire:click="$set('status', '{{ $key }}')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-white border transition {{ $status === $key ? 'border-[#0052cc] text-[#0052cc] shadow-sm' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
                <span class="w-2 h-2 rounded-full {{ $statusDots[$key] ?? 'bg-gray-400' }}"></span>
                {{ $label }}: <span class="font-bold">{{ $statusCounts[$key] ?? 0 }}</span>
            </button>
        @endforeach
        @if($status)
            <button wire:click="$set('status', '')" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-medium bg-gray-100 text-gray-500 hover:bg-gray-200 border border-gray-200 transition">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                Limpar filtro
            </button>
        @endif
    </div>

    {{-- ─── Filtros ────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1 max-w-sm">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
            <input type="text" wire:model.debounce.400ms="search" placeholder="Pesquisar por título..."
                class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0052cc]/30 bg-white">
        </div>
        <select wire:model="status"
            class="px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0052cc]/30 bg-white text-gray-700">
            <option value="">Todos os estados</option>
            <option value="negotiating">Em Negociação</option>
            <option value="accepted">Aceite</option>
            <option value="in_progress">Em Andamento</option>
            <option value="revision_requested">Revisão Pedida</option>
            <option value="delivered">Entregue</option>
            <option value="completed">Concluído</option>
            <option value="cancelled">Cancelado</option>
        </select>
    </div>

    {{-- ─── Cards de Projectos ─────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse($projects as $project)
            @php
                $sc    = $statusColors[$project->status] ?? 'bg-gray-100 text-gray-500 border-gray-200';
                $sl    = $statusLabels[$project->status] ?? $project->status;
                $dot   = $statusDots[$project->status]   ?? 'bg-gray-400';
                $isRevision = $project->status === 'revision_requested';
                $isDelivered = $project->status === 'delivered';
                $valor = $project->valor_liquido ?? ($project->valor * 0.9);
            @endphp
            <div class="bg-white rounded-2xl border {{ $isRevision ? 'border-red-300 shadow-red-100' : 'border-gray-100' }} shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all flex flex-col">

                {{-- Card Header --}}
                <div class="px-5 pt-5 pb-4 flex-1">
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <h3 class="font-bold text-gray-900 text-sm leading-snug line-clamp-2 flex-1">
                            {{ $project->titulo ?? 'Projecto #' . $project->id }}
                        </h3>
                        <span class="flex-shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $sc }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $dot }} {{ $isRevision ? 'animate-pulse' : '' }}"></span>
                            {{ $sl }}
                        </span>
                    </div>

                    {{-- Valor --}}
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <div class="text-[11px] text-gray-400 uppercase tracking-wide">Valor a receber</div>
                            <div class="text-xl font-extrabold text-emerald-600">Kz {{ number_format($valor, 0, ',', '.') }}</div>
                        </div>
                        <div class="text-right text-[11px] text-gray-400">
                            <div>Criado em</div>
                            <div class="font-semibold text-gray-600">{{ $project->created_at->format('d/m/Y') }}</div>
                        </div>
                    </div>

                    {{-- Alerta revisão --}}
                    @if($isRevision)
                        <div class="flex items-center gap-2 bg-red-50 border border-red-200 rounded-xl px-3 py-2 text-xs text-red-700 font-medium mb-3">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                            O cliente pediu revisão — submeta uma nova entrega
                        </div>
                    @endif
                </div>

                {{-- Card Footer: Acções --}}
                <div class="border-t border-gray-50 px-5 py-3 flex flex-wrap gap-2">
                    {{-- Chat --}}
                    <a href="{{ route('service.chat', ['service' => $project->id]) }}"
                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-sky-50 text-sky-700 hover:bg-sky-600 hover:text-white text-xs font-semibold transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 0 1-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        Chat
                    </a>

                    {{-- Entregar / Re-entregar --}}
                    @if(in_array($project->status, ['accepted', 'in_progress', 'revision_requested', 'delivered']))
                        @php
                            $btnClass = $isRevision
                                ? 'bg-red-600 text-white hover:bg-red-700'
                                : ($isDelivered ? 'bg-orange-50 text-orange-700 hover:bg-orange-600 hover:text-white' : 'bg-[#0052cc] text-white hover:bg-blue-700');
                            $btnLabel = ($isRevision || $isDelivered) ? 'Re-entregar' : 'Entregar';
                        @endphp
                        <a href="{{ route('freelancer.service.delivery', ['service' => $project->id]) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg {{ $btnClass }} text-xs font-semibold transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                            {{ $btnLabel }}
                            @if($isRevision)
                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                            @endif
                        </a>
                    @endif

                    {{-- Disputa --}}
                    @if(in_array($project->status, ['accepted', 'in_progress', 'revision_requested', 'delivered']))
                        <a href="{{ route('service.dispute', ['service' => $project->id]) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-gray-50 text-gray-500 hover:bg-red-600 hover:text-white text-xs font-semibold transition-colors border border-gray-100">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                            Disputa
                        </a>
                    @endif

                    {{-- Moderação --}}
                    @if($project->status === 'em_moderacao')
                        <a href="{{ route('service.dispute', ['service' => $project->id]) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-50 text-[#0055ff] hover:bg-[#0055ff] hover:text-white text-xs font-semibold transition-colors">
                            Em Moderação
                        </a>
                    @endif

                    {{-- Avaliar / Avaliado --}}
                    @if($project->status === 'completed')
                        @if(in_array($project->id, $reviewedIds))
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-green-50 text-green-600 text-xs font-semibold">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                Avaliado
                            </span>
                        @else
                            <a href="{{ route('service.review.leave', ['service' => $project->id]) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-yellow-50 text-yellow-700 hover:bg-yellow-500 hover:text-white text-xs font-semibold transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/></svg>
                                Avaliar
                            </a>
                        @endif
                    @endif
                </div>

            </div>
        @empty
            <div class="col-span-full flex flex-col items-center py-20 text-gray-400">
                <svg class="w-14 h-14 opacity-25 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                <p class="text-base font-semibold">Nenhum projecto encontrado</p>
                <p class="text-sm mt-1">Candidate-se a projectos disponíveis para começar</p>
                <a href="{{ route('freelancer.available-projects') }}" class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#0052cc] text-white text-sm font-bold hover:bg-blue-700 transition">
                    Ver Projectos Disponíveis
                </a>
            </div>
        @endforelse
    </div>

    {{-- Paginação --}}
    @if($projects->hasPages())
        <div class="py-2">{{ $projects->links() }}</div>
    @endif

</div>
