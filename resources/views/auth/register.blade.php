@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-screen pt-24 pb-12" style="background: #101c2c; color: #fff;">
    <div class="bg-white/90 rounded-xl shadow-lg p-8 w-full max-w-md border border-cyan-200">
        <h1 class="text-2xl font-extrabold text-cyan-400 mb-6 text-center">Cadastro de Freelancer</h1>
        <form method="POST" action="/register">
            @csrf
            <input type="hidden" name="role" value="freelancer">
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="name">Nome</label>
                <input class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-cyan-400" type="text" name="name" id="name" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="email">E-mail</label>
                <input class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-cyan-400" type="email" name="email" id="email" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="password">Senha</label>
                <input class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-cyan-400" type="password" name="password" id="password" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2" for="password_confirmation">Confirme a Senha</label>
                <input class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-cyan-400" type="password" name="password_confirmation" id="password_confirmation" required>
            </div>
            <button type="submit" class="w-full bg-cyan-400 text-[#101c2c] font-bold py-2 px-4 rounded hover:bg-cyan-300 transition animate-pulse">Cadastrar como Freelancer</button>
        </form>
        <div class="mt-6 text-center">
            <span class="text-gray-700">Já tem uma conta?</span>
            <a href="/login" class="text-cyan-500 font-bold hover:underline ml-1">Entrar</a>
        </div>
    </div>
</div>
@endsection
