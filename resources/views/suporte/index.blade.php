@extends('layouts.main')

@section('content')
<div class="pub-page" style="padding-top:0">
    <style>
        #support-form input:-webkit-autofill,
        #support-form input:-webkit-autofill:hover,
        #support-form input:-webkit-autofill:focus,
        #support-form textarea:-webkit-autofill,
        #support-form select:-webkit-autofill {
            -webkit-text-fill-color: #0f172a;
            -webkit-box-shadow: 0 0 0 1000px #ffffff inset;
            caret-color: #0f172a;
        }
        .support-field-error {
            display: none;
            color: #dc2626;
            font-size: .8rem;
            margin-top: .35rem;
        }
        .support-field-error.is-visible { display: block; }
        #support-form .support-invalid {
            border-color: #dc2626;
        }
    </style>
    <div class="pub-container--md" style="padding-top:0.75rem;padding-bottom:4rem;">

        <div class="pub-hero" style="margin-bottom:2.5rem;">
            <div class="pub-hero-label">Ajuda</div>
            <h1 class="pub-hero-title">Suporte</h1>
            <p class="pub-hero-sub">Tem alguma dúvida ou problema? Envie-nos uma mensagem e respondemos em até 24 horas.</p>
        </div>

        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-2xl px-6 py-4 mb-6 font-semibold text-base flex items-center gap-2">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M5 12.5 9.5 17 19 7.5"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-2xl px-6 py-4 mb-6 text-base">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 border border-[#e6f3fa]">
            <h2 class="text-xl font-extrabold text-[#0f172a] mb-6">Enviar mensagem</h2>

            <form id="support-form" method="POST" action="{{ route('suporte.enviar') }}" class="space-y-5" novalidate>
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-[#374151] mb-1">Nome <span class="text-red-500">*</span></label>
                        <input type="text" name="nome" value="{{ old('nome', auth()->user()?->name) }}"
                            class="w-full border border-[#cbd5e1] rounded-xl px-4 py-3 text-[#0f172a] focus:outline-none focus:ring-2 focus:ring-[#0055ff] transition"
                            placeholder="O seu nome completo" maxlength="100">
                        <p class="support-field-error @error('nome') is-visible @enderror" data-error-for="nome">@error('nome'){{ $message }}@else Este campo é obrigatório.@enderror</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-[#374151] mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}"
                            class="w-full border border-[#cbd5e1] rounded-xl px-4 py-3 text-[#0f172a] focus:outline-none focus:ring-2 focus:ring-[#0055ff] transition"
                            placeholder="o.seu@email.com" maxlength="150">
                        <p class="support-field-error @error('email') is-visible @enderror" data-error-for="email">@error('email'){{ $message }}@else Este campo é obrigatório.@enderror</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[#374151] mb-1">Assunto <span class="text-red-500">*</span></label>
                    <select name="assunto"
                        class="w-full border border-[#cbd5e1] rounded-xl px-4 py-3 text-[#0f172a] focus:outline-none focus:ring-2 focus:ring-[#0055ff] transition bg-white"
                        >
                        <option value="">Selecione um assunto</option>
                        <option value="Dúvida geral" @selected(old('assunto')=='Dúvida geral')>Dúvida geral</option>
                        <option value="Problema técnico" @selected(old('assunto')=='Problema técnico')>Problema técnico</option>
                        <option value="Pagamentos e faturas" @selected(old('assunto')=='Pagamentos e faturas')>Pagamentos e faturas</option>
                        <option value="Conta e acesso" @selected(old('assunto')=='Conta e acesso')>Conta e acesso</option>
                        <option value="Disputa entre utilizadores" @selected(old('assunto')=='Disputa entre utilizadores')>Disputa entre utilizadores</option>
                        <option value="Reportar conteúdo" @selected(old('assunto')=='Reportar conteúdo')>Reportar conteúdo</option>
                        <option value="Outro" @selected(old('assunto')=='Outro')>Outro</option>
                    </select>
                    <p class="support-field-error @error('assunto') is-visible @enderror" data-error-for="assunto">@error('assunto'){{ $message }}@else Este campo é obrigatório.@enderror</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[#374151] mb-1">Mensagem <span class="text-red-500">*</span></label>
                    <textarea name="mensagem" rows="6" maxlength="2000"
                        class="w-full border border-[#cbd5e1] rounded-xl px-4 py-3 text-[#0f172a] focus:outline-none focus:ring-2 focus:ring-[#0055ff] transition resize-none"
                        placeholder="Descreva o seu problema ou dúvida em detalhe...">{{ old('mensagem') }}</textarea>
                    <p class="support-field-error @error('mensagem') is-visible @enderror" data-error-for="mensagem">@error('mensagem'){{ $message }}@else Este campo é obrigatório.@enderror</p>
                </div>

                <button type="submit"
                    class="hp-btn hp-btn-primary w-full md:w-auto px-10 py-3 text-base font-bold rounded-xl">
                    Enviar mensagem
                </button>
            </form>
            <script>
                (() => {
                    const form = document.getElementById('support-form');
                    if (!form) return;

                    const fields = [...form.querySelectorAll('input, select, textarea')];
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                    const showError = (field, message) => {
                        const error = form.querySelector(`[data-error-for="${field.name}"]`);
                        if (!error) return;
                        error.textContent = message;
                        error.classList.add('is-visible');
                        field.classList.add('support-invalid');
                    };

                    const clearError = (field) => {
                        const error = form.querySelector(`[data-error-for="${field.name}"]`);
                        if (error) error.classList.remove('is-visible');
                        field.classList.remove('support-invalid');
                    };

                    fields.forEach((field) => {
                        field.addEventListener('input', () => clearError(field));
                        field.addEventListener('change', () => clearError(field));
                    });

                    form.addEventListener('submit', (event) => {
                        let valid = true;
                        fields.forEach((field) => {
                            clearError(field);
                            if (!field.value.trim()) {
                                showError(field, 'Este campo é obrigatório.');
                                valid = false;
                            } else if (field.name === 'email' && !emailPattern.test(field.value.trim())) {
                                showError(field, 'Indique um email válido.');
                                valid = false;
                            }
                        });

                        if (!valid) {
                            event.preventDefault();
                            const firstInvalid = form.querySelector('.support-invalid');
                            if (firstInvalid) firstInvalid.focus();
                        }
                    });
                })();
            </script>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-8">
            <div class="bg-white rounded-2xl border border-[#e6f3fa] shadow p-6 text-center">
                <div class="mb-3 flex justify-center text-[#0055ff]">
                    @include('components.icon', ['name' => 'mail', 'class' => 'w-8 h-8'])
                </div>
                <div class="font-bold text-[#0f172a] mb-1">Email</div>
                <a href="mailto:contacto@24horas.ao" class="text-[#0055ff] font-semibold text-sm hover:underline">contacto@24horas.ao</a>
            </div>
            <div class="bg-white rounded-2xl border border-[#e6f3fa] shadow p-6 text-center">
                <div class="mb-3 flex justify-center text-[#0055ff]">
                    @include('components.icon', ['name' => 'clock', 'class' => 'w-8 h-8'])
                </div>
                <div class="font-bold text-[#0f172a] mb-1">Tempo de resposta</div>
                <p class="text-[#64748b] text-sm m-0">Respondemos em até 24 horas úteis</p>
            </div>
            <div class="bg-white rounded-2xl border border-[#e6f3fa] shadow p-6 text-center">
                <div class="mb-3 flex justify-center text-[#0055ff]">
                    @include('components.icon', ['name' => 'file-text', 'class' => 'w-8 h-8'])
                </div>
                <div class="font-bold text-[#0f172a] mb-1">Como funciona</div>
                <a href="{{ route('sobre.como-funciona') }}" class="text-[#0055ff] font-semibold text-sm hover:underline">Ver guia rápido</a>
            </div>
        </div>

    </div>
</div>
@endsection
