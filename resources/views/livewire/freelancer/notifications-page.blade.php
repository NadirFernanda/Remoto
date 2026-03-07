<div class="min-h-screen bg-white">
    <div class="max-w-3xl mx-auto px-4 py-10">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Notificações</h1>
            <a href="{{ route('freelancer.dashboard') }}" class="btn-eq btn-primary">Voltar ao Dashboard</a>
        </div>

        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Mais Recentes</h2>
            <div class="space-y-3">
                @forelse($recent as $notification)
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-5 py-4 flex items-center gap-4 shadow-sm">
                        <div class="flex-1 min-w-0">
                            <div class="text-gray-800 font-medium truncate">
                                @if($notification->service_id)
                                    <a href="{{ route('freelancer.service.review', $notification->service_id) }}" class="text-[#00baff] hover:underline">
                                        {{ $notification->message }}
                                    </a>
                                @else
                                    {{ $notification->message }}
                                @endif
                            </div>
                            <div class="text-xs text-gray-400 mt-1">{{ $notification->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        @if($notification->service_id)
                            <div class="flex gap-2">
                                <a href="{{ route('freelancer.service.review', $notification->service_id) }}" class="btn-eq small" title="Ver projeto">Ver</a>
                                <a href="{{ route('freelancer.service.review', $notification->service_id) }}" class="btn-eq btn-outline small" title="Enviar proposta">Enviar proposta</a>
                                @php $unread = auth()->check() ? \App\Models\ChatRead::unreadCount($notification->service_id, auth()->id()) : 0; @endphp
                                <a href="{{ route('service.chat', $notification->service_id) }}" class="btn-eq btn-outline small relative" title="Ir para o chat">Chat
                                    @if($unread > 0)
                                        <span class="absolute -top-1 -right-2 inline-flex items-center justify-center w-5 h-5 text-xs bg-red-500 text-white rounded-full">{{ $unread > 9 ? '9+' : $unread }}</span>
                                    @endif
                                </a>
                            </div>
                        @endif
                    </div>
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
                        <div class="bg-white border border-gray-100 rounded-xl px-5 py-4 flex items-center gap-4 shadow-sm">
                            <div class="flex-1 min-w-0">
                                <div class="text-gray-700 truncate">
                                    @if($notification->service_id)
                                        <a href="{{ route('freelancer.service.review', $notification->service_id) }}" class="text-[#00baff] hover:underline">
                                            {{ $notification->message }}
                                        </a>
                                    @else
                                        {{ $notification->message }}
                                    @endif
                                </div>
                                <div class="text-xs text-gray-400 mt-1">{{ $notification->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            @if($notification->service_id)
                                <div class="flex gap-2">
                                    <a href="{{ route('freelancer.service.review', $notification->service_id) }}" class="btn-eq small" title="Ver projeto">Ver</a>
                                    <a href="{{ route('freelancer.service.review', $notification->service_id) }}" class="btn-eq btn-outline small" title="Enviar proposta">Enviar proposta</a>
                                    @php $unread = auth()->check() ? \App\Models\ChatRead::unreadCount($notification->service_id, auth()->id()) : 0; @endphp
                                    <a href="{{ route('service.chat', $notification->service_id) }}" class="btn-eq btn-outline small relative" title="Ir para o chat">Chat
                                        @if($unread > 0)
                                            <span class="absolute -top-1 -right-2 inline-flex items-center justify-center w-5 h-5 text-xs bg-red-500 text-white rounded-full">{{ $unread > 9 ? '9+' : $unread }}</span>
                                        @endif
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                @empty
                    <div class="bg-white border border-gray-100 rounded-xl px-5 py-8 text-center text-gray-400">Nenhuma notificação encontrada.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
