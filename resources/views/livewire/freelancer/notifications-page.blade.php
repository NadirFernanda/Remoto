<div>
    <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Mais Recentes</h2>
            <div class="space-y-3">
                @forelse($recent as $notification)
                    <a href="@php
                        if ($notification->service_id) {
                            echo in_array($notification->type, ['service_chosen', 'delivery_approved', 'revision_requested'])
                                ? route('freelancer.service.delivery', $notification->service_id)
                                : route('freelancer.service.review', $notification->service_id);
                        } else { echo '#'; }
                    @endphp"
                       class="block bg-gray-50 border border-gray-100 rounded-xl px-5 py-4 flex items-center gap-4 shadow-sm transition hover:bg-cyan-50 focus:bg-cyan-50 outline-none"
                       tabindex="0">
                        <div class="flex-1 min-w-0">
                            <div class="text-gray-800 font-medium truncate">
                                {{ $notification->message }}
                            </div>
                            <div class="text-xs text-gray-400 mt-1">{{ $notification->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        @if($notification->service_id)
                            <div class="flex gap-2">
                                <span class="btn-eq small cursor-pointer" title="Ver projecto">Ver</span>
                                <span class="btn-eq btn-outline small cursor-pointer" title="Enviar proposta">Enviar proposta</span>
                                @php $unread = auth()->check() ? \App\Models\ChatRead::unreadCount($notification->service_id, auth()->id()) : 0; @endphp
                                <span class="btn-eq btn-outline small relative cursor-pointer" title="Ir para o chat">Chat
                                    @if($unread > 0)
                                        <span class="absolute -top-1 -right-2 inline-flex items-center justify-center w-5 h-5 text-xs bg-red-500 text-white rounded-full">{{ $unread > 9 ? '9+' : $unread }}</span>
                                    @endif
                                </span>
                            </div>
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
                        <a href="@php
                            if ($notification->service_id) {
                                echo in_array($notification->type, ['service_chosen', 'delivery_approved', 'revision_requested'])
                                    ? route('freelancer.service.delivery', $notification->service_id)
                                    : route('freelancer.service.review', $notification->service_id);
                            } else { echo '#'; }
                        @endphp"
                           class="block bg-white border border-gray-100 rounded-xl px-5 py-4 flex items-center gap-4 shadow-sm transition hover:bg-cyan-50 focus:bg-cyan-50 outline-none"
                           tabindex="0">
                            <div class="flex-1 min-w-0">
                                <div class="text-gray-700 truncate">
                                    {{ $notification->message }}
                                </div>
                                <div class="text-xs text-gray-400 mt-1">{{ $notification->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            @if($notification->service_id)
                                <div class="flex gap-2">
                                    <span class="btn-eq small cursor-pointer" title="Ver projecto">Ver</span>
                                    <span class="btn-eq btn-outline small cursor-pointer" title="Enviar proposta">Enviar proposta</span>
                                    @php $unread = auth()->check() ? \App\Models\ChatRead::unreadCount($notification->service_id, auth()->id()) : 0; @endphp
                                    <span class="btn-eq btn-outline small relative cursor-pointer" title="Ir para o chat">Chat
                                        @if($unread > 0)
                                            <span class="absolute -top-1 -right-2 inline-flex items-center justify-center w-5 h-5 text-xs bg-red-500 text-white rounded-full">{{ $unread > 9 ? '9+' : $unread }}</span>
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </a>
                    @endif
                @empty
                    <div class="bg-white border border-gray-100 rounded-xl px-5 py-8 text-center text-gray-400">Nenhuma notificação encontrada.</div>
                @endforelse
            </div>
        </div>
</div>
