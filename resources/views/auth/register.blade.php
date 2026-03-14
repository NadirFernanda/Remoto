@extends('layouts.main')

@section('content')
<div class="pub-page" style="display:flex;align-items:center;justify-content:center;">
    <div class="pub-container--sm" style="width:100%;padding-top:2rem;padding-bottom:3rem;">
        <div class="pub-auth-card">
            {{-- Logo + título --}}
            <div style="text-align:center;margin-bottom:1.75rem;">
                <a href="/" style="display:inline-block;margin-bottom:1.25rem;">
                    <img src="{{ asset('img/logo.png') }}" alt="24 Horas" style="height:56px;object-fit:contain;filter:drop-shadow(0 0 10px rgba(0,186,255,.35));">
                </a>
                <h1 id="titulo-registo" class="pub-auth-title">Criar conta como Freelancer</h1>
                <p class="pub-auth-sub">Preencha os dados para começar</p>
            </div>

            @if(session('status'))
                <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:.75rem 1rem;color:#16a34a;font-size:.875rem;margin-bottom:1.25rem;text-align:center;">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:.75rem 1rem;color:#dc2626;font-size:.875rem;margin-bottom:1.25rem;">
                    <ul style="margin:0;padding-left:1.2rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="/register" novalidate onsubmit="return validateRegisterForm(event)">
                @csrf
                {{-- Role selector (3 opções) --}}
                <div class="pub-field" x-data="{ role: '{{ old('role', 'freelancer') }}' }">
                    <label style="display:block;margin-bottom:.6rem;">Quero registar-me como:</label>
                    <div class="role-selector-grid">

                        {{-- Freelancer --}}
                        <label @click="role='freelancer'" :style="role==='freelancer' ? 'border-color:#00baff;background:#f0fbff;' : 'border-color:#e2e8f0;background:#fff;'"
                               style="border:2px solid;border-radius:12px;padding:.75rem .5rem;cursor:pointer;text-align:center;transition:all .2s;">
                            <input type="radio" name="role" value="freelancer" x-model="role" style="display:none;">
                            <div style="font-size:1.4rem;margin-bottom:.25rem;">💼</div>
                            <div style="font-size:.8rem;font-weight:700;color:#0f172a;">Freelancer</div>
                            <div style="font-size:.65rem;color:#64748b;margin-top:.2rem;">Ofereço serviços</div>
                        </label>

                        {{-- Cliente --}}
                        <label @click="role='cliente'" :style="role==='cliente' ? 'border-color:#00baff;background:#f0fbff;' : 'border-color:#e2e8f0;background:#fff;'"
                               style="border:2px solid;border-radius:12px;padding:.75rem .5rem;cursor:pointer;text-align:center;transition:all .2s;">
                            <input type="radio" name="role" value="cliente" x-model="role" style="display:none;">
                            <div style="font-size:1.4rem;margin-bottom:.25rem;">🏢</div>
                            <div style="font-size:.8rem;font-weight:700;color:#0f172a;">Cliente</div>
                            <div style="font-size:.65rem;color:#64748b;margin-top:.2rem;">Contrato serviços</div>
                        </label>

                        {{-- Criador/Seguidor --}}
                        <label @click="role='creator'" :style="role==='creator' ? 'border-color:#00baff;background:#f0fbff;' : 'border-color:#e2e8f0;background:#fff;'"
                               style="border:2px solid;border-radius:12px;padding:.75rem .5rem;cursor:pointer;text-align:center;transition:all .2s;">
                            <input type="radio" name="role" value="creator" x-model="role" style="display:none;">
                            <div style="font-size:1.4rem;margin-bottom:.25rem;">🎬</div>
                            <div style="font-size:.8rem;font-weight:700;color:#0f172a;">Criador</div>
                            <div style="font-size:.65rem;color:#64748b;margin-top:.2rem;">Crio conteúdo</div>
                        </label>

                    </div>
                    @error('role')<div class="pub-field-error">{{ $message }}</div>@enderror
                </div>

                <div class="pub-field">
                    <label for="name">Nome completo</label>
                    <input class="pub-input" type="text" name="name" id="reg-name" value="{{ old('name') }}" placeholder="O seu nome">
                    <div id="name-error" class="pub-field-error" style="display:none;"></div>
                    @error('name')<div class="pub-field-error">{{ $message }}</div>@enderror
                </div>

                <div class="pub-field">
                    <label for="email">E-mail</label>
                    <input class="pub-input" type="email" name="email" id="reg-email" value="{{ old('email') }}" placeholder="seu@email.com">
                    <div id="reg-email-error" class="pub-field-error" style="display:none;"></div>
                    @error('email')<div class="pub-field-error">{{ $message }}</div>@enderror
                </div>

                <div class="pub-field">
                    <label for="password">Palavra-passe</label>
                    <input class="pub-input" type="password" name="password" id="reg-password" placeholder="••••••••">
                    <div id="reg-password-error" class="pub-field-error" style="display:none;"></div>
                    @error('password')<div class="pub-field-error">{{ $message }}</div>@enderror
                </div>

                <div class="pub-field">
                    <label for="password_confirmation">Confirmar palavra-passe</label>
                    <input class="pub-input" type="password" name="password_confirmation" id="reg-password-confirm" placeholder="••••••••">
                    <div id="reg-confirm-error" class="pub-field-error" style="display:none;"></div>
                    @error('password_confirmation')<div class="pub-field-error">{{ $message }}</div>@enderror
                </div>

                <button type="submit" id="botao-registo" class="pub-btn-primary" style="width:100%;padding:.75rem;font-size:1rem;margin-top:.25rem;">Criar conta</button>
            </form>

            <p style="text-align:center;margin-top:1.5rem;font-size:.875rem;color:#64748b;">
                Já tem conta?
                <a href="/login" style="color:#00baff;font-weight:700;text-decoration:none;">Entrar</a>
            </p>
        </div>
    </div>
