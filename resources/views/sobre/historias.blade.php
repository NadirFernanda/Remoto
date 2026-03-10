@extends('layouts.main')

@section('content')
<div class="pub-page">
    <div class="pub-container--md" style="padding-top:2.5rem;padding-bottom:4rem;">

        <div class="pub-hero" style="margin-bottom:2.5rem;">
            <div class="pub-hero-label">Comunidade</div>
            <h1 class="pub-hero-title">Histórias de sucesso</h1>
            <p class="pub-hero-sub">Conheça clientes e freelancers que transformaram as suas vidas e negócios usando a 24 Horas Remoto.</p>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 mb-6 border border-[#e6f3fa] transition hover:shadow-2xl">
            <div class="flex gap-6 items-start flex-wrap">
                <div class="w-14 h-14 min-w-[3.25rem] rounded-full bg-gradient-to-br from-[#00baff] to-[#0077cc] flex items-center justify-center text-white font-black text-2xl">A</div>
                <div class="flex-1 min-w-[200px]">
                    <div class="font-extrabold text-[#0f172a] mb-1">Ana Fernandes — Desenvolvedora Web, Luanda</div>
                    <div class="text-[#94a3b8] text-xs mb-3">Freelancer verificada · 48 projectos concluídos</div>
                    <p class="text-[#475569] text-lg leading-relaxed m-0">"Antes da 24 Horas Remoto, eu dependia de indicações e passava meses sem trabalho. Hoje tenho uma carteira de clientes estável, recebo em kwanzas de forma segura e consigo prever a minha receita mensal. A plataforma mudou completamente a forma como trabalho."</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 mb-6 border border-[#e6f3fa] transition hover:shadow-2xl">
            <div class="flex gap-6 items-start flex-wrap">
                <div class="w-14 h-14 min-w-[3.25rem] rounded-full bg-gradient-to-br from-[#06b6d4] to-[#0077cc] flex items-center justify-center text-white font-black text-2xl">M</div>
                <div class="flex-1 min-w-[200px]">
                    <div class="font-extrabold text-[#0f172a] mb-1">Manuel Costa — CEO, Startup de Logística, Benguela</div>
                    <div class="text-[#94a3b8] text-xs mb-3">Cliente · 12 projetos contratados</div>
                    <p class="text-[#475569] text-lg leading-relaxed m-0">"Precisávamos de um sistema de gestão de frotas personalizado. Publicámos o projeto, recebemos 8 propostas em 48 horas e contratámos o freelancer certo ao primeiro pedido de revisão. O escrow deu-nos a tranquilidade de pagar apenas após validar cada entrega."</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 mb-6 border border-[#e6f3fa] transition hover:shadow-2xl">
            <div class="flex gap-6 items-start flex-wrap">
                <div class="w-14 h-14 min-w-[3.25rem] rounded-full bg-gradient-to-br from-[#10b981] to-[#0077cc] flex items-center justify-center text-white font-black text-2xl">S</div>
                <div class="flex-1 min-w-[200px]">
                    <div class="font-extrabold text-[#0f172a] mb-1">Sofia Mbala — Designer Gráfico, Huambo</div>
                    <div class="text-[#94a3b8] text-xs mb-3">Freelancer verificada · 31 projectos concluídos</div>
                    <p class="text-[#475569] text-lg leading-relaxed m-0">"Sou de Huambo e nunca imaginei conseguir clientes de Luanda ou do exterior trabalhando remotamente. A 24 Horas Remoto abriu esse caminho para mim. Hoje o meu rendimento como designer ultrapassou o meu antigo salário de empregada."</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 text-center border border-[#e6f3fa] transition hover:shadow-2xl">
            <h2 class="text-xl md:text-2xl font-extrabold text-[#0f172a] mb-2">A sua história pode ser a próxima</h2>
            <p class="text-[#64748b] mb-4 text-lg">Junte-se a milhares de profissionais que já transformaram as suas carreiras.</p>
            <a href="/register" class="inline-block bg-[#00baff] hover:bg-[#009ad6] text-white font-extrabold px-8 py-3 rounded-xl text-lg transition">Começar agora</a>
        </div>

    </div>
</div>
@endsection
