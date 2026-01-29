<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background: #101c2c; color: #fff; min-height: 100vh;">
    @include('components.header')
    <main class="pt-24">
        @yield('content')
    </main>
    @include('components.footer')
</body>
</html>
