@extends('layouts.main')

@section('content')
<div style="max-width:760px;margin:0 auto;padding:2.5rem 1.25rem 4rem;color:#e2e8f0;">

    <div style="text-align:center;margin-bottom:1.75rem;">
        <img src="{{ asset('img/logo.png') }}" alt="" style="height:36px;margin-bottom:1rem;">
        <h1 style="font-size:1.5rem;font-weight:800;color:#f1f5f9;margin:0 0 .4rem;">Instalar aplicação</h1>
        <p style="color:#94a3b8;font-size:.9375rem;max-width:460px;margin:0 auto;line-height:1.5;">
            Acesso rápido ao painel, mensagens e notificações — sem downloads nem ficheiros,
            abre como uma aplicação normal.
        </p>
    </div>

    <div style="background:#141928;border:1px solid rgba(255,255,255,.08);border-radius:1rem;padding:1.75rem;text-align:center;">
        @if($isIos)
            <div style="font-size:2rem;margin-bottom:.5rem;">📲</div>
            <div style="text-align:left;background:rgba(255,255,255,.04);border-radius:.75rem;padding:1rem 1.25rem;">
                <p style="color:#cbd5e1;font-size:.875rem;line-height:1.7;margin:0;">
                    1. Toque no ícone <strong>Partilhar</strong>
                    <svg style="display:inline;vertical-align:-3px;" width="15" height="15" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v13m0-13l4 4m-4-4L8 7M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                    na barra do navegador.<br>
                    2. Toque em <strong>"Adicionar ao Ecrã Principal"</strong>.
                </p>
            </div>
        @else
            <button type="button" id="pwa-install-btn" disabled
                class="hp-btn-pulse"
                style="display:inline-flex;align-items:center;gap:.5rem;background:#0055ff;color:#fff;border:none;padding:.75rem 1.5rem;border-radius:10px;font-weight:700;font-size:.9rem;cursor:pointer;opacity:.5;">
                A preparar…
            </button>
            <p id="pwa-fallback-hint" style="color:#64748b;font-size:.78rem;margin-top:1rem;line-height:1.6;">
                Se o botão não activar: no computador, use o ícone de instalação na barra de endereço
                (Chrome/Edge); no telemóvel, abra o menu (⋮) e toque em "Instalar aplicação" ou
                "Adicionar ao ecrã principal".
            </p>
            <script>
                (function () {
                    var btn = document.getElementById('pwa-install-btn');

                    function enable() {
                        btn.disabled = false;
                        btn.style.opacity = '1';
                        btn.textContent = 'Instalar aplicação';
                    }

                    if (window.deferredPwaPrompt) enable();
                    window.addEventListener('pwa-install-available', enable);

                    btn.addEventListener('click', function () {
                        if (!window.deferredPwaPrompt) return;
                        window.deferredPwaPrompt.prompt();
                        window.deferredPwaPrompt.userChoice.finally(function () {
                            window.deferredPwaPrompt = null;
                            btn.textContent = 'Instalado ✓';
                        });
                    });

                    setTimeout(function () {
                        if (btn.disabled) btn.textContent = 'Indisponível neste navegador';
                    }, 1500);
                })();
            </script>
        @endif
    </div>
</div>
@endsection
