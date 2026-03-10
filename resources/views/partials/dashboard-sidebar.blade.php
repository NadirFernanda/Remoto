@php
    $currentUrl  = request()->url();
    $currentPath = '/' . request()->path();
    function sidebarActive(string $route): string {
        try { $url = route($route); } catch (\Exception $e) { return ''; }
        return request()->url() === $url ? 'active' : '';
    }
@endphp

{{-- ...removed logo/brand bar for cleaner sidebar... --}}

{{-- ─── User profile card ───────────────────────────────────── --}}
@if(auth()->check())
@php $u = auth()->user(); @endphp
<div class="px-4 pt-6 pb-4 border-b border-gray-100">
    <div class="flex items-center gap-3">
        <img src="{{ $u->avatarUrl() }}" alt="{{ $u->name }}"
            class="w-10 h-10 rounded-full object-cover ring-2 ring-[#00baff]/20 flex-shrink-0"
            onerror="this.onerror=null;this.src='{{ asset('img/default-avatar.svg') }}'">
        <div class="min-w-0">
            <p class="text-sm text-gray-800 truncate leading-tight" style="max-width:160px">{{ $u->name }}</p>
            <p class="text-xs text-gray-400 truncate leading-tight" style="max-width:160px">{{ $u->email }}</p>
        </div>
    </div>

    {{-- Affiliate code --}}
    @if(!empty($u->affiliate_code))
        <div class="mt-3 flex items-center gap-1.5 bg-gray-50 rounded-lg px-3 py-2">
            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 015.656 0l-4 4a4 4 0 01-5.656-5.656l1.1-1.1"/>
            </svg>
            <span id="sidebarAffiliate" class="text-xs text-gray-500 font-mono flex-1 select-all">{{ $u->affiliate_code }}</span>
            <button x-data
                x-on:click="navigator.clipboard.writeText('{{ $u->affiliate_code }}');$el.textContent='✓'"
                class="text-xs text-[#00baff] hover:text-[#009ad6] transition font-medium">
                Copiar
            </button>
        </div>
    @else
        <form method="POST" action="{{ route('affiliate.generate') }}" class="mt-3">
            @csrf
            <button type="submit"
                class="w-full flex items-center justify-center gap-1.5 text-xs text-[#00baff] border border-[#00baff]/30 rounded-lg px-3 py-2 hover:bg-[#00baff]/5 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Gerar código de afiliado
            </button>
        </form>
    @endif
</div>
@endif

{{-- ─── Navigation ──────────────────────────────────────────── --}}
@php
    $role         = optional(auth()->user())->activeRole() ?? 'cliente';
    $adminRole    = optional(auth()->user())->admin_role;
    $isMaster     = in_array($adminRole, ['master', null]);
    $isGestor     = in_array($adminRole, ['master', 'gestor', null]);
    $isFinanceiro = in_array($adminRole, ['master', 'financeiro', null]);
    $isSettings   = $isMaster;

    // Helper to check active route
    $isActive = fn(string $routeName) =>
        request()->routeIs($routeName) ? true : false;
@endphp

<style>
    .snav-item {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.5rem 1rem;
        border-radius: 0.625rem;
        font-size: 0.8125rem;
        color: #444;
        text-decoration: none;
        transition: background 0.15s, color 0.15s;
        margin: 0 0.5rem;
    }
    .snav-item:hover {
        background: #f3f4f6;
        color: #111;
    }
    .snav-item.snav-active {
        background: #e8f7ff;
        color: #00baff;
        font-weight: 500;
    }
    .snav-item svg { flex-shrink: 0; opacity: 0.7; }
    .snav-item.snav-active svg { opacity: 1; }
    .snav-group-label {
        font-size: 0.65rem;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #9ca3af;
        padding: 0.75rem 1.5rem 0.25rem;
    }
    .snav-divider {
        height: 1px;
        background: #f3f4f6;
        margin: 0.5rem 1rem;
    }
</style>

