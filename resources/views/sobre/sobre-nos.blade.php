@extends('layouts.main')

@section('content')
<div class="pub-page" style="padding-top:0">
    <div class="pub-container--md" style="padding-top:0.75rem;padding-bottom:4rem;">

        <div class="pub-hero" style="margin-bottom:2.5rem;">
            <div class="pub-hero-label">Empresa</div>
            <h1 class="pub-hero-title">Sobre nós</h1>
            <p class="pub-hero-sub">Conheça a missão, os valores e a história da plataforma que conecta talentos a oportunidades em Angola.</p>
        </div>

        <div class="rounded-3xl shadow-xl p-7 md:p-8 mb-5 border border-white/10 transition hover:shadow-2xl" style="background:#0d1424;">
            <h2 class="text-2xl md:text-3xl font-extrabold text-[#f1f5f9] mb-4">A nossa missão</h2>
            <p class="text-[#cbd5e1] text-base leading-relaxed">A 24 Horas Remoto nasceu com um propósito claro: democratizar o acesso a serviços digitais de qualidade em Angola e aproximar clientes de freelancers talentosos de forma segura, rápida e transparente. Acreditamos que qualquer empresa — grande ou pequena — merece acesso a profissionais qualificados; e que todo profissional qualificado merece encontrar trabalho digno e bem remunerado.</p>
        </div>

        <div class="rounded-3xl shadow-xl p-7 md:p-8 mb-5 border border-white/10 transition hover:shadow-2xl" style="background:#0d1424;">
            <h2 class="text-2xl md:text-3xl font-extrabold text-[#f1f5f9] mb-4">A nossa história</h2>
            <p class="text-[#cbd5e1] text-base leading-relaxed">Fundada por profissionais que viveram na pele os desafios do mercado freelance angolano, a 24 Horas Remoto surgiu em resposta à necessidade de uma plataforma local, construída com compreensão profunda das realidades culturais e económicas do País. Desde o início, o foco esteve em criar um ambiente de confiança mútua: clientes sabem que receberão o serviço acordado; freelancers sabem que serão pagos.</p>
        </div>

        <div class="rounded-3xl shadow-xl p-7 md:p-8 mb-5 border border-white/10 transition hover:shadow-2xl" style="background:#0d1424;">
            <h2 class="text-2xl md:text-3xl font-extrabold text-[#f1f5f9] mb-6">Os nossos valores</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <div class="rounded-2xl p-5 border border-white/10" style="background:#111c31;">
                    <div class="font-extrabold mb-2 text-lg" style="color:#5b8cff;">Confiança</div>
                    <p class="text-sm leading-relaxed m-0" style="color:#cbd5e1;">Cada transação é protegida por um sistema de escrow que libera o pagamento apenas quando o trabalho é entregue e aprovado.</p>
                </div>
                <div class="rounded-2xl p-5 border border-white/10" style="background:#111c31;">
                    <div class="font-extrabold mb-2 text-lg" style="color:#5b8cff;">Transparência</div>
                    <p class="text-sm leading-relaxed m-0" style="color:#cbd5e1;">Preços claros, avaliações reais e histórico de projectos visível para que todos tomem decisões informadas.</p>
                </div>
                <div class="rounded-2xl p-5 border border-white/10" style="background:#111c31;">
                    <div class="font-extrabold mb-2 text-lg" style="color:#5b8cff;">Excelência</div>
                    <p class="text-sm leading-relaxed m-0" style="color:#cbd5e1;">Verificamos a identidade dos freelancers e incentivamos a formação contínua para manter o padrão de qualidade elevado.</p>
                </div>
            </div>
        </div>

        <div class="rounded-3xl shadow-xl p-7 md:p-8 border border-white/10 transition hover:shadow-2xl" style="background:#0d1424;">
            <h2 class="text-2xl md:text-3xl font-extrabold text-[#f1f5f9] mb-4">Onde estamos</h2>
            <p class="text-[#cbd5e1] text-base leading-relaxed">Com sede em Luanda e presença em todas as províncias de Angola, a 24 Horas Remoto opera de forma 100% digital para que clientes e freelancers possam trabalhar de qualquer lugar do país — e do mundo.</p>
        </div>

    </div>
</div>
@endsection
