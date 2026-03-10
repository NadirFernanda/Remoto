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
                <h1 class="pub-auth-title">Recuperar palavra-passe</h1>
                <p class="pub-auth-sub">Informe o seu e-mail para receber o link de redefinição.</p>
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

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="pub-field">
                    <label for="forgot-email">E-mail</label>
                    <input type="email" name="email" id="forgot-email" class="pub-input" placeholder="seu@email.com" value="{{ old('email') }}" required autofocus>
                </div>
                <button type="submit" class="pub-btn-primary" style="width:100%;padding:.75rem;font-size:1rem;margin-top:.5rem;">Enviar link de recuperação</button>
            </form>

            <p style="text-align:center;margin-top:1.5rem;font-size:.875rem;color:#64748b;">
                Lembrou a palavra-passe?
                <a href="/login" style="color:#00baff;font-weight:700;text-decoration:none;">Entrar</a>
            </p>
        </div>
    </div>
</div>
@endsection
