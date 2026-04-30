@extends('layouts.main')

@section('content')

{{-- ============================
     HERO — CARROSSEL
============================== --}}
<section class="hp-hero"
    x-data="{
        slide: 0,
        total: 3,
        timer: null,
        start(){ this.timer = setInterval(() => { this.slide = (this.slide + 1) % this.total }, 5000) },
        stop(){ clearInterval(this.timer) },
        go(n){ this.slide = n; this.stop(); this.start() }
    }"
    x-init="start()"
    @mouseenter="stop()"
    @mouseleave="start()">

        {{-- Imagens de fundo em crossfade (overlay azul forte acima delas) --}}

    <div class="hp-hero-bg" :class="slide===0 ? 'hp-bg-active' : ''" style="background-image:url('/img/heru1.jpg'); background-position:center 30%;"></div>
    <div class="hp-hero-bg" :class="slide===1 ? 'hp-bg-active' : ''" style="background-image:url('/img/heru2.jpg'); background-position:center 55%; transform:scale(1.05);"></div>
    <div class="hp-hero-bg" :class="slide===2 ? 'hp-bg-active' : ''" style="background-image:url('/img/heru3.jpg'); background-position:center 40%;"></div>
    <div class="hp-hero-overlay"></div>

    {{-- Slide 1 --}}
    <div class="hp-hero-slide" x-show="slide===0">
        <div class="hp-hero-inner">
            <div class="hp-hero-text">
                <h1 class="hp-hero-title">Contrate os melhores<br>freelancers para<br><span class="hp-hero-accent">qualquer projecto</span></h1>
                <ul class="hp-hero-bullets">
                    <li>N.º 1 marketplace freelance de Angola</li>
                    <li>Qualquer serviço que precisar</li>
                    <li>Receba propostas em minutos, gratuitamente</li>
                    <li>Pague só quando estiver 100% satisfeito</li>
                </ul>
                <div class="hp-ctas">
                    <a href="/register" class="hp-btn hp-btn-white">Contratar Freelancer</a>
                    <a href="/register" class="hp-btn hp-btn-outline-white">Ganhar Dinheiro como Freelancer</a>
                </div>
            </div>
            <div class="hp-hero-card">
                <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem;">
                    <div class="hp-hero-card-avatar">A</div>
                    <div>
                        <div class="hp-hero-card-stars">★★★★★</div>
                        <div class="hp-hero-card-name">Ana Souza</div>
                        <div class="hp-hero-card-role">Designer UI/UX</div>
                    </div>
                </div>
                <p class="hp-hero-card-quote">"Projecto entregue antes do prazo, comunicação excelente e resultado profissional."</p>
                <div class="hp-hero-card-tag">
                    <span class="hp-hero-card-tag-label">Design de App</span>
                    <span class="hp-hero-card-tag-price">Kz 85.000</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Slide 2 --}}
    <div class="hp-hero-slide" x-show="slide===1" style="display:none;">
        <div class="hp-hero-inner">
            <div class="hp-hero-text">
                <h1 class="hp-hero-title">Ganhe dinheiro a fazer<br>o que <span class="hp-hero-accent">ama</span></h1>
                <ul class="hp-hero-bullets">
                    <li>Crie o seu perfil gratuito em minutos</li>
                    <li>Aceda a milhares de projectos todos os dias</li>
                    <li>Defina o seu preço e horários</li>
                    <li>Receba pagamentos seguros e rápidos</li>
                </ul>
                <div class="hp-ctas">
                    <a href="/register" class="hp-btn hp-btn-white">Começar como Freelancer</a>
                    <a href="{{ route('public.projects') }}" class="hp-btn hp-btn-outline-white">Ver projectos disponíveis</a>
                </div>
            </div>
            <div class="hp-hero-card">
                <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem;">
                    <div class="hp-hero-card-avatar" style="background:#c7f7e0;color:#059669;">M</div>
                    <div>
                        <div class="hp-hero-card-stars">★★★★★</div>
                        <div class="hp-hero-card-name">Marcos Oliveira</div>
                        <div class="hp-hero-card-role">Dev Full Stack</div>
                    </div>
                </div>
                <p class="hp-hero-card-quote">"Encontrei projectos incríveis logo na primeira semana. A plataforma é simples e o pagamento é seguro."</p>
                <div class="hp-hero-card-tag">
                    <span class="hp-hero-card-tag-label">Sistema Web</span>
                    <span class="hp-hero-card-tag-price">Kz 240.000</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Slide 3 --}}
    <div class="hp-hero-slide" x-show="slide===2" style="display:none;">
        <div class="hp-hero-inner">
            <div class="hp-hero-text">
                <h1 class="hp-hero-title">Tudo que o seu negócio<br>precisa, num <span class="hp-hero-accent">só lugar</span></h1>
                <ul class="hp-hero-bullets">
                    <li>Design, Dev, Marketing, Redação e muito mais</li>
                    <li>+5.000 profissionais verificados activos</li>
                    <li>Sistema de custódia: pague com segurança</li>
                    <li>Suporte dedicado 24 horas por dia</li>
                </ul>
                <div class="hp-ctas">
                    <a href="/register" class="hp-btn hp-btn-white">Publicar projecto gratuito</a>
                    <a href="{{ route('freelancers.index') }}" class="hp-btn hp-btn-outline-white">Explorar freelancers</a>
                </div>
            </div>
            <div class="hp-hero-card">
                <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem;">
                    <div class="hp-hero-card-avatar" style="background:#fde8ff;color:#9333ea;">C</div>
                    <div>
                        <div class="hp-hero-card-stars">★★★★★</div>
                        <div class="hp-hero-card-name">Carla Ferreira</div>
                        <div class="hp-hero-card-role">Marketing Digital</div>
                    </div>
                </div>
                <p class="hp-hero-card-quote">"Minha campanha teve 3× mais resultados. Profissional incrível, contratada em menos de 2 horas!"</p>
                <div class="hp-hero-card-tag">
                    <span class="hp-hero-card-tag-label">Campanha Ads</span>
                    <span class="hp-hero-card-tag-price">Kz 120.000</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Dots --}}
    <div class="hp-hero-dots">
        <button @click="go(0)" :class="slide===0 ? 'hp-dot-active' : ''" class="hp-hero-dot" aria-label="Slide 1"></button>
        <button @click="go(1)" :class="slide===1 ? 'hp-dot-active' : ''" class="hp-hero-dot" aria-label="Slide 2"></button>
        <button @click="go(2)" :class="slide===2 ? 'hp-dot-active' : ''" class="hp-hero-dot" aria-label="Slide 3"></button>
    </div>
