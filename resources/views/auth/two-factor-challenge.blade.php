@extends('layouts.main')

@section('content')
<div class="pub-page" style="padding-top:0;display:flex;align-items:center;justify-content:center;">
    <div class="pub-container--sm" style="width:100%;padding-top:2rem;padding-bottom:3rem;">
        <div class="pub-auth-card" style="text-align:center;" x-data="{ useRecovery: false }">
            <a href="/" style="display:inline-block;margin-bottom:1.5rem;">
                <img src="{{ asset('img/logo.png') . '?v=' . filemtime(public_path('img/logo.png')) }}" alt="24 Horas" style="height:52px;object-fit:contain;filter:drop-shadow(0 0 10px rgba(0,186,255,.35));">
            </a>

            <h1 class="pub-auth-title">Verificação em dois passos</h1>
            <p class="pub-auth-sub" style="margin-bottom:1.75rem;">Introduza o código gerado pela sua app autenticadora.</p>

            @if($errors->any())
                <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:.75rem 1rem;color:#dc2626;font-size:.875rem;margin-bottom:1.25rem;">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('warning'))
                <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:.75rem 1rem;color:#b45309;font-size:.875rem;margin-bottom:1.25rem;">
                    {{ session('warning') }}
                </div>
            @endif

            <form method="POST" action="{{ route('2fa.challenge.verify') }}">
                @csrf
                <div x-show="!useRecovery">
                    <div class="pub-field">
                        <label for="code">Código de 6 dígitos</label>
                        <input type="text" name="code" id="code" maxlength="6" class="pub-input"
                            placeholder="000000" x-bind:required="!useRecovery" autofocus inputmode="numeric"
                            style="text-align:center;font-size:1.5rem;font-weight:900;letter-spacing:.4em;">
                    </div>
                </div>
                <div x-show="useRecovery" x-cloak>
                    <div class="pub-field">
                        <label for="recovery_code">Código de recuperação</label>
                        <input type="text" name="recovery_code" id="recovery_code" class="pub-input"
                            placeholder="XXXX-XXXX" x-bind:required="useRecovery"
                            style="text-align:center;font-size:1.1rem;font-weight:700;letter-spacing:.1em;">
                    </div>
                </div>
                <button type="submit" class="pub-btn-primary" style="width:100%;padding:.75rem;font-size:1rem;">Verificar</button>
            </form>

            <button type="button" @click="useRecovery = !useRecovery" style="background:none;border:none;color:#00baff;font-weight:700;font-size:.875rem;cursor:pointer;font-family:inherit;text-decoration:underline;margin-top:1.25rem;">
                <span x-show="!useRecovery">Usar código de recuperação</span>
                <span x-show="useRecovery" x-cloak>Usar código da app autenticadora</span>
            </button>
        </div>
    </div>
</div>
@endsection
