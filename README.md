# SITE FREELANCER – One Page Pitch

SITE FREELANCER é uma plataforma que torna simples contratar e trabalhar como freelancer:

- Clientes preenchem um briefing guiado e em poucos minutos têm um pedido claro, com valor definido e taxas transparentes.
- Freelancers recebem projetos já estruturados, escolhem quais aceitar e entregam tudo pela própria plataforma.
- Toda a comunicação, notificações e pagamentos ficam centralizados, reduzindo ruídos e aumentando a segurança para os dois lados.

Ideal para agências, pequenos negócios, empreendedores digitais e freelancers que querem profissionalizar a forma como contratam e entregam serviços.

---

## SITE FREELANCER – One Page Pitch (EN)

SITE FREELANCER is a platform that makes hiring and working as a freelancer simple and predictable:

- Clients go through a guided briefing and, in a few minutes, have a clear project with a fixed price and transparent fees.
- Freelancers receive structured projects, choose which ones to accept and deliver everything inside the platform.
- All communication, notifications and payments are centralized, reducing friction and increasing trust on both sides.

Perfect for agencies, small businesses, digital entrepreneurs and freelancers who want a more professional way to manage projects and service delivery.

---

## SITE FREELANCER

SITE FREELANCER é uma plataforma digital que conecta empresas e empreendedores a freelancers especializados, garantindo pedidos bem estruturados, comunicação centralizada e pagamentos seguros.

- Para **clientes**: menos burocracia, mais resultado. O fluxo guiado de briefing ajuda a descrever exatamente o que precisa, o sistema calcula taxas automaticamente e o projeto é distribuído rapidamente para freelancers qualificados.
- Para **freelancers**: oportunidades constantes, gestão simples e previsibilidade. Projetos chegam já com escopo, valor fechado e prazos claros, permitindo foco total na entrega.

Com base em Laravel e Livewire, o SITE FREELANCER foi pensado para ser um produto escalável, com boa experiência de uso tanto para quem contrata quanto para quem executa.

## Para quem é

- **Agências e pequenos negócios** que precisam contratar serviços criativos ou digitais com rapidez, sem perder tempo escrevendo longos briefings do zero.
- **Empreendedores e infoprodutores** que querem padronizar a contratação de freelancers (design, tráfego, copy, etc.) com mais controle sobre orçamento e entregas.
- **Freelancers iniciantes** que buscam um fluxo simples para começar a receber projetos com briefing claro e valor definido.
- **Freelancers experientes** que desejam aumentar a recorrência de pedidos e centralizar comunicação, entregas e pagamentos em um único lugar.

## Tecnologias

- PHP 8+ / Laravel
- Livewire
- Vite (frontend, assets)
- MySQL (ou outro banco compatível com Laravel)

## Requisitos

- PHP 8.1 ou superior com extensões comuns do Laravel habilitadas
- Composer
- Node.js (recomendado 18+)
- Banco de dados MySQL/MariaDB configurado

## Instalação e Configuração

1. Clonar o repositório e instalar as dependências PHP:
   - `composer install`
2. Instalar as dependências do frontend:
   - `npm install`
3. Criar o arquivo de ambiente:
   - `cp .env.example .env` (ou copie manualmente no Windows)
4. Configurar as variáveis do `.env`:
   - `APP_URL`
   - Configurações de banco de dados (`DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`)
   - Configurações de e-mail (para notificações por e-mail, se usadas)
5. Gerar a chave da aplicação:
   - `php artisan key:generate`
6. Rodar as migrações (e seeders, se existirem):
   - `php artisan migrate` ou `php artisan migrate --seed`

## Como Rodar o Projeto em Desenvolvimento

- Servidor Laravel:
  - `php artisan serve`
- Build/watch de assets (CSS/JS):
  - `npm run dev` (ou `npm run build` para produção)

## Principais Áreas da Aplicação

- Cliente
  - Publicação de pedidos
  - Dashboard e histórico de pedidos
  - Cancelamento de pedidos
  - Perfil e configurações
