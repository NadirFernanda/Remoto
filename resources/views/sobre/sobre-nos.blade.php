@extends('layouts.main')

@section('content')
<div class="pub-page">
    <div class="pub-container--md" style="padding-top:2.5rem;padding-bottom:4rem;">

        <div class="pub-hero" style="margin-bottom:2.5rem;">
            <div class="pub-hero-label">Empresa</div>
            <h1 class="pub-hero-title">Sobre nós</h1>
            <p class="pub-hero-sub">Conheça a missão, os valores e a história da plataforma que conecta talentos a oportunidades em Angola.</p>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 mb-6 border border-[#e6f3fa] transition hover:shadow-2xl">
            <h2 class="text-2xl md:text-3xl font-extrabold text-[#0f172a] mb-4">Nossa missão</h2>
            <p class="text-[#475569] text-lg leading-relaxed">A 24 Horas Remoto nasceu com um propósito claro: democratizar o acesso a serviços digitais de qualidade em Angola e aproximar clientes de freelancers talentosos de forma segura, rápida e transparente. Acreditamos que qualquer empresa — grande ou pequena — merece acesso a profissionais qualificados; e que todo profissional qualificado merece encontrar trabalho digno e bem remunerado.</p>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 mb-6 border border-[#e6f3fa] transition hover:shadow-2xl">
            <h2 class="text-2xl md:text-3xl font-extrabold text-[#0f172a] mb-4">Nossa história</h2>
            <p class="text-[#475569] text-lg leading-relaxed">Fundada por profissionais que viveram na pele os desafios do mercado freelance angolano, a 24 Horas Remoto surgiu em resposta à necessidade de uma plataforma local, construída com compreensão profunda das realidades culturais e económicas do País. Desde o início, o foco esteve em criar um ambiente de confiança mútua: clientes sabem que receberão o serviço acordado; freelancers sabem que serão pagos.</p>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 mb-6 border border-[#e6f3fa] transition hover:shadow-2xl">
            <h2 class="text-2xl md:text-3xl font-extrabold text-[#0f172a] mb-6">Nossos valores</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <div class="bg-[#f8fafc] rounded-2xl p-6 border border-[#e2e8f0]">
                    <div class="text-[#00baff] font-extrabold mb-2 text-lg">Confiança</div>
                    <p class="text-[#64748b] text-base leading-relaxed m-0">Cada transação é protegida por um sistema de escrow que libera o pagamento apenas quando o trabalho é entregue e aprovado.</p>
                </div>
                <div class="bg-[#f8fafc] rounded-2xl p-6 border border-[#e2e8f0]">
                    <div class="text-[#00baff] font-extrabold mb-2 text-lg">Transparência</div>
                    <p class="text-[#64748b] text-base leading-relaxed m-0">Preços claros, avaliações reais e histórico de projetos visível para que todos tomem decisões informadas.</p>
                </div>
                <div class="bg-[#f8fafc] rounded-2xl p-6 border border-[#e2e8f0]">
                    <div class="text-[#00baff] font-extrabold mb-2 text-lg">Excelência</div>
                    <p class="text-[#64748b] text-base leading-relaxed m-0">Verificamos a identidade dos freelancers e incentivamos a formação contínua para manter o padrão de qualidade elevado.</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 border border-[#e6f3fa] transition hover:shadow-2xl">
            <h2 class="text-2xl md:text-3xl font-extrabold text-[#0f172a] mb-4">Onde estamos</h2>
            <p class="text-[#475569] text-lg leading-relaxed">Com sede em Luanda e presença em todas as províncias de Angola, a 24 Horas Remoto opera de forma 100% digital para que clientes e freelancers possam trabalhar de qualquer lugar do país — e do mundo.</p>
        </div>

    </div>
</div>
@endsection
