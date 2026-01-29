<header class="w-full py-4 px-8 flex items-center justify-between border-b border-cyan-900 fixed top-0 left-0 z-50" style="background: #101c2c;">
    <style>
        .logo-text-24 {
            color: #00baff;
            font-weight: 900;
            font-size: 2rem;
            letter-spacing: 1px;
        }
        .logo-text-horas {
            background: #00baff;
            color: #fff;
            border-radius: 8px;
            padding: 2px 16px;
            font-weight: 700;
            font-size: 1.1rem;
            margin-left: 4px;
        }
        .nav-link {
            margin-left: 32px;
            font-size: 1.1rem;
            color: #fff;
            font-weight: 500;
            transition: color 0.2s;
        }
        .nav-link:hover {
            color: #00baff;
        }
    </style>
    <div class="flex items-center">
        <span class="logo-text-24">24</span>
        <span class="logo-text-horas">HORAS</span>
    </div>
    <nav class="flex items-center gap-2 md:gap-6 text-xs md:text-sm">
        <a href="#categorias" class="nav-link">Categorias</a>
        <a href="#servicos" class="nav-link">Serviços</a>
        <a href="#depoimentos" class="nav-link">Depoimentos</a>
        <a href="/login" class="nav-link">Login</a>
        <a href="/register" class="nav-link">Cadastro</a>
        <a href="/register?freelancer=1" class="nav-link bg-cyan-400 text-[#101c2c] rounded px-3 py-1 font-bold ml-2 hover:bg-cyan-300 transition animate-pulse">Torne-se freelancer</a>
    </nav>
</header>
