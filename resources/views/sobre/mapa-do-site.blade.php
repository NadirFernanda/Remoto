@extends('layouts.main')

@section('content')
<div class="pub-page" style="padding-top:0">
    <div class="pub-container--md" style="padding-top:0.75rem;padding-bottom:4rem;">

        <div class="pub-hero" style="margin-bottom:2.5rem;">
            <div class="pub-hero-label">Navegação</div>
            <h1 class="pub-hero-title">Mapa do site</h1>
            <p class="pub-hero-sub">Encontre rapidamente qualquer página e funcionalidade da plataforma 24 Horas Remoto.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
            <div class="rounded-3xl shadow-xl p-7 md:p-8 border border-white/10 transition hover:shadow-2xl" style="background:#0d1424;">
                <h2 class="text-base font-extrabold text-[#5b8cff] mb-4 uppercase tracking-wide">Público</h2>
                <ul class="flex flex-col gap-2">
                    <li><a href="{{ route('home') }}" class="text-[#cbd5e1] text-sm hover:text-white transition">Início</a></li>
                    <li><a href="{{ route('public.projects') }}" class="text-[#cbd5e1] text-sm hover:text-white transition">Projectos disponíveis</a></li>
                    <li><a href="{{ route('freelancers.index') }}" class="text-[#cbd5e1] text-sm hover:text-white transition">Freelancers</a></li>
                    <li><a href="{{ route('freelancers.search') }}" class="text-[#cbd5e1] text-sm hover:text-white transition">Pesquisa avançada</a></li>
                </ul>
            </div>
            <div class="rounded-3xl shadow-xl p-7 md:p-8 border border-white/10 transition hover:shadow-2xl" style="background:#0d1424;">
                <h2 class="text-base font-extrabold text-[#5b8cff] mb-4 uppercase tracking-wide">Conta</h2>
                <ul class="flex flex-col gap-2">
                    <li><a href="/login" class="text-[#cbd5e1] text-sm hover:text-white transition">Entrar</a></li>
                    <li><a href="/register" class="text-[#cbd5e1] text-sm hover:text-white transition">Criar conta</a></li>
                    <li><a href="{{ route('dashboard') }}" class="text-[#cbd5e1] text-sm hover:text-white transition">Painel do utilizador</a></li>
                </ul>
            </div>
            <div class="rounded-3xl shadow-xl p-7 md:p-8 border border-white/10 transition hover:shadow-2xl" style="background:#0d1424;">
                <h2 class="text-base font-extrabold text-[#5b8cff] mb-4 uppercase tracking-wide">Sobre</h2>
                <ul class="flex flex-col gap-2">
                    <li><a href="{{ route('sobre.sobre-nos') }}" class="text-[#cbd5e1] text-sm hover:text-white transition">Sobre nós</a></li>
                    <li><a href="{{ route('sobre.como-funciona') }}" class="text-[#cbd5e1] text-sm hover:text-white transition">Como funciona</a></li>
                    <li><a href="{{ route('sobre.seguranca') }}" class="text-[#cbd5e1] text-sm hover:text-white transition">Segurança</a></li>
                    <li><a href="{{ route('sobre.investidores') }}" class="text-[#cbd5e1] text-sm hover:text-white transition">Investidores</a></li>
                    <li><a href="{{ route('sobre.historias') }}" class="text-[#cbd5e1] text-sm hover:text-white transition">Histórias</a></li>
                    <li><a href="{{ route('sobre.noticias') }}" class="text-[#cbd5e1] text-sm hover:text-white transition">Notícias</a></li>
                    <li><a href="{{ route('sobre.equipe') }}" class="text-[#cbd5e1] text-sm hover:text-white transition">Equipa</a></li>
                    <li><a href="{{ route('sobre.premios') }}" class="text-[#cbd5e1] text-sm hover:text-white transition">Prémios</a></li>
                    <li><a href="{{ route('sobre.comunicados') }}" class="text-[#cbd5e1] text-sm hover:text-white transition">Comunicados de imprensa</a></li>
                    <li><a href="{{ route('sobre.carreiras') }}" class="text-[#cbd5e1] text-sm hover:text-white transition">Carreiras</a></li>
                </ul>
            </div>
        </div>

    </div>
</div>
@endsection
