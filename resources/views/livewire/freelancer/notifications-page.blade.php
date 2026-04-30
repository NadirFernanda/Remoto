@php
    $totalUnread = $notifications->getCollection()->where('read', false)->count();
    $total       = $notifications->total();
    $recentIds   = $recent->pluck('id')->all();
@endphp

<div class="max-w-4xl mx-auto space-y-6">

    {{-- ─── Gradient Header ──────────────────────────────────── --}}
    <div class="bg-gradient-to-r from-[#00baff] to-[#0095cc] rounded-2xl p-6 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold">Notificações</h2>
            <p class="text-sm text-white/75 mt-1">Actualizações sobre os seus projectos e actividade</p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <div class="bg-white/10 border border-white/20 rounded-xl px-5 py-2.5 text-center">
                <div class="text-xs text-white/60 font-medium">Total</div>
                <div class="text-2xl font-extrabold">{{ $total }}</div>
            </div>
            @if($totalUnread > 0)
                <div class="bg-red-500/20 border border-red-400/40 rounded-xl px-5 py-2.5 text-center">
                    <div class="flex items-center gap-1.5 justify-center mb-0.5">
                        <span class="w-2 h-2 rounded-full bg-red-400 animate-pulse"></span>
                        <span class="text-xs text-white/70 font-medium">Não lidas</span>
                    </div>
                    <div class="text-2xl font-extrabold">{{ $totalUnread }}</div>
                </div>
            @else
                <div class="bg-emerald-500/20 border border-emerald-400/40 rounded-xl px-5 py-2.5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    <span class="text-sm font-semibold text-white/80">Tudo lido</span>
                </div>
            @endif
        </div>
    </div>

    {{-- ─── Mais Recentes ────────────────────────────────────── --}}
    @if($recent->isNotEmpty())
    <div class="space-y-3">
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest px-1">Mais recentes</h3>
        @foreach($recent as $notification)
            @php $isUnread = !$notification->read; @endphp
            <a href="{{ $notification->getUrl() }}"
               class="flex items-start gap-4 p-4 rounded-2xl border transition-all group
                   {{ $isUnread
                       ? 'bg-blue-50/60 border-[#0052cc]/25 shadow-sm hover:shadow-md'
                       : 'bg-white border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5' }}">
                <div class="flex-shrink-0 mt-1">
                    <span class="w-2.5 h-2.5 block rounded-full {{ $isUnread ? 'bg-[#0052cc] animate-pulse' : 'bg-gray-200' }}"></span>
                </div>
                <div class="flex-1 min-w-0">
                    @if($notification->title)
                        <p class="font-bold text-gray-900 text-sm leading-snug {{ $isUnread ? 'text-[#0052cc]' : '' }}">{{ $notification->title }}</p>
                    @endif
                    <p class="text-sm text-gray-600 mt-0.5 leading-relaxed">{{ $notification->message }}</p>
                    <p class="text-xs text-gray-400 mt-1.5">{{ $notification->created_at->diffForHumans() }} &middot; {{ $notification->created_at->format('d/m/Y H:i') }}</p>
                </div>
                @if($notification->getUrl() !== '#')
                    <svg class="w-4 h-4 text-gray-300 flex-shrink-0 mt-1 group-hover:text-[#0052cc] transition-colors" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
                    </svg>
                @endif
            </a>
        @endforeach
    </div>
    @endif

    {{-- ─── Todas as Notificações ────────────────────────────── --}}
    <div class="space-y-3">
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest px-1">Todas as notificações</h3>

        @php $shown = 0; @endphp
        @forelse($notifications as $notification)
            @if(!in_array($notification->id, $recentIds))
                @php
                    $shown++;
                    $isUnread = !$notification->read;
                @endphp
                <a href="{{ $notification->getUrl() }}"
                   class="flex items-start gap-4 p-4 rounded-2xl border transition-all group
                       {{ $isUnread
                           ? 'bg-blue-50/40 border-[#0052cc]/20 shadow-sm hover:shadow-md'
                           : 'bg-white border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5' }}">
                    <div class="flex-shrink-0 mt-1">
                        <span class="w-2.5 h-2.5 block rounded-full {{ $isUnread ? 'bg-[#0052cc]' : 'bg-gray-200' }}"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        @if($notification->title)
                            <p class="font-bold text-gray-900 text-sm leading-snug">{{ $notification->title }}</p>
                        @endif
                        <p class="text-sm text-gray-600 mt-0.5 leading-relaxed">{{ $notification->message }}</p>
                        <p class="text-xs text-gray-400 mt-1.5">{{ $notification->created_at->diffForHumans() }} &middot; {{ $notification->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($notification->getUrl() !== '#')
                        <svg class="w-4 h-4 text-gray-300 flex-shrink-0 mt-1 group-hover:text-[#0052cc] transition-colors" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
                        </svg>
                    @endif
                </a>
            @endif
        @empty
            <div class="flex flex-col items-center py-20 text-gray-400 bg-white rounded-2xl border border-dashed border-gray-200">
                <svg class="w-14 h-14 opacity-20 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
                <p class="text-base font-semibold">Nenhuma notificação encontrada</p>
                <p class="text-sm mt-1">As actualizações dos seus projectos aparecerão aqui</p>
            </div>
        @endforelse
    </div>

    {{-- Paginação --}}
    @if($notifications->hasPages())
        <div class="py-2">{{ $notifications->links() }}</div>
    @endif

</div>
