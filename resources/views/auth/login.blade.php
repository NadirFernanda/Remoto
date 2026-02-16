@extends('layouts.main')

@section('content')
    <div class="min-h-screen flex items-center justify-center py-12">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-10">
            @if(session('status'))
                <div class="mb-4 p-2 bg-cyan-100 text-cyan-700 rounded text-center text-sm">
                    {{ session('status') }}
                </div>
            @endif
            <h2 class="text-3xl font-extrabold text-cyan-600 mb-8 text-center tracking-tight">Entrar na Plataforma</h2>
            <form method="POST" action="/login" class="space-y-6" novalidate onsubmit="return validateLoginForm(event)">
                @csrf
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">E-mail</label>
                    <input type="email" name="email" id="login-email" class="w-full border border-cyan-400 rounded-xl px-4 py-3 focus:ring-2 focus:ring-cyan-500 focus:outline-none text-gray-900 placeholder-gray-400 transition-all" placeholder="Seu e-mail" required autofocus>
                    <div id="email-error" class="text-red-600 text-sm mt-1 hidden"></div>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Senha</label>
                    <input type="password" name="password" id="login-password" class="w-full border border-cyan-400 rounded-xl px-4 py-3 focus:ring-2 focus:ring-cyan-500 focus:outline-none text-gray-900 placeholder-gray-400 transition-all" placeholder="Sua senha" required>
                    <div id="password-error" class="text-red-600 text-sm mt-1 hidden"></div>
                </div>
                @if($errors->any())
                    <div class="mb-2 p-2 bg-red-100 text-red-700 rounded text-center text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif
                <button type="submit" class="w-full bg-white text-[#003a5c] font-bold py-3 rounded-xl shadow-md border-2 border-cyan-500 hover:bg-cyan-50 transition-all duration-150 text-lg">Entrar</button>
            </form>
        </div>
    </div>

    <script>
    function validateLoginForm(event) {
        let valid = true;
        const email = document.getElementById('login-email');
        const password = document.getElementById('login-password');
        const emailError = document.getElementById('email-error');
        const passwordError = document.getElementById('password-error');
        emailError.classList.add('hidden');
        passwordError.classList.add('hidden');
        email.classList.remove('border-red-500');
        password.classList.remove('border-red-500');

        if (!email.value) {
            emailError.textContent = 'Por favor, preencha o campo de e-mail.';
            emailError.classList.remove('hidden');
            email.classList.add('border-red-500');
            valid = false;
        } else if (!/^\S+@\S+\.\S+$/.test(email.value)) {
            emailError.textContent = 'Digite um e-mail válido.';
            emailError.classList.remove('hidden');
            email.classList.add('border-red-500');
            valid = false;
        }
        if (!password.value) {
            passwordError.textContent = 'Por favor, preencha o campo de senha.';
            passwordError.classList.remove('hidden');
            password.classList.add('border-red-500');
            valid = false;
        }
        return valid;
    }
    </script>
@endsection
