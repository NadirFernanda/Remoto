@extends('layouts.main')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded shadow max-w-md w-full text-center">
        <h1 class="text-2xl font-bold mb-4">Verifique seu e-mail</h1>
        <p class="mb-4">Antes de continuar, por favor verifique seu endereço de e-mail clicando no link que enviamos para você.</p>
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Reenviar e-mail de verificação</button>
        </form>
        @if (session('message'))
            <div class="mt-4 text-green-600 font-semibold">{{ session('message') }}</div>
        @endif
    </div>
</div>
@endsection
