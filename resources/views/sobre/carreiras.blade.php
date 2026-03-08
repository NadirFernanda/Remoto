@extends('layouts.main')

@section('content')
<div class="pub-page">
    <div class="pub-container--md" style="padding-top:2.5rem;padding-bottom:4rem;">

        <div class="pub-hero" style="margin-bottom:2.5rem;">
            <div class="pub-hero-label">Trabalhe connosco</div>
            <h1 class="pub-hero-title">Carreiras na 24 Horas Remoto</h1>
            <p class="pub-hero-sub">Junte-se a uma equipe que está a redefinir o futuro do trabalho em Angola. Construímos uma cultura de impacto, autonomia e crescimento contínuo.</p>
        </div>

        <div class="pub-card" style="margin-bottom:1.5rem;">
            <h2 style="font-size:1.1rem;font-weight:800;color:#0f172a;margin-bottom:.75rem;">Por que trabalhar na 24 Horas Remoto?</h2>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;">
                <div style="background:#f8fafc;border-radius:12px;padding:1.25rem;border:1px solid #e2e8f0;">
                    <div style="color:#00baff;font-weight:800;margin-bottom:.35rem;">🚀 Missão com impacto</div>
                    <p style="color:#64748b;font-size:.88rem;margin:0;line-height:1.55;">O seu trabalho muda directamente a vida de freelancers e empresas angolanas.</p>
                </div>
                <div style="background:#f8fafc;border-radius:12px;padding:1.25rem;border:1px solid #e2e8f0;">
                    <div style="color:#00baff;font-weight:800;margin-bottom:.35rem;">🌍 100% Remoto</div>
                    <p style="color:#64748b;font-size:.88rem;margin:0;line-height:1.55;">Trabalhe de qualquer lugar de Angola — ou do mundo — com flexibilidade total.</p>
                </div>
                <div style="background:#f8fafc;border-radius:12px;padding:1.25rem;border:1px solid #e2e8f0;">
                    <div style="color:#00baff;font-weight:800;margin-bottom:.35rem;">📈 Crescimento acelerado</div>
                    <p style="color:#64748b;font-size:.88rem;margin:0;line-height:1.55;">Equipe pequena, decisões rápidas, visibilidade real do seu trabalho desde o primeiro dia.</p>
                </div>
            </div>
        </div>

        <div class="pub-card" style="margin-bottom:1.25rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
                <div>
                    <div style="font-weight:800;color:#0f172a;font-size:1rem;">Desenvolvedor(a) Full-Stack (Laravel / Vue.js)</div>
                    <div style="color:#64748b;font-size:.88rem;margin-top:.2rem;">Engenharia · Remoto · Tempo inteiro</div>
                </div>
                <a href="mailto:carreiras@remoto.ao?subject=Candidatura%20Full-Stack" style="display:inline-block;background:#00baff;color:#fff;font-weight:700;padding:.55rem 1.25rem;border-radius:8px;text-decoration:none;font-size:.9rem;white-space:nowrap;">Candidatar-se</a>
            </div>
        </div>

        <div class="pub-card" style="margin-bottom:1.25rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
                <div>
                    <div style="font-weight:800;color:#0f172a;font-size:1rem;">Designer de Produto (UI/UX)</div>
                    <div style="color:#64748b;font-size:.88rem;margin-top:.2rem;">Produto · Remoto · Tempo inteiro</div>
                </div>
                <a href="mailto:carreiras@remoto.ao?subject=Candidatura%20Designer%20UX" style="display:inline-block;background:#00baff;color:#fff;font-weight:700;padding:.55rem 1.25rem;border-radius:8px;text-decoration:none;font-size:.9rem;white-space:nowrap;">Candidatar-se</a>
            </div>
        </div>

        <div class="pub-card" style="margin-bottom:1.25rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
                <div>
                    <div style="font-weight:800;color:#0f172a;font-size:1rem;">Especialista em Suporte e Comunidade</div>
                    <div style="color:#64748b;font-size:.88rem;margin-top:.2rem;">Operações · Luanda ou Remoto · Tempo inteiro</div>
                </div>
                <a href="mailto:carreiras@remoto.ao?subject=Candidatura%20Suporte" style="display:inline-block;background:#00baff;color:#fff;font-weight:700;padding:.55rem 1.25rem;border-radius:8px;text-decoration:none;font-size:.9rem;white-space:nowrap;">Candidatar-se</a>
            </div>
        </div>

        <div class="pub-card" style="background:#f8fafc;">
            <h3 style="font-size:1rem;font-weight:800;color:#0f172a;margin-bottom:.5rem;">Não encontrou a vaga certa?</h3>
            <p style="color:#475569;font-size:.9rem;line-height:1.7;margin:0;">Envie o seu CV e carta de motivação para <strong>carreiras@remoto.ao</strong>. Guardamos o seu perfil e contactamos assim que surgir uma oportunidade adequada.</p>
        </div>

    </div>
</div>
@endsection
