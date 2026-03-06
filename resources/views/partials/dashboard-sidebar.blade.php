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

<nav class="flex flex-col gap-2">
    @php $role = optional(auth()->user())->role ?? 'client'; @endphp

    @if($role === 'freelancer')
        <a href="{{ route('freelancer.dashboard') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222] font-medium">Dashboard</a>
        <a href="{{ route('freelancer.available-projects') }}" class="py-2 px-4 rounded hover:bg-cyan-100 text-cyan-700 font-bold">Projetos Disponíveis</a>
        <a href="#" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222] font-medium">Histórico</a>
        <a href="{{ route('profile.edit') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222] font-medium">Editar Perfil</a>
        <a href="{{ route('freelancer.settings') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222] font-medium">Configurações</a>
    @elseif($role === 'admin')
        <a href="{{ route('admin.dashboard') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222] font-medium">Dashboard</a>
        <a href="/admin/users" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222] font-medium">Usuários</a>
        <a href="/admin/services" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222] font-medium">Serviços</a>
    @else
        <a href="{{ route('client.dashboard') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222] font-medium">Dashboard</a>
        <a href="{{ route('client.orders') }}" class="py-2 px-4 rounded hover:bg-cyan-100 text-cyan-700 font-bold">Meus Pedidos</a>
        <a href="{{ route('client.briefing') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222] font-medium">Novo Pedido</a>
        <a href="#" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222] font-medium">Histórico</a>
        <a href="{{ route('profile.edit') }}" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222] font-medium">Editar Perfil</a>
        <a href="#" class="py-2 px-4 rounded hover:bg-gray-100 text-[#222] font-medium">Configurações</a>
    @endif

    <form method="POST" action="{{ route('logout') }}" class="mt-4">
        @csrf
        <button type="submit" class="btn-primary w-full" style="margin-top: 0.5rem;">Sair</button>
    </form>
</nav>
