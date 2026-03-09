@extends('layouts.main')

@section('content')
<div class="pub-page" style="display:flex;align-items:center;justify-content:center;">
    <div class="pub-container--sm" style="width:100%;padding-top:2rem;padding-bottom:3rem;">
        <div class="pub-auth-card">
            {{-- Logo + título --}}
            <div style="text-align:center;margin-bottom:2rem;">
                <a href="/" style="display:inline-block;margin-bottom:1.25rem;">
                    <img src="{{ asset('img/logo.png') }}" alt="24 Horas" style="height:56px;object-fit:contain;filter:drop-shadow(0 0 10px rgba(0,186,255,.35));">
                </a>
                <h1 class="pub-auth-title">Entrar na 24 Horas</h1>
                <p class="pub-auth-sub">Bem-vindo de volta! Acesse a sua conta.</p>
            </div>

            @if(session('status'))
                <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:.75rem 1rem;color:#16a34a;font-size:.875rem;margin-bottom:1.25rem;text-align:center;">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:.75rem 1rem;color:#dc2626;font-size:.875rem;margin-bottom:1.25rem;text-align:center;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="/login" novalidate onsubmit="return validateLoginForm(event)">
                @csrf
                <div class="pub-field">
                    <label for="login-email">E-mail</label>
                    <input type="email" name="email" id="login-email" class="pub-input" placeholder="seu@email.com" value="{{ old('email') }}" required autofocus>
                    <div id="email-error" class="pub-field-error" style="display:none;"></div>
                </div>
                <div class="pub-field">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.4rem;">
                        <label for="login-password" style="margin-bottom:0;">Senha</label>
                        <a href="{{ route('password.request') }}" style="font-size:.8rem;color:#00baff;font-weight:600;text-decoration:none;">Esqueci a minha senha</a>
                    </div>
                    <input type="password" name="password" id="login-password" class="pub-input" placeholder="••••••••" required>
                    <div id="password-error" class="pub-field-error" style="display:none;"></div>
                </div>
                <button type="submit" class="pub-btn-primary" style="width:100%;padding:.75rem;font-size:1rem;margin-top:.5rem;">Entrar</button>
            </form>

            <p style="text-align:center;margin-top:1.5rem;font-size:.875rem;color:#64748b;">
                Não tem conta?
                <a href="/register" style="color:#00baff;font-weight:700;text-decoration:none;">Criar conta grátis</a>
            </p>
        </div>
    </div>
</div>

<script>
function validateLoginForm(event) {
    let valid = true;
    const email = document.getElementById('login-email');
    const password = document.getElementById('login-password');
    const emailError = document.getElementById('email-error');
    const passwordError = document.getElementById('password-error');
    emailError.style.display = 'none';
    passwordError.style.display = 'none';
    email.style.borderColor = '';
    password.style.borderColor = '';
    if (!email.value) {
        emailError.textContent = 'Preencha o e-mail.';
        emailError.style.display = 'block';
        email.style.borderColor = '#dc2626';
        valid = false;
    } else if (!/^\S+@\S+\.\S+$/.test(email.value)) {
        emailError.textContent = 'Digite um e-mail válido.';
        emailError.style.display = 'block';
        email.style.borderColor = '#dc2626';
        valid = false;
    }
    if (!password.value) {
        passwordError.textContent = 'Preencha a senha.';
        passwordError.style.display = 'block';
        password.style.borderColor = '#dc2626';
        valid = false;
    }
    return valid;
}
</script>
@endsection
