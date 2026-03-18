@extends('layouts.main')

@section('content')
<div class="pub-page" style="padding-top:0">
    <div class="pub-container--md" style="padding-top:0.75rem;padding-bottom:4rem;">

        <div class="pub-hero" style="margin-bottom:2.5rem;">
            <div class="pub-hero-label">Guia</div>
            <h1 class="pub-hero-title">Como funciona</h1>
            <p class="pub-hero-sub">Da publicação do projecto à entrega final — veja como a 24 Horas Remoto facilita cada passo da sua contratação.</p>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 mb-6 border border-[#e6f3fa] transition hover:shadow-2xl">
            <h2 class="text-2xl md:text-3xl font-extrabold text-[#0f172a] mb-6">Para clientes</h2>
            <div class="flex flex-col gap-6">
                <div class="flex gap-4 items-start">
                    <div class="w-10 h-10 min-w-[2.5rem] rounded-full bg-gradient-to-br from-[#00baff] to-[#0077cc] text-white font-extrabold text-lg flex items-center justify-center">1</div>
                    <div><span class="font-bold text-[#0f172a]">Crie uma conta gratuita</span><br><span class="text-[#64748b] text-base">Registe-se em menos de dois minutos com o seu e-mail ou conta social.</span></div>
                </div>
                <div class="flex gap-4 items-start">
                    <div class="w-10 h-10 min-w-[2.5rem] rounded-full bg-gradient-to-br from-[#00baff] to-[#0077cc] text-white font-extrabold text-lg flex items-center justify-center">2</div>
                    <div><span class="font-bold text-[#0f172a]">Publique o seu projecto</span><br><span class="text-[#64748b] text-base">Descreva o que precisa, defina o orçamento e o prazo. O nosso formulário guiado ajuda-o a estruturar o briefing em poucos minutos.</span></div>
                </div>
                <div class="flex gap-4 items-start">
                    <div class="w-10 h-10 min-w-[2.5rem] rounded-full bg-gradient-to-br from-[#00baff] to-[#0077cc] text-white font-extrabold text-lg flex items-center justify-center">3</div>
                    <div><span class="font-bold text-[#0f172a]">Receba e avalie propostas</span><br><span class="text-[#64748b] text-base">Freelancers verificados enviam propostas com orçamento e prazo. Compare perfis, portfólios e avaliações antes de escolher.</span></div>
                </div>
                <div class="flex gap-4 items-start">
                    <div class="w-10 h-10 min-w-[2.5rem] rounded-full bg-gradient-to-br from-[#00baff] to-[#0077cc] text-white font-extrabold text-lg flex items-center justify-center">4</div>
                    <div><span class="font-bold text-[#0f172a]">Pague em segurança</span><br><span class="text-[#64748b] text-base">O valor fica retido na plataforma (escrow) e só é libertado após a sua aprovação da entrega.</span></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 mb-6 border border-[#e6f3fa] transition hover:shadow-2xl">
            <h2 class="text-2xl md:text-3xl font-extrabold text-[#0f172a] mb-6">Para freelancers</h2>
            <div class="flex flex-col gap-6">
                <div class="flex gap-4 items-start">
                    <div class="w-10 h-10 min-w-[2.5rem] rounded-full bg-gradient-to-br from-[#06b6d4] to-[#0077cc] text-white font-extrabold text-lg flex items-center justify-center">1</div>
                    <div><span class="font-bold text-[#0f172a]">Crie o seu perfil profissional</span><br><span class="text-[#64748b] text-base">Adicione competências, portfólio e experiências. Quanto mais completo o perfil, mais propostas recebe.</span></div>
                </div>
                <div class="flex gap-4 items-start">
                    <div class="w-10 h-10 min-w-[2.5rem] rounded-full bg-gradient-to-br from-[#06b6d4] to-[#0077cc] text-white font-extrabold text-lg flex items-center justify-center">2</div>
                    <div><span class="font-bold text-[#0f172a]">Explore os projectos disponíveis</span><br><span class="text-[#64748b] text-base">Filtre por categoria, orçamento e prazo. Submeta propostas nos projectos que se alinham com as suas competências.</span></div>
                </div>
                <div class="flex gap-4 items-start">
                    <div class="w-10 h-10 min-w-[2.5rem] rounded-full bg-gradient-to-br from-[#06b6d4] to-[#0077cc] text-white font-extrabold text-lg flex items-center justify-center">3</div>
                    <div><span class="font-bold text-[#0f172a]">Trabalhe e comunique</span><br><span class="text-[#64748b] text-base">Use o chat integrado para alinhar expectativas, partilhar actualizações e entregar milestones.</span></div>
                </div>
                <div class="flex gap-4 items-start">
                    <div class="w-10 h-10 min-w-[2.5rem] rounded-full bg-gradient-to-br from-[#06b6d4] to-[#0077cc] text-white font-extrabold text-lg flex items-center justify-center">4</div>
                    <div><span class="font-bold text-[#0f172a]">Receba o seu pagamento</span><br><span class="text-[#64748b] text-base">Após a aprovação do cliente, o valor é creditado imediatamente na sua carteira digital e pode ser transferido para a sua conta.</span></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 border border-[#e6f3fa] transition hover:shadow-2xl">
            <h2 class="text-2xl md:text-3xl font-extrabold text-[#0f172a] mb-4">Taxas de serviço</h2>
            <p class="text-[#475569] text-lg leading-relaxed">A plataforma cobra uma taxa de serviço sobre cada transação concluída com sucesso. O registo e a publicação de projectos são gratuitos. Consulte a tabela de preços actualizada na secção de ajuda da sua conta.</p>
        </div>

    </div>
</div>
@endsection
