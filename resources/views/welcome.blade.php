@extends('layouts.main')

@section('content')

{{-- ============================
     HERO SECTION
============================== --}}
<section style="background: linear-gradient(135deg, #00baff 0%, #0077cc 100%); padding: 5rem 1.5rem 4rem;">
    <div style="max-width:1200px; margin:0 auto; display:flex; align-items:center; justify-content:space-between; gap:3rem; flex-wrap:wrap;">
        <!-- Texto -->
        <div style="flex:1; min-width:280px;">
            <h1 style="font-size:clamp(2rem,4vw,3.2rem); font-weight:900; color:#fff; line-height:1.15; margin-bottom:1.25rem; font-family:'Poppins',sans-serif;">
                Contrate freelancers<br>especializados em<br><span style="color:#fff176;">24 horas</span>
            </h1>
            <p style="font-size:1.15rem; color:rgba(255,255,255,0.9); margin-bottom:2rem; max-width:480px; line-height:1.6;">
                A plataforma mais rÃ¡pida para conectar clientes e profissionais. Poste um projeto gratuito e receba propostas em minutos.
            </p>
            <div style="display:flex; gap:1rem; flex-wrap:wrap;">
                <a href="/register" style="background:#fff; color:#00baff; font-weight:800; padding:.85rem 2rem; border-radius:10px; text-decoration:none; font-size:1rem; box-shadow: 0 6px 24px rgba(0,0,0,0.12);">
                    Publicar projeto grÃ¡tis
                </a>
                <a href="{{ route('freelancers.index') }}" style="background:transparent; color:#fff; font-weight:700; padding:.85rem 2rem; border-radius:10px; text-decoration:none; font-size:1rem; border:2px solid rgba(255,255,255,0.7);">
                    Ver freelancers
                </a>
            </div>
            <!-- EstatÃ­sticas -->
            <div style="display:flex; gap:2.5rem; margin-top:2.5rem; flex-wrap:wrap;">
                <div>
                    <div style="font-size:1.6rem; font-weight:900; color:#fff;">+5.000</div>
                    <div style="font-size:.85rem; color:rgba(255,255,255,0.8);">Freelancers ativos</div>
                </div>
                <div>
                    <div style="font-size:1.6rem; font-weight:900; color:#fff;">+12.000</div>
                    <div style="font-size:.85rem; color:rgba(255,255,255,0.8);">Projetos concluÃ­dos</div>
                </div>
                <div>
                    <div style="font-size:1.6rem; font-weight:900; color:#fff;">98%</div>
                    <div style="font-size:.85rem; color:rgba(255,255,255,0.8);">Clientes satisfeitos</div>
                </div>
            </div>
        </div>
        <!-- Card visual -->
        <div style="flex:0 0 auto; background:rgba(255,255,255,0.12); backdrop-filter:blur(10px); border-radius:20px; padding:2rem; min-width:260px; max-width:320px; border:1px solid rgba(255,255,255,0.25);">
            <div style="display:flex; align-items:center; gap:.75rem; margin-bottom:1.25rem;">
                <div style="width:48px; height:48px; border-radius:50%; background:#fff176; display:flex; align-items:center; justify-content:center; font-size:1.4rem; font-weight:900; color:#0077cc;">A</div>
                <div>
                    <div style="color:#fff; font-weight:700; font-size:.95rem;">Ana Souza</div>
                    <div style="color:rgba(255,255,255,0.75); font-size:.8rem;">Designer UI/UX Â· â­ 4.9</div>
                </div>
            </div>
            <div style="color:rgba(255,255,255,0.9); font-size:.88rem; margin-bottom:1.25rem; line-height:1.5;">"Projeto entregue antes do prazo, comunicaÃ§Ã£o excelente e resultado profissional."</div>
            <div style="background:rgba(255,255,255,0.15); border-radius:8px; padding:.6rem 1rem; display:flex; justify-content:space-between; align-items:center;">
                <span style="color:#fff; font-size:.85rem;">Design de App</span>
                <span style="color:#fff176; font-weight:800; font-size:.95rem;">R$ 850</span>
            </div>
        </div>
    </div>
