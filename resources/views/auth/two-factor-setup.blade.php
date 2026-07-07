@extends('layouts.main')

@section('content')
<div class="pub-page" style="padding-top:0;display:flex;align-items:center;justify-content:center;">
    <div class="pub-container--sm" style="width:100%;padding-top:2rem;padding-bottom:3rem;">
        <div class="pub-auth-card" style="text-align:center;">
            <a href="/" style="display:inline-block;margin-bottom:1.5rem;">
                <img src="{{ asset('img/logo.png') . '?v=' . filemtime(public_path('img/logo.png')) }}" alt="24 Horas" style="height:52px;object-fit:contain;filter:drop-shadow(0 0 10px rgba(0,186,255,.35));">
            </a>

            <h1 class="pub-auth-title">Configurar autenticação de dois factores</h1>
            <p class="pub-auth-sub" style="margin-bottom:1.5rem;">
                Obrigatório para todos os administradores. Leia o código QR com o Google Authenticator,
                Authy ou outra app compatível.
            </p>

            @if($errors->any())
                <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:.75rem 1rem;color:#dc2626;font-size:.875rem;margin-bottom:1.25rem;">
                    {{ $errors->first() }}
                </div>
            @endif

            <div style="background:#fff;border-radius:14px;padding:1.25rem;display:inline-block;margin-bottom:1.25rem;">
                {!! $qrSvg !!}
            </div>

            <p style="font-size:.8rem;color:#94a3b8;margin-bottom:.35rem;">Não consegue ler o código? Introduza a chave manualmente:</p>
            <p style="font-family:monospace;font-size:.9rem;font-weight:700;color:#0d1424;background:#f1f5f9;border-radius:8px;padding:.5rem .75rem;display:inline-block;letter-spacing:.05em;margin-bottom:1.5rem;word-break:break-all;">
                {{ $secret }}
            </p>

            <form method="POST" action="{{ route('2fa.setup.confirm') }}">
                @csrf
                <div class="pub-field">
                    <label for="code">Código de 6 dígitos</label>
                    <input type="text" name="code" id="code" maxlength="6" class="pub-input"
                        placeholder="000000" required autofocus inputmode="numeric"
                        style="text-align:center;font-size:1.5rem;font-weight:900;letter-spacing:.4em;">
                </div>
                <button type="submit" class="pub-btn-primary" style="width:100%;padding:.75rem;font-size:1rem;">Confirmar e activar</button>
            </form>
        </div>
    </div>
</div>
@endsection
