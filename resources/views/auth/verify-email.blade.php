@extends('layouts.main')

@section('content')
<div class="auth-status-page flex items-center justify-center min-h-screen px-4 py-8">
    <div class="auth-status-card p-6 sm:p-8 rounded-2xl shadow max-w-md w-full text-center">
        <h1 class="text-2xl font-bold mb-4">Verifique o seu e-mail</h1>
        <p class="mb-4">Antes de continuar, por favor verifique o seu endereço de e-mail clicando no link que lhe enviámos.</p>
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-primary w-full sm:w-auto">Reenviar e-mail de verificação</button>
        </form>
        @if (session('message'))
            <div class="mt-4 text-green-600 font-semibold">{{ session('message') }}</div>
        @endif
    </div>
</div>
@endsection
