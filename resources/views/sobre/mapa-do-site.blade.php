@extends('layouts.main')

@section('content')
<div class="pub-page" style="padding-top:0">
    <div class="pub-container--md" style="padding-top:0.75rem;padding-bottom:4rem;">

        <div class="pub-hero" style="margin-bottom:2.5rem;">
            <div class="pub-hero-label">Navegação</div>
            <h1 class="pub-hero-title">Mapa do site</h1>
            <p class="pub-hero-sub">Encontre rapidamente qualquer página e funcionalidade da plataforma 24 Horas Remoto.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 border border-[#e6f3fa] transition hover:shadow-2xl">
                <h2 class="text-base font-extrabold text-[#00baff] mb-4 uppercase tracking-wide">Público</h2>
                <ul class="flex flex-col gap-2">
                    <li><a href="{{ route('home') }}" class="text-[#475569] text-base hover:text-[#00baff] transition">Início</a></li>
                    <li><a href="{{ route('public.projects') }}" class="text-[#475569] text-base hover:text-[#00baff] transition">Projectos disponíveis</a></li>
                    <li><a href="{{ route('freelancers.index') }}" class="text-[#475569] text-base hover:text-[#00baff] transition">Freelancers</a></li>
                    <li><a href="{{ route('freelancers.search') }}" class="text-[#475569] text-base hover:text-[#00baff] transition">Pesquisa avançada</a></li>
                </ul>
            </div>
            <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 border border-[#e6f3fa] transition hover:shadow-2xl">
                <h2 class="text-base font-extrabold text-[#00baff] mb-4 uppercase tracking-wide">Conta</h2>
                <ul class="flex flex-col gap-2">
                    <li><a href="/login" class="text-[#475569] text-base hover:text-[#00baff] transition">Entrar</a></li>
                    <li><a href="/register" class="text-[#475569] text-base hover:text-[#00baff] transition">Criar conta</a></li>
                    <li><a href="{{ route('dashboard') }}" class="text-[#475569] text-base hover:text-[#00baff] transition">Painel do utilizador</a></li>
                </ul>
            </div>
            <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 border border-[#e6f3fa] transition hover:shadow-2xl">
                <h2 class="text-base font-extrabold text-[#00baff] mb-4 uppercase tracking-wide">Sobre</h2>
                <ul class="flex flex-col gap-2">
                    <li><a href="{{ route('sobre.sobre-nos') }}" class="text-[#475569] text-base hover:text-[#00baff] transition">Sobre nós</a></li>
                    <li><a href="{{ route('sobre.como-funciona') }}" class="text-[#475569] text-base hover:text-[#00baff] transition">Como funciona</a></li>
                    <li><a href="{{ route('sobre.seguranca') }}" class="text-[#475569] text-base hover:text-[#00baff] transition">Segurança</a></li>
                    <li><a href="{{ route('sobre.investidores') }}" class="text-[#475569] text-base hover:text-[#00baff] transition">Investidores</a></li>
                    <li><a href="{{ route('sobre.historias') }}" class="text-[#475569] text-base hover:text-[#00baff] transition">Histórias</a></li>
                    <li><a href="{{ route('sobre.noticias') }}" class="text-[#475569] text-base hover:text-[#00baff] transition">Notícias</a></li>
                    <li><a href="{{ route('sobre.equipe') }}" class="text-[#475569] text-base hover:text-[#00baff] transition">Equipa</a></li>
                    <li><a href="{{ route('sobre.premios') }}" class="text-[#475569] text-base hover:text-[#00baff] transition">Prémios</a></li>
                    <li><a href="{{ route('sobre.comunicados') }}" class="text-[#475569] text-base hover:text-[#00baff] transition">Comunicados de imprensa</a></li>
                    <li><a href="{{ route('sobre.carreiras') }}" class="text-[#475569] text-base hover:text-[#00baff] transition">Carreiras</a></li>
                </ul>
            </div>
        </div>

    </div>
</div>
@endsection
