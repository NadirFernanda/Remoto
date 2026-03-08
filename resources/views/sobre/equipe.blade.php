@extends('layouts.main')

@section('content')
<div class="pub-page">
    <div class="pub-container--md" style="padding-top:2.5rem;padding-bottom:4rem;">

        <div class="pub-hero" style="margin-bottom:2.5rem;">
            <div class="pub-hero-label">Pessoas</div>
            <h1 class="pub-hero-title">Nossa equipe</h1>
            <p class="pub-hero-sub">Conheça as pessoas apaixonadas que constroem e evoluem a 24 Horas Remoto todos os dias.</p>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1.5rem;margin-bottom:1.5rem;">

            <div class="pub-card" style="text-align:center;">
                <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#00baff,#0077cc);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:900;font-size:1.6rem;margin:0 auto 1rem;">N</div>
                <div style="font-weight:800;color:#0f172a;font-size:1rem;">Nadir Fernanda</div>
                <div style="color:#00baff;font-size:.82rem;font-weight:700;margin:.2rem 0 .6rem;">CEO &amp; Co-fundadora</div>
                <p style="color:#64748b;font-size:.88rem;line-height:1.55;margin:0;">Visionária por trás da 24 Horas Remoto, com experiência em empreendedorismo digital e desenvolvimento de produto na África Subsaariana.</p>
            </div>

            <div class="pub-card" style="text-align:center;">
                <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#06b6d4,#0077cc);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:900;font-size:1.6rem;margin:0 auto 1rem;">P</div>
                <div style="font-weight:800;color:#0f172a;font-size:1rem;">Paulo Mendes</div>
                <div style="color:#00baff;font-size:.82rem;font-weight:700;margin:.2rem 0 .6rem;">CTO &amp; Co-fundador</div>
                <p style="color:#64748b;font-size:.88rem;line-height:1.55;margin:0;">Engenheiro de software com mais de 10 anos de experiência em plataformas de marketplace e sistemas de pagamento seguros.</p>
            </div>

            <div class="pub-card" style="text-align:center;">
                <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#10b981,#0077cc);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:900;font-size:1.6rem;margin:0 auto 1rem;">C</div>
                <div style="font-weight:800;color:#0f172a;font-size:1rem;">Catarina Lopes</div>
                <div style="color:#00baff;font-size:.82rem;font-weight:700;margin:.2rem 0 .6rem;">Directora de Produto</div>
                <p style="color:#64748b;font-size:.88rem;line-height:1.55;margin:0;">Especialista em UX e growth, responsável por transformar feedback de utilizadores em funcionalidades que fazem a diferença real.</p>
            </div>

            <div class="pub-card" style="text-align:center;">
                <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#f59e0b,#0077cc);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:900;font-size:1.6rem;margin:0 auto 1rem;">R</div>
                <div style="font-weight:800;color:#0f172a;font-size:1rem;">Roberto Silva</div>
                <div style="color:#00baff;font-size:.82rem;font-weight:700;margin:.2rem 0 .6rem;">Director de Operações</div>
                <p style="color:#64748b;font-size:.88rem;line-height:1.55;margin:0;">Garante que cada transação, debate e resolução de disputa ocorre com eficiência e justiça para ambas as partes.</p>
            </div>

        </div>

        <div class="pub-card" style="text-align:center;">
            <h2 style="font-size:1.1rem;font-weight:800;color:#0f172a;margin-bottom:.5rem;">Quer fazer parte da nossa equipe?</h2>
            <p style="color:#64748b;font-size:.9rem;margin-bottom:1rem;">Estamos sempre à procura de talentos apaixonados pela nossa missão.</p>
            <a href="{{ route('sobre.carreiras') }}" style="display:inline-block;background:#00baff;color:#fff;font-weight:800;padding:.75rem 2rem;border-radius:10px;text-decoration:none;font-size:.95rem;">Ver vagas abertas</a>
        </div>

    </div>
</div>
@endsection
