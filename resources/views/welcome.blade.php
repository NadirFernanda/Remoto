<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Conecte Clientes e Freelancers em 24 Horas</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            background: #101c2c;
            color: #fff;
        }
        .logo-header {
            height: 48px;
            margin-right: 8px;
        }
        .logo-text-24 {
            color: #00baff;
            font-weight: 900;
            font-size: 2rem;
            letter-spacing: 1px;
        }
        .logo-text-horas {
            background: #00baff;
            color: #fff;
            border-radius: 8px;
            padding: 2px 16px;
            font-weight: 700;
            font-size: 1.1rem;
            margin-left: 4px;
        }
        .nav-link {
            margin-left: 32px;
            font-size: 1.1rem;
            color: #fff;
            font-weight: 500;
            transition: color 0.2s;
        }
        .nav-link:hover {
            color: #00baff;
        }
        /* HERO/CAROUSEL ONLY */
        .hero-title {
            color: #00baff;
            font-size: 2.8rem;
            font-weight: 900;
            margin-bottom: 1.5rem;
            line-height: 1.1;
            letter-spacing: 0.5px;
            text-align: center;
        }
        @media (min-width: 768px) {
            .hero-title {
                font-size: 3.2rem;
            }
        }
        .hero-desc {
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 2.5rem;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        .hero-btn {
            background: #00baff;
            color: #fff;
            font-weight: 700;
            font-size: 1.2rem;
            border-radius: 8px;
            padding: 0.75rem 2.5rem;
            margin-right: 1.5rem;
            border: none;
            transition: background 0.2s;
        }
        .hero-btn:hover {
            background: #0099cc;
        }
        .hero-btn-outline {
            background: transparent;
            color: #00baff;
            border: 2px solid #00baff;
            font-weight: 700;
            font-size: 1.2rem;
            border-radius: 8px;
            padding: 0.75rem 2.5rem;
            transition: background 0.2s, color 0.2s;
        }
        .hero-btn-outline:hover {
            background: #00baff;
            color: #fff;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    @include('components.header')
    <main class="flex-1 flex flex-col items-center justify-center text-center px-4 pt-32 md:pt-36">
        <!-- HERO CAROUSEL SECTION -->
        <section class="w-full py-10 relative" x-data="{
            slide: 0,
            slides: 3,
            interval: null,
            start() {
                if (this.interval) clearInterval(this.interval);
                this.interval = setInterval(() => {
                    this.slide = (this.slide + 1) % this.slides;
                }, 1500);
            },
            stop() {
                if (this.interval) clearInterval(this.interval);
                this.interval = null;
            }
        }" x-init="start()" @mouseenter="stop()" @mouseleave="start()">
            <div class="max-w-4xl mx-auto bg-transparent rounded-lg overflow-hidden">
                <!-- Carousel Slides -->
                <template x-if="slide === 0">
                    <div class="flex flex-col items-center justify-center text-center min-h-[340px] transition-all duration-500">
                        <h1 class="hero-title">Conecte Clientes e Freelancers em 24 Horas</h1>
                        <p class="hero-desc">Plataforma web para contratação de serviços remotos, com segurança, agilidade e facilidade. Encontre o profissional ideal ou o projeto perfeito em poucos cliques.</p>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mt-4">
                            <a href="/briefing" class="hero-btn">Quero Contratar</a>
                            <a href="/register" class="hero-btn-outline">Sou Freelancer</a>
                        </div>
                    </div>
                </template>
                <template x-if="slide === 1">
                    <div class="flex flex-col items-center justify-center text-center min-h-[340px] transition-all duration-500">
                        <h1 class="hero-title">Publique seu projeto e receba propostas rápidas</h1>
                        <p class="hero-desc">Descreva sua necessidade e receba orçamentos de freelancers qualificados em minutos.</p>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mt-4">
                            <a href="/briefing" class="hero-btn">Publicar Projeto</a>
                        </div>
                    </div>
                </template>
                <template x-if="slide === 2">
                    <div class="flex flex-col items-center justify-center text-center min-h-[340px] transition-all duration-500">
                        <h1 class="hero-title">Contrate com segurança e agilidade</h1>
                        <p class="hero-desc">Pagamento protegido, suporte dedicado e profissionais avaliados. Tudo para garantir o sucesso do seu projeto.</p>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mt-4">
                            <a href="/register" class="hero-btn">Criar Conta Grátis</a>
                        </div>
                    </div>
                </template>
            </div>
            <!-- Carousel Dots (Indicators) -->
            <div class="absolute left-0 right-0 bottom-4 flex justify-center items-center gap-2 z-10">
                <template x-for="i in slides" :key="i">
                    <button @click="slide = i - 1; start();"
                        :class="{
                            'bg-white border-cyan-400 scale-125 shadow-lg': slide === (i-1),
                            'bg-transparent border-cyan-400 opacity-60': slide !== (i-1)
                        }"
                        class="transition-all duration-200 w-3 h-3 rounded-full mx-1 focus:outline-none border"></button>
                </template>
            </div>
            <!-- Carousel Navigation -->
            <button @click="slide = (slide - 1 + slides) % slides; start();" class="absolute left-2 top-1/2 -translate-y-1/2 bg-cyan-900 bg-opacity-70 hover:bg-cyan-700 text-cyan-100 rounded-full w-8 h-8 flex items-center justify-center">&#8592;</button>
            <button @click="slide = (slide + 1) % slides; start();" class="absolute right-2 top-1/2 -translate-y-1/2 bg-cyan-900 bg-opacity-70 hover:bg-cyan-700 text-cyan-100 rounded-full w-8 h-8 flex items-center justify-center">&#8594;</button>
        </section>

        <!-- SERVIÇOS EM DESTAQUE -->
        <section class="w-full max-w-5xl mx-auto mt-8 py-12" style="background:#00baff;">
            <h2 class="text-2xl font-bold text-cyan-400 mb-6 text-left">Serviços em Destaque</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-[#162032] rounded-lg p-6 shadow text-left">
                    <h3 class="text-lg font-bold text-cyan-300 mb-2">Criação de Sites</h3>
                    <p class="mb-4" style="color:#fff;">Landing pages, institucionais, lojas virtuais e mais.</p>
                    <span class="text-cyan-400 font-bold">A partir de R$ 500</span>
                </div>
                <div class="bg-[#162032] rounded-lg p-6 shadow text-left">
                    <h3 class="text-lg font-bold text-cyan-300 mb-2">Design Gráfico</h3>
                    <p class="mb-4" style="color:#fff;">Logotipos, banners, posts para redes sociais e identidade visual.</p>
                    <span class="text-cyan-400 font-bold">A partir de R$ 150</span>
                </div>
                <div class="bg-[#162032] rounded-lg p-6 shadow text-left">
                    <h3 class="text-lg font-bold text-cyan-300 mb-2">Redação & Tradução</h3>
                    <p class="mb-4" style="color:#fff;">Textos para blogs, revisão, tradução e copywriting.</p>
                    <span class="text-cyan-400 font-bold">A partir de R$ 80</span>
                </div>
            </div>
        </section>

        <!-- FREELANCERS EM DESTAQUE -->
        <section class="w-full max-w-5xl mx-auto py-12">
            <h2 class="text-2xl font-bold text-cyan-400 mb-6 text-left">Freelancers em Destaque</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-[#162032] rounded-lg p-6 shadow text-left flex flex-col items-start">
                    <div class="flex items-center mb-2">
                        <span class="bg-cyan-400 text-[#101c2c] font-bold rounded-full px-3 py-1 mr-2">Ana Silva</span>
                        <span class="text-gray-400 text-sm">Design Gráfico</span>
                    </div>
                    <p class="text-gray-300 mb-2">Especialista em identidade visual e social media.</p>
                    <span class="text-cyan-400 text-sm">Avaliação: ★★★★☆</span>
                </div>
                <div class="bg-[#162032] rounded-lg p-6 shadow text-left flex flex-col items-start">
                    <div class="flex items-center mb-2">
                        <span class="bg-cyan-400 text-[#101c2c] font-bold rounded-full px-3 py-1 mr-2">Carlos Souza</span>
                        <span class="text-gray-400 text-sm">Desenvolvedor Web</span>
                    </div>
                    <p class="text-gray-300 mb-2">Criação de sites responsivos e sistemas sob medida.</p>
                    <span class="text-cyan-400 text-sm">Avaliação: ★★★★★</span>
                </div>
                <div class="bg-[#162032] rounded-lg p-6 shadow text-left flex flex-col items-start">
                    <div class="flex items-center mb-2">
                        <span class="bg-cyan-400 text-[#101c2c] font-bold rounded-full px-3 py-1 mr-2">Juliana Lima</span>
                        <span class="text-gray-400 text-sm">Redatora</span>
                    </div>
                    <p class="text-gray-300 mb-2">Textos otimizados para SEO e conteúdo institucional.</p>
                    <span class="text-cyan-400 text-sm">Avaliação: ★★★★☆</span>
                </div>
            </div>
        </section>

        <!-- DEPOIMENTOS -->
        <section class="w-full max-w-4xl mx-auto py-12 mb-20">
            <h2 class="text-2xl font-bold text-cyan-400 mb-6 text-left">Depoimentos</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-[#162032] rounded-lg p-6 shadow text-left">
                    <p class="text-gray-200 italic mb-2">"Encontrei o freelancer ideal em menos de 24 horas! Recomendo a plataforma para todos que buscam agilidade."</p>
                    <span class="text-cyan-400 font-bold">— João P.</span>
                </div>
                <div class="bg-[#162032] rounded-lg p-6 shadow text-left">
                    <p class="text-gray-200 italic mb-2">"Fiz meu cadastro e já consegui meus primeiros clientes. Interface fácil e pagamentos garantidos."</p>
                    <span class="text-cyan-400 font-bold">— Mariana R.</span>
                </div>
            </div>
        </section>
    </main>
    <!-- LINKS INSTITUCIONAIS -->
    <section class="w-full bg-white py-14">
        <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 px-4">
            <div>
                <h3 class="font-semibold text-lg mb-3 text-gray-900">Categorias</h3>
                <ul class="space-y-2 text-base text-gray-700">
                    <li>Design Gráfico</li>
                    <li>Marketing Digital</li>
                    <li>Redação e Tradução</li>
                    <li>Vídeo e Animação</li>
                    <li>Música e Áudio</li>
                    <li>Programação e Tecnologia</li>
                    <li>Serviços de IA</li>
                    <li>Consultoria</li>
                    <li>Dados</li>
                    <li>Negócios</li>
                    <li>Crescimento Pessoal & Hobbies</li>
                </ul>
            </div>
            <div>
                <h3 class="font-semibold text-lg mb-3 text-gray-900">Para clientes</h3>
                <ul class="space-y-2 text-base text-gray-700">
                    <li>Como funciona</li>
                    <li>Histórias de sucesso</li>
                    <li>Guia de qualidade</li>
                    <li>Guias da plataforma</li>
                    <li>Central de Ajuda</li>
                    <li>Pesquisar freelancers por habilidade</li>
                </ul>
            </div>
            <div>
                <h3 class="font-semibold text-lg mb-3 text-gray-900">Para freelancers</h3>
                <ul class="space-y-2 text-base text-gray-700">
                    <li>Torne-se freelancer</li>
                    <li>Torne-se uma agência</li>
                    <li>Programa de benefícios</li>
                    <li>Central da comunidade</li>
                    <li>Fórum</li>
                    <li>Eventos</li>
                </ul>
            </div>
            <div>
                <h3 class="font-semibold text-lg mb-3 text-gray-900">Soluções corporativas</h3>
                <ul class="space-y-2 text-base text-gray-700">
                    <li>Pro</li>
                    <li>Gerenciamento de projetos</li>
                    <li>Serviços especializados</li>
                    <li>Marketing de Conteúdo</li>
                    <li>Ferramentas para dropshipping</li>
                    <li>Criador de loja com IA</li>
                    <li>Logo Maker</li>
                    <li>Contato comercial</li>
                </ul>
            </div>
        </div>
    </section>

    <footer class="w-full bg-[#101c2c] border-t border-cyan-900 py-4 text-center mt-0">
        <span class="text-gray-400 text-sm">&copy; 2026 Plataforma 24 Horas Remoto. Todos os direitos reservados.</span>
    </footer>

    @include('components.freelancer-modal')
</body>
</html>
