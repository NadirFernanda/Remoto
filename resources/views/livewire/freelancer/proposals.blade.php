<div class="max-w-5xl mx-auto space-y-6">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div class="rounded-xl bg-blue-50 border border-blue-200 px-4 py-3 text-sm text-blue-700">{{ session('info') }}</div>
    @endif

    {{-- ─── Gradient Header ──────────────────────────────────── --}}
    <div class="bg-gradient-to-r from-[#0052cc] to-[#0a1228] rounded-2xl p-6 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold">Propostas Recebidas</h2>
            <p class="text-sm text-white/75 mt-1">Convites directos de clientes para trabalhar em projectos</p>
        </div>
        <div class="flex gap-3 flex-wrap">
            @foreach(['pending' => ['label' => 'Pendentes', 'dot' => 'bg-amber-400'], 'accepted' => ['label' => 'Aceites', 'dot' => 'bg-emerald-400'], 'rejected' => ['label' => 'Recusadas', 'dot' => 'bg-red-400']] as $k => $meta)
                <div class="text-center bg-white/10 border border-white/20 rounded-xl px-4 py-2">
                    <div class="flex items-center gap-1.5 justify-center">
                        <span class="w-2 h-2 rounded-full {{ $meta['dot'] }} {{ $k === 'pending' && $counts['pending'] > 0 ? 'animate-pulse' : '' }}"></span>
                        <span class="text-xs text-white/70 font-medium">{{ $meta['label'] }}</span>
                    </div>
                    <div class="text-xl font-extrabold">{{ $counts[$k] ?? 0 }}</div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ─── Tabs ─────────────────────────────────────────────── --}}
    <div class="flex flex-wrap gap-2">
        @foreach(['pending' => 'Pendentes', 'accepted' => 'Aceites', 'rejected' => 'Recusadas'] as $key => $label)
            <button wire:click="setTab('{{ $key }}')"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold border transition
                    {{ $tab === $key ? 'bg-[#0052cc] text-white border-[#0052cc] shadow-sm' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300' }}">
                {{ $label }}
                @if(($counts[$key] ?? 0) > 0)
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold
                        {{ $tab === $key ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600' }}">{{ $counts[$key] }}</span>
                @endif
            </button>
        @endforeach
    </div>

    {{-- ─── Cards de Propostas ──────────────────────────────── --}}
    <div class="space-y-4">
        @forelse($proposals as $proposal)
            @php
                $badgeMap = [
                    'pending'  => ['class' => 'bg-amber-50 text-amber-700 border-amber-200',  'dot' => 'bg-amber-400', 'label' => 'Pendente'],
                    'accepted' => ['class' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'dot' => 'bg-emerald-400', 'label' => 'Aceite'],
                    'rejected' => ['class' => 'bg-red-50 text-red-600 border-red-200',        'dot' => 'bg-red-400',   'label' => 'Recusada'],
                ];
                $badge = $badgeMap[$proposal->status] ?? ['class' => 'bg-gray-100 text-gray-600 border-gray-200', 'dot' => 'bg-gray-400', 'label' => $proposal->status];
            @endphp
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
                <div class="p-5 flex items-start gap-4">

                    {{-- Avatar --}}
                    <div class="w-12 h-12 rounded-xl overflow-hidden flex-shrink-0 border border-gray-100 shadow-sm">
                        <img src="{{ $proposal->sender->avatarUrl() }}" alt="{{ $proposal->sender->name }}" class="w-full h-full object-cover">
                    </div>

                    {{-- Corpo --}}
                    <div class="flex-1 min-w-0">

                        {{-- Título + badge --}}
                        <div class="flex items-start justify-between gap-3 flex-wrap mb-1">
                            <h3 class="font-bold text-gray-900 text-sm leading-snug">
                                {{ $proposal->title ?? 'Sem título' }}
                            </h3>
                            <span class="flex-shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $badge['class'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }} {{ $proposal->status === 'pending' ? 'animate-pulse' : '' }}"></span>
                                {{ $badge['label'] }}
                            </span>
                        </div>

                        {{-- De + data --}}
                        <p class="text-xs text-gray-400 mb-3">
                            De <span class="font-semibold text-gray-600">{{ $proposal->sender->name }}</span>
                            &middot; {{ $proposal->created_at->diffForHumans() }}
                        </p>

                        {{-- Mensagem --}}
                        @if($proposal->message)
                            <div class="text-sm text-gray-600 bg-gray-50 rounded-xl px-4 py-3 leading-relaxed mb-4 border border-gray-100">
                                {!! nl2br(e(Str::limit($proposal->message, 300))) !!}
                            </div>
                        @endif

                        {{-- Footer: valor + acções --}}
                        <div class="flex items-center justify-between flex-wrap gap-3">
                            <div class="flex items-center gap-3 text-sm">
                                @if($proposal->value)
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33"/></svg>
                                        <span class="font-extrabold text-emerald-600">Kz {{ number_format($proposal->value, 0, ',', '.') }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">Sem orçamento definido</span>
                                @endif

                                @if($proposal->attachments && count($proposal->attachments) > 0)
                                    <span class="flex items-center gap-1 text-gray-400 text-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 002.112 2.13"/></svg>
                                        {{ count($proposal->attachments) }} anexo(s)
                                    </span>
                                @endif
                            </div>

                            {{-- Botões de acção --}}
                            <div class="flex gap-2 flex-wrap">
                                @if($proposal->status === 'pending')
                                    <button wire:click="openChat({{ $proposal->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-cyan-50 text-cyan-700 hover:bg-cyan-600 hover:text-white text-xs font-semibold transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 0 1-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                        Negociar
                                    </button>
                                    <button wire:click="decline({{ $proposal->id }})"
                                        wire:confirm="Tem a certeza que quer recusar esta proposta?"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-50 text-gray-500 hover:bg-red-600 hover:text-white text-xs font-semibold transition-colors border border-gray-100">
                                        Recusar
                                    </button>
                                    <button wire:click="accept({{ $proposal->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#0052cc] text-white hover:bg-blue-700 text-xs font-bold transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                        Aceitar
                                    </button>
                                @elseif($proposal->status === 'accepted')
                                    <button wire:click="openChat({{ $proposal->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#0052cc] text-white hover:bg-blue-700 text-xs font-bold transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 0 1-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                        Ir ao Chat
                                    </button>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-50 text-red-400 text-xs font-semibold border border-red-100">
                                        Recusada
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center py-20 text-gray-400 bg-white rounded-2xl border border-gray-100">
                <svg class="w-14 h-14 opacity-20 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="text-base font-semibold">Nenhuma proposta {{ $tab === 'pending' ? 'pendente' : ($tab === 'accepted' ? 'aceite' : 'recusada') }}</p>
                @if($tab === 'pending')
                    <p class="text-sm mt-1">Quando um cliente enviar uma proposta, ela aparecerá aqui.</p>
                @endif
            </div>
        @endforelse
    </div>

    {{-- Paginação --}}
    @if($proposals->hasPages())
        <div class="py-2">{{ $proposals->links() }}</div>
    @endif

</div>
