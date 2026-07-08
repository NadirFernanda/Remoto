@extends('layouts.main')

@section('content')
<div style="max-width:760px;margin:0 auto;padding:2.5rem 1.25rem 4rem;color:#e2e8f0;">

    <div style="text-align:center;margin-bottom:1.75rem;">
        <img src="{{ asset('img/logo.png') }}" alt="" style="height:36px;margin-bottom:1rem;">
        <h1 style="font-size:1.5rem;font-weight:800;color:#f1f5f9;margin:0 0 .4rem;">Extensão de navegador</h1>
        <p style="color:#94a3b8;font-size:.9375rem;max-width:460px;margin:0 auto;line-height:1.5;">
            Acesso rápido ao painel, mensagens e notificações, sem abrir o site.
        </p>
    </div>

    @if($isMobile)
        <div style="background:#141928;border:1px solid rgba(255,255,255,.08);border-radius:1rem;padding:1.75rem;text-align:center;">
            <div style="font-size:2rem;margin-bottom:.5rem;">💻</div>
            <h2 style="font-size:1rem;font-weight:700;color:#f1f5f9;margin:0 0 .5rem;">Só funciona no computador</h2>
            <p style="color:#94a3b8;font-size:.875rem;line-height:1.6;margin:0 0 1.25rem;">
                Esta extensão instala-se no Chrome, Edge ou Brave do seu computador. Abra este link
                por lá para instalar.
            </p>
            <button type="button" onclick="navigator.clipboard.writeText('{{ url()->current() }}');this.textContent='Link copiado ✓'"
                style="display:inline-flex;align-items:center;gap:.5rem;background:#0055ff;color:#fff;border:none;padding:.65rem 1.25rem;border-radius:10px;font-weight:700;font-size:.875rem;cursor:pointer;">
                Copiar link
            </button>
        </div>
    @else
        <div style="text-align:center;margin-bottom:2rem;">
            <a href="{{ route('extension.download') }}"
               class="btn-primary hp-btn-pulse"
               style="font-size:1rem;padding:.75rem 1.5rem;">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/>
                </svg>
                Descarregar extensão (.zip)
            </a>
        </div>

        <div style="background:#141928;border:1px solid rgba(255,255,255,.08);border-radius:1rem;padding:1.5rem 1.75rem;">
            <h2 style="font-size:.95rem;font-weight:700;color:#f1f5f9;margin:0 0 .85rem;">Instalação (Chrome, Edge, Brave)</h2>
            <ol style="margin:0;padding-left:1.1rem;line-height:1.85;color:#cbd5e1;font-size:.9rem;">
                <li>Extraia o .zip descarregado numa pasta.</li>
                <li>Abra <code style="background:rgba(255,255,255,.08);padding:.1rem .4rem;border-radius:4px;">chrome://extensions</code> e active o <strong>Modo de desenvolvedor</strong>.</li>
                <li><strong>Carregar sem compactação</strong> → seleccione a pasta extraída.</li>
                <li>Abra a extensão e inicie sessão com o seu e-mail e palavra-passe.</li>
            </ol>
        </div>
    @endif
</div>
@endsection
