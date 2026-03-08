@extends('layouts.main')

@section('content')
<div class="pub-page">
    <div class="pub-container--md" style="padding-top:2.5rem;padding-bottom:4rem;">

        <div class="pub-hero" style="margin-bottom:2.5rem;">
            <div class="pub-hero-label">Guia</div>
            <h1 class="pub-hero-title">Como funciona</h1>
            <p class="pub-hero-sub">Da publicação do projeto à entrega final — veja como a Remoto facilita cada passo da sua contratação.</p>
        </div>

        <div class="pub-card" style="margin-bottom:1.5rem;">
            <h2 style="font-size:1.15rem;font-weight:800;color:#0f172a;margin-bottom:1rem;">Para clientes</h2>
            <div style="display:flex;flex-direction:column;gap:1rem;">
                <div style="display:flex;gap:1rem;align-items:flex-start;">
                    <div style="width:36px;height:36px;min-width:36px;border-radius:50%;background:linear-gradient(135deg,#00baff,#0077cc);color:#fff;font-weight:900;font-size:.95rem;display:flex;align-items:center;justify-content:center;">1</div>
                    <div><strong style="color:#0f172a;">Crie uma conta gratuita</strong><br><span style="color:#64748b;font-size:.9rem;">Registe-se em menos de dois minutos com o seu e-mail ou conta social.</span></div>
                </div>
                <div style="display:flex;gap:1rem;align-items:flex-start;">
                    <div style="width:36px;height:36px;min-width:36px;border-radius:50%;background:linear-gradient(135deg,#00baff,#0077cc);color:#fff;font-weight:900;font-size:.95rem;display:flex;align-items:center;justify-content:center;">2</div>
                    <div><strong style="color:#0f172a;">Publique o seu projeto</strong><br><span style="color:#64748b;font-size:.9rem;">Descreva o que precisa, defina o orçamento e o prazo. O nosso formulário guiado ajuda-o a estruturar o briefing em poucos minutos.</span></div>
                </div>
                <div style="display:flex;gap:1rem;align-items:flex-start;">
                    <div style="width:36px;height:36px;min-width:36px;border-radius:50%;background:linear-gradient(135deg,#00baff,#0077cc);color:#fff;font-weight:900;font-size:.95rem;display:flex;align-items:center;justify-content:center;">3</div>
                    <div><strong style="color:#0f172a;">Receba e avalie propostas</strong><br><span style="color:#64748b;font-size:.9rem;">Freelancers verificados enviam propostas com orçamento e prazo. Compare perfis, portfólios e avaliações antes de escolher.</span></div>
                </div>
                <div style="display:flex;gap:1rem;align-items:flex-start;">
                    <div style="width:36px;height:36px;min-width:36px;border-radius:50%;background:linear-gradient(135deg,#00baff,#0077cc);color:#fff;font-weight:900;font-size:.95rem;display:flex;align-items:center;justify-content:center;">4</div>
                    <div><strong style="color:#0f172a;">Pague em segurança</strong><br><span style="color:#64748b;font-size:.9rem;">O valor fica retido na plataforma (escrow) e só é liberado após a sua aprovação da entrega.</span></div>
                </div>
            </div>
        </div>

        <div class="pub-card" style="margin-bottom:1.5rem;">
            <h2 style="font-size:1.15rem;font-weight:800;color:#0f172a;margin-bottom:1rem;">Para freelancers</h2>
            <div style="display:flex;flex-direction:column;gap:1rem;">
                <div style="display:flex;gap:1rem;align-items:flex-start;">
                    <div style="width:36px;height:36px;min-width:36px;border-radius:50%;background:linear-gradient(135deg,#06b6d4,#0077cc);color:#fff;font-weight:900;font-size:.95rem;display:flex;align-items:center;justify-content:center;">1</div>
                    <div><strong style="color:#0f172a;">Crie o seu perfil profissional</strong><br><span style="color:#64748b;font-size:.9rem;">Adicione competências, portfólio e experiências. Quanto mais completo o perfil, mais propostas recebe.</span></div>
                </div>
                <div style="display:flex;gap:1rem;align-items:flex-start;">
                    <div style="width:36px;height:36px;min-width:36px;border-radius:50%;background:linear-gradient(135deg,#06b6d4,#0077cc);color:#fff;font-weight:900;font-size:.95rem;display:flex;align-items:center;justify-content:center;">2</div>
                    <div><strong style="color:#0f172a;">Explore os projetos disponíveis</strong><br><span style="color:#64748b;font-size:.9rem;">Filtre por categoria, orçamento e prazo. Submeta propostas nos projetos que se alinham com as suas competências.</span></div>
                </div>
                <div style="display:flex;gap:1rem;align-items:flex-start;">
                    <div style="width:36px;height:36px;min-width:36px;border-radius:50%;background:linear-gradient(135deg,#06b6d4,#0077cc);color:#fff;font-weight:900;font-size:.95rem;display:flex;align-items:center;justify-content:center;">3</div>
                    <div><strong style="color:#0f172a;">Trabalhe e comunique</strong><br><span style="color:#64748b;font-size:.9rem;">Use o chat integrado para alinhar expectativas, partilhar actualizações e entregar milestones.</span></div>
                </div>
                <div style="display:flex;gap:1rem;align-items:flex-start;">
                    <div style="width:36px;height:36px;min-width:36px;border-radius:50%;background:linear-gradient(135deg,#06b6d4,#0077cc);color:#fff;font-weight:900;font-size:.95rem;display:flex;align-items:center;justify-content:center;">4</div>
                    <div><strong style="color:#0f172a;">Receba o seu pagamento</strong><br><span style="color:#64748b;font-size:.9rem;">Após a aprovação do cliente, o valor é creditado imediatamente na sua carteira digital e pode ser transferido para a sua conta.</span></div>
                </div>
            </div>
        </div>

        <div class="pub-card">
            <h2 style="font-size:1.15rem;font-weight:800;color:#0f172a;margin-bottom:.75rem;">Taxas de serviço</h2>
            <p style="color:#475569;line-height:1.7;">A plataforma cobra uma taxa de serviço sobre cada transação concluída com sucesso. O registo e a publicação de projetos são gratuitos. Consulte a tabela de preços atualizada na secção de ajuda da sua conta.</p>
        </div>

    </div>
</div>
@endsection
