@extends('layouts.main')

@section('content')
<style>
    * { box-sizing: border-box; }

    .reg-wrap {
        min-height: calc(100vh - 72px);
        display: flex;
    }

    /* ── LEFT PANEL ── */
    .reg-left {
        flex: 0 0 50%;
        max-width: 560px;
        background: #fff;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 2.5rem 4rem;
        overflow-y: auto;
    }
    .reg-headline { font-size: 1.65rem; font-weight: 900; color: #0a0f1e; line-height: 1.2; margin-bottom: .4rem; }
    .reg-headline .grad {
        background: linear-gradient(135deg, #00c8ff 0%, #0055ff 60%, #0033cc 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .reg-sub { color: #64748b; font-size: .9rem; margin-bottom: 1.5rem; }

    /* role selector */
    .reg-role-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem; margin-bottom: 1.25rem; }
    .reg-role-card {
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        padding: .9rem .75rem;
        cursor: pointer;
        text-align: center;
        transition: all .2s;
        background: #fff;
    }
    .reg-role-card.active {
        border-color: #0055ff;
        background: #f0f7ff;
        box-shadow: 0 0 0 3px rgba(0,80,255,.1);
    }
    .reg-role-icon { font-size: 1.5rem; margin-bottom: .35rem; }
    .reg-role-title { font-size: .83rem; font-weight: 700; color: #0f172a; line-height: 1.2; }
    .reg-role-title span { color: #0055ff; }
    .reg-role-desc { font-size: .68rem; color: #64748b; margin-top: .3rem; line-height: 1.4; }

    /* form fields */
    .lf-group { position: relative; margin-bottom: .9rem; }
    .lf-label { display: block; font-size: .82rem; font-weight: 600; color: #374151; margin-bottom: .35rem; }
    .lf-icon  { position: absolute; left: .9rem; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
    .lf-input {
        width: 100%;
        padding: .82rem 1rem .82rem 2.6rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: .9rem;
        color: #1e293b;
        background: #f8fafc;
        outline: none;
        transition: border-color .18s, box-shadow .18s;
        font-family: inherit;
    }
    .lf-input:focus { border-color: #0055ff; box-shadow: 0 0 0 3px rgba(0,80,255,.1); background: #fff; }
    .lf-input.has-error { border-color: #dc2626; }
    .lf-eye  { position: absolute; right: .9rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #94a3b8; padding: 0; }
    .lf-error { color: #dc2626; font-size: .78rem; margin-top: .3rem; display: none; }
    .lf-error.show { display: block; }

    .lf-submit {
        width: 100%;
        padding: .9rem;
        background: linear-gradient(135deg, #00c8ff 0%, #0055ff 60%, #0033cc 100%);
        color: #fff;
        font-weight: 800;
        font-size: 1rem;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        box-shadow: 0 6px 24px rgba(0,80,255,.3);
        transition: opacity .15s, transform .15s;
        font-family: inherit;
        margin-top: .5rem;
    }
    .lf-submit:hover { opacity: .88; transform: translateY(-1px); }

    .reg-login-link { text-align: center; margin-top: 1.25rem; font-size: .875rem; color: #94a3b8; }
    .reg-login-link a { color: #0055ff; font-weight: 700; text-decoration: none; }
    .reg-login-link a:hover { text-decoration: underline; }

    .login-alert-error { background: #fef2f2; border: 1px solid #fca5a5; border-radius: 10px; padding: .75rem 1rem; color: #dc2626; font-size: .875rem; margin-bottom: 1rem; }
    .login-alert-ok    { background: #f0fdf4; border: 1px solid #86efac; border-radius: 10px; padding: .75rem 1rem; color: #16a34a; font-size: .875rem; margin-bottom: 1rem; }

    /* ── RIGHT PANEL ── */
    .reg-right {
        flex: 1;
        position: relative;
        overflow: hidden;
        min-height: 400px;
        background: #060e24;
        display: flex;
        align-items: stretch;
    }
    .reg-right-content {
        flex: 1;
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 3rem 2.5rem 3rem 3rem;
        max-width: 55%;
    }
    .reg-right-img {
        position: absolute;
        right: 0;
        bottom: 0;
        top: 0;
        width: 52%;
        background-size: cover;
        background-position: center top;
        background-repeat: no-repeat;
    }
    /* blue glow orb */
    .reg-right::before {
        content: '';
        position: absolute;
        top: 10%;
        right: 30%;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(0,80,255,.25) 0%, transparent 70%);
        border-radius: 50%;
        z-index: 1;
    }

    .rr-logo { margin-bottom: 2rem; }
    .rr-logo img { height: 52px; object-fit: contain; filter: brightness(0) invert(1); }

    .rr-headline { font-size: 1.7rem; font-weight: 900; color: #fff; line-height: 1.22; margin-bottom: .75rem; }
    .rr-headline .rr-accent {
        background: linear-gradient(135deg, #00c8ff 0%, #0055ff 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .rr-desc { font-size: .88rem; color: rgba(255,255,255,.65); line-height: 1.65; margin-bottom: 2rem; }

    .rr-features { display: flex; flex-direction: column; gap: 1rem; margin-bottom: 2rem; }
    .rr-feature { display: flex; align-items: flex-start; gap: .85rem; }
    .rr-feature-icon {
        width: 36px; height: 36px; border-radius: 10px; flex-shrink: 0;
        background: rgba(0,80,255,.2); border: 1px solid rgba(0,80,255,.3);
        display: flex; align-items: center; justify-content: center;
    }
    .rr-feature-title { font-size: .875rem; font-weight: 700; color: #f1f5f9; margin-bottom: .15rem; }
    .rr-feature-text  { font-size: .78rem; color: rgba(255,255,255,.55); line-height: 1.5; }

    .rr-stats {
        display: flex; align-items: center; gap: .875rem;
        background: rgba(255,255,255,.07);
        border: 1px solid rgba(255,255,255,.1);
        border-radius: 14px; padding: .75rem 1rem;
    }
    .rr-stats-avatars { display: flex; }
    .rr-stats-avatars img {
        width: 30px; height: 30px; border-radius: 50%; border: 2px solid #060e24;
        object-fit: cover; margin-left: -8px;
    }
    .rr-stats-avatars img:first-child { margin-left: 0; }
    .rr-stats-text { font-size: .8rem; color: rgba(255,255,255,.8); font-weight: 600; line-height: 1.4; }
    .rr-stats-text span { color: #00c8ff; }

    /* ── RESPONSIVE ── */
    @media (max-width: 900px) {
        .reg-wrap { flex-direction: column; }
        .reg-left { flex: unset; max-width: 100%; padding: 2rem 1.75rem; }
        .reg-right { min-height: 260px; }
        .reg-right-content { max-width: 60%; padding: 2rem 1.5rem; }
    }
    @media (max-width: 600px) {
        .reg-right-content { max-width: 100%; }
        .reg-right-img { display: none; }
        .rr-headline { font-size: 1.35rem; }
    }
</style>

<div class="reg-wrap">

    {{-- ─── LEFT: form ────────────────────────────────────────────────── --}}
    <div class="reg-left">

        <h1 class="reg-headline">
            Criar conta como <span class="grad" id="titulo-role">freelancer.</span>
        </h1>
        <p class="reg-sub">Preencha os dados para começar</p>

        @if(session('status'))
            <div class="login-alert-ok">{{ session('status') }}</div>
        @endif
        @if($errors->any())
            <div class="login-alert-error">
                <ul style="margin:0;padding-left:1.2rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ url('/register') . (request('ref') ? '?ref=' . urlencode(request('ref')) : '') }}"
              novalidate
              x-data="{ role: '{{ old('role', 'freelancer') }}' }"
              onsubmit="return validateRegisterForm(event)">
            @csrf

            @if(request('ref'))
            <div style="display:flex;align-items:center;gap:.6rem;background:#f0fbff;border:1px solid #bae6fd;border-radius:10px;padding:.6rem .875rem;margin-bottom:.9rem;">
                <svg width="15" height="15" fill="none" stroke="#0284c7" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                <span style="font-size:.8rem;color:#0369a1;font-weight:600;">A registar com link de convite de afiliado</span>
            </div>
            @endif

            {{-- Role selector --}}
            <label style="display:block;font-size:.82rem;font-weight:600;color:#374151;margin-bottom:.5rem;">Quero registar-me como:</label>
            <div class="reg-role-grid">
                <label :class="role === 'freelancer' ? 'reg-role-card active' : 'reg-role-card'"
                       @click="role='freelancer'; document.getElementById('titulo-role').textContent='freelancer.'">
                    <input type="radio" name="role" value="freelancer" x-model="role" style="display:none;">
                    <div class="reg-role-icon">💼</div>
                    <div class="reg-role-title">Freelancer<br><span>Criador</span></div>
                    <div class="reg-role-desc">Ofereço serviços &amp; crio conteúdos</div>
                </label>
                <label :class="role === 'cliente' ? 'reg-role-card active' : 'reg-role-card'"
                       @click="role='cliente'; document.getElementById('titulo-role').textContent='cliente.'">
                    <input type="radio" name="role" value="cliente" x-model="role" style="display:none;">
                    <div class="reg-role-icon">🏢</div>
                    <div class="reg-role-title">Cliente<br><span>Seguidor</span></div>
                    <div class="reg-role-desc">Contrato serviços &amp; sigo criadores</div>
                </label>
            </div>
            @error('role')<div class="lf-error show">{{ $message }}</div>@enderror

            {{-- Nome --}}
            <div class="lf-group">
                <label class="lf-label" for="reg-name">Nome completo</label>
                <span class="lf-icon">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </span>
                <input class="lf-input {{ $errors->has('name') ? 'has-error' : '' }}"
                       type="text" name="name" id="reg-name"
                       value="{{ old('name') }}" placeholder="O seu nome completo">
                <p class="lf-error {{ $errors->has('name') ? 'show' : '' }}" id="name-error">{{ $errors->first('name') }}</p>
            </div>

            {{-- Email --}}
            <div class="lf-group">
                <label class="lf-label" for="reg-email">E-mail</label>
                <span class="lf-icon">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </span>
                <input class="lf-input {{ $errors->has('email') ? 'has-error' : '' }}"
                       type="email" name="email" id="reg-email"
                       value="{{ old('email') }}" placeholder="seu@email.com">
                <p class="lf-error {{ $errors->has('email') ? 'show' : '' }}" id="reg-email-error">{{ $errors->first('email') }}</p>
            </div>

            {{-- Palavra-passe --}}
            <div class="lf-group">
                <label class="lf-label" for="reg-password">Palavra-passe</label>
                <span class="lf-icon">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0110 0v4"/>
                    </svg>
                </span>
                <input class="lf-input {{ $errors->has('password') ? 'has-error' : '' }}"
                       type="password" name="password" id="reg-password"
                       placeholder="••••••••" style="padding-right:2.6rem;">
                <button type="button" class="lf-eye" onclick="togglePw('reg-password','eye-show-pw','eye-hide-pw')" aria-label="Mostrar/ocultar">
                    <svg id="eye-show-pw" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg id="eye-hide-pw" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
                <p class="lf-error {{ $errors->has('password') ? 'show' : '' }}" id="reg-password-error">{{ $errors->first('password') }}</p>
            </div>

            {{-- Confirmar palavra-passe --}}
            <div class="lf-group">
                <label class="lf-label" for="reg-password-confirm">Confirmar palavra-passe</label>
                <span class="lf-icon">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0110 0v4"/>
                    </svg>
                </span>
                <input class="lf-input {{ $errors->has('password_confirmation') ? 'has-error' : '' }}"
                       type="password" name="password_confirmation" id="reg-password-confirm"
                       placeholder="••••••••" style="padding-right:2.6rem;">
                <button type="button" class="lf-eye" onclick="togglePw('reg-password-confirm','eye-show-pwc','eye-hide-pwc')" aria-label="Mostrar/ocultar">
                    <svg id="eye-show-pwc" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg id="eye-hide-pwc" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
                <p class="lf-error {{ $errors->has('password_confirmation') ? 'show' : '' }}" id="reg-confirm-error">{{ $errors->first('password_confirmation') }}</p>
            </div>

            <button type="submit" class="lf-submit" id="botao-registo">Criar conta</button>
        </form>

        <p class="reg-login-link">
            Já tem conta? <a href="{{ route('login') }}">Entrar</a>
        </p>

    </div>

    {{-- ─── RIGHT: visual ──────────────────────────────────────────────── --}}
    <div class="reg-right">

        {{-- Woman image positioned right --}}
        <div class="reg-right-img"
             style="background-image: url('{{ asset('img/Assets_feminino_cadastro.png') . '?v=' . filemtime(public_path('img/Assets_feminino_cadastro.png')) }}');"></div>

        <div class="reg-right-content">

            <div class="rr-logo">
                <img src="{{ asset('img/logo.png') . '?v=' . filemtime(public_path('img/logo.png')) }}" alt="24 Horas">
            </div>

            <h2 class="rr-headline">
                Conectamos talentos<br>
                a <span class="rr-accent">oportunidades reais.</span>
            </h2>

            <p class="rr-desc">A plataforma completa para impulsionar<br>sua carreira, seus projectos e seus resultados.</p>

            <div class="rr-features">
                <div class="rr-feature">
                    <div class="rr-feature-icon">
                        <svg width="17" height="17" fill="none" stroke="#00c8ff" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="rr-feature-title">Seguro e confiável</p>
                        <p class="rr-feature-text">Seus dados protegidos com tecnologia de ponta.</p>
                    </div>
                </div>
                <div class="rr-feature">
                    <div class="rr-feature-icon">
                        <svg width="17" height="17" fill="none" stroke="#00c8ff" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="rr-feature-title">Rápido e eficiente</p>
                        <p class="rr-feature-text">Encontre oportunidades e comece a trabalhar em poucos cliques.</p>
                    </div>
                </div>
                <div class="rr-feature">
                    <div class="rr-feature-icon">
                        <svg width="17" height="17" fill="none" stroke="#00c8ff" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="rr-feature-title">Comunidade activa</p>
                        <p class="rr-feature-text">Milhares de profissionais e clientes conectados todos os dias.</p>
                    </div>
                </div>
            </div>

            <div class="rr-stats">
                <div class="rr-stats-avatars">
                    <img src="{{ asset('img/default-avatar.svg') }}" alt="">
                    <img src="{{ asset('img/default-avatar.svg') }}" alt="">
                    <img src="{{ asset('img/default-avatar.svg') }}" alt="">
                </div>
                <p class="rr-stats-text">
                    <span>+5.000</span> freelancers activos<br>
                    <span>+2.000</span> projectos publicados
                </p>
            </div>

        </div>
    </div>

</div>

<script>
function togglePw(inputId, showId, hideId) {
    var inp  = document.getElementById(inputId);
    var show = document.getElementById(showId);
    var hide = document.getElementById(hideId);
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

function validateRegisterForm(event) {
    let valid = true;
    const fields = [
        { id: 'reg-name',             errId: 'name-error',         empty: 'Preencha o seu nome completo.' },
        { id: 'reg-email',            errId: 'reg-email-error',    empty: 'Preencha o e-mail.' },
        { id: 'reg-password',         errId: 'reg-password-error', empty: 'Preencha a palavra-passe.' },
        { id: 'reg-password-confirm', errId: 'reg-confirm-error',  empty: 'Confirme a sua palavra-passe.' },
    ];
    fields.forEach(f => {
        var el = document.getElementById(f.id), err = document.getElementById(f.errId);
        err.classList.remove('show'); el.classList.remove('has-error');
    });
    fields.forEach(f => {
        var el = document.getElementById(f.id), err = document.getElementById(f.errId);
        if (!el.value.trim()) {
            err.textContent = f.empty; err.classList.add('show');
            el.classList.add('has-error');
            if (valid) el.focus();
            valid = false;
        }
    });
    var emailEl = document.getElementById('reg-email'), emailErr = document.getElementById('reg-email-error');
    if (emailEl.value.trim() && !/^\S+@\S+\.\S+$/.test(emailEl.value)) {
        emailErr.textContent = 'Introduza um e-mail válido.'; emailErr.classList.add('show');
        emailEl.classList.add('has-error');
        if (valid) emailEl.focus();
        valid = false;
    }
    var pw = document.getElementById('reg-password'), pwc = document.getElementById('reg-password-confirm');
    var pwErr = document.getElementById('reg-password-error'), pwcErr = document.getElementById('reg-confirm-error');
    if (pw.value && pw.value.length < 8) {
        pwErr.textContent = 'A palavra-passe deve ter no mínimo 8 caracteres.'; pwErr.classList.add('show');
        pw.classList.add('has-error');
        if (valid) pw.focus();
        valid = false;
    }
    if (pw.value && pwc.value && pw.value !== pwc.value) {
        pwcErr.textContent = 'As palavras-passe não coincidem.'; pwcErr.classList.add('show');
        pwc.classList.add('has-error');
        if (valid) pwc.focus();
        valid = false;
    }
    if (!valid) event.preventDefault();
    return valid;
}
</script>
@endsection
