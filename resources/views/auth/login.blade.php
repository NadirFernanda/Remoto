@extends('layouts.main')

@section('content')
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', system-ui, sans-serif; }

    .login-wrap {
        min-height: calc(100vh - 72px);
        display: flex;
    }

    /* ── LEFT PANEL ── */
    .login-left {
        flex: 0 0 50%;
        max-width: 560px;
        background: #fff;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 3rem 4rem;
    }
    .login-logo { height: 56px; object-fit: contain; margin-bottom: 2rem; }

    .login-headline { font-size: 1.85rem; font-weight: 900; color: #0a0f1e; line-height: 1.18; margin-bottom: .65rem; }
    .login-headline .grad {
        background: linear-gradient(135deg, #00c8ff 0%, #0055ff 60%, #0033cc 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .login-sub { color: #64748b; font-size: .95rem; line-height: 1.65; margin-bottom: 2rem; }
    .login-sub span { color: #0055ff; font-weight: 700; }

    .login-alert-error { background: #fef2f2; border: 1px solid #fca5a5; border-radius: 10px; padding: .75rem 1rem; color: #dc2626; font-size: .875rem; margin-bottom: 1.25rem; }
    .login-alert-ok    { background: #f0fdf4; border: 1px solid #86efac; border-radius: 10px; padding: .75rem 1rem; color: #16a34a; font-size: .875rem; margin-bottom: 1.25rem; }

    .lf-group { position: relative; margin-bottom: 1rem; }
    .lf-icon  { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
    .lf-input {
        width: 100%;
        padding: .88rem 1rem .88rem 2.8rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: .95rem;
        color: #1e293b;
        background: #f8fafc;
        outline: none;
        transition: border-color .18s, box-shadow .18s;
        font-family: inherit;
    }
    .lf-input:focus { border-color: #0055ff; box-shadow: 0 0 0 3px rgba(0,80,255,.12); background: #fff; }
    .lf-input.has-error { border-color: #dc2626; }
    .lf-eye  { position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #94a3b8; padding: 0; }
    .lf-error { color: #dc2626; font-size: .78rem; margin-top: .3rem; display: none; }
    .lf-error.show { display: block; }

    .lf-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    .lf-remember { display: flex; align-items: center; gap: .5rem; font-size: .875rem; color: #64748b; cursor: pointer; }
    .lf-remember input { width: 15px; height: 15px; accent-color: #0055ff; }
    .lf-forgot { font-size: .875rem; color: #0055ff; font-weight: 600; text-decoration: none; }
    .lf-forgot:hover { text-decoration: underline; }

    .lf-submit {
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, #00c8ff 0%, #0055ff 60%, #0033cc 100%);
        color: #fff;
        font-weight: 800;
        font-size: 1rem;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        letter-spacing: .01em;
        box-shadow: 0 6px 24px rgba(0,80,255,.3);
        transition: opacity .15s, transform .15s;
        font-family: inherit;
    }
    .lf-submit:hover { opacity: .88; transform: translateY(-1px); }

    .lf-register { text-align: center; margin-top: 1.75rem; font-size: .875rem; color: #94a3b8; }
    .lf-register a { color: #0055ff; font-weight: 700; text-decoration: none; }
    .lf-register a:hover { text-decoration: underline; }

    /* ── RIGHT PANEL ── */
    .login-right {
        flex: 1;
        position: relative;
        overflow: hidden;
        min-height: 400px;
        background-color: #060e24;
        background-size: cover;
        background-position: center 15%;
        background-repeat: no-repeat;
    }

    /* security badge */
    .lr-badge {
        position: absolute;
        right: 2rem;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
        text-align: center;
        background: rgba(5, 30, 100, .65);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border: 1px solid rgba(100, 160, 255, .28);
        border-radius: 18px;
        padding: 1.25rem 1.1rem;
        min-width: 120px;
    }
    .lr-badge-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: linear-gradient(135deg, #0055ff, #0033cc);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto .75rem;
    }
    .lr-badge-title { color: #fff; font-weight: 800; font-size: .95rem; margin-bottom: .35rem; }
    .lr-badge-sub   { color: rgba(255,255,255,.65); font-size: .72rem; line-height: 1.5; }

    /* ── RESPONSIVE ── */
    @media (max-width: 768px) {
        .login-wrap { flex-direction: column; }
        .login-left { flex: unset; max-width: 100%; padding: 2.5rem 1.75rem; }
        .login-right { min-height: 300px; height: 300px; }
        .lr-badge { right: 1rem; padding: .75rem; }
    }
</style>

<div class="login-wrap">

    {{-- ─── LEFT: form ─────────────────────────────────────────────────── --}}
    <div class="login-left">

        <a href="/">
            <img src="{{ asset('img/logo.png') . '?v=' . filemtime(public_path('img/logo.png')) }}"
                 alt="24 Horas" class="login-logo">
        </a>

        <h1 class="login-headline">
            GESTÃO <span class="grad">INTELIGENTE.</span><br>
            RESULTADOS <span class="grad">REAIS.</span>
        </h1>

        <p class="login-sub">
            A plataforma completa para impulsionar<br>
            sua empresa, <span>24 horas</span> por dia.
        </p>

        @if($errors->any())
            <div class="login-alert-error">{{ $errors->first() }}</div>
        @endif
        @if(session('status'))
            <div class="login-alert-ok">{{ session('status') }}</div>
        @endif

        <form method="POST" action="/login" novalidate id="login-form">
            @csrf

            {{-- Email / username --}}
            <div class="lf-group">
                <span class="lf-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </span>
                <input type="text" name="email" id="lf-email"
                       class="lf-input {{ $errors->has('email') ? 'has-error' : '' }}"
                       placeholder="Usuário ou e-mail"
                       value="{{ old('email') }}" required autofocus>
                <p class="lf-error {{ $errors->has('email') ? 'show' : '' }}" id="lf-email-err">
                    {{ $errors->first('email') }}
                </p>
            </div>

            {{-- Password --}}
            <div class="lf-group">
                <span class="lf-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0110 0v4"/>
                    </svg>
                </span>
                <input type="password" name="password" id="lf-pw"
                       class="lf-input {{ $errors->has('password') ? 'has-error' : '' }}"
                       placeholder="Palavra-passe"
                       style="padding-right:2.8rem;"
                       required>
                <button type="button" class="lf-eye" onclick="togglePw()" aria-label="Mostrar/ocultar senha">
                    <svg id="eye-show" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg id="eye-hide" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
                <p class="lf-error {{ $errors->has('password') ? 'show' : '' }}" id="lf-pw-err">
                    {{ $errors->first('password') }}
                </p>
            </div>

            {{-- Remember + Forgot --}}
            <div class="lf-row">
                <label class="lf-remember">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Lembrar-me
                </label>
                <a href="{{ route('password.request') }}" class="lf-forgot">Esqueceu sua senha?</a>
            </div>

            <button type="submit" class="lf-submit">Entrar</button>
        </form>

        <p class="lf-register">
            — Ainda não tem uma conta? <a href="/register">Cadastre-se</a> —
        </p>

    </div>

    {{-- ─── RIGHT: visual ───────────────────────────────────────────────── --}}
    <div class="login-right"
         style="background-image: url('{{ asset('img/login.jpeg') . '?v=' . filemtime(public_path('img/login.jpeg')) }}');">


        <div class="lr-badge">
            <div class="lr-badge-icon">
                <svg width="20" height="20" fill="#fff" viewBox="0 0 24 24">
                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                </svg>
            </div>
            <p class="lr-badge-title">Segurança</p>
            <p class="lr-badge-sub">Seus dados protegidos<br>com tecnologia de<br>ponta</p>
        </div>

    </div>

</div>

<script>
function togglePw() {
    var inp = document.getElementById('lf-pw');
    var show = document.getElementById('eye-show');
    var hide = document.getElementById('eye-hide');
    if (inp.type === 'password') {
        inp.type = 'text';
        show.style.display = 'none';
        hide.style.display = 'block';
    } else {
        inp.type = 'password';
        show.style.display = 'block';
        hide.style.display = 'none';
    }
}
</script>
@endsection
