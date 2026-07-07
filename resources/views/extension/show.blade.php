@extends('layouts.main')

@section('content')
<div style="max-width:760px;margin:0 auto;padding:2.5rem 1.25rem 4rem;color:#e2e8f0;">

    <div style="text-align:center;margin-bottom:2rem;">
        <img src="{{ asset('img/logo.png') }}" alt="" style="height:40px;margin-bottom:1.25rem;">
        <h1 style="font-size:1.75rem;font-weight:800;color:#f1f5f9;margin:0 0 .5rem;">Extensão de navegador</h1>
        <p style="color:#94a3b8;font-size:1rem;max-width:520px;margin:0 auto;line-height:1.6;">
            Acesso rápido ao seu painel, mensagens e notificações — directamente do seu navegador,
            sem precisar de abrir o site.
        </p>
    </div>

    <div style="text-align:center;margin-bottom:2.5rem;">
        <a href="{{ route('extension.download') }}"
           class="btn-primary hp-btn-pulse"
           style="font-size:1.05rem;padding:.85rem 1.75rem;">
            <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/>
            </svg>
            Descarregar extensão (.zip)
        </a>
    </div>

    <div style="background:#141928;border:1px solid rgba(255,255,255,.08);border-radius:1rem;padding:1.75rem;">
        <h2 style="font-size:1.05rem;font-weight:700;color:#f1f5f9;margin:0 0 1rem;">Como instalar (Chrome / Edge / Brave)</h2>
        <ol style="margin:0;padding-left:1.25rem;line-height:2;color:#cbd5e1;">
            <li>Descarregue o ficheiro <strong>.zip</strong> acima e extraia-o para uma pasta no seu computador.</li>
            <li>Abra <code style="background:rgba(255,255,255,.08);padding:.1rem .4rem;border-radius:4px;">chrome://extensions</code> no seu navegador.</li>
            <li>Active o <strong>Modo de desenvolvedor</strong> (canto superior direito).</li>
            <li>Clique em <strong>Carregar sem compactação</strong> e seleccione a pasta extraída
                (<code style="background:rgba(255,255,255,.08);padding:.1rem .4rem;border-radius:4px;">site-freelancer-extensao</code>).</li>
            <li>Clique no ícone da extensão na barra do navegador, indique o endereço do site
                ({{ request()->getSchemeAndHttpHost() }}) e inicie sessão com a sua conta.</li>
        </ol>
    </div>

    <p style="text-align:center;color:#64748b;font-size:.82rem;margin-top:1.5rem;">
        Compatível com qualquer navegador baseado em Chromium (Chrome, Edge, Brave, Opera).
    </p>
</div>
@endsection
