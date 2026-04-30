@extends('layouts.main')

@section('content')
<div class="login-wrap" style="display:flex;min-height:calc(100vh - 70px);font-family:'Inter',system-ui,sans-serif;">

    {{-- COLUNA ESQUERDA — imagem + branding --}}
    <div style="flex:0 0 55%;position:relative;overflow:hidden;display:flex;flex-direction:column;justify-content:flex-end;padding:3rem;background:#0f172a;"
         class="login-hero-col">

        {{-- Imagem de fundo --}}
        <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=1400&h=900&fit=crop&auto=format&q=80"
             alt=""
             style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:.35;pointer-events:none;">

        {{-- Gradiente sobre a imagem --}}
        <div style="position:absolute;inset:0;background:linear-gradient(135deg,rgba(0,112,255,.55) 0%,rgba(15,23,42,.92) 60%);pointer-events:none;"></div>

        {{-- Conteúdo sobre a imagem --}}
        <div style="position:relative;z-index:1;">

            {{-- Logo --}}
            <div style="margin-bottom:2.5rem;">
                <img src="{{ asset('img/logo.png') }}" alt="24 Horas" style="height:52px;object-fit:contain;filter:drop-shadow(0 0 14px rgba(0,186,255,.5));">
            </div>

            {{-- Tagline --}}
            <h2 style="font-size:2.1rem;font-weight:800;color:#fff;line-height:1.2;margin:0 0 .75rem;">
                Conectamos talento<br>a oportunidade.
            </h2>
            <p style="color:rgba(255,255,255,.7);font-size:1rem;margin:0 0 2.5rem;max-width:380px;line-height:1.6;">
                A plataforma angolana líder para freelancers e clientes. Trabalho remoto, pagamento seguro, 24 horas por dia.
            </p>

            {{-- Stats --}}
            <div style="display:flex;gap:1.5rem;flex-wrap:wrap;">
                <div style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);backdrop-filter:blur(8px);border-radius:14px;padding:.9rem 1.4rem;min-width:110px;">
                    <div style="font-size:1.6rem;font-weight:800;color:#00baff;line-height:1;">+5 000</div>
                    <div style="font-size:.78rem;color:rgba(255,255,255,.65);margin-top:.25rem;font-weight:500;">Freelancers</div>
                </div>
                <div style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);backdrop-filter:blur(8px);border-radius:14px;padding:.9rem 1.4rem;min-width:110px;">
                    <div style="font-size:1.6rem;font-weight:800;color:#00baff;line-height:1;">+12 000</div>
                    <div style="font-size:.78rem;color:rgba(255,255,255,.65);margin-top:.25rem;font-weight:500;">Projectos</div>
                </div>
                <div style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);backdrop-filter:blur(8px);border-radius:14px;padding:.9rem 1.4rem;min-width:110px;">
                    <div style="font-size:1.6rem;font-weight:800;color:#00baff;line-height:1;">98%</div>
                    <div style="font-size:.78rem;color:rgba(255,255,255,.65);margin-top:.25rem;font-weight:500;">Satisfação</div>
                </div>
            </div>

            {{-- Testemunho --}}
            <div style="margin-top:2rem;background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);border-radius:16px;padding:1.1rem 1.4rem;max-width:420px;">
                <div style="display:flex;gap:.1rem;margin-bottom:.5rem;">
                    <svg style="width:14px;height:14px;fill:#f59e0b;" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    <svg style="width:14px;height:14px;fill:#f59e0b;" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    <svg style="width:14px;height:14px;fill:#f59e0b;" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    <svg style="width:14px;height:14px;fill:#f59e0b;" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    <svg style="width:14px;height:14px;fill:#f59e0b;" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                </div>
                <p style="color:rgba(255,255,255,.85);font-size:.875rem;line-height:1.55;margin:0 0 .6rem;font-style:italic;">
                    "Consegui os meus primeiros clientes em menos de uma semana. A plataforma é intuitiva e o suporte é excelente."
                </p>
                <div style="display:flex;align-items:center;gap:.6rem;">
                    <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#00baff,#0070ff);display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;font-size:.8rem;">M</div>
                    <div>
                        <div style="color:#fff;font-size:.8rem;font-weight:600;">Marcos Oliveira</div>
                        <div style="color:rgba(255,255,255,.5);font-size:.72rem;">Dev Full Stack · Luanda</div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- COLUNA DIREITA — formulário --}}
    <div class="login-form-col" style="flex:1;display:flex;align-items:center;justify-content:center;background:#f8fafc;padding:2.5rem 2rem;">
        <div style="width:100%;max-width:420px;">

            <div style="text-align:center;margin-bottom:2rem;">
                <h1 style="font-size:1.65rem;font-weight:800;color:#0f172a;margin:0 0 .4rem;">Bem-vindo de volta</h1>
                <p style="color:#64748b;font-size:.9rem;margin:0;">Aceda à sua conta 24 Horas</p>
            </div>

            @if(session('status'))
                <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:.75rem 1rem;color:#16a34a;font-size:.875rem;margin-bottom:1.25rem;text-align:center;">
                    {{ session('status') }}
                </div>
            @endif

            <div class="pub-auth-card" style="box-shadow:0 8px 40px rgba(0,0,0,.08);">
                <form method="POST" action="/login" novalidate onsubmit="return validateLoginForm(event)">
                    @csrf
                    <div class="pub-field">
                        <label for="login-email">E-mail</label>
                        <input type="email" name="email" id="login-email" class="pub-input" placeholder="seu@email.com"
                               value="{{ old('email') }}" required autofocus
                               style="{{ $errors->has('email') ? 'border-color:#dc2626;' : '' }}">
                        <div id="email-error" class="pub-field-error"
                             style="{{ $errors->has('email') ? 'display:block;' : 'display:none;' }}">
                            {{ $errors->first('email') }}
                        </div>
                    </div>
                    <div class="pub-field">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.4rem;">
                            <label for="login-password" style="margin-bottom:0;">Palavra-passe</label>
                            <a href="{{ route('password.request') }}" style="font-size:.8rem;color:#00baff;font-weight:600;text-decoration:none;">Esqueci a palavra-passe</a>
                        </div>
                        <input type="password" name="password" id="login-password" class="pub-input" placeholder="••••••••" required
                               style="{{ $errors->has('password') ? 'border-color:#dc2626;' : '' }}">
                        <div id="password-error" class="pub-field-error"
                             style="{{ $errors->has('password') ? 'display:block;' : 'display:none;' }}">
                            {{ $errors->first('password') }}
                        </div>
                    </div>
                    <button type="submit" class="pub-btn-primary" style="width:100%;padding:.8rem;font-size:1rem;margin-top:.5rem;border-radius:10px;">Entrar</button>
                </form>

                <p style="text-align:center;margin-top:1.5rem;font-size:.875rem;color:#64748b;margin-bottom:0;">
                    Não tem conta?
                    <a href="/register" style="color:#00baff;font-weight:700;text-decoration:none;">Criar conta gratuita</a>
                </p>
            </div>
        </div>
    </div>
