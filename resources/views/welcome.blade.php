@extends('layouts.main')

@section('content')
    <!-- Hero styles moved to resources/css/app.css (Modern Glass) -->

    <main class="flex-1 flex flex-col items-center justify-center text-center px-4">
        <!-- HERO CAROUSEL SECTION -->
        <section id="hero" class="w-full py-4 relative" x-data="{
            slide: 0,
            slides: 3,
            interval: null,
            start() {
                if (this.interval) clearInterval(this.interval);
                this.interval = setInterval(() => { this.slide = (this.slide + 1) % this.slides; }, 4000);
            },
            stop() { if (this.interval) clearInterval(this.interval); this.interval = null; }
        }" x-init="start()" @mouseenter="stop()" @mouseleave="start()" style="background-image: url('/img/heru1.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; background-color: #071422;">
            <div class="max-w-3xl mx-auto hero-card p-4 sm:p-8">
                <div x-show="slide === 0" class="flex flex-col items-center justify-center text-center min-h-[180px] sm:min-h-[220px] transition-all duration-500">
                    <h1 class="hero-title">Conecte clientes e freelancers em 24 horas</h1>
                    <p class="hero-desc">Encontre profissionais qualificados, gerencie pagamentos e acompanhe projetos com segurança e agilidade.</p>
                    <div class="hero-ctas mt-4">
                        <a href="/register" class="hero-btn">Criar conta</a>
                        <a href="/login" class="hero-btn-outline">Entrar</a>
                    </div>
                </div>
                <div x-show="slide === 1" style="display:none" class="flex flex-col items-center justify-center text-center min-h-[180px] sm:min-h-[240px] transition-all duration-500">
                    <h1 class="hero-title">Ganhe dinheiro indicando amigos!</h1>
                    <p class="hero-desc">Compartilhe seu link de afiliado e receba comissão a cada novo cadastro. Simples, rápido e transparente.</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center mt-6">
                        <a href="/register" class="hero-btn">Quero ser afiliado</a>
                    </div>
                </div>
                <div x-show="slide === 2" style="display:none" class="flex flex-col items-center justify-center text-center min-h-[180px] sm:min-h-[240px] transition-all duration-500">
                    <h1 class="hero-title">Tudo em um só lugar</h1>
                    <p class="hero-desc">Gerencie projetos, pagamentos, avaliações e comunicação em uma plataforma segura e intuitiva.</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center mt-6">
                        <a href="/register" class="hero-btn">Começar agora</a>
                    </div>
                </div>
            </div>
            <div class="flex justify-center gap-2 mt-4">
                <button @click="slide=0" :class="{'bg-[#00baff]': slide===0, 'bg-white/30': slide!==0}" class="w-3 h-3 rounded-full transition-all" aria-label="Slide 1"></button>
                <button @click="slide=1" :class="{'bg-[#00baff]': slide===1, 'bg-white/30': slide!==1}" class="w-3 h-3 rounded-full transition-all" aria-label="Slide 2"></button>
                <button @click="slide=2" :class="{'bg-[#00baff]': slide===2, 'bg-white/30': slide!==2}" class="w-3 h-3 rounded-full transition-all" aria-label="Slide 3"></button>
            </div>
        </section>

        <!-- BENEFÍCIOS/COMO FUNCIONA -->
        <div class="w-full h-2 bg-gradient-to-r from-cyan-400/30 via-cyan-500/40 to-cyan-400/30 my-6 md:my-8"></div>
        <section id="beneficios" class="w-full max-w-5xl mx-auto py-8 md:py-12 px-4 grid md:grid-cols-3 gap-6 md:gap-8 bg-cyan-900/10 rounded-xl shadow-lg">
            <div class="bg-white/10 rounded-lg p-6 shadow text-center flex flex-col items-center">
                @include('components.icon', ['name' => 'clock', 'class' => 'w-12 h-12 mb-3 text-[#00baff]'])
                <h3 class="font-bold text-lg mb-2">Cadastro Rápido</h3>
                <p class="text-white/90">Crie sua conta em segundos e já comece a contratar ou oferecer serviços.</p>
            </div>
            <div class="bg-white/10 rounded-lg p-6 shadow text-center flex flex-col items-center">
                @include('components.icon', ['name' => 'check', 'class' => 'w-12 h-12 mb-3 text-[#00baff]'])
                <h3 class="font-bold text-lg mb-2">Segurança Garantida</h3>
                <p class="text-white/90">Pagamentos em custódia, avaliações reais e suporte dedicado para sua tranquilidade.</p>
            </div>
            <div class="bg-white/10 rounded-lg p-6 shadow text-center flex flex-col items-center">
                @include('components.icon', ['name' => 'check', 'class' => 'w-12 h-12 mb-3 text-[#00baff]'])
                <h3 class="font-bold text-lg mb-2">Ganhe com Indicações</h3>
                <p class="text-white/90">Indique amigos e receba comissão automática a cada novo cadastro realizado pelo seu link.</p>
            </div>
        </section>

        <!-- DEPOIMENTOS/CHAMADA FINAL -->
        <div class="flex justify-center items-center my-8 md:my-12">
            <div class="w-2 h-12 md:h-24 bg-gradient-to-b from-cyan-400 via-cyan-500 to-cyan-400 rounded-full mx-4 md:mx-6"></div>
            <span class="text-cyan-400 text-lg md:text-xl font-bold tracking-widest uppercase">Depoimentos</span>
            <div class="w-2 h-12 md:h-24 bg-gradient-to-b from-cyan-400 via-cyan-500 to-cyan-400 rounded-full mx-4 md:mx-6"></div>
        </div>
        <section id="depoimentos" class="w-full max-w-4xl mx-auto py-8 md:py-12 px-4 bg-transparent rounded-xl shadow-lg">
            <div class="bg-white/10 rounded-lg p-8 shadow text-center">
                <h2 class="text-2xl font-bold mb-4 text-[#00baff]">O que dizem nossos usuários</h2>
                <div class="grid md:grid-cols-2 gap-8">
                    <div class="bg-white/10 backdrop-blur-sm border border-cyan-400/30 rounded-2xl p-6 shadow-lg flex flex-col items-center transition hover:scale-105 hover:shadow-xl">
                        <svg class="w-8 h-8 text-cyan-400 mb-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2l4-4"/></svg>
                        <p class="text-white/90 italic mb-2 text-lg">“Encontrei o freelancer ideal em menos de 24 horas. Plataforma fácil e confiável!”</p>
                        <span class="text-cyan-400 font-bold mt-2">— Cliente Satisfeito</span>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm border border-cyan-400/30 rounded-2xl p-6 shadow-lg flex flex-col items-center transition hover:scale-105 hover:shadow-xl">
                        <svg class="w-8 h-8 text-cyan-400 mb-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                        <p class="text-white/90 italic mb-2 text-lg">“Recebi meu pagamento rápido e seguro. Recomendo para todos os profissionais!”</p>
                        <span class="text-cyan-400 font-bold mt-2">— Freelancer Top</span>
                    </div>
                </div>
                <div class="mt-8">
                    <a href="/register" class="hero-btn">Quero fazer parte</a>
                </div>
            </div>
        </section>

        @include('components.freelancer-modal')
    </main>
@endsection
