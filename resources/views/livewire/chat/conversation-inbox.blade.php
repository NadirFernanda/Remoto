<div class="max-w-4xl mx-auto space-y-6" x-data x-init="setInterval(() => $wire.$refresh(), 30000)">

    {{-- Flash --}}
    @if(session('success'))
        <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ─── Gradient Header ──────────────────────────────────── --}}
    <div class="bg-gradient-to-r from-[#00baff] to-[#0095cc] rounded-2xl p-6 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold">Mensagens</h2>
            <p class="text-sm text-white/75 mt-1">Todas as conversas dos seus projectos</p>
        </div>
        <div class="flex items-center gap-4">
            @if($totalUnread > 0)
                <div class="flex items-center gap-2 bg-red-500/20 border border-red-400/40 rounded-xl px-4 py-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-400 animate-pulse"></span>
                    <span class="text-sm font-bold text-white">{{ $totalUnread }} não {{ $totalUnread === 1 ? 'lida' : 'lidas' }}</span>
                </div>
            @else
                <div class="flex items-center gap-2 bg-white/10 border border-white/20 rounded-xl px-4 py-2.5">
                    <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    <span class="text-sm font-semibold text-white/80">Tudo lido</span>
                </div>
            @endif
            <div class="text-center bg-white/10 border border-white/20 rounded-xl px-4 py-2">
                <div class="text-xs text-white/60 font-medium">Conversas</div>
                <div class="text-xl font-extrabold">{{ $services->count() }}</div>
            </div>
        </div>
    </div>

    {{-- ─── Pesquisa ────────────────────────────────────────── --}}
    <div class="relative">
        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
        </svg>
        <input type="text"
               wire:model.debounce.350ms="search"
               placeholder="Pesquisar por projecto..."
               class="w-full pl-10 pr-4 py-3 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 bg-white shadow-sm">
    </div>

    {{-- ─── Lista de conversas ──────────────────────────────── --}}
    <div class="space-y-3">
        @forelse($services as $service)
            @php
                $user        = auth()->user();
                $isCliente   = $user->id === $service->cliente_id;
                $other       = $isCliente ? $service->freelancer : $service->cliente;
                $lastMsg     = $service->messages->first();
                $unread      = $unreadCounts[$service->id] ?? 0;
                $statusLabel = match($service->status) {
                    'published'          => 'Publicado',
                    'negotiating'        => 'Negociação',
                    'accepted'           => 'Aceite',
                    'in_progress'        => 'Em Andamento',
                    'revision_requested' => 'Revisão Pedida',
                    'delivered'          => 'Entregue',
                    'completed'          => 'Concluído',
                    'cancelled'          => 'Cancelado',
                    'em_moderacao'       => 'Em Moderação',
                    default              => $service->status,
                };
                $statusColor = match($service->status) {
                    'published'          => 'bg-blue-50 text-blue-700 border-blue-200',
                    'negotiating'        => 'bg-amber-50 text-amber-700 border-amber-200',
                    'accepted'           => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                    'in_progress'        => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                    'revision_requested' => 'bg-red-50 text-red-600 border-red-200',
                    'delivered'          => 'bg-orange-50 text-orange-700 border-orange-200',
                    'completed'          => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'cancelled'          => 'bg-gray-100 text-gray-500 border-gray-200',
                    'em_moderacao'       => 'bg-purple-50 text-purple-700 border-purple-200',
                    default              => 'bg-gray-100 text-gray-600 border-gray-200',
                };
            @endphp

            <a href="{{ route('service.chat', $service->id) }}"
               class="flex items-center gap-4 p-4 bg-white rounded-2xl border transition-all group
                   {{ $unread > 0
                       ? 'border-[#00baff]/30 shadow-sm shadow-blue-100 hover:shadow-md hover:shadow-blue-100'
                       : 'border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5' }}">

                {{-- Avatar com badge não lidas --}}
                <div class="relative flex-shrink-0">
                    <img src="{{ $other?->avatarUrl() ?? asset('img/default-avatar.svg') }}"
                         alt="{{ $other?->name ?? 'Utilizador' }}"
                         class="w-13 h-13 w-[52px] h-[52px] rounded-xl object-cover ring-2
                             {{ $unread > 0 ? 'ring-[#00baff]/30' : 'ring-gray-100' }}">
                    @if($unread > 0)
                        <span class="absolute -top-1.5 -right-1.5 min-w-[20px] h-5 px-1 bg-red-500 text-white text-[10px] rounded-full flex items-center justify-center font-bold leading-none">
                            {{ $unread > 9 ? '9+' : $unread }}
                        </span>
                    @endif
                </div>

                {{-- Conteúdo --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2 mb-0.5">
                        <p class="text-sm font-bold text-gray-900 truncate group-hover:text-[#00baff] transition-colors">
                            {{ $other?->name ?? 'Utilizador removido' }}
                        </p>
                        <span class="text-[11px] text-gray-400 flex-shrink-0 mt-0.5">
                            {{ $lastMsg?->created_at->diffForHumans() }}
                        </span>
                    </div>

                    <p class="text-xs font-semibold text-gray-500 truncate mb-1.5">
                        {{ $service->titulo }}
                    </p>

                    <div class="flex items-center justify-between gap-2">
                        <p class="text-xs text-gray-400 truncate flex-1 {{ $unread > 0 ? 'font-semibold text-gray-600' : '' }}">
                            @if($lastMsg)
                                @if($lastMsg->user_id === $user->id)
                                    <span class="text-gray-400 font-normal">Você: </span>
                                @endif
                                @if($lastMsg->anexo)
                                    <svg class="inline w-3.5 h-3.5 -mt-0.5 mr-0.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 002.112 2.13"/></svg>
                                    {{ $lastMsg->nome_original_anexo ?? 'Ficheiro' }}
                                @else
                                    {{ Str::limit($lastMsg->conteudo, 70) }}
                                @endif
                            @else
                                <span class="italic">Sem mensagens ainda</span>
                            @endif
                        </p>
                        <span class="flex-shrink-0 text-[10px] px-2 py-0.5 rounded-full font-semibold border {{ $statusColor }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>

                {{-- Seta --}}
                <svg class="w-4 h-4 text-gray-300 flex-shrink-0 group-hover:text-[#00baff] transition-colors" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
                </svg>
            </a>

        @empty
            <div class="flex flex-col items-center py-20 text-gray-400 bg-white rounded-2xl border border-gray-100">
                <svg class="w-14 h-14 opacity-20 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 0 1-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <p class="text-base font-semibold">Nenhuma conversa encontrada</p>
                <p class="text-sm mt-1">As conversas dos seus projectos aparecerão aqui</p>
            </div>
        @endforelse
    </div>
</div>