</section>

{{-- ============================
     COMO FUNCIONA
============================== --}}
<section class="hp-section hp-section--white">
    <div class="hp-container" style="text-align:center;">
        <p class="hp-label">Simples e rápido</p>
        <h2 class="hp-title">Como funciona</h2>
        <p class="hp-subtitle">Em poucos passos, conecta clientes e freelancers de forma segura.</p>
        <div class="hp-steps">
            <div class="hp-step">
                <div class="hp-step-icon">1</div>
                <h3>Publique o seu projecto</h3>
                <p>Descreva o que precisa e defina o seu orçamento. É gratuito e leva menos de 2 minutos.</p>
            </div>
            <div class="hp-step">
                <div class="hp-step-icon">2</div>
                <h3>Receba propostas</h3>
                <p>Freelancers qualificados enviam propostas personalizadas rapidamente.</p>
            </div>
            <div class="hp-step">
                <div class="hp-step-icon">3</div>
                <h3>Escolha e contrate</h3>
                <p>Analise perfis, portfólios e avaliações. Escolha o profissional ideal.</p>
            </div>
            <div class="hp-step">
                <div class="hp-step-icon">4</div>
                <h3>Pague com segurança</h3>
                <p>Pagamento em custódia: o freelancer só recebe após a sua aprovação.</p>
            </div>
        </div>
    </div>
</section>

