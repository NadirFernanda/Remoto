<div class="mb-8">
    <h2 class="font-semibold text-xl mb-2 text-[#222]">Notificações Recentes</h2>
    <div class="space-y-2">
        @forelse($notifications->take(3) as $notification)
            @php
                $url = '#';
                if ($notification->service_id) {
                    $url = in_array($notification->type, ['service_chosen', 'delivery_approved', 'revision_requested'])
                        ? route('freelancer.service.delivery', $notification->service_id)
                        : route('freelancer.service.review', $notification->service_id);
                }
            @endphp
            <a href="{{ $url }}" class="flex items-start gap-3 px-4 py-3 rounded-lg border border-gray-100 bg-gray-50 hover:bg-cyan-50 hover:border-cyan-200 transition">
                <span class="mt-1 w-2 h-2 rounded-full flex-shrink-0 {{ $notification->read ? 'bg-gray-300' : 'bg-[#00baff]' }}"></span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-800 leading-snug">{{ $notification->message }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $notification->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </a>
        @empty
            <p class="text-center py-4 text-[#888] text-sm">Nenhuma notificação recente.</p>
        @endforelse
        <div class="mt-3 text-right">
            <a href="{{ route('freelancer.notifications') }}" class="btn-nowrap">Mais notificações</a>
        </div>
    </div>
</div>
