@extends('layouts.main')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded shadow max-w-md w-full text-center">
        <h1 class="text-2xl font-bold mb-4">Verificação de Código</h1>
        <p class="mb-4">Digite o código de 6 dígitos enviado para seu e-mail.</p>
        <form method="POST" action="{{ route('otp.verify') }}">
            @csrf
            <input type="text" name="otp" maxlength="6" class="border rounded px-4 py-2 mb-4 w-full text-center" placeholder="Código OTP" required autofocus>
            @error('otp')
                <div class="text-red-600 mb-2">{{ $message }}</div>
            @enderror
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded w-full">Verificar</button>
        </form>
        <form method="POST" action="{{ route('otp.send') }}" class="mt-4">
            @csrf
            <button type="submit" class="text-blue-500 underline">Reenviar código</button>
        </form>
        @if (session('message'))
            <div class="mt-4 text-green-600 font-semibold">{{ session('message') }}</div>
        @endif
    </div>
</div>
@endsection
