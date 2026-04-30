@extends('layouts.main')

@section('content')
<div style="min-height:100%;background-image:linear-gradient(135deg,rgba(0,86,210,.82) 0%,rgba(10,18,40,.93) 100%),url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=1600&h=900&fit=crop&auto=format&q=80');background-size:cover;background-position:center;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:2.5rem 1rem;font-family:'Inter',system-ui,sans-serif;">

    <a href="/" style="display:inline-block;margin-bottom:1.5rem;">
        <img src="{{ asset('img/logo.png') }}" alt="24 Horas" style="height:52px;object-fit:contain;filter:drop-shadow(0 0 16px rgba(0,186,255,.6));">
    </a>

    <div style="text-align:center;margin-bottom:1.75rem;">
        <h1 style="font-size:1.75rem;font-weight:800;color:#fff;margin:0 0 .35rem;">Bem-vindo de volta</h1>
        <p style="color:rgba(255,255,255,.7);font-size:.95rem;margin:0;">Aceda a sua conta 24 Horas</p>
    </div>

    <div style="width:100%;max-width:440px;">
        @if(session('status'))
            <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:.75rem 1rem;color:#16a34a;font-size:.875rem;margin-bottom:1.25rem;text-align:center;">{{ session('status') }}</div>
        @endif

        <div style="background:rgba(255,255,255,.97);border-radius:20px;padding:2.25rem 2rem;box-shadow:0 20px 60px rgba(0,0,0,.3);">
            <form method="POST" action="/login" novalidate onsubmit="return validateLoginForm(event)">
                @csrf
                <div class="pub-field">
                    <label for="login-email">E-mail</label>
                    <input type="email" name="email" id="login-email" class="pub-input" placeholder="seu@email.com" value="{{ old('email') }}" required autofocus style="{{ $errors->has('email') ? 'border-color:#dc2626;' : '' }}">
                    <div id="email-error" class="pub-field-error" style="{{ $errors->has('email') ? 'display:block;' : 'display:none;' }}">{{ $errors->first('email') }}</div>
                </div>
                <div class="pub-field">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.4rem;">
                        <label for="login-password" style="margin-bottom:0;">Palavra-passe</label>
                        <a href="{{ route('password.request') }}" style="font-size:.8rem;color:#0070ff;font-weight:600;text-decoration:none;">Esqueci a palavra-passe</a>
                    </div>
                    <input type="password" name="password" id="login-password" class="pub-input" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" required style="{{ $errors->has('password') ? 'border-color:#dc2626;' : '' }}">
                    <div id="password-error" class="pub-field-error" style="{{ $errors->has('password') ? 'display:block;' : 'display:none;' }}">{{ $errors->first('password') }}</div>
                </div>
                <button type="submit" class="pub-btn-primary" style="width:100%;padding:.85rem;font-size:1rem;margin-top:.5rem;border-radius:10px;">Entrar</button>
            </form>
            <p style="text-align:center;margin-top:1.25rem;font-size:.875rem;color:#64748b;margin-bottom:0;">
                Nao tem conta? <a href="/register" style="color:#0070ff;font-weight:700;text-decoration:none;">Criar conta gratuita</a>
            </p>
        </div>
    </div>

    <div style="display:flex;gap:1rem;flex-wrap:wrap;justify-content:center;margin-top:2rem;">
        <div style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);backdrop-filter:blur(8px);border-radius:14px;padding:.75rem 1.25rem;text-align:center;">
            <div style="font-size:1.3rem;font-weight:800;color:#00baff;">+5 000</div>
            <div style="font-size:.75rem;color:rgba(255,255,255,.65);margin-top:.15rem;">Freelancers</div>
        </div>
        <div style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);backdrop-filter:blur(8px);border-radius:14px;padding:.75rem 1.25rem;text-align:center;">
            <div style="font-size:1.3rem;font-weight:800;color:#00baff;">+12 000</div>
            <div style="font-size:.75rem;color:rgba(255,255,255,.65);margin-top:.15rem;">Projectos</div>
        </div>
        <div style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);backdrop-filter:blur(8px);border-radius:14px;padding:.75rem 1.25rem;text-align:center;">
            <div style="font-size:1.3rem;font-weight:800;color:#00baff;">98%</div>
            <div style="font-size:.75rem;color:rgba(255,255,255,.65);margin-top:.15rem;">Satisfacao</div>
        </div>
    </div>

    <div style="margin-top:1.5rem;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);border-radius:16px;padding:1rem 1.4rem;max-width:440px;width:100%;">
        <div style="display:flex;gap:.15rem;margin-bottom:.4rem;">
            <svg style="width:13px;height:13px;fill:#f59e0b;" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            <svg style="width:13px;height:13px;fill:#f59e0b;" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            <svg style="width:13px;height:13px;fill:#f59e0b;" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            <svg style="width:13px;height:13px;fill:#f59e0b;" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            <svg style="width:13px;height:13px;fill:#f59e0b;" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
        </div>
        <p style="color:rgba(255,255,255,.85);font-size:.85rem;line-height:1.55;margin:0 0 .6rem;font-style:italic;">"Consegui os meus primeiros clientes em menos de uma semana. A plataforma e intuitiva e o suporte e excelente."</p>
        <div style="display:flex;align-items:center;gap:.6rem;">
            <div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#00baff,#0070ff);display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;font-size:.75rem;flex-shrink:0;">M</div>
            <div>
                <div style="color:#fff;font-size:.78rem;font-weight:600;">Marcos Oliveira</div>
                <div style="color:rgba(255,255,255,.5);font-size:.7rem;">Dev Full Stack - Luanda</div>
            </div>
        </div>
    </div>

</div>

<script>
function validateLoginForm(e) {
    var ok=true,em=document.getElementById('login-email'),pw=document.getElementById('login-password'),ee=document.getElementById('email-error'),pe=document.getElementById('password-error');
    ee.style.display='none';pe.style.display='none';em.style.borderColor='';pw.style.borderColor='';
    if(!em.value){ee.textContent='Preencha o e-mail.';ee.style.display='block';em.style.borderColor='#dc2626';ok=false;}
    else if(!/^\S+@\S+\.\S+$/.test(em.value)){ee.textContent='E-mail invalido.';ee.style.display='block';em.style.borderColor='#dc2626';ok=false;}
    if(!pw.value){pe.textContent='Preencha a palavra-passe.';pe.style.display='block';pw.style.borderColor='#dc2626';ok=false;}
    return ok;
}
</script>
@endsection
