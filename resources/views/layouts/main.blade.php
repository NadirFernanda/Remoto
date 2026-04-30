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
    <link rel="icon" href="{{ asset('img/logo.png') }}" sizes="any">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>[x-cloak]{display:none!important}</style>
</head>
@php $routeName = optional(request()->route())->getName(); @endphp
<body class="site-theme h-screen overflow-hidden {{ $routeName === 'profile.edit' ? 'profile-page' : '' }} {{ $routeName === 'home' ? 'homepage' : '' }}">
    <!-- Barra de progresso de scroll — fixed ao viewport, fora do header -->
    <div x-data="{progress:0}" x-init="$nextTick(()=>{var sc=document.getElementById('page-scroll');if(sc)sc.addEventListener('scroll',function(){var max=sc.scrollHeight-sc.clientHeight;progress=max>0?Math.round(sc.scrollTop/max*100):0;})})" class="scroll-progress-bar" :style="'width:'+progress+'%'"></div>
    @include('components.header')
    <!-- Scroll container: começa abaixo do header, scrollbar só aparece nesta área -->
    <div id="page-scroll" style="position:fixed;top:70px;left:0;right:0;bottom:0;overflow-y:auto;display:flex;flex-direction:column;">
        <main class="@yield('main-padding', 'pt-0') flex-1" style="@yield('main-style', '')">
            @include('components.flash-messages')
            @yield('content')
        </main>
        @include('components.footer')
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
