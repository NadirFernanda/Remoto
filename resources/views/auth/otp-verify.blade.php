@extends('layouts.main')

@section('content')
<div class="pub-page" style="display:flex;align-items:center;justify-content:center;">
    <div class="pub-container--sm" style="width:100%;padding-top:2rem;padding-bottom:3rem;">
        <div class="pub-auth-card" style="text-align:center;">
            {{-- Logo --}}
            <a href="/" style="display:inline-block;margin-bottom:1.5rem;">
                <img src="{{ asset('img/logo.png') }}" alt="24 Horas" style="height:52px;object-fit:contain;filter:drop-shadow(0 0 10px rgba(0,186,255,.35));">
            </a>

            {{-- Ícone de e-mail --}}
            <div style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#e0f7ff,#b3eeff);display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#00baff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="4" width="20" height="16" rx="2"/>
                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                </svg>
            </div>

            <h1 class="pub-auth-title">Verificar e-mail</h1>
            <p class="pub-auth-sub" style="margin-bottom:1.75rem;">Introduza o código de 6 dígitos que enviámos para o seu e-mail.</p>

            @if($errors->any())
                <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:.75rem 1rem;color:#dc2626;font-size:.875rem;margin-bottom:1.25rem;">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('message'))
                <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:.75rem 1rem;color:#16a34a;font-size:.875rem;margin-bottom:1.25rem;">
                    {{ session('message') }}
                </div>
            @endif

            <form method="POST" action="{{ route('otp.verify') }}">
                @csrf
                <div class="pub-field">
                    <label for="otp-input">Código de verificação</label>
                    <input type="text" name="otp" id="otp-input" maxlength="6" class="pub-input"
                        placeholder="000000" required autofocus
                        style="text-align:center;font-size:1.5rem;font-weight:900;letter-spacing:.4em;">
                </div>
                <button type="submit" class="pub-btn-primary" style="width:100%;padding:.75rem;font-size:1rem;">Verificar código</button>
            </form>

            <form method="POST" action="{{ route('otp.send') }}" style="margin-top:1.25rem;">
                @csrf
                <button type="submit" style="background:none;border:none;color:#00baff;font-weight:700;font-size:.875rem;cursor:pointer;font-family:inherit;text-decoration:underline;">
                    Reenviar código
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
