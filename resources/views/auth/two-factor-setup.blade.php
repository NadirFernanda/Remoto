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
            <p style="font-family:monospace;font-size:.9rem;font-weight:700;color:#0d1424;background:#f1f5f9;border-radius:8px;padding:.5rem .75rem;display:inline-block;letter-spacing:.05em;margin-bottom:.75rem;word-break:break-all;">
                {{ $secret }}
            </p>

            <details style="text-align:left;margin:0 auto 1.5rem;max-width:420px;">
                <summary style="cursor:pointer;font-size:.8rem;color:#00baff;font-weight:600;">Não tem uma app autenticadora? Ver alternativas</summary>
                <div style="font-size:.8rem;color:#94a3b8;line-height:1.7;margin-top:.6rem;padding:.85rem 1rem;background:#f8fafc;border-radius:8px;">
                    <strong style="color:#334155;">Sem telemóvel — extensão do navegador:</strong> instale a extensão
                    "Authenticator" no Chrome/Edge, cole a chave manual acima e gera o código no computador.<br><br>
                    <strong style="color:#334155;">Já usa um gestor de senhas</strong> (Bitwarden, 1Password): a maioria
                    tem suporte a códigos deste tipo — cole a chave manual lá.<br><br>
                    <strong style="color:#334155;">Acesso ao servidor por SSH:</strong> instale <code style="background:#e2e8f0;padding:.05rem .3rem;border-radius:4px;">oathtool</code>
                    (<code style="background:#e2e8f0;padding:.05rem .3rem;border-radius:4px;">apt-get install oathtool</code>)
                    e corra <code style="background:#e2e8f0;padding:.05rem .3rem;border-radius:4px;">oathtool --totp -b "chave"</code>
                    para gerar o código no terminal.<br><br>
                    <strong style="color:#334155;">App no telemóvel</strong> (mais comum): Google Authenticator,
                    Microsoft Authenticator ou Authy — leia o código QR acima.
                </div>
            </details>

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
