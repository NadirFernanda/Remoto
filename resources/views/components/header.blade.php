<header x-data="{open:false, scrolled:false}" x-init="scrolled = window.location.pathname !== '/'; window.addEventListener('scroll', ()=>{ scrolled = window.location.pathname !== '/' || window.scrollY > 30 })" :class="{'scrolled': scrolled}" class="site-header fixed top-0 left-0 z-50 w-full py-3">
    <div class="header-container px-4">
        <div class="flex items-center gap-3">
            <a href="/" class="flex items-center" aria-label="24 Horas">
                <img src="{{ asset('img/logo.png') }}" alt="24 Horas" class="site-logo">
            </a>
        </div>

        <nav class="nav-desktop flex-1 flex items-center justify-center gap-6">
            <div x-data="{open:false}" class="relative">
                <button @click="open = !open" class="nav-link flex items-center gap-1">
                    Contratar
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-cloak class="absolute left-0 mt-2 w-56 bg-white rounded-lg shadow-lg py-2 z-50">
                    <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-50">Por habilidade</a>
                    <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-50">Por localização</a>
                    <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-50">Por categoria</a>
                </div>
            </div>
            <div x-data="{open:false}" class="relative">
                <button @click="open = !open" class="nav-link flex items-center gap-1">
                    Encontrar trabalho
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-cloak class="absolute left-0 mt-2 w-56 bg-white rounded-lg shadow-lg py-2 z-50">
                    <a href="{{ route('public.projects') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Projetos</a>
                    <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-50">Concursos</a>
                    <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-50">Vagas</a>
                </div>
            </div>
            <div x-data="{open:false}" class="relative">
                <button @click="open = !open" class="nav-link flex items-center gap-1">
                    Soluções
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-cloak class="absolute left-0 mt-2 w-56 bg-white rounded-lg shadow-lg py-2 z-50">
                    <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-50">Empresas</a>
                    <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-50">Parceiros</a>
                    <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-50">Ajuda</a>
                </div>
            </div>
        </nav>
        <div class="flex items-center gap-2">
            @guest
                <a href="/login" class="nav-link">Login</a>
                <a href="/register" class="nav-link">Cadastro</a>
                <a href="{{ route('client.projects') }}" class="ml-2 px-4 py-2 rounded-lg bg-[#ff2d55] text-white font-bold shadow hover:bg-[#e60039] transition">Publicar Projeto</a>
            @else
                <a href="{{ route('client.projects') }}" class="ml-2 px-4 py-2 rounded-lg bg-[#ff2d55] text-white font-bold shadow hover:bg-[#e60039] transition">Publicar Projeto</a>
                <div class="flex items-center ml-4">
                    @if(auth()->user()->activeRole() !== 'freelancer')
                        <a href="{{ route('notifications') }}" class="nav-link">Notificações</a>
                    @endif
                    @if(auth()->user()->canSwitchRole())
                        <form method="POST" action="{{ route('switch.role') }}" class="inline">
                            @csrf
                            <button type="submit" class="nav-link flex items-center gap-1 text-cyan-400 hover:text-cyan-300 transition" title="Mudar para {{ auth()->user()->switchableRole() === 'freelancer' ? 'Freelancer' : 'Cliente' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                <span class="font-semibold">{{ auth()->user()->switchableRole() === 'freelancer' ? 'Freelancer' : 'Cliente' }}</span>
                            </button>
                        </form>
                    @endif
                    <div x-data="{open:false}" class="relative ml-4">
                        <button @click="open = !open" class="flex items-center gap-2 focus:outline-none">
                            <img src="{{ auth()->user()->avatarUrl() }}" alt="{{ auth()->user()->name }}" class="avatar-sm" onerror="this.onerror=null;this.src='{{ asset('img/default-avatar.svg') }}';">
                            <span class="hidden md:inline">{{ auth()->user()->name }}</span>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2">
                            @if(in_array(auth()->user()->activeRole(), ['cliente','client']))
                                <a href="{{ route('client.dashboard') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Dashboard</a>
                            @elseif(auth()->user()->activeRole() === 'freelancer')
                                <a href="{{ route('freelancer.dashboard') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Dashboard</a>
                            @else
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Dashboard</a>
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
        </div>

        <div class="mobile-nav flex items-center gap-3">
            <a href="/register" class="mobile-cta btn-primary">Cadastro</a>
            <button @click="open = !open" class="p-2 rounded-md text-white bg-[#00baff]/20 border border-white/20 hover:bg-[#00baff]/30 transition">
                <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <div x-show="open" x-transition class="px-4 pb-4 md:hidden">
        <div class="mobile-menu-dropdown flex flex-col gap-1 bg-[#071422] border border-white/10 rounded-xl p-3 mt-2 shadow-xl">
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
                @if(in_array(auth()->user()->activeRole(), ['cliente','client']))
                    <a href="{{ route('client.dashboard') }}" class="nav-link">Dashboard</a>
                @elseif(auth()->user()->activeRole() === 'freelancer')
                    <a href="{{ route('freelancer.dashboard') }}" class="nav-link">Dashboard</a>
                @else
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">Dashboard</a>
                @endif
                @if(auth()->user()->canSwitchRole())
                    <form method="POST" action="{{ route('switch.role') }}">
                        @csrf
                        <button type="submit" class="nav-link text-left text-cyan-400">
                            Mudar para {{ auth()->user()->switchableRole() === 'freelancer' ? 'Freelancer' : 'Cliente' }}
                        </button>
                    </form>
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
