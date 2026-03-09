<footer class="hp-footer">

    <div class="hp-footer-top">
        <!-- Primeira linha: Marca ocupa as duas colunas -->
        <div class="hp-footer-brand" style="grid-column: 1 / -1;">
            <a href="/" class="hp-footer-brand-logo" aria-label="24 Horas">
                <img src="{{ asset('img/logo.png') }}" alt="24 Horas" class="hp-footer-logo">
            </a>
            <p>A plataforma mais rápida para conectar clientes e freelancers. Segurança, agilidade e qualidade em cada projeto.</p>
            <div class="hp-footer-social">
                <a href="#" aria-label="Instagram" title="Instagram">...svg...</a>
                <a href="#" aria-label="LinkedIn" title="LinkedIn">...svg...</a>
                <a href="#" aria-label="Facebook" title="Facebook">...svg...</a>
                <a href="#" aria-label="Twitter/X" title="Twitter/X">...svg...</a>
            </div>
        </div>
        <!-- Segunda linha: Clientes + Freelancers -->
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
        <!-- Terceira linha: Empresa + Sobre -->
        <div class="hp-footer-col">
            <h4>Empresa</h4>
            <ul>
                <li><a href="{{ route('sobre.sobre-nos') }}">Sobre nós</a></li>
                <li><a href="{{ route('sobre.como-funciona') }}">Como funciona</a></li>
                <li><a href="#">Programa de afiliados</a></li>
                <li><a href="#">Termos de uso</a></li>
                <li><a href="#">Política de privacidade</a></li>
                <li><a href="{{ route('sobre.como-funciona') }}">Suporte</a></li>
            </ul>
        </div>
        <div class="hp-footer-col">
            <h4>Sobre</h4>
            <ul>
                <li><a href="{{ route('sobre.sobre-nos') }}">Sobre nós</a></li>
                <li><a href="{{ route('sobre.como-funciona') }}">Como funciona</a></li>
                <li><a href="{{ route('sobre.seguranca') }}">Segurança</a></li>
                <li><a href="{{ route('sobre.investidores') }}">Investidores</a></li>
                <li><a href="{{ route('sobre.mapa-do-site') }}">Mapa do site</a></li>
                <li><a href="{{ route('sobre.historias') }}">Histórias</a></li>
                <li><a href="{{ route('sobre.noticias') }}">Notícias</a></li>
                <li><a href="{{ route('sobre.equipe') }}">Equipe</a></li>
                <li><a href="{{ route('sobre.premios') }}">Prêmios</a></li>
                <li><a href="{{ route('sobre.comunicados') }}">Comunicados de imprensa</a></li>
                <li><a href="{{ route('sobre.carreiras') }}">Carreiras</a></li>
            </ul>
        </div>
    </div>

    <div class="hp-footer-bar">
        <div class="hp-footer-bar-inner">
            <span class="hp-footer-bar-copy">&copy; {{ date('Y') }} 24Horas. Todos os direitos reservados. &middot; Desenvolvido por <strong>Fernanda Gonçalves</strong></span>
            <div class="hp-footer-bar-links">
                <a href="#">Termos</a>
                <a href="#">Privacidade</a>
                <a href="#">Cookies</a>
                <a href="#">Suporte</a>
            </div>
        </div>
    </div>
</footer>
