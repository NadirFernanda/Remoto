@extends('layouts.main')

@section('content')
<div class="pub-page" style="padding-top:0">
    <div class="pub-container--md" style="padding-top:0.75rem;padding-bottom:4rem;">

        <div class="pub-hero" style="margin-bottom:2.5rem;">
            <div class="pub-hero-label">Investidores</div>
            <h1 class="pub-hero-title">Invista no futuro do trabalho em Angola</h1>
            <p class="pub-hero-sub">A 24 Horas Remoto está a transformar o mercado de serviços digitais no país. Conheça as nossas métricas, visão e oportunidades de parceria.</p>
        </div>

        <div class="pub-card rounded-3xl p-8 md:p-10 mb-6 transition">
            <h2 class="text-2xl md:text-3xl font-extrabold text-[#f1f5f9] mb-4">A oportunidade de mercado</h2>
            <p class="text-[#cbd5e1] text-lg leading-relaxed">Angola conta com mais de 35 milhões de habitantes, uma população jovem e crescentemente digital, e uma lacuna significativa em plataformas locais de serviços freelance. O mercado global de trabalho independente cresce a dois dígitos anualmente — e Angola está na base desta curva, com enorme espaço de expansão.</p>
        </div>

        <div class="pub-card rounded-3xl p-8 md:p-10 mb-6 transition">
            <h2 class="text-2xl md:text-3xl font-extrabold text-[#f1f5f9] mb-6">Métricas chave</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-center">
                <div class="bg-[#111c31] rounded-2xl p-8 border border-white/10">
                    <div class="text-3xl font-black text-[#0055ff]">{{ \App\Services\PlatformStatsService::format($totalUsers) }}</div>
                    <div class="text-[#cbd5e1] text-base mt-2">Utilizadores registados</div>
                </div>
                <div class="bg-[#111c31] rounded-2xl p-8 border border-white/10">
                    <div class="text-3xl font-black text-[#0055ff]">{{ \App\Services\PlatformStatsService::format($totalServicos) }}</div>
                    <div class="text-[#cbd5e1] text-base mt-2">Serviços publicados</div>
                </div>
                <div class="bg-[#111c31] rounded-2xl p-8 border border-white/10">
                    <div class="text-3xl font-black text-[#0055ff]">{{ $satisfacao > 0 ? $satisfacao . '%' : 'N/D' }}</div>
                    <div class="text-[#cbd5e1] text-base mt-2">Taxa de satisfação</div>
                </div>
            </div>
        </div>

        <div class="pub-card rounded-3xl p-8 md:p-10 mb-6 transition">
            <h2 class="text-2xl md:text-3xl font-extrabold text-[#f1f5f9] mb-4">Modelo de negócio</h2>
            <p class="text-[#cbd5e1] text-lg leading-relaxed">A 24 Horas Remoto opera com um modelo de comissão sobre transações, publicidade premium e planos de assinatura para empresas. As fontes de receita são diversificadas e escaláveis sem custos marginais proporcionais.</p>
        </div>

        <div class="pub-card rounded-3xl p-8 md:p-10 transition">
            <h2 class="text-2xl md:text-3xl font-extrabold text-[#f1f5f9] mb-4">Entre em contacto</h2>
            <p class="text-[#cbd5e1] text-lg leading-relaxed">Para apresentações de investimento, parcerias estratégicas ou oportunidades de co-investimento, contacte a nossa equipa através de <strong class="text-[#f1f5f9]">investidores@remoto.ao</strong>. Responderemos dentro de 48 horas úteis.</p>
        </div>

    </div>
</div>
@endsection
