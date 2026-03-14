<div class="pub-container--md" style="padding: 2rem 0 3rem;">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div class="mb-4 rounded-xl bg-blue-50 border border-blue-200 px-4 py-3 text-sm text-blue-700">
            {{ session('info') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 style="font-size:1.5rem;font-weight:900;color:#0f172a;margin:0;">Propostas recebidas</h1>
            <p class="text-sm text-gray-500 mt-1">Convites directos de clientes para trabalhar em projectos</p>
        </div>
        @if($counts['pending'] > 0)
            <span class="inline-flex items-center gap-1.5 bg-blue-100 text-blue-700 text-sm font-semibold px-3 py-1.5 rounded-full">
                <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                {{ $counts['pending'] }} pendente{{ $counts['pending'] > 1 ? 's' : '' }}
            </span>
        @endif
    </div>

    {{-- Tabs --}}
    <div class="flex flex-wrap gap-1 bg-gray-100 rounded-xl p-1 mb-6">
        <button wire:click="setTab('pending')"
            class="px-4 py-2 text-sm font-semibold rounded-lg transition-all {{ $tab === 'pending' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
            Pendentes
            @if($counts['pending'] > 0)
                <span class="ml-1.5 bg-blue-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $counts['pending'] }}</span>
            @endif
        </button>
        <button wire:click="setTab('accepted')"
            class="px-4 py-2 text-sm font-semibold rounded-lg transition-all {{ $tab === 'accepted' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
            Aceites
            @if($counts['accepted'] > 0)
                <span class="ml-1.5 bg-green-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $counts['accepted'] }}</span>
            @endif
        </button>
        <button wire:click="setTab('rejected')"
            class="px-4 py-2 text-sm font-semibold rounded-lg transition-all {{ $tab === 'rejected' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
            Recusadas
        </button>
    </div>

    {{-- Lista de propostas --}}
    @forelse($proposals as $proposal)
        <div class="pub-card mb-4" style="padding:1.5rem;">
            <div class="flex items-start gap-4">

                {{-- Avatar do cliente --}}
                <div style="width:48px;height:48px;border-radius:12px;overflow:hidden;flex-shrink:0;border:2px solid #e8edf3;">
                    <img src="{{ $proposal->sender->avatarUrl() }}" alt="{{ $proposal->sender->name }}" style="width:100%;height:100%;object-fit:cover;">
                </div>

                {{-- Conteúdo --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-3 flex-wrap">
                        <div>
                            <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin:0 0 .2rem;">
                                {{ $proposal->title ?? 'Sem título' }}
                            </h3>
                            <p class="text-sm text-gray-500">
                                De <span class="font-semibold text-gray-700">{{ $proposal->sender->name }}</span>
                                · {{ $proposal->created_at->diffForHumans() }}
                            </p>
                        </div>
                        {{-- Badge de status --}}
                        @php
                            $badgeClass = match($proposal->status) {
                                'pending'  => 'bg-amber-50 text-amber-700 border border-amber-200',
                                'accepted' => 'bg-green-50 text-green-700 border border-green-200',
                                'rejected' => 'bg-red-50 text-red-600 border border-red-200',
                                default    => 'bg-gray-100 text-gray-600',
                            };
                            $badgeLabel = match($proposal->status) {
                                'pending'  => 'Pendente',
                                'accepted' => 'Aceite',
                                'rejected' => 'Recusada',
                                default    => $proposal->status,
                            };
                        @endphp
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $badgeClass }}">{{ $badgeLabel }}</span>
                    </div>

                    {{-- Mensagem --}}
                    @if($proposal->message)
                        <div class="mt-3 text-sm text-gray-600 bg-gray-50 rounded-xl p-3 leading-relaxed">
                            {!! nl2br(e(Str::limit($proposal->message, 300))) !!}
                        </div>
                    @endif

                    {{-- Valor + acções --}}
                    <div class="mt-4 flex items-center justify-between flex-wrap gap-3">
                        <div class="flex items-center gap-4 text-sm">
                            @if($proposal->value)
                                <div class="flex items-center gap-1.5 text-gray-700">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                    </svg>
                                    <span>Orçamento: <strong class="text-blue-600">{{ number_format($proposal->value, 2) }} AOA</strong></span>
                                </div>
                            @else
                                <span class="text-gray-400 text-sm">Sem orçamento definido</span>
                            @endif

                            {{-- Anexos --}}
                            @if($proposal->attachments && count($proposal->attachments) > 0)
                                <span class="flex items-center gap-1 text-gray-500">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    {{ count($proposal->attachments) }} anexo(s)
                                </span>
                            @endif
                        </div>

                        @if($proposal->status === 'pending')
                            <div class="flex gap-2 flex-wrap">
                                <button wire:click="openChat({{ $proposal->id }})"
                                    class="btn-outline text-sm px-4 py-1.5 flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/>
                                    </svg>
                                    Negociar
                                </button>
                                <button wire:click="decline({{ $proposal->id }})"
                                    wire:confirm="Tem a certeza que quer recusar esta proposta?"
                                    class="btn-outline text-sm px-4 py-1.5">
                                    Recusar
                                </button>
                                <button wire:click="accept({{ $proposal->id }})"
                                    class="btn-primary text-sm px-4 py-1.5">
                                    Aceitar
                                </button>
                            </div>
                        @elseif($proposal->status === 'accepted')
                            <button wire:click="openChat({{ $proposal->id }})"
                                class="btn-primary text-sm px-4 py-1.5 flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/>
                                </svg>
                                Ir ao chat
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="pub-card text-center py-16" style="color:#64748b;">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <p class="font-semibold text-gray-500">Nenhuma proposta {{ $tab === 'pending' ? 'pendente' : ($tab === 'accepted' ? 'aceite' : 'recusada') }}</p>
            @if($tab === 'pending')
                <p class="text-sm text-gray-400 mt-1">Quando um cliente enviar uma proposta, ela aparecerá aqui.</p>
            @endif
        </div>
    @endforelse

    {{-- Paginação --}}
    @if($proposals->hasPages())
        <div class="mt-6">
            {{ $proposals->links() }}
        </div>
    @endif

</div>
