# Fluxo de Cadastro e Verificação de E-mail

1. O usuário preenche o formulário de cadastro e envia os dados.
2. Após o cadastro, o sistema exibe a mensagem: "Cadastro realizado! Verifique seu e-mail para ativar a conta."
3. O usuário recebe um e-mail com um link de verificação.
4. O usuário deve acessar o e-mail e clicar no link de verificação.
5. Ao clicar no link, se não estiver logado, o sistema redireciona para a tela de login.
6. O usuário faz login normalmente com e-mail e senha.
7. Após o login, o sistema verifica o e-mail e libera o acesso à plataforma.

**Observação:**
- Esse fluxo garante máxima segurança, pois só quem sabe a senha pode ativar a conta, mesmo que o link de verificação seja interceptado.
- O usuário só precisa verificar o e-mail uma vez após o cadastro.

---

# Fluxo Alternativo: Verificação por Código (OTP)

1. O usuário faz login normalmente com e-mail e senha.
2. Se o e-mail não estiver verificado, o sistema exibe a tela para digitar o código OTP.
3. O sistema envia automaticamente um código de 6 dígitos para o e-mail do usuário.
4. O usuário digita o código recebido na tela.
5. Se o código estiver correto e válido, o e-mail é marcado como verificado e o acesso é liberado.
6. Caso o código expire ou seja digitado incorretamente, o usuário pode solicitar o reenvio.

**Vantagens:**
- Experiência mais fluida: o usuário não precisa sair do fluxo de login.
- Segurança mantida: só quem tem acesso ao e-mail consegue validar o código.
- O código expira em 10 minutos e só pode ser usado uma vez.

**Observação:**
- O fluxo OTP pode ser ativado/desativado conforme a necessidade do projeto.

---

# Fluxo de Pagamento em Custódia (Escrow)

1. O cliente faz o pedido e realiza o pagamento antecipado. O valor fica retido na plataforma (em custódia).
2. O freelancer executa o serviço normalmente.
3. Após a entrega do serviço pelo freelancer, o cliente pode revisar o trabalho.
4. Se estiver satisfeito, o cliente acessa o painel do pedido e clica no botão **"Liberar pagamento"**.
5. Ao clicar, o status do pedido é atualizado e o admin é notificado para processar o pagamento ao freelancer.
6. O valor é então liberado para o freelancer (pode ser automático ou após conferência manual do admin).
7. Todo o processo é registrado para fins de auditoria e segurança.

**Vantagens desse fluxo:**
- Segurança para ambas as partes: o freelancer só trabalha se o dinheiro estiver retido, e o cliente só libera se estiver satisfeito.
- Reduz fraudes e inadimplência.
- Transparência e rastreabilidade para a plataforma.

**Observação:**
- Em caso de disputa, a plataforma pode intervir antes da liberação do pagamento.

---

# Fluxo de Seleção de Freelancer pelo Cliente

1. Após publicar o pedido e realizar o pagamento, o cliente visualiza no painel uma lista de freelancers candidatos ao projeto (até 6, mas pode ser menos).
2. Cada candidato aparece com nome, status (pendente, escolhido, rejeitado) e botão de ação.
3. O cliente pode analisar os perfis e clicar em **"Escolher"** para selecionar o freelancer desejado.
4. Ao escolher:
   - O status do candidato selecionado muda para **"Escolhido"**.
   - Todos os outros candidatos são automaticamente marcados como **"Rejeitados"**.
   - O campo `freelancer_id` do pedido é atualizado e o status do projeto muda para **"accepted"**.
   - O freelancer escolhido recebe uma **notificação interna** informando que foi selecionado para o projeto.
   - Os freelancers rejeitados recebem uma **notificação interna** informando que não foram escolhidos.
5. O cliente pode acompanhar o andamento do projeto com o freelancer escolhido normalmente.

**Vantagens desse fluxo:**
- O cliente tem total controle sobre a escolha do profissional.
- Transparência para todos os candidatos.
- Notificações automáticas garantem comunicação clara e rápida.
- O sistema permite a escolha mesmo se houver menos de 6 candidatos.

**Observação:**
- Todo o processo é registrado para fins de auditoria e segurança.
- O fluxo é integrado ao painel do cliente, tornando a experiência simples e intuitiva.