</section>

{{-- ============================
     COMO FUNCIONA
============================== --}}
<section style="background:#ffffff; padding:4.5rem 1.5rem;">
    <div style="max-width:1100px; margin:0 auto; text-align:center;">
        <p style="color:#00baff; font-weight:700; font-size:.9rem; letter-spacing:.08em; text-transform:uppercase; margin-bottom:.5rem;">Simples e rÃ¡pido</p>
        <h2 style="font-size:clamp(1.6rem,3vw,2.4rem); font-weight:900; color:#0f172a; margin-bottom:.75rem; font-family:'Poppins',sans-serif;">Como funciona</h2>
        <p style="color:#64748b; font-size:1.05rem; max-width:560px; margin:0 auto 3rem;">Em poucos passos, vocÃª conecta clientes e freelancers de forma segura.</p>
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px,1fr)); gap:2rem;">
            @foreach([
                ['icon'=>'1', 'title'=>'Publique seu projeto', 'desc'=>'Descreva o que precisa e defina seu orÃ§amento. Ã‰ gratuito e leva menos de 2 minutos.'],
                ['icon'=>'2', 'title'=>'Receba propostas', 'desc'=>'Freelancers qualificados enviam propostas personalizadas rapidamente.'],
                ['icon'=>'3', 'title'=>'Escolha e contrate', 'desc'=>'Analise perfis, portfÃ³lios e avaliaÃ§Ãµes. Escolha o profissional ideal.'],
                ['icon'=>'4', 'title'=>'Pague com seguranÃ§a', 'desc'=>'Pagamento em custÃ³dia: o freelancer sÃ³ recebe apÃ³s sua aprovaÃ§Ã£o.'],
            ] as $step)
            <div style="background:#f8fafc; border-radius:16px; padding:2rem 1.5rem; text-align:center; border:1px solid #e2e8f0;">
                <div style="width:52px; height:52px; border-radius:50%; background:linear-gradient(135deg,#00baff,#0077cc); color:#fff; font-size:1.3rem; font-weight:900; display:flex; align-items:center; justify-content:center; margin:0 auto 1.25rem;">{{ $step['icon'] }}</div>
                <h3 style="font-size:1.05rem; font-weight:800; color:#0f172a; margin-bottom:.5rem;">{{ $step['title'] }}</h3>
                <p style="color:#64748b; font-size:.9rem; line-height:1.55;">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================
     CATEGORIAS POPULARES
============================== --}}
<section style="background:#f5f7fa; padding:4.5rem 1.5rem;">
    <div style="max-width:1100px; margin:0 auto; text-align:center;">
        <p style="color:#00baff; font-weight:700; font-size:.9rem; letter-spacing:.08em; text-transform:uppercase; margin-bottom:.5rem;">Explore</p>
        <h2 style="font-size:clamp(1.6rem,3vw,2.4rem); font-weight:900; color:#0f172a; margin-bottom:.75rem; font-family:'Poppins',sans-serif;">Categorias populares</h2>
        <p style="color:#64748b; font-size:1.05rem; max-width:560px; margin:0 auto 3rem;">Encontre o profissional certo para cada tipo de projeto.</p>
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(160px,1fr)); gap:1.25rem;">
            @foreach([
                ['emoji'=>'ðŸŽ¨', 'label'=>'Design & Arte'],
                ['emoji'=>'ðŸ’»', 'label'=>'Desenvolvimento Web'],
                ['emoji'=>'ðŸ“±', 'label'=>'Apps Mobile'],
                ['emoji'=>'âœï¸', 'label'=>'RedaÃ§Ã£o & ConteÃºdo'],
                ['emoji'=>'ðŸ“Š', 'label'=>'Marketing Digital'],
                ['emoji'=>'ðŸŽ¬', 'label'=>'VÃ­deo & Ãudio'],
                ['emoji'=>'ðŸ”', 'label'=>'SEO & Analytics'],
                ['emoji'=>'ðŸ¤', 'label'=>'Suporte & Admin'],
            ] as $cat)
            <a href="{{ route('freelancers.index') }}" style="background:#fff; border-radius:14px; padding:1.5rem 1rem; text-align:center; border:1px solid #e2e8f0; text-decoration:none; display:flex; flex-direction:column; align-items:center; gap:.6rem;">
                <span style="font-size:2rem;">{{ $cat['emoji'] }}</span>
                <span style="font-size:.85rem; font-weight:700; color:#1e293b;">{{ $cat['label'] }}</span>
            </a>
            @endforeach
        </div>
        <div style="margin-top:2.5rem;">
            <a href="{{ route('freelancers.index') }}" style="background:#00baff; color:#fff; font-weight:800; padding:.85rem 2.5rem; border-radius:10px; text-decoration:none; font-size:1rem; display:inline-block;">
                Ver todas as categorias â†’
            </a>
        </div>
    </div>