</div>

<script>
function validateRegisterForm(event) {
    let valid = true;
    const fields = [
        { id: 'reg-name',            errId: 'name-error',         empty: 'Preencha o seu nome completo.' },
        { id: 'reg-email',           errId: 'reg-email-error',    empty: 'Preencha o e-mail.' },
        { id: 'reg-password',        errId: 'reg-password-error', empty: 'Preencha a palavra-passe.' },
        { id: 'reg-password-confirm',errId: 'reg-confirm-error',  empty: 'Confirme a sua palavra-passe.' },
    ];
    fields.forEach(f => {
        const el  = document.getElementById(f.id);
        const err = document.getElementById(f.errId);
        err.style.display = 'none';
        el.style.borderColor = '';
    });
    fields.forEach(f => {
        const el  = document.getElementById(f.id);
        const err = document.getElementById(f.errId);
        if (!el.value.trim()) {
            err.textContent = f.empty;
            err.style.display = 'block';
            el.style.borderColor = '#dc2626';
            if (valid) el.focus();
            valid = false;
        }
    });
    const emailEl = document.getElementById('reg-email');
    const emailErr = document.getElementById('reg-email-error');
    if (emailEl.value.trim() && !/^\S+@\S+\.\S+$/.test(emailEl.value)) {
        emailErr.textContent = 'Introduza um e-mail válido.';
        emailErr.style.display = 'block';
        emailEl.style.borderColor = '#dc2626';
        if (valid) emailEl.focus();
        valid = false;
    }
    const pw  = document.getElementById('reg-password');
    const pwc = document.getElementById('reg-password-confirm');
    const pwErr  = document.getElementById('reg-password-error');
    const pwcErr = document.getElementById('reg-confirm-error');
    if (pw.value && pw.value.length < 8) {
        pwErr.textContent = 'A palavra-passe deve ter no mínimo 8 caracteres.';
        pwErr.style.display = 'block';
        pw.style.borderColor = '#dc2626';
        if (valid) pw.focus();
        valid = false;
    }
    if (pw.value && pwc.value && pw.value !== pwc.value) {
        pwcErr.textContent = 'As palavras-passe não coincidem.';
        pwcErr.style.display = 'block';
        pwc.style.borderColor = '#dc2626';
        if (valid) pwc.focus();
        valid = false;
    }
    if (!valid) event.preventDefault();
    return valid;
}

document.addEventListener('DOMContentLoaded', function() {
    const freelancerRadio = document.getElementById('role-freelancer');
    const clientRadio = document.getElementById('role-cliente');
    const submitButton = document.getElementById('botao-registo');
    const tituloRegisto = document.getElementById('titulo-registo');
    function updateRegistoUI() {
        if (clientRadio.checked) {
            submitButton.textContent = 'Registar como Cliente';
            if (tituloRegisto) tituloRegisto.textContent = 'Criar conta como Cliente';
        } else {
            submitButton.textContent = 'Registar como Freelancer';
            if (tituloRegisto) tituloRegisto.textContent = 'Criar conta como Freelancer';
        }
    }
    freelancerRadio.addEventListener('change', updateRegistoUI);
    clientRadio.addEventListener('change', updateRegistoUI);
    updateRegistoUI();
});
</script>
@endsection
