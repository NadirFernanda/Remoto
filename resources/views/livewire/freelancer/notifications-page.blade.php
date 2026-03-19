<div>
    <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Mais Recentes</h2>
            <div class="space-y-3">
                @forelse($recent as $notification)
                    <a href="{{ $notification->getUrl() }}"
                       class="block bg-gray-50 border border-gray-100 rounded-xl px-5 py-4 flex items-center gap-4 shadow-sm transition hover:bg-cyan-50 focus:bg-cyan-50 outline-none"
                       tabindex="0">
                        <span class="mt-1 w-2.5 h-2.5 flex-shrink-0 rounded-full {{ $notification->read ? 'bg-gray-300' : 'bg-[#00baff]' }}"></span>
                        <div class="flex-1 min-w-0">
                            @if($notification->title)
                                <div class="text-gray-800 font-semibold truncate">{{ $notification->title }}</div>
                            @endif
                            <div class="text-gray-700 leading-snug mt-0.5">{{ $notification->message }}</div>
                            <div class="text-xs text-gray-400 mt-1">{{ $notification->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        @if($notification->getUrl() !== '#')
                            <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        @endif
                    </a>
                @empty
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-5 py-8 text-center text-gray-400">Nenhuma notificação recente.</div>
                @endforelse
            </div>
        </div>

        <div>
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Todas as Notificações</h2>
            <div class="space-y-3">
                @php $recentIds = $recent->pluck('id')->all(); @endphp
                @forelse($notifications as $notification)
                    @if(!in_array($notification->id, $recentIds))
                        <a href="{{ $notification->getUrl() }}"
                           class="block bg-white border border-gray-100 rounded-xl px-5 py-4 flex items-center gap-4 shadow-sm transition hover:bg-cyan-50 focus:bg-cyan-50 outline-none"
                           tabindex="0">
                            <span class="mt-1 w-2.5 h-2.5 flex-shrink-0 rounded-full {{ $notification->read ? 'bg-gray-300' : 'bg-[#00baff]' }}"></span>
                            <div class="flex-1 min-w-0">
                                @if($notification->title)
                                    <div class="text-gray-700 font-semibold truncate">{{ $notification->title }}</div>
                                @endif
                                <div class="text-gray-600 leading-snug mt-0.5">{{ $notification->message }}</div>
                                <div class="text-xs text-gray-400 mt-1">{{ $notification->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            @if($notification->getUrl() !== '#')
                                <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            @endif
                        </a>
                    @endif
                @empty
                    <div class="bg-white border border-gray-100 rounded-xl px-5 py-8 text-center text-gray-400">Nenhuma notificação encontrada.</div>
                @endforelse
            </div>
        </div>
</div>