</section>

{{-- ============================
     POR QUÃŠ ESCOLHER A PLATAFORMA
============================== --}}
<section style="background:#ffffff; padding:4.5rem 1.5rem;">
    <div style="max-width:1100px; margin:0 auto; display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:2.5rem; align-items:center;">
        <div>
            <p style="color:#00baff; font-weight:700; font-size:.9rem; letter-spacing:.08em; text-transform:uppercase; margin-bottom:.5rem;">Por que nÃ³s</p>
            <h2 style="font-size:clamp(1.6rem,3vw,2.4rem); font-weight:900; color:#0f172a; margin-bottom:1.25rem; font-family:'Poppins',sans-serif;">Trabalhe com<br>seguranÃ§a e confianÃ§a</h2>
            <p style="color:#64748b; font-size:1.05rem; line-height:1.7; margin-bottom:2rem;">Nossa plataforma protege clientes e freelancers com sistema de custÃ³dia, verificaÃ§Ã£o de identidade e suporte dedicado.</p>
            <a href="/register" style="background:#00baff; color:#fff; font-weight:800; padding:.85rem 2rem; border-radius:10px; text-decoration:none; font-size:1rem; display:inline-block;">
                Criar conta gratuita
            </a>
        </div>
        <div style="display:flex; flex-direction:column; gap:1.25rem;">
            @foreach([
                ['icon'=>'ðŸ”’', 'title'=>'Pagamento seguro', 'desc'=>'Dinheiro fica em custÃ³dia e sÃ³ Ã© liberado quando vocÃª aprovar o trabalho.'],
                ['icon'=>'âœ…', 'title'=>'Freelancers verificados', 'desc'=>'Perfis validados, portfÃ³lios reais e avaliaÃ§Ãµes de clientes anteriores.'],
                ['icon'=>'ðŸ’¬', 'title'=>'Suporte 24h', 'desc'=>'Nossa equipe estÃ¡ disponÃ­vel para ajudar clientes e freelancers a qualquer momento.'],
                ['icon'=>'âš¡', 'title'=>'Resultados rÃ¡pidos', 'desc'=>'Receba propostas em minutos e inicie seu projeto em menos de 24 horas.'],
            ] as $item)
            <div style="display:flex; gap:1rem; align-items:flex-start; background:#f8fafc; border-radius:12px; padding:1.25rem; border:1px solid #e2e8f0;">
                <div style="font-size:1.6rem; flex-shrink:0;">{{ $item['icon'] }}</div>
                <div>
                    <div style="font-weight:800; color:#0f172a; font-size:.95rem; margin-bottom:.25rem;">{{ $item['title'] }}</div>
                    <div style="color:#64748b; font-size:.88rem; line-height:1.5;">{{ $item['desc'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================
     DEPOIMENTOS
============================== --}}
<section style="background:#f5f7fa; padding:4.5rem 1.5rem;">
    <div style="max-width:1100px; margin:0 auto; text-align:center;">
        <p style="color:#00baff; font-weight:700; font-size:.9rem; letter-spacing:.08em; text-transform:uppercase; margin-bottom:.5rem;">Depoimentos</p>
        <h2 style="font-size:clamp(1.6rem,3vw,2.4rem); font-weight:900; color:#0f172a; margin-bottom:.75rem; font-family:'Poppins',sans-serif;">O que dizem nossos usuÃ¡rios</h2>
        <p style="color:#64748b; font-size:1.05rem; max-width:560px; margin:0 auto 3rem;">HistÃ³rias reais de clientes e freelancers que transformaram seus negÃ³cios.</p>
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px,1fr)); gap:1.75rem;">
            @foreach([
                ['initial'=>'C','name'=>'Carlos Mendes','role'=>'Cliente Â· SÃ£o Paulo','text'=>'"Publicou o projeto e jÃ¡ tinha 8 propostas em menos de 2 horas. Contratei uma designer incrÃ­vel e o logo ficou perfeito!"','stars'=>5],
                ['initial'=>'J','name'=>'Juliana Lima','role'=>'Freelancer Â· Designer UI/UX','text'=>'"JÃ¡ consegui 15 clientes pela plataforma. O sistema de pagamento Ã© seguro e o suporte responde rapidinho."','stars'=>5],
                ['initial'=>'R','name'=>'Rafael Costa','role'=>'Cliente Â· Rio de Janeiro','text'=>'"Meu site foi desenvolvido em 5 dias. Qualidade profissional, comunicaÃ§Ã£o excelente e dentro do orÃ§amento!"','stars'=>5],
            ] as $dep)
            <div style="background:#fff; border-radius:16px; padding:2rem; text-align:left; border:1px solid #e2e8f0; box-shadow:0 2px 12px rgba(0,0,0,0.04);">
                <div style="color:#f59e0b; font-size:1rem; margin-bottom:1rem; letter-spacing:.1em;">
                    {{ str_repeat('â˜…', $dep['stars']) }}
                </div>
                <p style="color:#334155; font-size:.95rem; line-height:1.65; margin-bottom:1.5rem; font-style:italic;">{{ $dep['text'] }}</p>
                <div style="display:flex; align-items:center; gap:.75rem;">
                    <div style="width:42px; height:42px; border-radius:50%; background:linear-gradient(135deg,#00baff,#0077cc); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:900; font-size:1rem; flex-shrink:0;">{{ $dep['initial'] }}</div>
                    <div>
                        <div style="font-weight:800; color:#0f172a; font-size:.9rem;">{{ $dep['name'] }}</div>
                        <div style="color:#94a3b8; font-size:.78rem;">{{ $dep['role'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================
     CTA FINAL
============================== --}}
<section style="background:linear-gradient(135deg,#00baff 0%,#0077cc 100%); padding:5rem 1.5rem; text-align:center;">
    <div style="max-width:700px; margin:0 auto;">
        <h2 style="font-size:clamp(1.8rem,3.5vw,2.8rem); font-weight:900; color:#fff; margin-bottom:1rem; font-family:'Poppins',sans-serif;">Pronto para comeÃ§ar?</h2>
        <p style="color:rgba(255,255,255,0.9); font-size:1.1rem; margin-bottom:2.5rem; line-height:1.6;">Junte-se a milhares de clientes e freelancers que jÃ¡ confiam na 24 Horas. Cadastro 100% gratuito.</p>
        <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
            <a href="/register" style="background:#fff; color:#00baff; font-weight:900; padding:1rem 2.5rem; border-radius:12px; text-decoration:none; font-size:1.05rem; box-shadow:0 8px 24px rgba(0,0,0,0.15);">
                Criar conta grÃ¡tis
            </a>
            <a href="{{ route('public.projects') }}" style="background:transparent; color:#fff; font-weight:700; padding:1rem 2.5rem; border-radius:12px; text-decoration:none; font-size:1.05rem; border:2px solid rgba(255,255,255,0.7);">
                Ver projetos disponÃ­veis
            </a>
        </div>
    </div>
</section>

@include('components.freelancer-modal')

@endsection
