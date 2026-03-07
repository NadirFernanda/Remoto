<div class="mb-6 px-2">
    @if(auth()->check())
        <div class="flex items-center gap-3 p-2 rounded bg-gray-50">
            <img src="{{ auth()->user()->avatarUrl() }}" alt="{{ auth()->user()->name }}" class="w-12 h-12 rounded-full object-cover" onerror="this.onerror=null;this.src='{{ asset('img/default-avatar.svg') }}'">
            <div>
                <div class="font-semibold">{{ auth()->user()->name }}</div>
                <div class="text-xs text-gray-500">{{ auth()->user()->email }}</div>
            </div>
        </div>

        @if(!empty(auth()->user()->affiliate_code))
            <div class="mt-3">
                <label class="text-xs text-gray-600">Código de afiliado</label>
                <div class="flex mt-1">
                    <input id="sidebarAffiliate" type="text" class="flex-1 px-2 py-1 border rounded-l bg-gray-100 text-sm" value="{{ auth()->user()->affiliate_code }}" readonly>
                    <button x-data x-on:click="navigator.clipboard.writeText(document.getElementById('sidebarAffiliate').value); $dispatch('copied')" class="px-3 py-1 bg-cyan-600 text-white rounded-r text-sm">Copiar</button>
                </div>
            </div>
        @else
            <div class="mt-3">
                <form method="POST" action="{{ route('affiliate.generate') }}">
                    @csrf
                    <button type="submit" class="w-full px-3 py-2 bg-cyan-600 text-white rounded text-sm">Gerar código de afiliado</button>
                </form>
            </div>
        @endif
    @endif
</div>

<nav class="flex flex-col gap-2 flex-1">
    @php
        $role      = optional(auth()->user())->activeRole() ?? 'cliente';
        $adminRole = optional(auth()->user())->admin_role; // master | gestor | financeiro | null
        $isMaster      = in_array($adminRole, ['master', null]);
        $isGestor      = in_array($adminRole, ['master', 'gestor', null]);
        $isFinanceiro  = in_array($adminRole, ['master', 'financeiro', null]);
        $isSettings    = $isMaster;
    @endphp

    @if($role === 'freelancer')
        <a href="{{ route('freelancer.dashboard') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222]">Dashboard</a>
        <a href="{{ route('freelancer.available-projects') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222]">Projetos Disponíveis</a>
        <a href="#" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222]">Histórico</a>
        <a href="{{ route('reviews.panel') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222]">Avaliações</a>
        <a href="{{ route('freelancer.notifications') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222]">Notificações</a>
        <a href="{{ route('freelancer.settings') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222]">Configurações</a>

    @elseif($role === 'admin')
        <a href="{{ route('admin.dashboard') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222]">Dashboard</a>

        @if($isGestor)
        <div class="pt-2">
            <p class="px-4 text-xs text-gray-400 uppercase tracking-wide mb-1">Gestão de Clientes</p>
            <a href="{{ route('admin.users') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222]">Utilizadores</a>
            <a href="{{ route('admin.services') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222]">Serviços</a>
            <a href="{{ route('admin.disputes') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222]">Disputas</a>
        </div>
        @endif

        @if($isFinanceiro)
        <div class="pt-2">
            <p class="px-4 text-xs text-gray-400 uppercase tracking-wide mb-1">Gestão Financeira</p>
            <a href="{{ route('admin.financial') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222]">Visão Geral</a>
            <a href="{{ route('admin.commissions') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222]">Comissões</a>
            <a href="{{ route('admin.payouts') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222]">Saques</a>
        </div>
        @endif

        @if($isGestor)
        <div class="pt-2">
            <p class="px-4 text-xs text-gray-400 uppercase tracking-wide mb-1">Suporte</p>
            <a href="{{ route('admin.disputes') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222]">Disputas</a>
            <a href="{{ route('admin.notifications.mass') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222]">Notificações em Massa</a>
        </div>
        @endif

        @if($isSettings)
        <div class="pt-2">
            <p class="px-4 text-xs text-gray-400 uppercase tracking-wide mb-1">Configurações</p>
            <a href="{{ route('admin.settings') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222]">Configurações Gerais</a>
            <a href="{{ route('admin.categories') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222]">Categorias</a>
            <a href="{{ route('admin.fees') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222]">Taxas e Comissões</a>
        </div>
        @endif

        @if($isGestor)
        <div class="pt-2">
            <p class="px-4 text-xs text-gray-400 uppercase tracking-wide mb-1">Auditoria</p>
            <a href="{{ route('admin.audit') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222]">Logs & Auditoria</a>
        </div>
        @endif

    @else
        <a href="{{ route('client.dashboard') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222]">Dashboard</a>
        <a href="{{ route('client.orders') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222]">Meus Pedidos</a>
        <a href="{{ route('client.briefing') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222]">Novo Pedido</a>
        <a href="#" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222]">Histórico</a>
        <a href="{{ route('reviews.panel') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222]">Avaliações</a>
        <a href="{{ route('notifications') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222]">Notificações</a>
        <a href="#" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222]">Configurações</a>
    @endif

    {{-- Botão de troca de estado removido conforme solicitado --}}

    <form method="POST" action="{{ route('logout') }}" class="mt-auto pt-8">
        @csrf
        <button type="submit" class="btn-primary w-full">Sair</button>
    </form>
</nav>
