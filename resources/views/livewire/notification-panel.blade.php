<div class="max-w-3xl mx-auto" x-data x-init="setInterval(() => $wire.refresh(), 60000)">

    {{-- ─── Gradient Header Card ───────────────────────────────────────── --}}
    <div class="bg-gradient-to-r from-[#00c8ff] to-[#0033cc] rounded-2xl p-6 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-extrabold flex items-center gap-2">
                Notificações
                @if($notifications->where('read', false)->count() > 0)
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold bg-white/20 border border-white/30">
                        {{ $notifications->where('read', false)->count() }}
                    </span>
                @endif
            </h2>
            <p class="text-sm text-white/75 mt-1">Acompanhe todas as suas actualizações em tempo real.</p>
        </div>
        <div class="flex items-center gap-2 text-white/70 text-xs">
            <div class="w-1.5 h-1.5 rounded-full bg-green-300 animate-pulse"></div>
            Auto-actualiza a cada minuto
        </div>
    </div>

    {{-- ─── Stats row ───────────────────────────────────────────────────── --}}
    @if($notifications->count() > 0)
    <div class="grid grid-cols-3 gap-3 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 p-4 text-center">
            <p class="text-2xl font-black text-gray-800">{{ $notifications->count() }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Total</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4 text-center">
            <p class="text-2xl font-black" style="color:#0055ff;">{{ $notifications->where('read', false)->count() }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Não lidas</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4 text-center">
            <p class="text-2xl font-black text-green-500">{{ $notifications->where('read', true)->count() }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Lidas</p>
        </div>
    </div>
    @endif

    {{-- ─── List ────────────────────────────────────────────────────────── --}}
    <div class="space-y-2">
        @forelse($notifications as $notification)
            @php
                // Icon + colour by type
                [$iconBg, $iconColor, $iconPath] = match($notification->type) {
                    'payment_released', 'saque_aprovado', 'refund_approved'
                        => ['bg-green-50', 'text-green-500',
                           'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    'delivery_approved', 'service_chosen', 'proposal_accepted'
                        => ['bg-green-50', 'text-green-500',
                           'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    'service_rejected', 'saque_rejeitado', 'refund_rejected', 'project_cancelled', 'proposal_rejected'
                        => ['bg-red-50', 'text-red-500',
                           'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    'revision_requested'
                        => ['bg-yellow-50', 'text-yellow-500',
                           'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                    'dispute_admin_reply', 'dispute_resolved'
                        => ['bg-yellow-50', 'text-yellow-600',
                           'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                    'nova_mensagem'
                        => ['bg-blue-50', 'text-[#0055ff]',
                           'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
                    'novo_projeto', 'project_invite', 'direct_invite'
                        => ['bg-blue-50', 'text-blue-500',
                           'M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    'delivery_submitted'
                        => ['bg-blue-50', 'text-blue-500',
                           'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12'],
                    'support_ticket_new', 'support_ticket_reply'
                        => ['bg-indigo-50', 'text-indigo-500',
                           'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z'],
                    'proposal_received'
                        => ['bg-blue-50', 'text-blue-500',
                           'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                    default
                        => ['bg-gray-100', 'text-gray-400',
                           'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
                };

                $isUnread = !$notification->read;
            @endphp

            <a href="{{ route('notification.open', $notification->id) }}"
               class="flex items-start gap-4 p-4 rounded-2xl border transition-all group
                      {{ $isUnread
                           ? 'bg-blue-50 border-blue-100 hover:border-blue-200'
                           : 'bg-white border-gray-100 hover:border-gray-200 hover:shadow-sm' }}">

                {{-- Icon --}}
                <div class="w-10 h-10 rounded-xl {{ $iconBg }} flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-5 h-5 {{ $iconColor }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}"/>
                    </svg>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            @if($notification->title)
                                <p class="text-sm font-semibold text-gray-800 leading-snug truncate">{{ $notification->title }}</p>
                            @endif
                            <p class="text-sm text-gray-500 leading-snug mt-0.5 {{ $notification->title ? '' : 'font-medium text-gray-700' }}">
                                {{ $notification->message }}
                            </p>
                        </div>
                        <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
                            <span class="text-[11px] text-gray-400 whitespace-nowrap">{{ $notification->created_at->diffForHumans() }}</span>
                            @if($isUnread)
                                <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:#0055ff;"></span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Arrow --}}
                <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-400 flex-shrink-0 mt-3 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        @empty
            {{-- Empty state --}}
            <div class="bg-white rounded-2xl border border-dashed border-gray-200 py-16 px-6 text-center">
                <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-600 mb-1">Está tudo em dia!</p>
                <p class="text-xs text-gray-400">Não há notificações neste momento.</p>
            </div>
        @endforelse
    </div>

</div>