{{-- ============================
     CATEGORIAS POPULARES
============================== --}}
<section class="hp-section hp-section--gray">
    <div class="hp-container" style="text-align:center;">
        <p class="hp-label">Explore</p>
        <h2 class="hp-title">Categorias populares</h2>
        <p class="hp-subtitle">Encontre o profissional certo para cada tipo de projecto.</p>
        <div class="hp-categories">
            <a href="{{ route('freelancers.index') }}" class="hp-cat-card">
                <span class="hp-cat-emoji"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h4l10.5-10.5a2.121 2.121 0 0 0-3-3L5 17v3"/></svg></span>
                <span class="hp-cat-label">Design & Arte</span>
            </a>
            <a href="{{ route('freelancers.index') }}" class="hp-cat-card">
                <span class="hp-cat-emoji"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg></span>
                <span class="hp-cat-label">Desenvolvimento Web</span>
            </a>
            <a href="{{ route('freelancers.index') }}" class="hp-cat-card">
                <span class="hp-cat-emoji"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg></span>
                <span class="hp-cat-label">Apps Mobile</span>
            </a>
            <a href="{{ route('freelancers.index') }}" class="hp-cat-card">
                <span class="hp-cat-emoji"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></span>
                <span class="hp-cat-label">Redação & Conteúdo</span>
            </a>
            <a href="{{ route('freelancers.index') }}" class="hp-cat-card">
                <span class="hp-cat-emoji"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/><line x1="2" y1="20" x2="22" y2="20"/></svg></span>
                <span class="hp-cat-label">Marketing Digital</span>
            </a>
            <a href="{{ route('freelancers.index') }}" class="hp-cat-card">
                <span class="hp-cat-emoji"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg></span>
                <span class="hp-cat-label">Vídeo & Áudio</span>
            </a>
            <a href="{{ route('freelancers.index') }}" class="hp-cat-card">
                <span class="hp-cat-emoji"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
                <span class="hp-cat-label">SEO & Analytics</span>
            </a>
            <a href="{{ route('freelancers.index') }}" class="hp-cat-card">
                <span class="hp-cat-emoji"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3z"/><path d="M3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/></svg></span>
                <span class="hp-cat-label">Suporte & Admin</span>
            </a>
        </div>
        <div style="margin-top:2.5rem;">
            <a href="{{ route('freelancers.index') }}" class="hp-btn hp-btn-primary">Ver todas as categorias →</a>
        </div>
    </div>
</section>

{{-- ============================
     POR QUÊ ESCOLHER
============================== --}}
<section class="hp-section hp-section--white">
    <div class="hp-container">
        <div class="hp-benefits-grid">
            <div class="hp-benefits-text">
                <p class="hp-label">Por que nós</p>
                <h2 class="hp-title">Trabalhe com<br>segurança e confiança</h2>
                <p style="color:#94a3b8; font-size:1.05rem; line-height:1.7; margin-bottom:2rem;">A nossa plataforma protege clientes e freelancers com sistema de custódia, verificação de identidade e suporte dedicado.</p>
                <a href="/register" class="hp-btn hp-btn-primary">Criar conta gratuita</a>
            </div>
            <div class="hp-benefit-list">
                <div class="hp-benefit-item">
                    <div class="hp-benefit-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
                    <div><h4>Pagamento seguro</h4><p>Dinheiro fica em custódia e só é libertado quando aprovar o trabalho.</p></div>
                </div>
                <div class="hp-benefit-item">
                    <div class="hp-benefit-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg></div>
                    <div><h4>Freelancers verificados</h4><p>Perfis validados, portfólios reais e avaliações de clientes anteriores.</p></div>
                </div>
                <div class="hp-benefit-item">
                    <div class="hp-benefit-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
                    <div><h4>Suporte 24h</h4><p>A nossa equipa está disponível para ajudar clientes e freelancers a qualquer momento.</p></div>
                </div>
                <div class="hp-benefit-item">
                    <div class="hp-benefit-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></div>
                    <div><h4>Resultados rápidos</h4><p>Receba propostas em minutos e inicie o seu projecto em menos de 24 horas.</p></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================
     DEPOIMENTOS
