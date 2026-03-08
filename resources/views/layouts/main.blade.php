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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
@php $routeName = optional(request()->route())->getName(); @endphp
<body class="site-theme min-h-screen flex flex-col {{ $routeName === 'profile.edit' ? 'profile-page' : '' }} {{ $routeName === 'home' ? 'homepage' : '' }}">
    @include('components.header')
    <main class="pt-24 flex-1">
        @include('components.flash-messages')
        @yield('content')
    </main>
    @include('components.footer')
    @livewireScripts
</body>
</html>
