<div x-data="{ open: false }" x-init="setInterval(() => $wire.refresh(), 60000)" class="relative" @click.outside="open = false">

    {{-- Bell button --}}
    <button @click="open = !open"
        class="relative flex items-center justify-center w-9 h-9 rounded-full hover:bg-white/10 transition"
        aria-label="Notificações">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
        </svg>
        @if($unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-0.5 flex items-center justify-center text-[10px] font-bold bg-red-500 text-white rounded-full leading-none shadow">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown panel --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-1"
         x-cloak
         class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-50">
            <span class="text-sm font-bold text-gray-800">Notificações</span>
            @if($unreadCount > 0)
                <button wire:click="markAllRead" class="text-xs text-[#00baff] hover:underline font-medium">
                    Marcar todas como lidas
                </button>
            @endif
        </div>

        {{-- List --}}
        <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
            @forelse($recent as $notif)
                @php
                    $icon = match($notif['type'] ?? '') {
                        'service_chosen', 'delivery_approved', 'payment_released', 'saque_aprovado' => 'text-green-500',
                        'novo_projeto', 'proposal_received', 'delivery_submitted'                   => 'text-blue-500',
                        'service_rejected', 'saque_rejeitado', 'refund_rejected'                    => 'text-red-400',
                        'revision_requested', 'dispute_admin_reply', 'dispute_resolved'             => 'text-yellow-500',
                        'nova_mensagem', 'direct_invite', 'project_invite'                          => 'text-purple-500',
                        default                                                                     => 'text-[#00baff]',
                    };
                @endphp
                <a href="{{ route('notification.open', $notif['id']) }}"
                   class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition {{ $notif['read'] ? 'opacity-70' : '' }}">
                    <span class="mt-1.5 w-2 h-2 flex-shrink-0 rounded-full {{ $notif['read'] ? 'bg-gray-200' : 'bg-[#00baff]' }}"></span>
                    <div class="flex-1 min-w-0">
                        @if(!empty($notif['title']))
                            <p class="text-xs font-semibold text-gray-800 truncate">{{ $notif['title'] }}</p>
                        @endif
                        <p class="text-xs text-gray-600 leading-snug line-clamp-2 mt-0.5">{{ $notif['message'] }}</p>
                        <p class="text-[11px] text-gray-400 mt-1">{{ $notif['created_at'] }}</p>
                    </div>
                </a>
            @empty
                <div class="px-4 py-8 text-center text-sm text-gray-400">
                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
                    </svg>
                    Sem notificações
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        <div class="px-4 py-2.5 border-t border-gray-50 bg-gray-50/50">
            @auth
                @if(auth()->user()->activeRole() === 'freelancer')
                    <a href="{{ route('freelancer.notifications') }}" class="block text-center text-xs text-[#00baff] font-medium hover:underline">
                        Ver todas as notificações
                    </a>
                @else
                    <a href="{{ route('notifications') }}" class="block text-center text-xs text-[#00baff] font-medium hover:underline">
                        Ver todas as notificações
                    </a>
                @endif
            @endauth
        </div>
    </div>
</div>