============================== --}}
<section class="hp-section hp-section--gray">
    <div class="hp-container" style="text-align:center;">
        <p class="hp-label">Depoimentos</p>
        <h2 class="hp-title">O que dizem os nossos utilizadores</h2>
        <p class="hp-subtitle">Histórias reais de clientes e freelancers que transformaram os seus negócios.</p>
        <div class="hp-testimonials">
            <div class="hp-testimonial">
                <div class="hp-testimonial-stars">★★★★★</div>
                <p class="hp-testimonial-text">"Publiquei o projecto e já tinha 8 propostas em menos de 2 horas. Contratei uma designer incrível e o logótipo ficou perfeito!"</p>
                <div class="hp-testimonial-author">
                    <div class="hp-testimonial-avatar">C</div>
                    <div><div class="hp-testimonial-name">Carlos Mendes</div><div class="hp-testimonial-role">Cliente · Luanda</div></div>
                </div>
            </div>
            <div class="hp-testimonial">
                <div class="hp-testimonial-stars">★★★★★</div>
                <p class="hp-testimonial-text">"Já consegui 15 clientes pela plataforma. O sistema de pagamento é seguro e o suporte responde rapidinho."</p>
                <div class="hp-testimonial-author">
                    <div class="hp-testimonial-avatar">J</div>
                    <div><div class="hp-testimonial-name">Juliana Lima</div><div class="hp-testimonial-role">Freelancer · Designer UI/UX</div></div>
                </div>
            </div>
            <div class="hp-testimonial">
                <div class="hp-testimonial-stars">★★★★★</div>
                <p class="hp-testimonial-text">"Meu site foi desenvolvido em 5 dias. Qualidade profissional, comunicação excelente e dentro do orçamento!"</p>
                <div class="hp-testimonial-author">
                    <div class="hp-testimonial-avatar">R</div>
                    <div><div class="hp-testimonial-name">Rafael Costa</div><div class="hp-testimonial-role">Cliente · Benguela</div></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================
     COMUNIDADE / CRIADORES
