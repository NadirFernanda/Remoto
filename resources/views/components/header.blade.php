<header x-data="{open:false}" class="site-header fixed top-0 left-0 z-50 w-full py-3">
    <div class="header-container px-4">
        <div class="flex items-center gap-3">
            <a href="/" class="flex items-center" aria-label="24 Horas">
                <img src="{{ asset('img/logo-24horas-remoto.jpeg') }}" alt="24 Horas" class="site-logo">
            </a>
        </div>

        <nav class="nav-desktop flex items-center">
            <a href="#categorias" class="nav-link">Categorias</a>
            <a href="{{ route('freelancers.index') }}" class="nav-link">Freelancers</a>
            <a href="{{ route('public.projects') }}" class="nav-link">Serviços</a>
            <a href="#depoimentos" class="nav-link">Depoimentos</a>
            @guest
                <a href="/login" class="nav-link">Login</a>
                <a href="/register" class="nav-link btn-primary ml-3">Cadastro</a>
            @else
                <div class="flex items-center gap-3">
                    @if(auth()->user()->role !== 'freelancer')
                        <a href="{{ route('notifications') }}" class="nav-link">Notificações</a>
                    @endif
                    <div x-data="{open:false}" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2 focus:outline-none">
                            <img src="{{ auth()->user()->avatarUrl() }}" alt="{{ auth()->user()->name }}" class="avatar-sm" onerror="this.onerror=null;this.src='{{ asset('build/img/default-avatar.png') }}';">
                            <span class="hidden md:inline text-sm">{{ auth()->user()->name }}</span>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2">
                            @if(in_array(auth()->user()->role, ['cliente','client']))
                                <a href="{{ route('client.profile') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Meu perfil</a>
                            @else
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Meu perfil</a>
                            @endif
                            @if(auth()->user()->role === 'freelancer')
                                <a href="{{ route('freelancer.dashboard') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Dashboard</a>
                            @endif
                            <a href="{{ route('freelancer.notifications') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Notificações</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50">Sair</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endguest
        </nav>

        <div class="mobile-nav flex items-center gap-3">
            <a href="/register" class="mobile-cta btn-primary">Cadastro</a>
            <button @click="open = !open" class="p-2 rounded-md text-white/90 bg-white/5">
                @include('components.icon', ['name' => 'menu', 'class' => 'h-6 w-6', ''])
                @include('components.icon', ['name' => 'close', 'class' => 'h-6 w-6', ''])
            </button>
        </div>
    </div>

    <div x-show="open" x-transition class="px-4 pb-4 md:hidden">
        <div class="flex flex-col gap-2">
            <a href="#categorias" class="nav-link">Categorias</a>
            <a href="{{ route('freelancers.index') }}" class="nav-link">Freelancers</a>
            <a href="{{ route('public.projects') }}" class="nav-link">Serviços</a>
            <a href="#depoimentos" class="nav-link">Depoimentos</a>
            @guest
                <a href="/login" class="nav-link">Login</a>
                <a href="/register" class="nav-link btn-primary">Cadastro</a>
            @else
                <div class="flex items-center gap-3 px-2 py-2 border rounded-lg">
                    <img src="{{ auth()->user()->avatarUrl() }}" alt="{{ auth()->user()->name }}" class="avatar-sm">
                    <div class="flex-1">
                        <div class="font-semibold">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-gray-400">{{ auth()->user()->email }}</div>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}" class="nav-link">Meu perfil</a>
                @if(auth()->user()->role === 'freelancer')
                    <a href="{{ route('freelancer.dashboard') }}" class="nav-link">Dashboard</a>
                @endif
                <a href="{{ route('freelancer.notifications') }}" class="nav-link">Notificações</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link text-left">Sair</button>
                </form>
            @endguest
        </div>
    </div>
</header>
