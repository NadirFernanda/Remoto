<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Fluxo de Novo Pedido

O fluxo de criação de um novo pedido no sistema funciona assim:

1. **Briefing do Pedido**
   - O cliente preenche um formulário com:
     - Título do pedido (obrigatório)
     - Tipo de negócio
     - Necessidade
     - Público-alvo
     - Estilo desejado
     - Cores preferidas
     - Onde será utilizado
   - O sistema gera automaticamente uma descrição profissional (briefing inteligente) a partir das respostas.
   - O cliente pode revisar/editar a descrição sugerida.

2. **Definição do Valor**
   - O cliente informa o valor do serviço.
   - O sistema calcula e exibe a taxa da plataforma e o valor líquido do freelancer.

3. **Pagamento**
   - O cliente realiza o pagamento do pedido.
   - Após confirmação, o pedido é publicado e fica disponível para freelancers.

4. **Notificações para Freelancers**
   - Todos os freelancers ativos recebem uma **notificação interna** no sistema sobre o novo projeto.
   - Freelancers que optaram por receber e-mails também recebem uma **notificação por e-mail** com detalhes do projeto e link direto para aceitar.
   - O freelancer pode ativar/desativar o recebimento de e-mails de novos projetos nas configurações da conta.

5. **Exibição Pública**
   - No site público, cada projeto exibe:
     - Título
     - Status
     - Descrição (texto gerado pelo briefing inteligente)
     - Valor
     - Data de publicação

6. **Edição de Título**
   - O cliente pode editar o título do pedido já criado diretamente na tela de detalhes, via modal e AJAX.

> **Observação:**
> O valor informado pelo cliente no momento do pedido é **fixo e não negociável**. Cabe ao freelancer decidir se aceita ou recusa o projeto conforme o valor e as condições apresentadas.

Esse fluxo garante que todos os pedidos tenham título, descrição clara, estejam prontos para análise dos freelancers e que os profissionais sejam notificados de novas oportunidades.
