@extends('layouts.main')

@section('content')
<div class="pub-page" style="padding-top:0;display:flex;align-items:center;justify-content:center;">
    <div class="pub-container--sm" style="width:100%;padding-top:2rem;padding-bottom:3rem;">
        <div class="pub-auth-card" style="text-align:center;">
            <div style="width:64px;height:64px;border-radius:50%;background:#0b1220;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
            </div>

            <h1 class="pub-auth-title">Guarde os seus códigos de recuperação</h1>
            <p class="pub-auth-sub" style="margin-bottom:1.5rem;">
                Cada código só pode ser usado uma vez, caso perca o acesso à sua app autenticadora.
                Esta é a única vez que serão mostrados.
            </p>

            <div style="background:#0d1424;border-radius:12px;padding:1.25rem;margin-bottom:1.5rem;display:grid;grid-template-columns:1fr 1fr;gap:.6rem;">
                @foreach($codes as $code)
                    <span style="font-family:monospace;font-size:.95rem;font-weight:700;color:#e2e8f0;">{{ $code }}</span>
                @endforeach
            </div>

            <form method="POST" action="{{ route('2fa.recovery-codes.ack') }}">
                @csrf
                <button type="submit" class="pub-btn-primary" style="width:100%;padding:.75rem;font-size:1rem;">Já guardei os códigos</button>
            </form>
        </div>
    </div>
</div>
@endsection
