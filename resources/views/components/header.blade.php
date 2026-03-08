<header x-data="{open:false, scrolled:false}" x-init="scrolled = window.location.pathname !== '/'; window.addEventListener('scroll', ()=>{ scrolled = window.location.pathname !== '/' || window.scrollY > 30 })" :class="{'scrolled': scrolled}" class="site-header fixed top-0 left-0 z-50 w-full py-3">
    <div class="header-container px-4">

        <!-- Esquerda: Logo + Nav agrupados -->
        <div style="display:flex;align-items:center;gap:0;flex-shrink:0;">
            <a href="/" class="flex items-center" aria-label="24 Horas" style="margin-right:1.5rem;">
                <img src="{{ asset('img/logo.png') }}" alt="24 Horas" class="site-logo">
            </a>

            <nav class="nav-desktop" style="display:flex;align-items:center;gap:0.25rem;margin-left:0;">
                <div x-data="{open:false}" class="relative">
                    <button @click="open = !open" class="nav-link" style="display:flex;align-items:center;gap:0.35rem;white-space:nowrap;">
                        Contratar
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak
                         class="absolute left-0 mt-2 bg-white rounded-2xl z-50"
                         style="width:740px;box-shadow:0 24px 64px rgba(0,0,0,.15);border:1px solid #e8eef4;">
                        <div style="display:flex;">
                            <!-- Coluna esquerda: opções de navegação -->
                            <div style="width:290px;padding:1.5rem 1.25rem;border-right:1px solid #f0f4f8;flex-shrink:0;">
                                <p style="font-size:.68rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin:0 0 1rem .25rem;">Encontrar profissionais</p>
                                <a href="{{ route('freelancers.search') }}"
                                   style="display:flex;align-items:flex-start;gap:.875rem;padding:.75rem .875rem;border-radius:.875rem;text-decoration:none;transition:background .15s;margin-bottom:.375rem;"
                                   onmouseover="this.style.background='#f0f7ff'" onmouseout="this.style.background='transparent'">
                                    <span style="width:40px;height:40px;border-radius:10px;background:#dbeafe;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <svg width="20" height="20" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                                    </span>
                                    <span style="flex:1;">
                                        <span style="display:block;font-weight:700;color:#0f172a;font-size:.875rem;">Por habilidade</span>
                                        <span style="display:block;font-size:.775rem;color:#64748b;margin-top:.2rem;line-height:1.5;">Procura um profissional com uma habilidade específica? Comece aqui.</span>
                                    </span>
                                    <svg width="16" height="16" fill="none" stroke="#cbd5e1" stroke-width="2.5" viewBox="0 0 24 24" style="margin-top:2px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                                </a>
                                <a href="{{ route('freelancers.search') }}"
                                   style="display:flex;align-items:flex-start;gap:.875rem;padding:.75rem .875rem;border-radius:.875rem;text-decoration:none;transition:background .15s;margin-bottom:.375rem;"
                                   onmouseover="this.style.background='#f0f7ff'" onmouseout="this.style.background='transparent'">
                                    <span style="width:40px;height:40px;border-radius:10px;background:#d1fae5;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <svg width="20" height="20" fill="none" stroke="#059669" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </span>
                                    <span style="flex:1;">
                                        <span style="display:block;font-weight:700;color:#0f172a;font-size:.875rem;">Por localização</span>
                                        <span style="display:block;font-size:.775rem;color:#64748b;margin-top:.2rem;line-height:1.5;">Pesquise profissionais com base na sua localização e fuso horário.</span>
                                    </span>
                                    <svg width="16" height="16" fill="none" stroke="#cbd5e1" stroke-width="2.5" viewBox="0 0 24 24" style="margin-top:2px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                                </a>
                                <a href="{{ route('freelancers.index') }}"
                                   style="display:flex;align-items:flex-start;gap:.875rem;padding:.75rem .875rem;border-radius:.875rem;text-decoration:none;transition:background .15s;"
                                   onmouseover="this.style.background='#f0f7ff'" onmouseout="this.style.background='transparent'">
                                    <span style="width:40px;height:40px;border-radius:10px;background:#ede9fe;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <svg width="20" height="20" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                    </span>
                                    <span style="flex:1;">
                                        <span style="display:block;font-weight:700;color:#0f172a;font-size:.875rem;">Por categoria</span>
                                        <span style="display:block;font-size:.775rem;color:#64748b;margin-top:.2rem;line-height:1.5;">Encontre profissionais que se encaixam numa categoria de projeto.</span>
                                    </span>
                                    <svg width="16" height="16" fill="none" stroke="#cbd5e1" stroke-width="2.5" viewBox="0 0 24 24" style="margin-top:2px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                                </a>
                            </div>
                            <!-- Coluna direita: cards de categorias -->
                            <div style="flex:1;padding:1.5rem 1.25rem;">
                                <p style="font-size:.68rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin:0 0 1rem .25rem;">Categorias populares</p>
                                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.625rem;">
                                    <a href="{{ route('freelancers.search', ['skill' => 'design']) }}"
                                       style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .15s,box-shadow .15s;"
                                       onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 24px rgba(99,102,241,.25)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                        <div style="height:78px;background:linear-gradient(135deg,#6366f1 0%,#a78bfa 100%);display:flex;align-items:center;justify-content:center;position:relative;">
                                            <svg width="32" height="32" fill="none" stroke="rgba(255,255,255,.7)" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                        <div style="background:#f8fafc;padding:.45rem .6rem;font-size:.75rem;font-weight:700;color:#1e293b;">Designers Gráficos</div>
                                    </a>
                                    <a href="{{ route('freelancers.search', ['skill' => 'web']) }}"
                                       style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .15s,box-shadow .15s;"
                                       onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 24px rgba(14,165,233,.25)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                        <div style="height:78px;background:linear-gradient(135deg,#0ea5e9 0%,#0284c7 100%);display:flex;align-items:center;justify-content:center;">
                                            <svg width="32" height="32" fill="none" stroke="rgba(255,255,255,.7)" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                        </div>
                                        <div style="background:#f8fafc;padding:.45rem .6rem;font-size:.75rem;font-weight:700;color:#1e293b;">Dev. de Websites</div>
                                    </a>
                                    <a href="{{ route('freelancers.search', ['skill' => 'mobile']) }}"
                                       style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .15s,box-shadow .15s;"
                                       onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 24px rgba(16,185,129,.25)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                        <div style="height:78px;background:linear-gradient(135deg,#10b981 0%,#059669 100%);display:flex;align-items:center;justify-content:center;">
                                            <svg width="32" height="32" fill="none" stroke="rgba(255,255,255,.7)" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        </div>
                                        <div style="background:#f8fafc;padding:.45rem .6rem;font-size:.75rem;font-weight:700;color:#1e293b;">Apps Mobile</div>
                                    </a>
                                    <a href="{{ route('freelancers.search', ['skill' => 'video']) }}"
                                       style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .15s,box-shadow .15s;"
                                       onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 24px rgba(249,115,22,.25)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                        <div style="height:78px;background:linear-gradient(135deg,#f97316 0%,#ef4444 100%);display:flex;align-items:center;justify-content:center;">
                                            <svg width="32" height="32" fill="none" stroke="rgba(255,255,255,.7)" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.893L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        </div>
                                        <div style="background:#f8fafc;padding:.45rem .6rem;font-size:.75rem;font-weight:700;color:#1e293b;">Edição de Vídeo</div>
                                    </a>
                                    <a href="{{ route('freelancers.search', ['skill' => 'marketing']) }}"
                                       style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .15s,box-shadow .15s;"
                                       onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 24px rgba(236,72,153,.25)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                        <div style="height:78px;background:linear-gradient(135deg,#ec4899 0%,#db2777 100%);display:flex;align-items:center;justify-content:center;">
                                            <svg width="32" height="32" fill="none" stroke="rgba(255,255,255,.7)" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                                        </div>
                                        <div style="background:#f8fafc;padding:.45rem .6rem;font-size:.75rem;font-weight:700;color:#1e293b;">Marketing Digital</div>
                                    </a>
                                    <a href="{{ route('freelancers.search', ['skill' => 'redacao']) }}"
                                       style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .15s,box-shadow .15s;"
                                       onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 24px rgba(234,179,8,.25)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                        <div style="height:78px;background:linear-gradient(135deg,#eab308 0%,#d97706 100%);display:flex;align-items:center;justify-content:center;">
                                            <svg width="32" height="32" fill="none" stroke="rgba(255,255,255,.7)" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </div>
                                        <div style="background:#f8fafc;padding:.45rem .6rem;font-size:.75rem;font-weight:700;color:#1e293b;">Redação & Conteúdo</div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div x-data="{open:false}" class="relative">
                    <button @click="open = !open" class="nav-link" style="display:flex;align-items:center;gap:0.35rem;white-space:nowrap;">
                        Encontrar trabalho
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak class="absolute left-0 mt-2 w-56 bg-white rounded-lg py-2 z-50" style="text-align:left;">
                        <a href="{{ route('public.projects') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Projetos</a>
                        <a href="{{ route('public.projects') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Concursos</a>
                        <a href="{{ route('freelancer.available-projects') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Vagas</a>
                    </div>
                </div>
                <div x-data="{open:false}" class="relative">
                    <button @click="open = !open" class="nav-link" style="display:flex;align-items:center;gap:0.35rem;white-space:nowrap;">
                        Soluções
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak class="absolute left-0 mt-2 w-56 bg-white rounded-lg py-2 z-50" style="text-align:left;">
                        <a href="{{ route('sobre.investidores') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Empresas</a>
                        <a href="{{ route('sobre.sobre-nos') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Parceiros</a>
                        <a href="{{ route('sobre.como-funciona') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Ajuda</a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Direita: Botões -->
        <div class="header-actions">
            @guest
                <a href="/login" class="nav-link">Login</a>
                <a href="/register" class="nav-link">Cadastro</a>
                <a href="{{ route('client.projects') }}" class="ml-2 px-4 py-2 rounded-lg bg-[#ff2d55] text-white font-bold shadow hover:bg-[#e60039] transition hp-btn-pulse">Publicar projecto</a>
            @else
                <a href="{{ route('client.projects') }}" class="ml-2 px-4 py-2 rounded-lg bg-[#ff2d55] text-white font-bold shadow hover:bg-[#e60039] transition hp-btn-pulse">Publicar projecto</a>
                <div style="display:flex;align-items:center;gap:.75rem;">
                    @if(auth()->user()->activeRole() !== 'freelancer')
                        <a href="{{ route('notifications') }}" class="nav-link">Notificações</a>
                    @endif
                    @if(auth()->user()->canSwitchRole())
                        <form method="POST" action="{{ route('switch.role') }}" class="inline">
                            @csrf
                            <button type="submit" class="nav-link" style="display:flex;align-items:center;gap:.35rem;" title="Mudar para {{ auth()->user()->switchableRole() === 'freelancer' ? 'Freelancer' : 'Cliente' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                <span>{{ auth()->user()->switchableRole() === 'freelancer' ? 'Freelancer' : 'Cliente' }}</span>
                            </button>
                        </form>
                    @endif
                    <div x-data="{open:false}" class="relative">
                        <button @click="open = !open" class="nav-link" style="display:flex;align-items:center;gap:.5rem;margin-left:0;">
                            <img src="{{ auth()->user()->avatarUrl() }}" alt="{{ auth()->user()->name }}" class="avatar-sm" onerror="this.onerror=null;this.src='{{ asset('img/default-avatar.svg') }}';">
                            <span>{{ auth()->user()->name }}</span>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-lg py-2">
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
