@extends('layouts.main')

@section('content')
<div class="pub-page">
    <div class="pub-container--md" style="padding-top:2.5rem;padding-bottom:4rem;">

        <div class="pub-hero" style="margin-bottom:2.5rem;">
            <div class="pub-hero-label">Navegação</div>
            <h1 class="pub-hero-title">Mapa do site</h1>
            <p class="pub-hero-sub">Encontre rapidamente qualquer página e funcionalidade da plataforma Remoto.</p>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1.5rem;">

            <div class="pub-card">
                <h2 style="font-size:1rem;font-weight:800;color:#00baff;margin-bottom:.75rem;text-transform:uppercase;letter-spacing:.04em;">Público</h2>
                <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:.5rem;">
                    <li><a href="{{ route('home') }}" style="color:#475569;text-decoration:none;font-size:.9rem;">Início</a></li>
                    <li><a href="{{ route('public.projects') }}" style="color:#475569;text-decoration:none;font-size:.9rem;">Projetos disponíveis</a></li>
                    <li><a href="{{ route('freelancers.index') }}" style="color:#475569;text-decoration:none;font-size:.9rem;">Freelancers</a></li>
                    <li><a href="{{ route('freelancers.search') }}" style="color:#475569;text-decoration:none;font-size:.9rem;">Busca avançada</a></li>
                </ul>
            </div>

            <div class="pub-card">
                <h2 style="font-size:1rem;font-weight:800;color:#00baff;margin-bottom:.75rem;text-transform:uppercase;letter-spacing:.04em;">Conta</h2>
                <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:.5rem;">
                    <li><a href="/login" style="color:#475569;text-decoration:none;font-size:.9rem;">Entrar</a></li>
                    <li><a href="/register" style="color:#475569;text-decoration:none;font-size:.9rem;">Criar conta</a></li>
                    <li><a href="{{ route('dashboard') }}" style="color:#475569;text-decoration:none;font-size:.9rem;">Painel do utilizador</a></li>
                </ul>
            </div>

            <div class="pub-card">
                <h2 style="font-size:1rem;font-weight:800;color:#00baff;margin-bottom:.75rem;text-transform:uppercase;letter-spacing:.04em;">Sobre</h2>
                <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:.5rem;">
                    <li><a href="{{ route('sobre.sobre-nos') }}" style="color:#475569;text-decoration:none;font-size:.9rem;">Sobre nós</a></li>
                    <li><a href="{{ route('sobre.como-funciona') }}" style="color:#475569;text-decoration:none;font-size:.9rem;">Como funciona</a></li>
                    <li><a href="{{ route('sobre.seguranca') }}" style="color:#475569;text-decoration:none;font-size:.9rem;">Segurança</a></li>
                    <li><a href="{{ route('sobre.investidores') }}" style="color:#475569;text-decoration:none;font-size:.9rem;">Investidores</a></li>
                    <li><a href="{{ route('sobre.historias') }}" style="color:#475569;text-decoration:none;font-size:.9rem;">Histórias</a></li>
                    <li><a href="{{ route('sobre.noticias') }}" style="color:#475569;text-decoration:none;font-size:.9rem;">Notícias</a></li>
                    <li><a href="{{ route('sobre.equipe') }}" style="color:#475569;text-decoration:none;font-size:.9rem;">Equipe</a></li>
                    <li><a href="{{ route('sobre.premios') }}" style="color:#475569;text-decoration:none;font-size:.9rem;">Prêmios</a></li>
                    <li><a href="{{ route('sobre.comunicados') }}" style="color:#475569;text-decoration:none;font-size:.9rem;">Comunicados de imprensa</a></li>
                    <li><a href="{{ route('sobre.carreiras') }}" style="color:#475569;text-decoration:none;font-size:.9rem;">Carreiras</a></li>
                </ul>
            </div>

        </div>

    </div>
</div>
@endsection
