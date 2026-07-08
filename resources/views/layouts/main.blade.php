<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('img/logo.png') . '?v=' . filemtime(public_path('img/logo.png')) }}" sizes="any">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/pwa/icon-192.png') }}">
    <meta name="theme-color" content="#080d1a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="24 Horas">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>[x-cloak]{display:none!important}</style>
    <style>
        .pwa-installed .js-pwa-install-cta { display: none !important; }
        .js-pwa-installed-msg { display: none; }
        .pwa-installed .js-pwa-installed-msg { display: block; }
    </style>
    <script>
        // Aplica a classe o mais cedo possível (antes do body renderizar) para
        // esconder botões de instalação sem qualquer flash visual.
        (function () {
            var installed = localStorage.getItem('pwa_installed') === '1'
                || window.matchMedia('(display-mode: standalone)').matches
                || window.navigator.standalone === true;
            if (installed) {
                document.documentElement.classList.add('pwa-installed');
                localStorage.setItem('pwa_installed', '1');
            }
        })();

        // O evento beforeinstallprompt pode disparar assim que a página carrega —
        // o listener tem de estar registado ANTES disso, por isso fica aqui no
        // <head> e não junto aos outros scripts no fim do <body> (onde chegava
        // tarde demais e perdia o evento, mostrando sempre "Indisponível").
        //
        // O Chrome só dispara este evento quando a app NÃO está instalada — por
        // isso, se disparar, é prova de que uma desinstalação anterior aconteceu
        // de facto. Não há nenhum evento "appuninstalled" para nos avisar disso
        // directamente, por isso usamos este como sinal indirecto para limpar a
        // memória antiga (localStorage) que ficava presa em "já instalada".
        window.addEventListener('beforeinstallprompt', function (event) {
            event.preventDefault();
            window.deferredPwaPrompt = event;
            localStorage.removeItem('pwa_installed');
            document.documentElement.classList.remove('pwa-installed');
            window.dispatchEvent(new CustomEvent('pwa-install-available'));
        });
        window.addEventListener('appinstalled', function () {
            window.deferredPwaPrompt = null;
            localStorage.setItem('pwa_installed', '1');
            document.documentElement.classList.add('pwa-installed');
        });

        // Regista o service worker o mais cedo possível — o Chrome só considera
        // o site instalável (e só dispara beforeinstallprompt) depois de ter um
        // service worker activo, por isso registá-lo tarde atrasa tudo o resto.
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(function () {});
        }
    </script>
</head>
@php $routeName = optional(request()->route())->getName(); @endphp
<body class="site-theme {{ $routeName === 'profile.edit' ? 'profile-page' : '' }} {{ $routeName === 'home' ? 'homepage' : '' }}" style="height:100dvh;display:flex;flex-direction:column;overflow:hidden;background:#080d1a;">
    {{-- Banner de impersonation --}}
    @if(session('impersonating_admin_id'))
    <div style="position:fixed;top:0;left:0;right:0;z-index:99999;background:#dc2626;color:#fff;display:flex;align-items:center;justify-content:center;gap:16px;padding:10px 16px;font-size:13px;font-weight:600;font-family:Arial,sans-serif;">
        <span style="display:flex;align-items:center;gap:8px;">
            <svg style="width:16px;height:16px;flex-shrink:0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Modo Suporte — a visualizar como: <strong>{{ auth()->user()->name }}</strong>
        </span>
        <a href="{{ route('admin.impersonate.stop') }}" style="background:#fff;color:#dc2626;padding:4px 14px;border-radius:20px;font-size:12px;font-weight:700;text-decoration:none;">
            Sair e Voltar ao Admin
        </a>
    </div>
    <div style="height:46px"></div>
    @endif
    <!-- Barra de progresso de scroll — fixed no topo absoluto do viewport -->
    <div x-data="{progress:0}" x-init="(function(){let sc=document.getElementById('page-scroll');if(sc)sc.addEventListener('scroll',function(){let m=sc.scrollHeight-sc.clientHeight;progress=m>0?Math.round(sc.scrollTop/m*100):0;})})()" class="scroll-progress-bar" :style="'width:'+progress+'%'"></div>
    @include('components.header')
    <!-- Container de scroll: ocupa exactamente o espaço que resta após o header -->
    <div id="page-scroll" style="flex:1;min-height:0;overflow-y:scroll;overflow-x:hidden;display:flex;flex-direction:column;">
        <main class="@yield('main-padding', 'pt-0') flex-1" style="display:flex;flex-direction:column;@yield('main-style', '')">
            @include('components.flash-messages')
            @yield('content')
        </main>
        @unless(Route::currentRouteName() === 'extension.show')
        @include('components.footer')
        @endunless
        @include('components.cookie-consent')
    </div>
    @livewireScripts
    <script>
        // Detecção de bfcache: quando o browser restaura uma página a partir do cache
        // de navegação (back/forward), os snapshots Livewire já não existem no servidor.
        // Forçar reload garante que o componente é inicializado de novo.
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });

        document.addEventListener('alpine:init', () => {
            Alpine.store('sidebar', {
                open: false,
                toggle() { this.open = !this.open; },
                close() { this.open = false; }
            });
        });
    </script>
</body>
</html>
