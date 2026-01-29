@extends('layouts.app')

@section('content')
<a href="/" class="fixed top-8 left-8 font-bold px-4 py-2 rounded transition shadow-lg z-50" style="margin-left: 40px; background: #00baff; color: #101c2c;">&larr; Voltar</a>
<div class="flex flex-col items-center justify-center min-h-screen pt-24 pb-12" style="background: #101c2c; color: #fff;">
    <div class="rounded-xl shadow-lg p-8 w-full max-w-md border border-cyan-200" style="margin-left: 56px; background: #101c2c; color: #fff;">
        <h1 class="text-2xl font-extrabold mb-6 text-center" style="color: #00baff;">Cadastro de Freelancer</h1>
        <form method="POST" action="/register">
            @csrf
            <input type="hidden" name="role" value="freelancer">
            <div class="mb-4">
                <label class="block font-bold mb-2" for="name" style="color: #00baff; background: none;">Nome</label>
                <input class="w-full px-3 py-2 border border-white rounded focus:outline-none focus:ring-2 focus:ring-cyan-400 bg-transparent" type="text" name="name" id="name" required>
            </div>
            <div class="mb-4">
                <label class="block font-bold mb-2" for="email" style="color: #00baff; background: none;">E-mail</label>
                <input class="w-full px-3 py-2 border border-white rounded focus:outline-none focus:ring-2 focus:ring-cyan-400 bg-transparent" type="email" name="email" id="email" required>
            </div>
            <div class="mb-4">
                <label class="block font-bold mb-2" for="password" style="color: #00baff; background: none;">Senha</label>
                <input class="w-full px-3 py-2 border border-white rounded focus:outline-none focus:ring-2 focus:ring-cyan-400 bg-transparent" type="password" name="password" id="password" required>
            </div>
            <div class="mb-6">
                <label class="block font-bold mb-2" for="password_confirmation" style="color: #00baff; background: none;">Confirme a Senha</label>
                <input class="w-full px-3 py-2 border border-white rounded focus:outline-none focus:ring-2 focus:ring-cyan-400 bg-transparent" type="password" name="password_confirmation" id="password_confirmation" required>
            </div>
            <button type="submit" class="w-full font-bold py-2 px-4 rounded transition animate-pulse" style="background: #00baff; color: #101c2c;">Cadastrar como Freelancer</button>
        </form>
        <div class="mt-6 text-center">
            <span class="text-gray-700">Já tem uma conta?</span>
            <a href="/login" class="text-cyan-500 font-bold hover:underline ml-1">Entrar</a>
        </div>
    </div>
</div>
@endsection
