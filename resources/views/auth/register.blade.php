@extends('layouts.main')

@section('content')
<div class="light-page min-h-screen pt-8 pb-12">
    <div class="container mx-auto px-4 py-8 flex flex-col items-center">
        <div class="w-full max-w-md bg-white rounded-lg shadow p-6">
            <button type="button" onclick="window.history.back()" class="mb-6 font-bold px-4 py-2 rounded transition shadow-lg bg-cyan-500 text-white">&larr; Voltar</button>
            <h1 id="titulo-cadastro" class="text-xl font-bold text-cyan-600 mb-4 text-center">Cadastro de Freelancer</h1>
            @if(session('status'))
                <div class="mb-4 p-2 bg-cyan-100 text-cyan-700 rounded text-center text-sm">
                    {{ session('status') }}
                </div>
            @endif
            <form method="POST" action="/register" lang="pt">
                @csrf
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Quero me cadastrar como:</label>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="role" value="freelancer" {{ old('role', 'freelancer') == 'freelancer' ? 'checked' : '' }}>
                            <span class="ml-2">Freelancer</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="role" value="cliente" {{ old('role') == 'cliente' ? 'checked' : '' }}>
                            <span class="ml-2">Cliente</span>
                        </label>
                    </div>
                    @error('role')
                        <div class="text-red-400 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block font-semibold mb-2" for="name">Nome</label>
                    <input class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none" type="text" name="name" id="name" value="{{ old('name') }}">
                    @error('name')
                        <div class="text-red-400 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block font-semibold mb-2" for="email">E-mail</label>
                    <input class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none" type="email" name="email" id="email" value="{{ old('email') }}">
                    @error('email')
                        <div class="text-red-400 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block font-semibold mb-2" for="password">Senha</label>
                    <input class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none" type="password" name="password" id="password">
                    @error('password')
                        <div class="text-red-400 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="block font-semibold mb-2" for="password_confirmation">Confirme a Senha</label>
                    <input class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none" type="password" name="password_confirmation" id="password_confirmation">
                    @error('password_confirmation')
                        <div class="text-red-400 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" id="botao-cadastro" class="w-full font-bold py-2 px-4 rounded transition bg-cyan-500 text-white">Cadastrar como Freelancer</button>
            </form>
            <div class="mt-6 text-center">
                <span class="text-gray-700">Já tem uma conta?</span>
                <a href="/login" class="text-cyan-500 font-bold hover:underline ml-1">Entrar</a>
            </div>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const freelancerRadio = document.querySelector('input[type="radio"][value="freelancer"]');
                const clientRadio = document.querySelector('input[type="radio"][value="cliente"]');
                const submitButton = document.getElementById('botao-cadastro');
                const tituloCadastro = document.getElementById('titulo-cadastro');
                function updateCadastroUI() {
                    if (clientRadio.checked) {
                        submitButton.textContent = 'Cadastrar como Cliente';
                        tituloCadastro.textContent = 'Cadastro de Cliente';
                    } else {
                        submitButton.textContent = 'Cadastrar como Freelancer';
                        tituloCadastro.textContent = 'Cadastro de Freelancer';
                    }
                }
                freelancerRadio.addEventListener('change', updateCadastroUI);
            clientRadio.addEventListener('change', updateCadastroUI);
            updateCadastroUI();
        });
        </script>
    </div>
</div>
@endsection
