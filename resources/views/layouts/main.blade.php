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
<body class="site-theme min-h-screen flex flex-col {{ $routeName === 'profile.edit' ? 'profile-page' : '' }} {{ $routeName === 'home' ? 'homepage' : '' }}">
    @include('components.header')
    <main class="@yield('main-padding', 'pt-[70px]') flex-1" style="@yield('main-style', '')">
        @include('components.flash-messages')
        @yield('content')
    </main>
    @include('components.footer')
    @include('components.cookie-consent')
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
