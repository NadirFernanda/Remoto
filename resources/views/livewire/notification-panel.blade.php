<div x-data x-init="setInterval(() => $wire.refresh(), 60000)">
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">Notificações</h1>
        <p class="text-sm text-gray-400 mt-0.5">Acompanhe todas as suas notificações</p>
    </div>

    <div class="space-y-2">
        @forelse($notifications as $notification)
            @php
                $dotColor = $notification->read ? 'bg-gray-200' : 'bg-[#00baff]';
                $typeColor = match($notification->type) {
                    'service_chosen', 'delivery_approved', 'payment_released', 'saque_aprovado', 'refund_approved' => 'bg-green-50 border-green-100',
                    'service_rejected', 'saque_rejeitado', 'refund_rejected', 'project_cancelled'                => 'bg-red-50 border-red-100',
                    'revision_requested', 'dispute_admin_reply', 'dispute_resolved'                              => 'bg-yellow-50 border-yellow-100',
                    'novo_projeto', 'proposal_received', 'delivery_submitted', 'project_invite', 'direct_invite' => 'bg-blue-50 border-blue-100',
                    'nova_mensagem'                                                                               => 'bg-purple-50 border-purple-100',
                    default                                                                                       => 'bg-gray-50 border-gray-100',
                };
            @endphp
            <a href="{{ route('notification.open', $notification->id) }}"
               class="flex items-start gap-3 px-4 py-3.5 rounded-xl border {{ $typeColor }} hover:opacity-80 transition {{ $notification->read ? 'opacity-70' : '' }}">
                <span class="mt-2 w-2.5 h-2.5 flex-shrink-0 rounded-full {{ $dotColor }}"></span>
                <div class="flex-1 min-w-0">
                    @if($notification->title)
                        <p class="text-sm font-semibold text-gray-800">{{ $notification->title }}</p>
                    @endif
                    <p class="text-sm text-gray-600 leading-snug mt-0.5">{{ $notification->message }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                </div>
            </a>
        @empty
            <div class="bg-white rounded-2xl border border-dashed border-gray-200 p-12 text-center">
                <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
                </svg>
                <p class="text-sm text-gray-400 font-medium">Nenhuma notificação encontrada.</p>
            </div>
        @endforelse
    </div>
</div>