- Freelancer
  - Dashboard do freelancer
  - Projetos disponíveis
  - Carteira (wallet)
  - Afiliação e patrocínios
  - Configurações de conta e notificações
- Notificações
  - Painel interno de notificações
  - (opcional) Notificações por e-mail

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

7. **Aceite ou Recusa do Freelancer**
   - O freelancer pode visualizar todos os projetos disponíveis e optar por **aceitar** ou **recusar** cada um.
   - Ao aceitar, o projeto é atribuído ao freelancer e sai da lista de disponíveis para outros.
   - Ao recusar, o projeto permanece disponível para outros freelancers.

8. **Pós-Aceite: Execução e Entrega**
   - Após o aceite, o freelancer inicia o trabalho e pode interagir com o cliente pela plataforma.
   - O freelancer faz a entrega do serviço pela área do projeto.
   - O cliente avalia a entrega e, se aprovado, o pagamento é liberado ao freelancer.

> **Observação:**
> O valor informado pelo cliente no momento do pedido é **fixo e não negociável**. Cabe ao freelancer decidir se aceita ou recusa o projeto conforme o valor e as condições apresentadas.

Esse fluxo garante que todos os pedidos tenham título, descrição clara, estejam prontos para análise dos freelancers e que os profissionais sejam notificados de novas oportunidades, com etapas claras de aceite, execução e entrega.

## Melhorias de Usabilidade e Funcionalidade nas Configurações do Freelancer

Para garantir uma experiência moderna e eficiente ao freelancer, as seguintes melhorias foram implementadas ou recomendadas na tela de configurações:

- Permitir upload e visualização da foto de perfil.
- Edição de nome e e-mail diretamente na tela de configurações.
- Status visual para notificações de e-mail (badge “Ativado”/“Desativado”).
- Separação clara em seções: Perfil e Notificações.
- Feedback visual ao salvar alterações (mensagem de sucesso/erro).
- Botão de salvar destacado e com feedback visual.
- Validação de campos obrigatórios (nome, e-mail, foto).
- Layout responsivo e acessível.

Essas melhorias tornam a área de configurações mais intuitiva, funcional e alinhada com as melhores práticas de UX.

## Rotas Importantes (Resumo)

Algumas rotas principais (padrão, podem ser ajustadas conforme evolução do projeto):

- `/` – página inicial (landing)
- `/projetos` – listagem pública de projetos
- `/pedido`, `/briefing`, `/valor`, `/pagamento` – fluxo de criação de novo pedido (cliente)
- `/cliente/dashboard` – painel do cliente
- `/freelancer/dashboard` – painel do freelancer
- `/freelancer/projetos-disponiveis` – projetos disponíveis para freelancers
- `/notificacoes` – painel de notificações internas

## Próximos Passos / Ideias de Evolução

- Melhorar testes automatizados para fluxos críticos (novo pedido, pagamento, aceite de projeto, avaliação).
- Detalhar papéis de usuário (cliente, freelancer, admin) e permissões.
- Documentar integrações externas (ex: PayPal fake/real, gateways de pagamento).
- Adicionar exemplos de payloads de API (se for exposto no futuro).
 Fuxos por implementar
 Cadastro, pagamento e chat
 o que me sugeres?

Aqui estão sugestões para melhorar o fluxo de cadastro e a arquitetura do projeto:

Centralize regras de cadastro em um controller dedicado (ex: RegisterController), facilitando manutenção e testes.



Padronize validações e mensagens de erro em português.
Considere testes automatizados para o fluxo de cadastro.
---

Este README é focado no projeto SITE FREELANCER. Para detalhes avançados do framework, consulte a documentação oficial do Laravel.

observações  importantes
Não, isso não é necessário na maioria dos casos. Remover a pasta vendor e o composer.lock só é recomendado quando há erros graves de dependências ou arquivos corrompidos, como o que você enfrentou.
rm -r vendor
del composer.lock

composer install
