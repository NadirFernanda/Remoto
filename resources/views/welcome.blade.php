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
    <div class="hp-hero-bg" :class="slide===1 ? 'hp-bg-active' : ''" style="background-image:url('/img/heru1.jpg'); background-position:center 55%; transform:scale(1.05);"></div>
    <div class="hp-hero-bg" :class="slide===2 ? 'hp-bg-active' : ''" style="background-image:url('/img/heru1.jpg'); background-position:right 40%;"></div>
    <div class="hp-hero-overlay"></div>

    {{-- Slide 1 --}}
    <div class="hp-hero-slide" x-show="slide===0" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="hp-hero-inner">
            <h1 class="hp-hero-title">Contrate os melhores<br>freelancers para<br><span class="hp-hero-accent">qualquer projeto</span></h1>
            <ul class="hp-hero-bullets">
                <li>Maior marketplace freelance do Brasil</li>
                <li>Qualquer serviço que você precisar</li>
                <li>Receba propostas em minutos, grátis</li>
                <li>Pague só quando estiver 100% satisfeito</li>
            </ul>
            <div class="hp-ctas">
                <a href="/register" class="hp-btn hp-btn-white">Contratar Freelancer</a>
                <a href="/register" class="hp-btn hp-btn-outline-white">Ganhar Dinheiro Freelancing</a>
            </div>
        </div>
    </div>

    {{-- Slide 2 --}}
    <div class="hp-hero-slide" x-show="slide===1" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display:none;">
        <div class="hp-hero-inner">
            <h1 class="hp-hero-title">Ganhe dinheiro fazendo<br>o que você <span class="hp-hero-accent">ama</span></h1>
            <ul class="hp-hero-bullets">
                <li>Crie seu perfil gratuito em minutos</li>
                <li>Acesse milhares de projetos todo dia</li>
                <li>Defina seu preço e horários</li>
                <li>Receba pagamentos seguros e rápidos</li>
            </ul>
            <div class="hp-ctas">
                <a href="/register" class="hp-btn hp-btn-white">Começar como Freelancer</a>
                <a href="{{ route('public.projects') }}" class="hp-btn hp-btn-outline-white">Ver projetos disponíveis</a>
            </div>
        </div>
    </div>

    {{-- Slide 3 --}}
    <div class="hp-hero-slide" x-show="slide===2" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display:none;">
        <div class="hp-hero-inner">
            <h1 class="hp-hero-title">Tudo que seu negócio<br>precisa, em um <span class="hp-hero-accent">só lugar</span></h1>
            <ul class="hp-hero-bullets">
                <li>Design, Dev, Marketing, Redação e muito mais</li>
                <li>+5.000 profissionais verificados ativos</li>
                <li>Sistema de custódia: pague com segurança</li>
                <li>Suporte dedicado 24 horas por dia</li>
            </ul>
            <div class="hp-ctas">
                <a href="/register" class="hp-btn hp-btn-white">Publicar projeto grátis</a>
                <a href="{{ route('freelancers.index') }}" class="hp-btn hp-btn-outline-white">Explorar freelancers</a>
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
        <p class="hp-subtitle">Em poucos passos, você conecta clientes e freelancers de forma segura.</p>
        <div class="hp-steps">
            <div class="hp-step">
                <div class="hp-step-icon">1</div>
                <h3>Publique seu projeto</h3>
                <p>Descreva o que precisa e defina seu orçamento. É gratuito e leva menos de 2 minutos.</p>
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
                <p>Pagamento em custódia: o freelancer só recebe após sua aprovação.</p>
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
        <p class="hp-subtitle">Encontre o profissional certo para cada tipo de projeto.</p>
        <div class="hp-categories">
            <a href="{{ route('freelancers.index') }}" class="hp-cat-card"><span class="hp-cat-emoji">🎨</span><span class="hp-cat-label">Design & Arte</span></a>
            <a href="{{ route('freelancers.index') }}" class="hp-cat-card"><span class="hp-cat-emoji">💻</span><span class="hp-cat-label">Desenvolvimento Web</span></a>
            <a href="{{ route('freelancers.index') }}" class="hp-cat-card"><span class="hp-cat-emoji">📱</span><span class="hp-cat-label">Apps Mobile</span></a>
            <a href="{{ route('freelancers.index') }}" class="hp-cat-card"><span class="hp-cat-emoji">✍️</span><span class="hp-cat-label">Redação & Conteúdo</span></a>
            <a href="{{ route('freelancers.index') }}" class="hp-cat-card"><span class="hp-cat-emoji">📊</span><span class="hp-cat-label">Marketing Digital</span></a>
            <a href="{{ route('freelancers.index') }}" class="hp-cat-card"><span class="hp-cat-emoji">🎬</span><span class="hp-cat-label">Vídeo & Áudio</span></a>
            <a href="{{ route('freelancers.index') }}" class="hp-cat-card"><span class="hp-cat-emoji">🔍</span><span class="hp-cat-label">SEO & Analytics</span></a>
            <a href="{{ route('freelancers.index') }}" class="hp-cat-card"><span class="hp-cat-emoji">🤝</span><span class="hp-cat-label">Suporte & Admin</span></a>
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
                <p style="color:#64748b; font-size:1.05rem; line-height:1.7; margin-bottom:2rem;">Nossa plataforma protege clientes e freelancers com sistema de custódia, verificação de identidade e suporte dedicado.</p>
                <a href="/register" class="hp-btn hp-btn-primary">Criar conta gratuita</a>
            </div>
            <div class="hp-benefit-list">
                <div class="hp-benefit-item">
                    <div class="hp-benefit-icon">🔒</div>
                    <div><h4>Pagamento seguro</h4><p>Dinheiro fica em custódia e só é liberado quando você aprovar o trabalho.</p></div>
                </div>
                <div class="hp-benefit-item">
                    <div class="hp-benefit-icon">✅</div>
                    <div><h4>Freelancers verificados</h4><p>Perfis validados, portfólios reais e avaliações de clientes anteriores.</p></div>
                </div>
                <div class="hp-benefit-item">
                    <div class="hp-benefit-icon">💬</div>
                    <div><h4>Suporte 24h</h4><p>Nossa equipe está disponível para ajudar clientes e freelancers a qualquer momento.</p></div>
                </div>
                <div class="hp-benefit-item">
                    <div class="hp-benefit-icon">⚡</div>
                    <div><h4>Resultados rápidos</h4><p>Receba propostas em minutos e inicie seu projeto em menos de 24 horas.</p></div>
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
        <h2 class="hp-title">O que dizem nossos usuários</h2>
        <p class="hp-subtitle">Histórias reais de clientes e freelancers que transformaram seus negócios.</p>
        <div class="hp-testimonials">
            <div class="hp-testimonial">
                <div class="hp-testimonial-stars">★★★★★</div>
                <p class="hp-testimonial-text">"Publiquei o projeto e já tinha 8 propostas em menos de 2 horas. Contratei uma designer incrível e o logo ficou perfeito!"</p>
                <div class="hp-testimonial-author">
                    <div class="hp-testimonial-avatar">C</div>
                    <div><div class="hp-testimonial-name">Carlos Mendes</div><div class="hp-testimonial-role">Cliente · São Paulo</div></div>
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
                    <div><div class="hp-testimonial-name">Rafael Costa</div><div class="hp-testimonial-role">Cliente · Rio de Janeiro</div></div>
                </div>
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
        <p class="hp-subtitle hp-subtitle--white" style="max-width:600px; margin:0 auto 2.5rem;">Junte-se a milhares de clientes e freelancers que já confiam na 24 Horas. Cadastro 100% gratuito.</p>
        <div class="hp-ctas" style="justify-content:center;">
            <a href="/register" class="hp-btn hp-btn-white">Criar conta grátis</a>
            <a href="{{ route('public.projects') }}" class="hp-btn hp-btn-outline-white">Ver projetos disponíveis</a>
        </div>
    </div>
</section>

@include('components.freelancer-modal')

@endsection