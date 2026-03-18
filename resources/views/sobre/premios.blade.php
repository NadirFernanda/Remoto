@extends('layouts.main')

@section('content')
<div class="pub-page" style="padding-top:0">
    <div class="pub-container--md" style="padding-top:0.75rem;padding-bottom:4rem;">

        <div class="pub-hero" style="margin-bottom:2.5rem;">
            <div class="pub-hero-label">Reconhecimento</div>
            <h1 class="pub-hero-title">Prémios e distinções</h1>
            <p class="pub-hero-sub">O reconhecimento externo que valida o impacto da 24 Horas Remoto no ecossistema digital de Angola.</p>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 mb-5 border border-[#e6f3fa] transition hover:shadow-2xl">
            <div class="flex gap-5 items-start flex-wrap">
                <div class="w-14 h-14 min-w-[56px] rounded-xl bg-gradient-to-br from-[#f59e0b] to-[#fbbf24] flex items-center justify-center text-2xl">🏆</div>
                <div>
                    <div class="font-extrabold text-[#0f172a] text-base mb-1">Melhor Startup de Tecnologia — Angola Tech Awards 2024</div>
                    <div class="text-[#94a3b8] text-xs mb-2">Angola Tech Foundation · Novembro 2024</div>
                    <p class="text-[#64748b] text-base leading-relaxed m-0">Distinguida entre mais de 120 candidatas como a startup com maior impacto positivo no mercado de trabalho digital angolano.</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 mb-5 border border-[#e6f3fa] transition hover:shadow-2xl">
            <div class="flex gap-5 items-start flex-wrap">
                <div class="w-14 h-14 min-w-[56px] rounded-xl bg-gradient-to-br from-[#e5e7eb] to-[#d1d5db] flex items-center justify-center text-2xl">🥈</div>
                <div>
                    <div class="font-extrabold text-[#0f172a] text-base mb-1">2.º Lugar — Pitch da Economia Digital CEAC 2024</div>
                    <div class="text-[#94a3b8] text-xs mb-2">Centro Empresarial de Angola · Setembro 2024</div>
                    <p class="text-[#64748b] text-base leading-relaxed m-0">Reconhecida pelo júri pelo modelo de negócio sustentável e pelo impacto social na empregabilidade de jovens formados.</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 mb-5 border border-[#e6f3fa] transition hover:shadow-2xl">
            <div class="flex gap-5 items-start flex-wrap">
                <div class="w-14 h-14 min-w-[56px] rounded-xl bg-gradient-to-br from-[#00baff] to-[#0077cc] flex items-center justify-center text-2xl">⭐</div>
                <div>
                    <div class="font-extrabold text-[#0f172a] text-base mb-1">Seleccionada — Programa de Aceleração AfriTech 2024</div>
                    <div class="text-[#94a3b8] text-xs mb-2">AfriTech Accelerator · Julho 2024</div>
                    <p class="text-[#64748b] text-base leading-relaxed m-0">Uma das 12 startups africanas seleccionadas para o programa de aceleração, que inclui mentoria e acesso a investidores globais.</p>
                </div>
            </div>
        </div>

        <div class="bg-[#f0f9ff] border border-[#bae6fd] rounded-3xl shadow-xl p-8 md:p-10 mb-5 transition hover:shadow-2xl">
            <h3 class="text-base font-extrabold text-[#0f172a] mb-2">Nominações em aberto</h3>
            <p class="text-[#475569] text-base leading-relaxed m-0">Se a sua organização pretende nomear a 24 Horas Remoto para uma distinção, contacte-nos em <strong>comunicacao@remoto.ao</strong>.</p>
        </div>

    </div>
</div>
@endsection
