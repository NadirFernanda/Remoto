<div class="container mx-auto p-4">
    <div class="mb-6 flex items-center justify-between mt-10">
        <h2 class="font-semibold text-xl text-[#222]">Todas as Notificações</h2>
        <a href="{{ route('freelancer.dashboard') }}" class="btn-nowrap">Voltar ao Dashboard</a>
    </div>
    <div class="mb-6">
        <h3 class="font-semibold text-lg mb-2 text-[#222]">Mais Recentes</h3>
        <div class="overflow-x-auto">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th class="py-2 px-4">Mensagem</th>
                        <th class="py-2 px-4">Data</th>
                        <th class="py-2 px-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent as $notification)
                        <tr class="border-b">
                            <td class="py-2 px-4 no-card">
                                @if($notification->service_id)
                                    <a href="{{ route('freelancer.service.review', $notification->service_id) }}" class="text-[#00baff] hover:underline">
                                        {{ $notification->message }}
                                    </a>
                                @else
                                    {{ $notification->message }}
                                @endif
                            </td>
                            <td class="py-2 px-4">{{ $notification->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-2 px-4 no-card">
                                @if($notification->service_id)
                                    <x-action-toolbar class="p-0">
                                        <a href="{{ route('freelancer.service.review', $notification->service_id) }}" class="btn-eq small" title="Ver projeto">Ver</a>
                                        <a href="{{ route('freelancer.service.review', $notification->service_id) }}" class="btn-eq btn-outline small" title="Enviar proposta">Enviar proposta</a>
                                        @php $unread = auth()->check() ? \App\Models\ChatRead::unreadCount($notification->service_id, auth()->id()) : 0; @endphp
                                        <a href="{{ route('service.chat', $notification->service_id) }}" class="btn-eq btn-outline small relative" title="Ir para o chat">Chat
                                            @if($unread > 0)
                                                <span class="absolute -top-1 -right-2 inline-flex items-center justify-center w-5 h-5 text-xs bg-red-500 text-white rounded-full">{{ $unread > 9 ? '9+' : $unread }}</span>
                                            @endif
                                        </a>
                                    </x-action-toolbar>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center py-4 text-[#888]">Nenhuma notificação recente.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="orders-table">
            <tbody>
                @php $recentIds = $recent->pluck('id')->all(); @endphp
                @forelse($notifications as $notification)
                    @if(!in_array($notification->id, $recentIds))
                        <tr class="border-b">
                            <td class="py-2 px-4">
                                @if($notification->service_id)
                                    <a href="{{ route('freelancer.service.review', $notification->service_id) }}" class="text-[#00baff] hover:underline">
                                        {{ $notification->message }}
                                    </a>
                                @else
                                    {{ $notification->message }}
                                @endif
                            </td>
                            <td class="py-2 px-4">{{ $notification->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-2 px-4">
                                @if($notification->service_id)
                                    <x-action-toolbar class="p-0">
                                        <a href="{{ route('freelancer.service.review', $notification->service_id) }}" class="btn-eq small" title="Ver projeto">Ver</a>
                                        <a href="{{ route('freelancer.service.review', $notification->service_id) }}" class="btn-eq btn-outline small" title="Enviar proposta">Enviar proposta</a>
                                        @php $unread = auth()->check() ? \App\Models\ChatRead::unreadCount($notification->service_id, auth()->id()) : 0; @endphp
                                        <a href="{{ route('service.chat', $notification->service_id) }}" class="btn-eq btn-outline small relative" title="Ir para o chat">Chat
                                            @if($unread > 0)
                                                <span class="absolute -top-1 -right-2 inline-flex items-center justify-center w-5 h-5 text-xs bg-red-500 text-white rounded-full">{{ $unread > 9 ? '9+' : $unread }}</span>
                                            @endif
                                        </a>
                                    </x-action-toolbar>
                                @endif
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="3" class="text-center py-4 text-[#888]">Nenhuma notificação encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