---

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
  Claro que posso — e vou organizar isso de forma **clara, profissional e lógica**, já a pensar em **documentação de sistema / proposta técnica**. Vou descrever o **fluxo completo do módulo de afilhados**, sem linguagem confusa.

---

# 📌 Módulo de Afilhados – Descrição do Fluxo

## 1. Geração do Link de Afilhado

* **Todos os utilizadores da plataforma** (freelancers e clientes) possuem automaticamente um **link único de afilhado**.
* Esse link identifica quem fez a indicação e é usado para rastrear novas inscrições e ações realizadas na plataforma.

---

## 2. Acesso via Link de Afilhado

* Quando um novo utilizador:

  * acede à plataforma através de um link de afilhado **e**
  * cria uma conta **e**
  * publica pelo menos **um serviço**,

➡️ o proprietário do link de afilhado recebe automaticamente uma **recompensa de 300 Kz**.

> ⚠️ Apenas publicar o serviço ativa o benefício. Apenas criar conta **não gera recompensa**.

---

## 3. Registo e Validação da Recompensa

* O sistema valida:

  * origem do acesso (link de afilhado),
  * criação de conta,
  * publicação do primeiro serviço.
* Após a validação:

  * é registada a comissão de **300 Kz**,
  * o valor é atribuído ao **saldo de afilhado** do utilizador que indicou.

---

## 4. Dashboard do Freelancer (Afilhado Ativo)

O freelancer terá **dois saldos separados**, permitindo total transparência e controlo:

### 🔹 Card 1 – Saldo Normal

* Representa os ganhos provenientes de:

  * serviços prestados,
  * vendas concluídas,
  * entregas aceites.

### 🔹 Card 2 – Saldo como Afilhado

* Representa os ganhos provenientes de:

  * indicações feitas através do link de afilhado,
  * comissões fixas de **300 Kz por serviço publicado**.

📊 O freelancer poderá:

* acompanhar os valores ganhos por cada via,
* consultar histórico de indicações,
* perceber claramente:

  * quanto ganha com o seu trabalho (X),
  * quanto ganha como afilhado (Y).

---

## 5. Dashboard do Cliente

O cliente também terá um **card dedicado ao módulo de afilhados**, permitindo acompanhamento das suas indicações.

### 🔹 Card – Afilhados do Cliente

Este card permitirá visualizar:

* número total de acessos ao link de afilhado,
* número de contas criadas através do link,
* número de serviços publicados a partir dessas contas,
* total de comissões acumuladas (300 Kz por serviço).

📈 Assim, o cliente consegue perceber:

* o impacto do seu link,
* quantas pessoas trouxe para a plataforma,
* quanto já ganhou como afilhado.

---

## 6. Controlo e Transparência

* Todos os movimentos ficam registados:

  * data,
  * utilizador indicado,
  * ação realizada (publicação de serviço),
  * valor atribuído.
* O sistema impede duplicações e fraudes (ex.: mesma pessoa indicar a si própria).

---

## 7. Benefícios do Fluxo

✔ Incentiva o crescimento orgânico da plataforma
✔ Motiva freelancers e clientes a divulgar o sistema
✔ Garante transparência financeira
✔ Separa claramente **ganhos por trabalho** e **ganhos por afiliação**

---

## Segurança no Sistema de Afiliados

O sistema de afiliados possui as seguintes medidas de segurança:

- **Impedir auto-indicação:** O afiliado não pode indicar a si mesmo, evitando fraudes.
- **Comissão apenas para novos usuários:** A comissão só é creditada se o usuário indicado for realmente novo e não houver indicação anterior.
- **Registro de IP e user-agent:** Cada indicação salva o IP e o user-agent do usuário cadastrado, permitindo rastreamento e auditoria.
- **Validação de afiliado ativo:** Apenas afiliados ativos (não bloqueados ou banidos) podem receber comissão.
- **Prevenção de duplicidade:** Não é possível gerar múltiplas comissões para o mesmo usuário ou e-mail.

Essas medidas garantem mais transparência, rastreabilidade e dificultam fraudes no programa de indicações.

---

Se quiseres, no próximo passo posso:

* transformar isso em **requisitos funcionais**
* desenhar o **fluxo técnico (backend / base de dados)**
* ou escrever isso em formato de **proposta comercial** para apresentar ao cliente

Diz-me como vais usar esse texto 👌
