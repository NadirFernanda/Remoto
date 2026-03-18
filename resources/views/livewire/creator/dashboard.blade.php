<div class="max-w-4xl mx-auto pb-8 px-4 space-y-8">

    @if(session('success'))
        <div class="p-3 bg-green-100 text-green-700 rounded-xl text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Painel do Criador</h1>
            <p class="text-sm text-gray-500 mt-0.5">Gerencie as suas assinaturas, conteúdos e ganhos</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('social.creator', $user) }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-[#00baff] border border-[#00baff] px-4 py-2 rounded-xl hover:bg-[#00baff]/5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Meu Perfil Público
            </a>
            <a href="{{ route('loja.index') }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-white bg-[#00baff] px-4 py-2 rounded-xl hover:bg-[#009ad6] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 2.304a3.004 3.004 0 01-.621 4.72m0 0v7.002"/>
                </svg>
                Minha Loja
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Assinantes</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalSubscribers }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Ganhos Mensais</p>
            <p class="text-3xl font-bold text-[#00baff] mt-1">{{ number_format($monthlyEarnings, 0, ',', '.') }} KZS</p>
            <p class="text-xs text-gray-400 mt-0.5">70% de cada assinatura</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Total Acumulado</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($allTimeEarnings, 0, ',', '.') }} KZS</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Ganhos Infoprodutos</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($infoprodutosEarnings, 0, ',', '.') }} KZS</p>
        </div>
    </div>

    {{-- Share links --}}
    <div class="bg-gradient-to-r from-[#00baff]/10 to-cyan-50 rounded-2xl p-5 flex flex-col md:flex-row items-start md:items-center gap-4">
        <div class="flex-1">
            <p class="text-sm font-semibold text-gray-900 mb-1">Partilhe o seu perfil nas redes sociais</p>
            <p class="text-xs text-gray-500">Use estes links como marketing para atrair novos assinantes.</p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-600 min-w-0">
                <svg class="w-3.5 h-3.5 text-[#00baff] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/>
                </svg>
                <span class="truncate max-w-48">{{ url(route('social.creator', $user)) }}</span>
                <button
                    onclick="navigator.clipboard.writeText('{{ url(route('social.creator', $user)) }}'); this.textContent = '✓'"
                    class="text-[#00baff] font-semibold flex-shrink-0 hover:underline">Copiar</button>
            </div>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">

        {{-- Active subscribers --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#00baff]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                </svg>
                Assinantes Activos
            </h2>
            @forelse($activeSubscriptions as $sub)
                <div class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0">
                    <img src="{{ $sub->subscriber->avatarUrl() }}" alt="{{ $sub->subscriber->name }}"
                         class="w-8 h-8 rounded-full object-cover"
                         onerror="this.src='{{ asset('img/default-avatar.svg') }}'">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $sub->subscriber->name }}</p>
                        <p class="text-xs text-gray-400">Expira {{ $sub->expires_at->format('d/m/Y') }}</p>
                    </div>
                    <span class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded-full">
                        +{{ number_format($sub->net_amount, 0, ',', '.') }} KZS
                    </span>
                </div>
            @empty
                <p class="text-sm text-gray-400 text-center py-6">Ainda não tem assinantes.<br>Partilhe o seu perfil!</p>
            @endforelse
        </div>

        {{-- Infoprodutos --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#00baff]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 2.304a3.004 3.004 0 01-.621 4.72m0 0v7.002"/>
                    </svg>
                    Infoprodutos
                </h2>
                <a href="{{ route('loja.index') }}" class="text-xs text-[#00baff] font-semibold hover:underline">Ver loja →</a>
            </div>
            @forelse($infoprodutos as $produto)
                <div class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $produto->titulo }}</p>
                        <p class="text-xs text-gray-400">{{ $produto->compras_count }} vendas</p>
                    </div>
                    <span class="text-xs font-semibold text-gray-700">{{ number_format($produto->preco, 0, ',', '.') }} KZS</span>
                </div>
            @empty
                <div class="text-center py-6">
                    <p class="text-sm text-gray-400 mb-3">Ainda não criou infoprodutos.</p>
                    <a href="{{ route('loja.index') }}" class="text-xs text-[#00baff] font-semibold hover:underline">Criar produto →</a>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Activate other profiles --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-base font-bold text-gray-900 mb-1">Outros Perfis</h2>
        <p class="text-sm text-gray-500 mb-4">Ative outros perfis para aceder a mais funcionalidades na mesma conta.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if(!auth()->user()->has_freelancer_profile)
                <a href="{{ route('creator.activate', ['profile' => 'freelancer']) }}"
                   class="flex items-center gap-4 p-4 border-2 border-dashed border-gray-200 rounded-xl hover:border-[#00baff]/50 hover:bg-blue-50/20 transition">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-[#00baff]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Ativar perfil Freelancer</p>
                        <p class="text-xs text-gray-400">Ofereça serviços e receba projetos</p>
                    </div>
                </a>
            @else
                <a href="{{ route('freelancer.dashboard') }}"
                   class="flex items-center gap-4 p-4 border-2 border-green-100 bg-green-50/30 rounded-xl hover:bg-green-50 transition">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Perfil Freelancer ativo</p>
                        <p class="text-xs text-[#00baff] font-medium">Ir para painel →</p>
                    </div>
                </a>
            @endif

            @if(!auth()->user()->has_cliente_profile)
                <a href="{{ route('creator.activate', ['profile' => 'cliente']) }}"
                   class="flex items-center gap-4 p-4 border-2 border-dashed border-gray-200 rounded-xl hover:border-[#00baff]/50 hover:bg-blue-50/20 transition">
                    <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Ativar perfil Cliente</p>
                        <p class="text-xs text-gray-400">Publique projetos e contrate freelancers</p>
                    </div>
                </a>
            @else
                <a href="{{ route('client.dashboard') }}"
                   class="flex items-center gap-4 p-4 border-2 border-green-100 bg-green-50/30 rounded-xl hover:bg-green-50 transition">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Perfil Cliente ativo</p>
                        <p class="text-xs text-[#00baff] font-medium">Ir para painel →</p>
                    </div>
                </a>
            @endif
        </div>
    </div>

</div>