</div>

<style>
@@media (max-width: 768px) {
    .login-hero-col { display: none !important; }
    .login-wrap {
        background-image: linear-gradient(135deg,rgba(0,112,255,.75) 0%,rgba(15,23,42,.92) 100%),
                          url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=900&h=1200&fit=crop&auto=format&q=80');
        background-size: cover;
        background-position: center;
        align-items: flex-start;
        padding-top: 2rem;
        padding-bottom: 3rem;
    }
    .login-form-col {
        background: transparent !important;
    }
    .login-form-col .pub-auth-card {
        background: rgba(255,255,255,0.97) !important;
    }
    .login-form-col h1 {
        color: #fff !important;
    }
    .login-form-col > div > p {
        color: rgba(255,255,255,0.75) !important;
    }
}
</style>

<script>
function validateLoginForm(event) {
    let valid = true;
    const email = document.getElementById('login-email');
    const password = document.getElementById('login-password');
    const emailError = document.getElementById('email-error');
    const passwordError = document.getElementById('password-error');
    emailError.style.display = 'none';
    passwordError.style.display = 'none';
    email.style.borderColor = '';
    password.style.borderColor = '';
    if (!email.value) {
        emailError.textContent = 'Preencha o e-mail.';
        emailError.style.display = 'block';
        email.style.borderColor = '#dc2626';
        valid = false;
    } else if (!/^\S+@\S+\.\S+$/.test(email.value)) {
        emailError.textContent = 'Introduza um e-mail válido.';
        emailError.style.display = 'block';
        email.style.borderColor = '#dc2626';
        valid = false;
    }
    if (!password.value) {
        passwordError.textContent = 'Preencha a palavra-passe.';
        passwordError.style.display = 'block';
        password.style.borderColor = '#dc2626';
        valid = false;
    }
    return valid;
}
</script>
@endsection
