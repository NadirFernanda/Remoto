<footer class="hp-footer">
    <div class="hp-footer-top">

        {{-- Marca --}}
        <div class="hp-footer-brand">
            <a href="/" class="hp-footer-brand-logo" aria-label="24 Horas">
                <img src="{{ asset('img/logo.png') }}" alt="24 Horas" class="hp-footer-logo">
            </a>
            <p>A plataforma mais rápida para conectar clientes e freelancers. Segurança, agilidade e qualidade em cada projeto.</p>
            <div class="hp-footer-social">
                <a href="#" aria-label="Instagram" title="Instagram">in</a>
                <a href="#" aria-label="LinkedIn"  title="LinkedIn">Li</a>
                <a href="#" aria-label="Facebook"  title="Facebook">Fb</a>
                <a href="#" aria-label="Twitter/X" title="Twitter/X">X</a>
            </div>
        </div>

        {{-- Para Clientes --}}
        <div class="hp-footer-col">
            <h4>Para Clientes</h4>
            <ul>
                <li><a href="/register">Criar conta grátis</a></li>
                <li><a href="{{ route('public.projects') }}">Publicar projeto</a></li>
                <li><a href="{{ route('freelancers.index') }}">Encontrar freelancers</a></li>
                <li><a href="{{ route('freelancers.search') }}">Busca avançada</a></li>
                <li><a href="/login">Entrar</a></li>
            </ul>
        </div>

        {{-- Para Freelancers --}}
        <div class="hp-footer-col">
            <h4>Para Freelancers</h4>
            <ul>
                <li><a href="/register">Cadastrar como freelancer</a></li>
                <li><a href="{{ route('public.projects') }}">Encontrar trabalhos</a></li>
                <li><a href="{{ route('freelancers.index') }}">Ver comunidade</a></li>
                @auth
                <li><a href="{{ route('dashboard') }}">Meu painel</a></li>
                @endauth
            </ul>
        </div>

        {{-- Empresa --}}
        <div class="hp-footer-col">
            <h4>Empresa</h4>
            <ul>
                <li><a href="#">Sobre nós</a></li>
                <li><a href="#">Como funciona</a></li>
                <li><a href="#">Programa de afiliados</a></li>
                <li><a href="#">Termos de uso</a></li>
                <li><a href="#">Política de privacidade</a></li>
                <li><a href="#">Suporte</a></li>
            </ul>
        </div>

        {{-- Sobre --}}
        <div class="hp-footer-col">
            <h4>Sobre</h4>
            <ul>
                <li><a href="#">Sobre nós</a></li>
                <li><a href="#">Como funciona</a></li>
                <li><a href="#">Segurança</a></li>
                <li><a href="#">Investidores</a></li>
                <li><a href="#">Mapa do site</a></li>
                <li><a href="#">Histórias</a></li>
                <li><a href="#">Notícias</a></li>
                <li><a href="#">Equipe</a></li>
                <li><a href="#">Prêmios</a></li>
                <li><a href="#">Comunicados de imprensa</a></li>
                <li><a href="#">Carreiras</a></li>
            </ul>
        </div>
    </div>

    <div class="hp-footer-bar">
        <div class="hp-footer-bar-inner">
            <span class="hp-footer-bar-copy">&copy; {{ date('Y') }} 24Horas. Todos os direitos reservados.</span>
            <div class="hp-footer-bar-links">
                <a href="#">Termos</a>
                <a href="#">Privacidade</a>
                <a href="#">Cookies</a>
                <a href="#">Suporte</a>
            </div>
        </div>
    </div>
</footer>
