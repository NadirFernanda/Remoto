@extends('layouts.main')

@section('content')
<div class="pub-page" style="padding-top:0">
    <div class="pub-container--md" style="padding-top:0.75rem;padding-bottom:4rem;">

        <div class="pub-hero" style="margin-bottom:2.5rem;">
            <div class="pub-hero-label">Trabalhe connosco</div>
            <h1 class="pub-hero-title">Carreiras na 24 Horas Remoto</h1>
            <p class="pub-hero-sub">Junte-se a uma equipa que está a redefinir o futuro do trabalho em Angola. Construímos uma cultura de impacto, autonomia e crescimento contínuo.</p>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 mb-6 border border-[#e6f3fa] transition hover:shadow-2xl">
            <h2 class="text-xl md:text-2xl font-extrabold text-[#0f172a] mb-4">Por que trabalhar na 24 Horas Remoto?</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <div class="bg-[#f8fafc] rounded-xl p-5 border border-[#e2e8f0]">
                    <div class="text-[#0055ff] font-extrabold mb-1 flex items-center gap-2">
                        @include('components.icon', ['name' => 'sparkles', 'class' => 'w-4 h-4'])
                        Missão com impacto
                    </div>
                    <p class="text-[#64748b] text-base m-0 leading-relaxed">O seu trabalho muda directamente a vida de freelancers e empresas angolanas.</p>
                </div>
                <div class="bg-[#f8fafc] rounded-xl p-5 border border-[#e2e8f0]">
                    <div class="text-[#0055ff] font-extrabold mb-1 flex items-center gap-2">
                        @include('components.icon', ['name' => 'globe', 'class' => 'w-4 h-4'])
                        100% Remoto
                    </div>
                    <p class="text-[#64748b] text-base m-0 leading-relaxed">Trabalhe de qualquer lugar de Angola — ou do mundo — com flexibilidade total.</p>
                </div>
                <div class="bg-[#f8fafc] rounded-xl p-5 border border-[#e2e8f0]">
                    <div class="text-[#0055ff] font-extrabold mb-1 flex items-center gap-2">
                        @include('components.icon', ['name' => 'chart', 'class' => 'w-4 h-4'])
                        Crescimento acelerado
                    </div>
                    <p class="text-[#64748b] text-base m-0 leading-relaxed">Equipa pequena, decisões rápidas, visibilidade real do seu trabalho desde o primeiro dia.</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 mb-5 border border-[#e6f3fa] transition hover:shadow-2xl">
            <div class="flex flex-wrap justify-between items-center gap-4">
                <div>
                    <div class="font-extrabold text-[#0f172a] text-base">Desenvolvedor(a) Full-Stack (Laravel / Vue.js)</div>
                    <div class="text-[#64748b] text-base mt-1">Engenharia · Remoto · Tempo inteiro</div>
                </div>
                <a href="{{ route('suporte', ['assunto' => 'Candidatura — Full-Stack']) }}" class="inline-block bg-[#0055ff] hover:bg-[#0047d9] text-white font-bold px-6 py-2 rounded-lg text-base transition whitespace-nowrap">Candidatar-se</a>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 mb-5 border border-[#e6f3fa] transition hover:shadow-2xl">
            <div class="flex flex-wrap justify-between items-center gap-4">
                <div>
                    <div class="font-extrabold text-[#0f172a] text-base">Designer de Produto (UI/UX)</div>
                    <div class="text-[#64748b] text-base mt-1">Produto · Remoto · Tempo inteiro</div>
                </div>
                <a href="{{ route('suporte', ['assunto' => 'Candidatura — Designer UI/UX']) }}" class="inline-block bg-[#0055ff] hover:bg-[#0047d9] text-white font-bold px-6 py-2 rounded-lg text-base transition whitespace-nowrap">Candidatar-se</a>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 mb-5 border border-[#e6f3fa] transition hover:shadow-2xl">
            <div class="flex flex-wrap justify-between items-center gap-4">
                <div>
                    <div class="font-extrabold text-[#0f172a] text-base">Especialista em Suporte e Comunidade</div>
                    <div class="text-[#64748b] text-base mt-1">Operações · Luanda ou Remoto · Tempo inteiro</div>
                </div>
                <a href="{{ route('suporte', ['assunto' => 'Candidatura — Suporte e Comunidade']) }}" class="inline-block bg-[#0055ff] hover:bg-[#0047d9] text-white font-bold px-6 py-2 rounded-lg text-base transition whitespace-nowrap">Candidatar-se</a>
            </div>
        </div>

        <div class="bg-[#0d1424] border border-white/10 rounded-3xl shadow-xl p-8 md:p-10 mb-5 transition hover:shadow-2xl">
            <h3 class="text-base font-extrabold text-[#f1f5f9] mb-2">Não encontrou a vaga certa?</h3>
            <p class="text-[#cbd5e1] text-base leading-relaxed m-0">Envie o seu CV e carta de motivação através do <a href="{{ route('suporte', ['assunto' => 'Candidatura espontânea']) }}" class="text-[#5b8cff] font-semibold hover:underline">formulário de suporte</a>. Guardamos o seu perfil e contactamos assim que surgir uma oportunidade adequada.</p>
        </div>

    </div>
</div>
@endsection