============================== --}}
<section class="hp-comunidade-section" style="background:#0a0f1e; padding:6rem 1rem; overflow:hidden; position:relative;">

    {{-- Imagem de fundo --}}
    <div style="position:absolute;inset:0;background-image:url('/img/heru3.jpg');background-size:cover;background-position:center 40%;"></div>

    {{-- Overlay escuro (mantém a cor #0a0f1e) --}}
    <div style="position:absolute;inset:0;background:#0a0f1e;opacity:.88;"></div>

    {{-- Glow decorativo --}}
    <div class="hp-comunidade-glow" style="position:absolute;top:-120px;left:50%;transform:translateX(-50%);width:700px;height:700px;background:radial-gradient(circle,rgba(0,186,255,.12) 0%,transparent 70%);pointer-events:none;z-index:1;"></div>
    <div class="hp-comunidade-glow" style="position:absolute;bottom:-80px;right:-80px;width:400px;height:400px;background:radial-gradient(circle,rgba(255,140,0,.08) 0%,transparent 70%);pointer-events:none;z-index:1;"></div>

    <div style="position:relative;z-index:1;max-width:1200px;margin:0 auto;">

        {{-- Badge --}}
        <div style="text-align:center;margin-bottom:1.25rem;">
            <span style="display:inline-flex;align-items:center;gap:.5rem;background:rgba(0,186,255,.12);border:1px solid rgba(0,186,255,.3);color:#00baff;font-size:.75rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;padding:.4rem 1rem;border-radius:999px;">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                Novidade · Comunidade 24 Horas
            </span>
        </div>

        {{-- Título --}}
        <h2 style="text-align:center;font-size:clamp(2rem,5vw,3.5rem);font-weight:900;color:#fff;line-height:1.1;margin-bottom:1rem;">
            Muito mais do que freelancing.<br>
            <span style="background:linear-gradient(90deg,#00baff,#0070ff);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">É uma comunidade.</span>
        </h2>
        <p style="text-align:center;color:#94a3b8;font-size:1.125rem;max-width:620px;margin:0 auto 3.5rem;line-height:1.7;">
            Partilhe o seu conhecimento, crie conteúdo exclusivo, construa a sua audiência — e ganhe dinheiro por isso. Ou subscreva os criadores que mais inspira e aceda a conteúdo premium.
        </p>

        {{-- Dois cards lado a lado --}}
        <div class="hp-comunidade-cards" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:1.5rem;margin-bottom:4rem;">

            {{-- Card Criador --}}
            <div style="background:linear-gradient(135deg,rgba(0,186,255,.08),rgba(0,186,255,.02));border:1px solid rgba(0,186,255,.2);border-radius:1.5rem;padding:2rem;position:relative;overflow:hidden;" x-data="{hover:false}" @mouseenter="hover=true" @mouseleave="hover=false" :style="hover ? 'transform:translateY(-4px);box-shadow:0 20px 60px rgba(0,186,255,.15);transition:.3s' : 'transition:.3s'">
                <div style="position:absolute;top:0;right:0;width:200px;height:200px;background:radial-gradient(circle,rgba(0,186,255,.1) 0%,transparent 70%);pointer-events:none;"></div>
                <div style="width:3rem;height:3rem;background:linear-gradient(135deg,#00baff,#0070ff);border-radius:.875rem;display:flex;align-items:center;justify-content:center;margin-bottom:1.25rem;">
                    <svg width="22" height="22" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                </div>
                <h3 style="font-size:1.375rem;font-weight:800;color:#fff;margin-bottom:.6rem;">Para Criadores</h3>
                <p style="color:#94a3b8;font-size:.9rem;line-height:1.7;margin-bottom:1.5rem;">Partilhe artigos, vídeos, áudios e dicas exclusivas. Defina o preço da sua subscrição e receba mensalmente dos seus fãs. A sua audiência, o seu negócio.</p>
                <ul style="list-style:none;padding:0;margin:0 0 1.75rem;display:flex;flex-direction:column;gap:.65rem;">
                    <li style="display:flex;align-items:center;gap:.6rem;color:#e2e8f0;font-size:.875rem;">
                        <span style="width:1.25rem;height:1.25rem;background:rgba(0,186,255,.2);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="10" height="10" fill="none" stroke="#00baff" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        </span>
                        Publicações com texto, imagens e vídeos
                    </li>
                    <li style="display:flex;align-items:center;gap:.6rem;color:#e2e8f0;font-size:.875rem;">
                        <span style="width:1.25rem;height:1.25rem;background:rgba(0,186,255,.2);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="10" height="10" fill="none" stroke="#00baff" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        </span>
                        Conteúdo exclusivo bloqueado para não-assinantes
                    </li>
                    <li style="display:flex;align-items:center;gap:.6rem;color:#e2e8f0;font-size:.875rem;">
                        <span style="width:1.25rem;height:1.25rem;background:rgba(0,186,255,.2);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="10" height="10" fill="none" stroke="#00baff" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        </span>
                        Receba 75% de cada subscrição directamente na carteira
                    </li>
                    <li style="display:flex;align-items:center;gap:.6rem;color:#e2e8f0;font-size:.875rem;">
                        <span style="width:1.25rem;height:1.25rem;background:rgba(0,186,255,.2);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="10" height="10" fill="none" stroke="#00baff" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        </span>
                        Estatísticas de seguidores, alcance e receita
                    </li>
                </ul>
                <a href="/register" style="display:inline-flex;align-items:center;gap:.5rem;background:linear-gradient(135deg,#00baff,#0070ff);color:#fff;font-size:.9rem;font-weight:700;padding:.7rem 1.5rem;border-radius:.875rem;text-decoration:none;transition:.2s;" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    Começar a criar
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>

            {{-- Card Assinante --}}
            <div style="background:linear-gradient(135deg,rgba(255,140,0,.07),rgba(255,60,0,.02));border:1px solid rgba(255,140,0,.2);border-radius:1.5rem;padding:2rem;position:relative;overflow:hidden;" x-data="{hover:false}" @mouseenter="hover=true" @mouseleave="hover=false" :style="hover ? 'transform:translateY(-4px);box-shadow:0 20px 60px rgba(255,140,0,.12);transition:.3s' : 'transition:.3s'">
                <div style="position:absolute;top:0;right:0;width:200px;height:200px;background:radial-gradient(circle,rgba(255,140,0,.08) 0%,transparent 70%);pointer-events:none;"></div>
                <div style="width:3rem;height:3rem;background:linear-gradient(135deg,#f59e0b,#ef4444);border-radius:.875rem;display:flex;align-items:center;justify-content:center;margin-bottom:1.25rem;">
                    <svg width="22" height="22" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 style="font-size:1.375rem;font-weight:800;color:#fff;margin-bottom:.6rem;">Para Assinantes</h3>
                <p style="color:#94a3b8;font-size:.9rem;line-height:1.7;margin-bottom:1.5rem;">Acompanhe os freelancers e especialistas que mais admira. Aceda a tutoriais, dicas avançadas e bastidores por uma subscrição mensal acessível — suportando directamente o criador.</p>
                <ul style="list-style:none;padding:0;margin:0 0 1.75rem;display:flex;flex-direction:column;gap:.65rem;">
                    <li style="display:flex;align-items:center;gap:.6rem;color:#e2e8f0;font-size:.875rem;">
                        <span style="width:1.25rem;height:1.25rem;background:rgba(245,158,11,.2);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="10" height="10" fill="none" stroke="#f59e0b" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        </span>
                        Feed social com posts públicos e exclusivos
                    </li>
                    <li style="display:flex;align-items:center;gap:.6rem;color:#e2e8f0;font-size:.875rem;">
                        <span style="width:1.25rem;height:1.25rem;background:rgba(245,158,11,.2);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="10" height="10" fill="none" stroke="#f59e0b" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        </span>
                        Assine por apenas 3.000 KZS/mês por criador
                    </li>
                    <li style="display:flex;align-items:center;gap:.6rem;color:#e2e8f0;font-size:.875rem;">
                        <span style="width:1.25rem;height:1.25rem;background:rgba(245,158,11,.2);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="10" height="10" fill="none" stroke="#f59e0b" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        </span>
                        Cancele quando quiser, sem compromisso
                    </li>
                    <li style="display:flex;align-items:center;gap:.6rem;color:#e2e8f0;font-size:.875rem;">
                        <span style="width:1.25rem;height:1.25rem;background:rgba(245,158,11,.2);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="10" height="10" fill="none" stroke="#f59e0b" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        </span>
                        Pagamento seguro via carteira digital integrada
                    </li>
                </ul>
                <a href="{{ route('social.feed') }}" style="display:inline-flex;align-items:center;gap:.5rem;background:linear-gradient(135deg,#f59e0b,#ef4444);color:#fff;font-size:.9rem;font-weight:700;padding:.7rem 1.5rem;border-radius:.875rem;text-decoration:none;transition:.2s;" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    Explorar comunidade
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>
        </div>

        {{-- Stats de prova social --}}
        <div class="hp-comunidade-stats" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:1rem;border-top:1px solid rgba(255,255,255,.07);padding-top:3rem;text-align:center;">
            <div>
                <p class="hp-comunidade-stat-value" style="font-size:2.25rem;font-weight:900;background:linear-gradient(90deg,#00baff,#0070ff);-webkit-background-clip:text;-webkit-text-fill-color:transparent;line-height:1;">+500</p>
                <p style="color:#94a3b8;font-size:.8rem;margin-top:.25rem;text-transform:uppercase;letter-spacing:.08em;font-weight:600;">Criadores activos</p>
            </div>
            <div>
                <p class="hp-comunidade-stat-value" style="font-size:2.25rem;font-weight:900;background:linear-gradient(90deg,#00baff,#0070ff);-webkit-background-clip:text;-webkit-text-fill-color:transparent;line-height:1;">+10 mil</p>
                <p style="color:#94a3b8;font-size:.8rem;margin-top:.25rem;text-transform:uppercase;letter-spacing:.08em;font-weight:600;">Publicações mensais</p>
            </div>
            <div>
                <p class="hp-comunidade-stat-value" style="font-size:2.25rem;font-weight:900;background:linear-gradient(90deg,#f59e0b,#ef4444);-webkit-background-clip:text;-webkit-text-fill-color:transparent;line-height:1;">3.000 KZS</p>
                <p style="color:#94a3b8;font-size:.8rem;margin-top:.25rem;text-transform:uppercase;letter-spacing:.08em;font-weight:600;">Por subscrição/mês</p>
            </div>
            <div>
                <p class="hp-comunidade-stat-value" style="font-size:2.25rem;font-weight:900;background:linear-gradient(90deg,#00baff,#0070ff);-webkit-background-clip:text;-webkit-text-fill-color:transparent;line-height:1;">75%</p>
                <p style="color:#94a3b8;font-size:.8rem;margin-top:.25rem;text-transform:uppercase;letter-spacing:.08em;font-weight:600;">Receita para o criador</p>
            </div>
        </div>

    </div>
</section>

{{-- ============================
     CTA FINAL
============================== --}}
<section class="hp-section hp-section--blue">
    <div class="hp-container--narrow hp-cta-text" style="text-align:center;">
        <h2 class="hp-title hp-title--white">Pronto para começar?</h2>
        <p class="hp-subtitle hp-subtitle--white" style="max-width:600px; margin:0 auto 2.5rem;">Junte-se a milhares de clientes e freelancers que já confiam na 24 Horas. Registo 100% gratuito.</p>
        <div class="hp-ctas" style="justify-content:center;">
            <a href="/register" class="hp-btn hp-btn-white">Criar conta gratuita</a>
            <a href="{{ route('public.projects') }}" class="hp-btn hp-btn-outline-white">Ver projectos disponíveis</a>
        </div>
    </div>
</section>

@include('components.freelancer-modal')

@endsection
