@extends('layouts.main')

@section('content')
<div class="pub-page">
    <div class="pub-container--md" style="padding-top:2.5rem;padding-bottom:4rem;">

        <div class="pub-hero" style="margin-bottom:2.5rem;">
            <div class="pub-hero-label">Comunidade</div>
            <h1 class="pub-hero-title">Histórias de sucesso</h1>
            <p class="pub-hero-sub">Conheça clientes e freelancers que transformaram as suas vidas e negócios usando a 24 Horas Remoto.</p>
        </div>

        <div class="pub-card" style="margin-bottom:1.5rem;">
            <div style="display:flex;gap:1rem;align-items:flex-start;flex-wrap:wrap;">
                <div style="width:52px;height:52px;min-width:52px;border-radius:50%;background:linear-gradient(135deg,#00baff,#0077cc);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:900;font-size:1.3rem;">A</div>
                <div style="flex:1;min-width:200px;">
                    <div style="font-weight:800;color:#0f172a;margin-bottom:.15rem;">Ana Fernandes — Desenvolvedora Web, Luanda</div>
                    <div style="color:#94a3b8;font-size:.82rem;margin-bottom:.75rem;">Freelancer verificada · 48 projectos concluídos</div>
                    <p style="color:#475569;line-height:1.7;margin:0;">"Antes da 24 Horas Remoto, eu dependia de indicações e passava meses sem trabalho. Hoje tenho uma carteira de clientes estável, recebo em kwanzas de forma segura e consigo prever a minha receita mensal. A plataforma mudou completamente a forma como trabalho."</p>
                </div>
            </div>
        </div>

        <div class="pub-card" style="margin-bottom:1.5rem;">
            <div style="display:flex;gap:1rem;align-items:flex-start;flex-wrap:wrap;">
                <div style="width:52px;height:52px;min-width:52px;border-radius:50%;background:linear-gradient(135deg,#06b6d4,#0077cc);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:900;font-size:1.3rem;">M</div>
                <div style="flex:1;min-width:200px;">
                    <div style="font-weight:800;color:#0f172a;margin-bottom:.15rem;">Manuel Costa — CEO, Startup de Logística, Benguela</div>
                    <div style="color:#94a3b8;font-size:.82rem;margin-bottom:.75rem;">Cliente · 12 projetos contratados</div>
                    <p style="color:#475569;line-height:1.7;margin:0;">"Precisávamos de um sistema de gestão de frotas personalizado. Publicámos o projeto, recebemos 8 propostas em 48 horas e contratámos o freelancer certo ao primeiro pedido de revisão. O escrow deu-nos a tranquilidade de pagar apenas após validar cada entrega."</p>
                </div>
            </div>
        </div>

        <div class="pub-card" style="margin-bottom:1.5rem;">
            <div style="display:flex;gap:1rem;align-items:flex-start;flex-wrap:wrap;">
                <div style="width:52px;height:52px;min-width:52px;border-radius:50%;background:linear-gradient(135deg,#10b981,#0077cc);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:900;font-size:1.3rem;">S</div>
                <div style="flex:1;min-width:200px;">
                    <div style="font-weight:800;color:#0f172a;margin-bottom:.15rem;">Sofia Mbala — Designer Gráfico, Huambo</div>
                    <div style="color:#94a3b8;font-size:.82rem;margin-bottom:.75rem;">Freelancer verificada · 31 projectos concluídos</div>
                    <p style="color:#475569;line-height:1.7;margin:0;">"Sou de Huambo e nunca imaginei conseguir clientes de Luanda ou do exterior trabalhando remotamente. A 24 Horas Remoto abriu esse caminho para mim. Hoje o meu rendimento como designer ultrapassou o meu antigo salário de empregada."</p>
                </div>
            </div>
        </div>

        <div class="pub-card" style="text-align:center;">
            <h2 style="font-size:1.1rem;font-weight:800;color:#0f172a;margin-bottom:.5rem;">A sua história pode ser a próxima</h2>
            <p style="color:#64748b;margin-bottom:1rem;font-size:.95rem;">Junte-se a milhares de profissionais que já transformaram as suas carreiras.</p>
            <a href="/register" style="display:inline-block;background:#00baff;color:#fff;font-weight:800;padding:.75rem 2rem;border-radius:10px;text-decoration:none;font-size:.95rem;">Começar agora</a>
        </div>

    </div>
</div>
@endsection