<nav class="flex flex-col flex-1 py-3 overflow-y-auto">

    @if($role === 'freelancer')

        <a href="{{ route('freelancer.dashboard') }}"
            class="snav-item {{ request()->routeIs('freelancer.dashboard') ? 'snav-active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Dashboard
        </a>
        <a href="{{ route('freelancer.available-projects') }}"
            class="snav-item {{ request()->routeIs('freelancer.available-projects') ? 'snav-active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Projetos Disponíveis
        </a>
        <a href="{{ route('freelancer.proposals') }}"
            class="snav-item {{ request()->routeIs('freelancer.proposals') ? 'snav-active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Propostas
            @php $pendingProposalCount = \App\Models\Proposal::where('recipient_id', auth()->id())->where('status','pending')->count(); @endphp
            @if($pendingProposalCount > 0)
                <span class="ml-auto bg-blue-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $pendingProposalCount }}</span>
            @endif
        </a>
        <a href="{{ route('reviews.panel') }}"
            class="snav-item {{ request()->routeIs('reviews.panel') ? 'snav-active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
            Avaliações
        </a>
        <a href="{{ route('freelancer.notifications') }}"
            class="snav-item {{ request()->routeIs('freelancer.notifications') ? 'snav-active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            Notificações
        </a>
        <a href="{{ route('freelancer.settings') }}"
            class="snav-item {{ request()->routeIs('freelancer.settings') ? 'snav-active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Configurações
        </a>

    @elseif($role === 'admin')

        <a href="{{ route('admin.dashboard') }}"
            class="snav-item {{ request()->routeIs('admin.dashboard') ? 'snav-active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Dashboard
        </a>


        @if($isGestor)
        <div class="snav-divider mt-1"></div>
        <p class="snav-group-label">Gestão de Utilizadores</p>
        <a href="{{ route('admin.users') }}"
            class="snav-item {{ request()->routeIs('admin.users') ? 'snav-active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Utilizadores
        </a>
        <a href="{{ route('admin.services') }}"
            class="snav-item {{ request()->routeIs('admin.services') ? 'snav-active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Serviços
        </a>
        <a href="#" class="snav-item">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Comercial <span class="ml-2 text-xs text-gray-400">Contratos & Parcerias</span>
        </a>
        @endif

        @if($isFinanceiro)
        <div class="snav-divider mt-1"></div>
        <p class="snav-group-label">Financeiro</p>
        <a href="{{ route('admin.financial') }}"
            class="snav-item {{ request()->routeIs('admin.financial') ? 'snav-active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Visão Geral
        </a>
        <a href="{{ route('admin.refunds') }}"
            class="snav-item {{ request()->routeIs('admin.refunds') ? 'snav-active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Reembolsos
        </a>
        <a href="{{ route('admin.commissions') }}"
            class="snav-item {{ request()->routeIs('admin.commissions') ? 'snav-active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Comissões
        </a>
        <a href="{{ route('admin.payouts') }}"
            class="snav-item {{ request()->routeIs('admin.payouts') ? 'snav-active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            Saques
        </a>
        @endif

        @if($isGestor)
        <div class="snav-divider mt-1"></div>
        <p class="snav-group-label">Suporte</p>
        <a href="{{ route('admin.disputes') }}"
            class="snav-item {{ request()->routeIs('admin.disputes') ? 'snav-active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            Disputas
        </a>
        <a href="{{ route('admin.notifications.mass') }}"
            class="snav-item {{ request()->routeIs('admin.notifications.mass') ? 'snav-active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
            Notificações em Massa
        </a>
        @endif

        @if($isSettings)
        <div class="snav-divider mt-1"></div>
        <p class="snav-group-label">Configurações</p>
        <a href="{{ route('admin.settings') }}"
            class="snav-item {{ request()->routeIs('admin.settings') ? 'snav-active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Configurações Gerais
        </a>
        <a href="{{ route('admin.categories') }}"
            class="snav-item {{ request()->routeIs('admin.categories') ? 'snav-active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            Categorias
        </a>
        <a href="{{ route('admin.fees') }}"
            class="snav-item {{ request()->routeIs('admin.fees') ? 'snav-active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            Taxas e Comissões
        </a>
        @endif

        @if($isGestor)
        <div class="snav-divider mt-1"></div>
        <p class="snav-group-label">Sistema</p>
        <a href="{{ route('admin.audit') }}"
            class="snav-item {{ request()->routeIs('admin.audit') ? 'snav-active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            Logs & Auditoria
        </a>
        @endif

    @else

        {{-- ── Cliente ── --}}
        <a href="{{ route('client.dashboard') }}"
            class="snav-item {{ request()->routeIs('client.dashboard') ? 'snav-active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Dashboard
        </a>
        <a href="{{ route('client.briefing') }}"
            class="snav-item {{ request()->routeIs('client.briefing') ? 'snav-active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Novo Pedido
        </a>
        <a href="{{ route('client.orders') }}"
            class="snav-item {{ request()->routeIs('client.orders') ? 'snav-active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Meus Pedidos
        </a>
        <a href="{{ route('reviews.panel') }}"
            class="snav-item {{ request()->routeIs('reviews.panel') ? 'snav-active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
            Avaliações
        </a>
        <a href="{{ route('notifications') }}"
            class="snav-item {{ request()->routeIs('notifications') ? 'snav-active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            Notificações
        </a>

    @endif

    {{-- ─── Logout ───────────────────────────────────────────── --}}
    <div class="mt-auto px-2 pb-4 pt-4">
        <div class="snav-divider mb-3 mx-0"></div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="snav-item w-full text-red-500 hover:bg-red-50 hover:text-red-600"
                style="margin:0; border-radius:0.625rem;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Terminar Sessão
            </button>
        </form>
    </div>

</nav>
