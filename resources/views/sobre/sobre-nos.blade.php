@extends('layouts.main')

@section('content')
<div class="pub-page">
    <div class="pub-container--md" style="padding-top:2.5rem;padding-bottom:4rem;">

        <div class="pub-hero" style="margin-bottom:2.5rem;">
            <div class="pub-hero-label">Empresa</div>
            <h1 class="pub-hero-title">Sobre nós</h1>
            <p class="pub-hero-sub">Conheça a missão, os valores e a história da plataforma que conecta talentos a oportunidades em Angola.</p>
        </div>

        <div class="pub-card" style="margin-bottom:1.5rem;">
            <h2 style="font-size:1.25rem;font-weight:800;color:#0f172a;margin-bottom:.75rem;">Nossa missão</h2>
            <p style="color:#475569;line-height:1.7;">A 24 Horas Remoto nasceu com um propósito claro: democratizar o acesso a serviços digitais de qualidade em Angola e aproximar clientes de freelancers talentosos de forma segura, rápida e transparente. Acreditamos que qualquer empresa — grande ou pequena — merece acesso a profissionais qualificados; e que todo profissional qualificado merece encontrar trabalho digno e bem remunerado.</p>
        </div>

        <div class="pub-card" style="margin-bottom:1.5rem;">
            <h2 style="font-size:1.25rem;font-weight:800;color:#0f172a;margin-bottom:.75rem;">Nossa história</h2>
            <p style="color:#475569;line-height:1.7;">Fundada por profissionais que viveram na pele os desafios do mercado freelance angolano, a 24 Horas Remoto surgiu em resposta à necessidade de uma plataforma local, construída com compreensão profunda das realidades culturais e económicas do País. Desde o início, o foco esteve em criar um ambiente de confiança mútua: clientes sabem que receberão o serviço acordado; freelancers sabem que serão pagos.</p>
        </div>

        <div class="pub-card" style="margin-bottom:1.5rem;">
            <h2 style="font-size:1.25rem;font-weight:800;color:#0f172a;margin-bottom:1rem;">Nossos valores</h2>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem;">
                <div style="background:#f8fafc;border-radius:12px;padding:1.25rem;border:1px solid #e2e8f0;">
                    <div style="color:#00baff;font-weight:800;margin-bottom:.35rem;">Confiança</div>
                    <p style="color:#64748b;font-size:.9rem;margin:0;line-height:1.55;">Cada transação é protegida por um sistema de escrow que libera o pagamento apenas quando o trabalho é entregue e aprovado.</p>
                </div>
                <div style="background:#f8fafc;border-radius:12px;padding:1.25rem;border:1px solid #e2e8f0;">
                    <div style="color:#00baff;font-weight:800;margin-bottom:.35rem;">Transparência</div>
                    <p style="color:#64748b;font-size:.9rem;margin:0;line-height:1.55;">Preços claros, avaliações reais e histórico de projetos visível para que todos tomem decisões informadas.</p>
                </div>
                <div style="background:#f8fafc;border-radius:12px;padding:1.25rem;border:1px solid #e2e8f0;">
                    <div style="color:#00baff;font-weight:800;margin-bottom:.35rem;">Excelência</div>
                    <p style="color:#64748b;font-size:.9rem;margin:0;line-height:1.55;">Verificamos a identidade dos freelancers e incentivamos a formação contínua para manter o padrão de qualidade elevado.</p>
                </div>
            </div>
        </div>

        <div class="pub-card">
            <h2 style="font-size:1.25rem;font-weight:800;color:#0f172a;margin-bottom:.75rem;">Onde estamos</h2>
            <p style="color:#475569;line-height:1.7;">Com sede em Luanda e presença em todas as províncias de Angola, a 24 Horas Remoto opera de forma 100% digital para que clientes e freelancers possam trabalhar de qualquer lugar do país — e do mundo.</p>
        </div>

    </div>
</div>
@endsection
