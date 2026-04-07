<div class="space-y-5" wire:poll.15s>

    {{-- ─── Header ──────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Mensagens</h1>
            <p class="text-sm text-gray-500">Todas as conversas dos seus projectos.</p>
        </div>
        @if($totalUnread > 0)
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-red-50 text-red-600 border border-red-200 self-start sm:self-auto">
                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                {{ $totalUnread }} não {{ $totalUnread === 1 ? 'lida' : 'lidas' }}
            </span>
        @endif
    </div>

    {{-- ─── Pesquisa ────────────────────────────────────── --}}
    <div class="relative max-w-xs">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
        </svg>
        <input type="text"
               wire:model.debounce.350ms="search"
               placeholder="Pesquisar projecto..."
               class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#00baff]/40 bg-white">
    </div>

    {{-- ─── Flash ───────────────────────────────────────── --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-[10px] px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    {{-- ─── Lista de conversas ──────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden divide-y divide-gray-100">

        @forelse($services as $service)
            @php
                $user        = auth()->user();
                $isCliente   = $user->id === $service->cliente_id;
                $other       = $isCliente ? $service->freelancer : $service->cliente;
                $lastMsg     = $service->messages->first();
                $unread      = $unreadCounts[$service->id] ?? 0;
                $statusLabel = match($service->status) {
                    'published'    => 'Publicado',
                    'negotiating'  => 'Negociação',
                    'accepted'     => 'Aceite',
                    'in_progress'  => 'Em Andamento',
                    'delivered'    => 'Entregue',
                    'completed'    => 'Concluído',
                    'cancelled'    => 'Cancelado',
                    'em_moderacao' => 'Em Moderação',
                    default        => $service->status,
                };
                $statusColor = match($service->status) {
                    'published'    => 'bg-blue-100 text-blue-700',
                    'negotiating'  => 'bg-amber-100 text-amber-700',
                    'accepted'     => 'bg-indigo-100 text-indigo-700',
                    'in_progress'  => 'bg-yellow-100 text-yellow-700',
                    'delivered'    => 'bg-orange-100 text-orange-700',
                    'completed'    => 'bg-green-100 text-green-700',
                    'cancelled'    => 'bg-red-100 text-red-600',
                    'em_moderacao' => 'bg-purple-100 text-purple-700',
                    default        => 'bg-gray-100 text-gray-600',
                };
            @endphp

            <a href="{{ route('service.chat', $service->id) }}"
               class="flex items-center gap-4 px-5 py-4 hover:bg-slate-50/80 transition group {{ $unread > 0 ? 'bg-blue-50/30' : '' }}">

                {{-- Avatar com badge de estado online --}}
                <div class="relative flex-shrink-0">
                    <img
                        src="{{ $other?->avatarUrl() ?? asset('img/default-avatar.svg') }}"
                        alt="{{ $other?->name ?? 'Utilizador' }}"
                        class="w-12 h-12 rounded-full object-cover ring-2 {{ $unread > 0 ? 'ring-[#00baff]/40' : 'ring-gray-200' }}">
                    @if($unread > 0)
                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold leading-none">{{ $unread > 9 ? '9+' : $unread }}</span>
                    @endif
                </div>

                {{-- Conteúdo principal --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2 mb-0.5">
                        <p class="text-sm font-semibold text-gray-900 truncate group-hover:text-[#00baff] transition-colors">
                            {{ $other?->name ?? 'Utilizador removido' }}
                        </p>
                        <span class="text-xs text-gray-400 flex-shrink-0">
                            {{ $lastMsg?->created_at->diffForHumans() }}
                        </span>
                    </div>

                    <p class="text-xs text-gray-500 truncate mb-1.5">
                        <span class="font-medium text-gray-700">{{ $service->titulo }}</span>
                    </p>

                    <div class="flex items-center justify-between gap-2">
                        <p class="text-xs text-gray-400 truncate flex-1">
                            @if($lastMsg)
                                @if($lastMsg->user_id === $user->id)
                                    <span class="text-gray-400">Você: </span>
                                @endif
                                @if($lastMsg->anexo)
                                    <svg class="inline w-3.5 h-3.5 -mt-0.5 mr-0.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    {{ $lastMsg->nome_original_anexo ?? 'Ficheiro' }}
                                @else
                                    {{ Str::limit($lastMsg->conteudo, 60) }}
                                @endif
                            @else
                                Sem mensagens ainda.
                            @endif
                        </p>
                        <span class="flex-shrink-0 text-xs px-2 py-0.5 rounded-full font-medium {{ $statusColor }}">
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
            <div class="text-center py-16">
                <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 0 1-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <p class="text-sm text-gray-500 font-medium">Nenhuma conversa encontrada.</p>
                <p class="text-xs text-gray-400 mt-1">As conversas dos seus projectos aparecerão aqui.</p>
            </div>
        @endforelse
    </div>
</div>
