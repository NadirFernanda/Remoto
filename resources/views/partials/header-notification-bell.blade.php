@php
$_bellUser        = auth()->user();
$_bellIsFreelancer = $_bellUser->activeRole() === 'freelancer';
$_bellFO = ['novo_projeto','service_chosen','revision_requested','project_started','payment_adjustment','delivery_approved','payment_released','saque_aprovado','saque_rejeitado','service_rejected','project_invite','direct_invite'];
$_bellCO = ['refund_processed','refund_approved','refund_rejected','delivery_submitted','proposal_accepted','proposal_rejected'];
$_bellUnread = \App\Models\Notification::where('user_id', $_bellUser->id)
    ->where('read', false)
    ->when(!$_bellIsFreelancer, fn($q) => $q->whereNotIn('type', $_bellFO))
    ->when($_bellIsFreelancer,  fn($q) => $q->whereNotIn('type', $_bellCO))
    ->count();
$_bellNotifUrl = $_bellIsFreelancer ? route('freelancer.notifications') : route('notifications');
@endphp

<div x-data="{
        open: false,
        unread: {{ (int) $_bellUnread }},
        items: [],
        loaded: false,
        async toggle() {
            this.open = !this.open;
            if (this.open && !this.loaded) await this.fetchData();
        },
        async fetchData() {
            try {
                const r = await fetch('/user/notification-data');
                if (r.ok) { const d = await r.json(); this.unread = d.unread_count; this.items = d.items; this.loaded = true; }
            } catch(e) {}
        },
        async markAllRead() {
            try {
                await fetch('/user/notifications/mark-all-read', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Content-Type': 'application/json' }
                });
                this.unread = 0;
                this.items = this.items.map(n => ({...n, read: true}));
            } catch(e) {}
        },
        dotColor(type) {
            const green  = ['service_chosen','delivery_approved','payment_released','saque_aprovado'];
            const blue   = ['novo_projeto','proposal_received','delivery_submitted','support_ticket_new','support_ticket_reply'];
            const red    = ['service_rejected','saque_rejeitado','refund_rejected'];
            const yellow = ['revision_requested','dispute_admin_reply','dispute_resolved'];
            const purple = ['nova_mensagem','direct_invite','project_invite'];
            if (green.includes(type))  return '#22c55e';
            if (blue.includes(type))   return '#3b82f6';
            if (red.includes(type))    return '#f87171';
            if (yellow.includes(type)) return '#eab308';
            if (purple.includes(type)) return '#a855f7';
            return '#00baff';
        }
     }"
     x-init="setInterval(async () => {
         try { const r = await fetch('/user/notification-data'); if (r.ok) { const d = await r.json(); unread = d.unread_count; if(open){ items = d.items; } } } catch(e) {}
     }, 60000)"
     class="relative"
     @click.outside="open = false">

    {{-- Bell button --}}
    <button @click="toggle()"
        class="relative flex items-center justify-center w-9 h-9 rounded-full hover:bg-white/10 transition"
        aria-label="Notificações">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
        </svg>
        <span x-show="unread > 0" x-text="unread > 99 ? '99+' : unread"
              class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-0.5 flex items-center justify-center text-[10px] font-bold bg-red-500 text-white rounded-full leading-none shadow">
        </span>
    </button>

    {{-- Dropdown --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
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
            <button x-show="unread > 0" @click="markAllRead()"
                    class="text-xs text-[#00baff] hover:underline font-medium">
                Marcar todas como lidas
            </button>
        </div>

        {{-- Loading state --}}
        <div x-show="!loaded" class="px-4 py-8 text-center text-sm text-gray-400">
            <svg class="w-5 h-5 mx-auto mb-1 animate-spin text-gray-300" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
        </div>

        {{-- Notification list --}}
        <div x-show="loaded" class="max-h-80 overflow-y-auto divide-y divide-gray-50">
            <template x-if="items.length === 0">
                <div class="px-4 py-8 text-center text-sm text-gray-400">
                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
                    </svg>
                    Sem notificações
                </div>
            </template>
            <template x-for="notif in items" :key="notif.id">
                <a :href="notif.url"
                   class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition"
                   :class="notif.read ? 'opacity-70' : ''">
                    <span class="mt-1.5 w-2 h-2 flex-shrink-0 rounded-full"
                          :style="notif.read ? 'background:#e5e7eb' : 'background:' + dotColor(notif.type)"></span>
                    <div class="flex-1 min-w-0">
                        <p x-show="notif.title" x-text="notif.title"
                           class="text-xs font-semibold text-gray-800 truncate"></p>
                        <p x-show="notif.sender_name || notif.type === 'admin_message'"
                           x-text="'Admin: ' + (notif.sender_name || 'Administração')"
                           class="text-[10px] font-medium text-[#0070ff] mb-0.5"></p>
                        <p x-text="notif.message"
                           class="text-xs text-gray-600 leading-snug line-clamp-2 mt-0.5"></p>
                        <p x-text="notif.created_at"
                           class="text-[11px] text-gray-400 mt-1"></p>
                    </div>
                </a>
            </template>
        </div>

        {{-- Footer --}}
        <div class="px-4 py-2.5 border-t border-gray-50 bg-gray-50/50">
            <a href="{{ $_bellNotifUrl }}" class="block text-center text-xs text-[#00baff] font-medium hover:underline">
                Ver todas as notificações
            </a>
        </div>
    </div>
</div>
