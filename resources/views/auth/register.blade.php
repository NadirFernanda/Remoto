@extends('layouts.main')

@section('content')
<div class="pub-page" style="display:flex;align-items:center;justify-content:center;">
    <div class="pub-container--sm" style="width:100%;padding-top:2rem;padding-bottom:3rem;">
        <div class="pub-auth-card">
            {{-- Logo + título --}}
            <div style="text-align:center;margin-bottom:1.75rem;">
                <a href="/" style="display:inline-block;margin-bottom:1.25rem;">
                    <img src="{{ asset('img/logo.png') }}" alt="24 Horas" style="height:56px;object-fit:contain;filter:drop-shadow(0 0 10px rgba(0,186,255,.35));">
                </a>
                <h1 id="titulo-cadastro" class="pub-auth-title">Criar conta como Freelancer</h1>
                <p class="pub-auth-sub">Preencha os dados para começar</p>
            </div>

            @if(session('status'))
                <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:.75rem 1rem;color:#16a34a;font-size:.875rem;margin-bottom:1.25rem;text-align:center;">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:.75rem 1rem;color:#dc2626;font-size:.875rem;margin-bottom:1.25rem;">
                    <ul style="margin:0;padding-left:1.2rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="/register" lang="pt">
                @csrf
                {{-- Role selector --}}
                <div class="pub-field">
                    <label>Quero me cadastrar como:</label>
                    <div style="display:flex;gap:1rem;margin-top:.4rem;">
                        <label style="display:flex;align-items:center;gap:.5rem;font-size:.9rem;font-weight:600;color:#0f172a;cursor:pointer;">
                            <input type="radio" name="role" value="freelancer" {{ old('role', 'freelancer') == 'freelancer' ? 'checked' : '' }} style="accent-color:#00baff;width:1rem;height:1rem;">
                            Freelancer
                        </label>
                        <label style="display:flex;align-items:center;gap:.5rem;font-size:.9rem;font-weight:600;color:#0f172a;cursor:pointer;">
                            <input type="radio" name="role" value="cliente" {{ old('role') == 'cliente' ? 'checked' : '' }} style="accent-color:#00baff;width:1rem;height:1rem;">
                            Cliente
                        </label>
                    </div>
                </div>

                <div class="pub-field">
                    <label for="name">Nome completo</label>
                    <input class="pub-input" type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Seu nome" required>
                    @error('name')<div class="pub-field-error">{{ $message }}</div>@enderror
                </div>

                <div class="pub-field">
                    <label for="email">E-mail</label>
                    <input class="pub-input" type="email" name="email" id="email" value="{{ old('email') }}" placeholder="seu@email.com" required>
                    @error('email')<div class="pub-field-error">{{ $message }}</div>@enderror
                </div>

                <div class="pub-field">
                    <label for="password">Senha</label>
                    <input class="pub-input" type="password" name="password" id="password" placeholder="••••••••" required>
                    @error('password')<div class="pub-field-error">{{ $message }}</div>@enderror
                </div>

                <div class="pub-field">
                    <label for="password_confirmation">Confirmar senha</label>
                    <input class="pub-input" type="password" name="password_confirmation" id="password_confirmation" placeholder="••••••••" required>
                    @error('password_confirmation')<div class="pub-field-error">{{ $message }}</div>@enderror
                </div>

                <button type="submit" id="botao-cadastro" class="pub-btn-primary" style="width:100%;padding:.75rem;font-size:1rem;margin-top:.25rem;">Cadastrar como Freelancer</button>
            </form>

            <p style="text-align:center;margin-top:1.5rem;font-size:.875rem;color:#64748b;">
                Já tem conta?
                <a href="/login" style="color:#00baff;font-weight:700;text-decoration:none;">Entrar</a>
            </p>
        </div>
    </div>
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
            tituloCadastro.textContent = 'Criar conta como Cliente';
        } else {
            submitButton.textContent = 'Cadastrar como Freelancer';
            tituloCadastro.textContent = 'Criar conta como Freelancer';
        }
    }
    freelancerRadio.addEventListener('change', updateCadastroUI);
    clientRadio.addEventListener('change', updateCadastroUI);
    updateCadastroUI();
});
</script>
@endsection
