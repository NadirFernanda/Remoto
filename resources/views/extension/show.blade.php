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

    <div class="js-pwa-installed-msg" style="background:#141928;border:1px solid rgba(16,185,129,.25);border-radius:1rem;padding:1.75rem;text-align:center;">
        <div style="font-size:2rem;margin-bottom:.5rem;display:flex;justify-content:center;align-items:center;">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5 9.2 16.7 19 6.9"/></svg>
        </div>
        <h2 style="font-size:1rem;font-weight:700;color:#f1f5f9;margin:0;">Já está instalada</h2>
        <p style="color:#94a3b8;font-size:.875rem;margin:.5rem 0 0;">Encontra o ícone no seu ecrã principal ou lista de aplicações.</p>
        <p style="margin:1rem 0 0;">
            <button type="button" onclick="localStorage.removeItem('pwa_installed');location.reload();"
                style="background:none;border:none;color:#64748b;font-size:.78rem;text-decoration:underline;cursor:pointer;font-family:inherit;">
                Já desinstalei — mostrar o botão de instalar outra vez
            </button>
        </p>
    </div>

    <div class="js-pwa-install-cta" style="background:#141928;border:1px solid rgba(255,255,255,.08);border-radius:1rem;padding:1.75rem;text-align:center;">
        @if($isIos)
            <div style="font-size:2rem;margin-bottom:.5rem;display:flex;justify-content:center;align-items:center;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="7" y="2.5" width="10" height="19" rx="2"/><path d="M10 18h4"/></svg>
            </div>
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
                Pode demorar alguns segundos a activar. Se não activar: no computador, use o ícone de
                instalação na barra de endereço (Chrome/Edge); no telemóvel, abra o menu (⋮) e toque em
                "Instalar aplicação" ou "Adicionar ao ecrã principal".
            </p>
            <script>
                (function () {
                    var btn = document.getElementById('pwa-install-btn');
                    var ready = false;

                    function enable() {
                        ready = true;
                        btn.disabled = false;
                        btn.style.opacity = '1';
                        btn.textContent = 'Instalar aplicação';
                    }

                    if (window.deferredPwaPrompt) enable();
                    window.addEventListener('pwa-install-available', enable);

                    btn.addEventListener('click', function () {
                        if (ready && window.deferredPwaPrompt) {
                            window.deferredPwaPrompt.prompt();
                            window.deferredPwaPrompt.userChoice.finally(function () {
                                window.deferredPwaPrompt = null;
                                btn.textContent = 'Instalado';
                        var ok = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                        ok.setAttribute('viewBox', '0 0 24 24');
                        ok.setAttribute('width', '18');
                        ok.setAttribute('height', '18');
                        ok.setAttribute('fill', 'none');
                        ok.setAttribute('stroke', 'currentColor');
                        ok.setAttribute('stroke-width', '2.2');
                        ok.setAttribute('stroke-linecap', 'round');
                        ok.setAttribute('stroke-linejoin', 'round');
                        ok.setAttribute('aria-hidden', 'true');
                        ok.innerHTML = '<path d="M5 12.5 9.2 16.7 19 6.9"/>';
                        btn.appendChild(ok);
                            });
                        } else if (!ready && btn.dataset.fallback === '1') {
                            window.location.reload();
                        }
                    });

                    // O Chrome pode demorar vários segundos a decidir que o site é
                    // instalável (regista o service worker, avalia critérios, etc.).
                    // Só ao fim de 8s — se ainda não activou — assumimos que vale a
                    // pena sugerir recarregar, em vez de desistir cedo demais.
                    setTimeout(function () {
                        if (!ready) {
                            btn.disabled = false;
                            btn.style.opacity = '1';
                            btn.textContent = 'Recarregar página';
                            btn.dataset.fallback = '1';
                        }
                    }, 8000);
                })();
            </script>
        @endif
    </div>

    <div class="js-pwa-install-cta" style="text-align:left;background:rgba(255,255,255,.03);border-radius:.75rem;padding:1.25rem 1.5rem;margin-top:1.25rem;">
        <p style="color:#94a3b8;font-size:.8rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;margin:0 0 .6rem;">Onde encontrar o ícone depois de instalar</p>
        <ul style="color:#cbd5e1;font-size:.85rem;line-height:1.8;margin:0;padding-left:1.1rem;">
            <li><strong>Computador (Windows):</strong> Menu Iniciar, ou afixe na Barra de Tarefas (clique direito no ícone e escolha "Afixar").</li>
            <li><strong>Computador (Mac):</strong> Launchpad ou pasta Aplicações.</li>
            <li><strong>Android:</strong> gaveta de aplicações ou ecrã principal, como qualquer app.</li>
            <li><strong>iPhone:</strong> ecrã principal, como qualquer app.</li>
        </ul>
        <p style="color:#64748b;font-size:.8rem;margin:.85rem 0 0;">
            A partir daí, abre directamente pelo ícone — não precisa de abrir o navegador nem de se lembrar do endereço do site.
        </p>
    </div>
</div>
@endsection
