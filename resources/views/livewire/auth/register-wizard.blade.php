<div>
<style>
    * { box-sizing: border-box; }

    .reg-wrap {
        min-height: calc(100vh - 72px);
        display: flex;
        align-items: stretch;
    }

    /* ── LEFT PANEL ── */
    .reg-left {
        flex: 0 0 50%;
        background: #fff;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        padding: 3.5rem 3.5rem 3rem;
        overflow-y: auto;
    }
    .reg-headline { font-size: 1.6rem; font-weight: 900; color: #0a0f1e; line-height: 1.2; margin-bottom: .4rem; }
    .reg-headline .grad {
        background: #0055ff;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .reg-sub { color: #64748b; font-size: .88rem; margin-bottom: 1.25rem; }

    /* step indicator */
    .reg-steps { display: flex; align-items: center; gap: .5rem; margin-bottom: 1.5rem; }
    .reg-step-dot {
        width: 26px; height: 26px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: .75rem; font-weight: 800; color: #94a3b8; background: #f1f5f9; border: 2px solid #e2e8f0;
        flex-shrink: 0;
    }
    .reg-step-dot.active { color: #fff; background: #0055ff; border-color: transparent; }
    .reg-step-dot.done { color: #fff; background: #16a34a; border-color: transparent; }
    .reg-step-label { font-size: .78rem; font-weight: 700; color: #64748b; }
    .reg-step-label.active { color: #0f172a; }
    .reg-step-line { flex: 1; height: 2px; background: #e2e8f0; }

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
        color: #1e293b !important;
        -webkit-text-fill-color: #1e293b !important;
        caret-color: #0055ff;
        background: #f8fafc;
        outline: none;
        transition: border-color .18s, box-shadow .18s;
        font-family: inherit;
    }
    .lf-input:focus { color: #1e293b !important; -webkit-text-fill-color: #1e293b !important; border-color: #0055ff; box-shadow: 0 0 0 3px rgba(0,80,255,.1); background: #fff; }
    .lf-input.has-error { border-color: #dc2626; }
    .lf-eye  { position: absolute; right: .9rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #94a3b8; padding: 0; }
    .lf-error { color: #dc2626; font-size: .78rem; margin-top: .3rem; display: none; }
    .lf-error.show { display: block; }

    .lf-submit {
        width: 100%;
        padding: .9rem;
        background: #0055ff;
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
    .lf-submit:disabled { opacity: .6; cursor: not-allowed; }

    .lf-back {
        width: 100%;
        padding: .75rem;
        background: #fff;
        color: #475569;
        font-weight: 700;
        font-size: .88rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        cursor: pointer;
        font-family: inherit;
        margin-top: .6rem;
    }
    .lf-back:hover { background: #f8fafc; }

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
        background: #0b1220;
        display: flex;
        align-items: flex-start;
    }
    .reg-right-content {
        width: 100%;
        position: relative;
        z-index: 2;
        padding: 3.5rem 3.5rem 3rem;
    }
    /* blue glow orbs */
    .reg-right::before {
        content: '';
        position: absolute;
        top: -10%;
        left: 20%;
        width: 380px;
        height: 380px;
        background: #0b1220;
        border-radius: 50%;
        z-index: 1;
        pointer-events: none;
    }
    .reg-right::after {
        content: '';
        position: absolute;
        bottom: -5%;
        right: 10%;
        width: 260px;
        height: 260px;
        background: #0b1220;
        border-radius: 50%;
        z-index: 1;
        pointer-events: none;
    }

    .rr-logo { margin-bottom: 1.75rem; }
    .rr-logo img { height: 48px; object-fit: contain; filter: brightness(0) invert(1); }

    .rr-badge {
        display: inline-flex; align-items: center; gap: .5rem;
        background: rgba(0,85,255,.15); border: 1px solid rgba(0,85,255,.32);
        border-radius: 50px; padding: .4rem .9rem;
        font-size: .75rem; font-weight: 700; color: #7da7ff;
        letter-spacing: .05em; text-transform: uppercase;
        margin-bottom: 1.25rem;
    }
    .rr-badge-dot { width: 6px; height: 6px; border-radius: 50%; background: #0055ff; }

    .rr-headline { font-size: 2rem; font-weight: 900; color: #fff; line-height: 1.18; margin-bottom: .75rem; }
    .rr-headline .rr-accent {
        background: #0055ff;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .rr-desc { font-size: .9rem; color: rgba(255,255,255,.55); line-height: 1.7; margin-bottom: 2rem; }

    .rr-features { display: flex; flex-direction: column; gap: 1.1rem; margin-bottom: 2rem; }
    .rr-feature { display: flex; align-items: flex-start; gap: 1rem; }
    .rr-feature-icon {
        width: 40px; height: 40px; border-radius: 12px; flex-shrink: 0;
        background: rgba(0,85,255,.18); border: 1px solid rgba(0,85,255,.32);
        display: flex; align-items: center; justify-content: center;
    }
    .rr-feature-title { font-size: .9rem; font-weight: 700; color: #f1f5f9; margin-bottom: .2rem; }
    .rr-feature-text  { font-size: .8rem; color: rgba(255,255,255,.45); line-height: 1.55; }

    .rr-divider { border: none; border-top: 1px solid rgba(255,255,255,.08); margin: 1.5rem 0; }

    .rr-stats {
        display: flex; align-items: center; gap: 1rem;
        background: rgba(255,255,255,.05);
        border: 1px solid rgba(255,255,255,.09);
        border-radius: 16px; padding: .9rem 1.1rem;
    }
    .rr-stats-avatars { display: flex; }
    .rr-stats-avatars img {
        width: 32px; height: 32px; border-radius: 50%; border: 2px solid #060e24;
        object-fit: cover; margin-left: -9px;
    }
    .rr-stats-avatars img:first-child { margin-left: 0; }
    .rr-stats-text { font-size: .82rem; color: rgba(255,255,255,.75); font-weight: 600; line-height: 1.45; }
    .rr-stats-text span { color: #7da7ff; }

    /* Keep the registration form readable when global theme rules are loaded. */
    .reg-wrap .reg-left {
        color: #0f172a;
    }
    .reg-wrap .reg-left .lf-label,
    .reg-wrap .reg-left > form > label {
        color: #334155 !important;
    }
    .reg-wrap .reg-left .reg-step-label {
        color: #64748b !important;
    }
    .reg-wrap .reg-left .reg-step-label.active {
        color: #0f172a !important;
    }
    .reg-wrap .reg-left .lf-icon,
    .reg-wrap .reg-left .lf-eye {
        color: #0055ff !important;
    }
    .reg-wrap .reg-left .lf-group > .lf-icon,
    .reg-wrap .reg-left .lf-group > .lf-eye {
        top: 4rem;
        z-index: 3;
    }
    .reg-wrap .reg-left .lf-input {
        color: #1e293b !important;
        -webkit-text-fill-color: #1e293b !important;
        caret-color: #0055ff !important;
        background: #fff !important;
        border-color: #dbe3ef !important;
    }
    .reg-wrap .reg-left .lf-input::placeholder {
        color: #64748b !important;
        opacity: 1;
    }
    .reg-wrap .reg-left .lf-input:focus {
        color: #1e293b !important;
        -webkit-text-fill-color: #1e293b !important;
        border-color: #0055ff !important;
        box-shadow: 0 0 0 3px rgba(0,85,255,.12) !important;
    }
    .reg-wrap .reg-left .reg-role-icon svg {
        color: #0055ff !important;
        stroke: #0055ff !important;
    }
    .reg-wrap .reg-left .lf-submit {
        min-height: 3.25rem;
        border-radius: 12px;
        background: #0055ff !important;
        box-shadow: 0 8px 20px rgba(0,85,255,.2);
    }
    .reg-wrap .reg-left .lf-submit:hover {
        background: #0044dd !important;
        opacity: 1;
    }
    .reg-wrap .reg-left .reg-login-link a {
        color: #0055ff !important;
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 960px) {
        .reg-wrap { flex-direction: column; }
        .reg-left  { flex: unset; padding: 2.5rem 2rem; }
        .reg-right { min-height: 320px; }
        .reg-right-content { padding: 2.5rem 2rem; }
        .rr-headline { font-size: 1.6rem; }
    }
    @media (max-width: 600px) {
        .reg-left { padding: 2rem 1.5rem; }
        .reg-right-content { padding: 2rem 1.5rem; }
        .rr-headline { font-size: 1.35rem; }
    }
</style>

<div class="reg-wrap">

    {{-- ─── LEFT: form ────────────────────────────────────────────────── --}}
    <div class="reg-left">

        <h1 class="reg-headline">
            @if($step === 1)
                Criar conta como <span class="grad">{{ $role === 'freelancer' ? 'freelancer.' : 'cliente.' }}</span>
            @else
                Validar a sua <span class="grad">identidade.</span>
            @endif
        </h1>
        <p class="reg-sub">
            @if($step === 1)
                Preencha os dados para começar
            @else
                Último passo — envie um documento para activar a sua conta
            @endif
        </p>

        {{-- Indicador de passos --}}
        <div class="reg-steps">
            <div class="reg-step-dot {{ $step === 1 ? 'active' : 'done' }}">
                @if($step === 1)
                    1
                @else
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 13l4 4L19 3"/></svg>
                @endif
            </div>
            <span class="reg-step-label {{ $step === 1 ? 'active' : '' }}">Dados da conta</span>
            <div class="reg-step-line"></div>
            <div class="reg-step-dot {{ $step === 2 ? 'active' : '' }}">2</div>
            <span class="reg-step-label {{ $step === 2 ? 'active' : '' }}">Identidade</span>
        </div>

        @if(session('status'))
            <div class="login-alert-ok">{{ session('status') }}</div>
        @endif

        @if($step === 1)
            {{-- ═══ PASSO 1: Dados da conta ═══ --}}
            @error('email')
                <div class="login-alert-error" x-data x-init="$el.scrollIntoView({behavior:'smooth', block:'center'})" style="display:flex;align-items:center;gap:.5rem;">
                    <svg width="16" height="16" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                    {{ $message }}
                </div>
            @enderror
            <form wire:submit.prevent="nextStep">

                {{-- Role selector --}}
                <label style="display:block;font-size:.82rem;font-weight:600;color:#374151;margin-bottom:.5rem;">Quero registar-me como:</label>
                <div class="reg-role-grid">
                    <label class="reg-role-card {{ $role === 'freelancer' ? 'active' : '' }}" wire:click="$set('role', 'freelancer')">
                        <input type="radio" style="display:none;">
                        <div class="reg-role-icon">
                            @include('components.icon', ['name' => 'briefcase', 'class' => 'w-6 h-6'])
                        </div>
                        <div class="reg-role-title">Freelancer<br><span>Criador</span></div>
                        <div class="reg-role-desc">Ofereço serviços &amp; crio conteúdos</div>
                    </label>
                    <label class="reg-role-card {{ $role === 'cliente' ? 'active' : '' }}" wire:click="$set('role', 'cliente')">
                        <input type="radio" style="display:none;">
                        <div class="reg-role-icon">
                            @include('components.icon', ['name' => 'store', 'class' => 'w-6 h-6'])
                        </div>
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
                    <input class="lf-input @error('name') has-error @enderror"
                           type="text" id="reg-name" wire:model="name" placeholder="O seu nome completo">
                    @error('name') <p class="lf-error show">{{ $message }}</p> @enderror
                </div>

                {{-- Email --}}
                <div class="lf-group">
                    <label class="lf-label" for="reg-email">E-mail</label>
                    <span class="lf-icon">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    <input class="lf-input @error('email') has-error @enderror"
                           type="email" id="reg-email" wire:model="email" placeholder="seu@email.com">
                    @error('email') <p class="lf-error show">{{ $message }}</p> @enderror
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
                    <input class="lf-input @error('password') has-error @enderror"
                           type="password" id="reg-password" wire:model="password"
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
                    @error('password') <p class="lf-error show">{{ $message }}</p> @enderror
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
                    <input class="lf-input @error('password_confirmation') has-error @enderror"
                           type="password" id="reg-password-confirm" wire:model="password_confirmation"
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
                    @error('password_confirmation') <p class="lf-error show">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="lf-submit" wire:loading.attr="disabled" wire:target="nextStep">
                    <span wire:loading.remove wire:target="nextStep">Seguinte — validar identidade</span>
                    <span wire:loading wire:target="nextStep">A verificar...</span>
                </button>
            </form>

            <p class="reg-login-link">
                Já tem conta? <a href="{{ route('login') }}">Entrar</a>
            </p>
        @else
            {{-- ═══ PASSO 2: Verificação de identidade (KYC) ═══ --}}
            <form wire:submit.prevent="submit" enctype="multipart/form-data">

                <div class="lf-group">
                    <label class="lf-label">Tipo de documento</label>
                    <select wire:model="documentType" class="lf-input" style="padding-left:1rem;">
                        <option value="bi">Bilhete de Identidade (BI)</option>
                        <option value="passport">Passaporte</option>
                        <option value="driving_license">Carta de condução</option>
                    </select>
                    @error('documentType') <p class="lf-error show">{{ $message }}</p> @enderror
                </div>

                <div class="lf-group">
                    <label class="lf-label" for="reg-document-number">Número do documento</label>
                    <input class="lf-input @error('documentNumber') has-error @enderror"
                           type="text" id="reg-document-number" wire:model="documentNumber"
                           style="padding-left:1rem;" placeholder="Ex.: 003456789LA042">
                    <p style="font-size:.72rem;color:#94a3b8;margin-top:.3rem;">Usado apenas para impedir contas duplicadas — não é partilhado publicamente.</p>
                    @error('documentNumber') <p class="lf-error show">{{ $message }}</p> @enderror
                </div>

                <div class="lf-group">
                    <label class="lf-label">Frente do documento</label>
                    <div x-data="{ nome: 'Nenhum ficheiro selecionado' }" style="display:flex;align-items:center;gap:.6rem;">
                        <label style="cursor:pointer;display:inline-flex;align-items:center;gap:.4rem;background:#f0f7ff;color:#0055ff;font-weight:700;font-size:.82rem;padding:.55rem .9rem;border-radius:8px;white-space:nowrap;">
                            Escolher ficheiro
                            <input type="file" wire:model="documentFront" accept="image/*,.pdf" style="display:none;"
                                   @change="nome = $event.target.files[0]?.name ?? 'Nenhum ficheiro selecionado'">
                        </label>
                        <span style="font-size:.78rem;color:#64748b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:180px;" x-text="nome"></span>
                    </div>
                    <p style="font-size:.72rem;color:#94a3b8;margin-top:.3rem;">JPG, PNG ou PDF · máx. 10MB</p>
                    @error('documentFront') <p class="lf-error show">{{ $message }}</p> @enderror
                    @if($documentFront) <p style="font-size:.72rem;color:#16a34a;margin-top:.2rem;display:flex;align-items:center;gap:.35rem;"><svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 13l4 4L19 3"/></svg>{{ $documentFront->getClientOriginalName() }}</p> @endif
                </div>

                <div class="lf-group">
                    <label class="lf-label">Verso do documento</label>
                    <div x-data="{ nome: 'Nenhum ficheiro selecionado' }" style="display:flex;align-items:center;gap:.6rem;">
                        <label style="cursor:pointer;display:inline-flex;align-items:center;gap:.4rem;background:#f0f7ff;color:#0055ff;font-weight:700;font-size:.82rem;padding:.55rem .9rem;border-radius:8px;white-space:nowrap;">
                            Escolher ficheiro
                            <input type="file" wire:model="documentBack" accept="image/*,.pdf" style="display:none;"
                                   @change="nome = $event.target.files[0]?.name ?? 'Nenhum ficheiro selecionado'">
                        </label>
                        <span style="font-size:.78rem;color:#64748b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:180px;" x-text="nome"></span>
                    </div>
                    <p style="font-size:.72rem;color:#94a3b8;margin-top:.3rem;">JPG, PNG ou PDF · máx. 10MB</p>
                    @error('documentBack') <p class="lf-error show">{{ $message }}</p> @enderror
                    @if($documentBack) <p style="font-size:.72rem;color:#16a34a;margin-top:.2rem;display:flex;align-items:center;gap:.35rem;"><svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 13l4 4L19 3"/></svg>{{ $documentBack->getClientOriginalName() }}</p> @endif
                </div>

                <div class="lf-group">
                    <label class="lf-label">Selfie com o documento <span style="color:#94a3b8;font-weight:400;">(opcional mas recomendado)</span></label>
                    <div x-data="{ nome: 'Nenhum ficheiro selecionado' }" style="display:flex;align-items:center;gap:.6rem;">
                        <label style="cursor:pointer;display:inline-flex;align-items:center;gap:.4rem;background:#f0f7ff;color:#0055ff;font-weight:700;font-size:.82rem;padding:.55rem .9rem;border-radius:8px;white-space:nowrap;">
                            Escolher ficheiro
                            <input type="file" wire:model="selfie" accept="image/*" style="display:none;"
                                   @change="nome = $event.target.files[0]?.name ?? 'Nenhum ficheiro selecionado'">
                        </label>
                        <span style="font-size:.78rem;color:#64748b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:180px;" x-text="nome"></span>
                    </div>
                    <p style="font-size:.72rem;color:#94a3b8;margin-top:.3rem;">Foto segurando o documento ao lado do rosto · JPG ou PNG · máx. 10MB</p>
                    @error('selfie') <p class="lf-error show">{{ $message }}</p> @enderror
                    @if($selfie) <p style="font-size:.72rem;color:#16a34a;margin-top:.2rem;display:flex;align-items:center;gap:.35rem;"><svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 13l4 4L19 3"/></svg>{{ $selfie->getClientOriginalName() }}</p> @endif
                </div>

                <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:.75rem 1rem;font-size:.78rem;color:#1d4ed8;margin-bottom:1rem;">
                    <p style="font-weight:700;margin-bottom:.2rem;display:flex;align-items:center;gap:.5rem;"><svg style="width:16px;height:16px;flex-shrink:0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 1 1 8 0v4"/></svg> Os seus documentos estão seguros</p>
                    <p>Armazenados de forma privada, apenas acessíveis pela equipa de verificação da 24HORAS.</p>
                </div>

                <button type="submit" class="lf-submit" wire:loading.attr="disabled" wire:target="submit">
                    <span wire:loading.remove wire:target="submit">Criar conta</span>
                    <span wire:loading wire:target="submit">A criar conta...</span>
                </button>
                <button type="button" class="lf-back" wire:click="back" wire:loading.attr="disabled" wire:target="submit">Voltar</button>
            </form>
        @endif

    </div>

    {{-- ─── RIGHT: visual ──────────────────────────────────────────────── --}}
    <div class="reg-right">

        <div class="reg-right-content">

            <div class="rr-logo">
                <img src="{{ asset('img/logo.png') . '?v=' . filemtime(public_path('img/logo.png')) }}" alt="24 Horas">
            </div>

            <div class="rr-badge">
                <span class="rr-badge-dot"></span>
                Plataforma Nº 1 em Angola
            </div>

            <h2 class="rr-headline">
                Conectamos talentos<br>
                a <span class="rr-accent">oportunidades reais.</span>
            </h2>

            <p class="rr-desc">A plataforma completa para impulsionar sua carreira, seus projectos e seus resultados — disponível 24 horas.</p>

            <div class="rr-features">
                <div class="rr-feature">
                    <div class="rr-feature-icon">
                        <svg width="18" height="18" fill="none" stroke="#0055ff" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="rr-feature-title">Seguro e confiável</p>
                        <p class="rr-feature-text">Pagamentos protegidos por escrow. Os seus dados nunca são partilhados.</p>
                    </div>
                </div>
                <div class="rr-feature">
                    <div class="rr-feature-icon">
                        <svg width="18" height="18" fill="none" stroke="#0055ff" stroke-width="2" viewBox="0 0 24 24">
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
                        <svg width="18" height="18" fill="none" stroke="#0055ff" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="rr-feature-title">Comunidade activa</p>
                        <p class="rr-feature-text">Milhares de profissionais e clientes conectados todos os dias.</p>
                    </div>
                </div>
            </div>

            <hr class="rr-divider">

            <div class="rr-stats">
                <div class="rr-stats-avatars">
                    <img src="{{ asset('img/default-avatar.svg') }}" alt="">
                    <img src="{{ asset('img/default-avatar.svg') }}" alt="">
                    <img src="{{ asset('img/default-avatar.svg') }}" alt="">
                </div>
                <p class="rr-stats-text">
                    <span>{{ \App\Services\PlatformStatsService::format($totalFreelancers) }}</span> freelancers activos<br>
                    <span>{{ \App\Services\PlatformStatsService::format($totalServicos) }}</span> serviços publicados
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
</script>
</div>
