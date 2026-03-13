<?php

namespace App\Modules\Marketplace\Services;

class BriefingTemplateService
{
    /**
     * Returns template data for a given service type slug.
     * Each template has: questions (list), example (string), tips (string), keywords (for matching).
     */
    public static function templates(): array
    {
        return [
            'Desenvolvimento de sites e sistemas web' => [
                'icon'     => 'code',
                'questions' => [
                    'Qual o objetivo principal do site (institucional, vendas, portfÃ³lio, blog)?',
                    'Quantas pÃ¡ginas/seÃ§Ãµes o site deve ter?',
                    'HÃ¡ referÃªncias de sites que vocÃª gosta?',
                    'Quem Ã© o pÃºblico-alvo?',
                    'Precisa de integraÃ§Ã£o com algum sistema (pagamento, CRM, API)?',
                ],
                'example'  => 'Preciso de um site institucional para minha empresa de consultoria. O site deve ter 5 pÃ¡ginas: Home, Sobre, ServiÃ§os, Blog e Contato. ReferÃªncias: exemplo.com. PÃºblico B2B, profissionais de 30-50 anos. Precisa de formulÃ¡rio de contacto e integraÃ§Ã£o com Google Analytics.',
                'tips'     => 'Quanto mais detalhes vocÃª fornecer (tecnologia preferida, integraÃ§Ãµes, prazo), mais rÃ¡pido encontrarÃ¡ o freelancer ideal.',
                'keywords' => ['web', 'site', 'sistema', 'desenvolvimento', 'php', 'laravel', 'react', 'html', 'css'],
            ],
            'CriaÃ§Ã£o de lojas virtuais (e-commerce)' => [
                'icon'     => 'shopping-cart',
                'questions' => [
                    'Quantos produtos pretende vender inicialmente?',
                    'Quais mÃ©todos de pagamento precisa (cartÃ£o, PayPal, transferÃªncia)?',
                    'Tem domÃ­nio e hospedagem ou precisa de indicaÃ§Ã£o?',
                    'Prefere alguma plataforma (WooCommerce, Shopify, personalizada)?',
                    'Precisa de gestÃ£o de stock/inventÃ¡rio?',
                ],
                'example'  => 'Precisamos de uma loja virtual para vender roupas femininas. Inicialmente teremos 50 produtos. Precisamos de pagamento por cartÃ£o, transferÃªncia bancÃ¡ria e referÃªncia. PreferÃªncia por WooCommerce. Precisamos de gestÃ£o de stock e envio de e-mails automÃ¡ticos.',
                'tips'     => 'Especifique a plataforma preferida e os mÃ©todos de pagamento disponÃ­veis no seu paÃ­s para agilizar a proposta.',
                'keywords' => ['e-commerce', 'loja', 'woocommerce', 'shopify', 'magento', 'vendas', 'stock', 'pagamento'],
            ],
            'Desenvolvimento de aplicativos mobile' => [
                'icon'     => 'phone',
                'questions' => [
                    'O app deve funcionar no Android, iOS ou ambos?',
                    'Qual Ã© a funcionalidade principal do app?',
                    'Precisa de login/cadastro de utilizadores?',
                    'HÃ¡ backend (servidor) existente ou precisa ser criado do zero?',
                    'Tem design/protÃ³tipo ou precisa que o freelancer crie?',
                ],
                'example'  => 'App Android e iOS para delivery de refeiÃ§Ãµes. Funcionamento: utilizador regista, navega restaurantes, faz pedido e paga. Precisa de backend com API REST. Temos design no Figma. O diferencial Ã© o chat ao vivo com o restaurante.',
                'tips'     => 'Descreva os fluxos principais (telas) e se jÃ¡ tem um design. Isso reduz muito o tempo e custo.',
                'keywords' => ['app', 'mobile', 'android', 'ios', 'flutter', 'react native', 'swift', 'kotlin'],
            ],
            'Design grÃ¡fico (logos, banners, identidade visual)' => [
                'icon'     => 'palette',
                'questions' => [
                    'Precisa de logo, identidade completa ou material especÃ­fico?',
                    'Qual o estilo visual preferido (moderno, minimalista, clÃ¡ssico)?',
                    'Tem cores ou fontes de referÃªncia?',
                    'Quais arquivos de entrega precisa (AI, PDF, PNG)?',
                    'Para que plataformas serÃ¡ usado (redes sociais, impressÃ£o, web)?',
                ],
                'example'  => 'Preciso de identidade visual completa para startup de tecnologia. Incluindo: logo, paleta de cores, tipografia, cartÃ£o de visita e assinatura de e-mail. Estilo moderno e minimalista. ReferÃªncias: Stripe, Notion. Cores: tons de azul e branco. Entrega em AI, PDF e PNG.',
                'tips'     => 'Envie referÃªncias (sites, marcas que admira) para o freelancer entender melhor o estilo desejado.',
                'keywords' => ['design', 'logo', 'identidade visual', 'branding', 'photoshop', 'illustrator', 'figma'],
            ],
            'RedaÃ§Ã£o de textos, artigos e blogs' => [
                'icon'     => 'pencil',
                'questions' => [
                    'Qual o tema e objetivo do conteÃºdo?',
                    'Qual o tom/voz pretendida (formal, informal, tÃ©cnica)?',
                    'Quantos artigos/textos e qual o tamanho mÃ©dio (palavras)?',
                    'Precisa de optimizaÃ§Ã£o SEO? Se sim, quais palavras-chave?',
                    'Tem guia de estilo ou brand voice estabelecida?',
                ],
                'example'  => 'Preciso de 10 artigos mensais para blog de finanÃ§as pessoais. Cada artigo com 800-1200 palavras, tom informal mas educativo, optimizados para SEO com palavras-chave fornecidas. PÃºblico: portugueses com 25-40 anos que querem aprender a poupar.',
                'tips'     => 'ForneÃ§a exemplos de artigos que gosta para orientar o tom e estilo do conteÃºdo.',
                'keywords' => ['redaÃ§Ã£o', 'copywriting', 'conteÃºdo', 'seo', 'blog', 'artigo', 'texto'],
            ],
            'Marketing digital (SEO, Google Ads, Facebook Ads)' => [
                'icon'     => 'chart-bar',
                'questions' => [
                    'Qual plataforma: Google Ads, Meta Ads, SEO orgÃ¢nico ou tudo?',
                    'Qual o orÃ§amento mensal para anÃºncios?',
                    'Qual o produto/serviÃ§o a promover?',
                    'HÃ¡ campanhas ativas? Quais os resultados actuais?',
                    'Qual o objectivo: leads, vendas, brand awareness?',
                ],
                'example'  => 'Precisamos de gestÃ£o mensal de Google Ads e Meta Ads para clÃ­nica dentÃ¡ria em Luanda. OrÃ§amento: 500 USD/mÃªs em anÃºncios. Objectivo: 50 novos leads por mÃªs. NÃ£o temos campanhas activas. KPI principal: custo por lead.',
                'tips'     => 'Inclua o orÃ§amento de anÃºncios e o custo mÃ¡ximo que aceita pagar por cada lead/venda.',
                'keywords' => ['marketing', 'seo', 'google ads', 'facebook ads', 'meta ads', 'trÃ¡fego', 'leads', 'campanhas'],
            ],
            'GestÃ£o de redes sociais' => [
                'icon'     => 'share',
                'questions' => [
                    'Quais redes sociais precisam de gestÃ£o (Instagram, Facebook, LinkedIn)?',
                    'Quantas publicaÃ§Ãµes por semana?',
                    'Precisa de criaÃ§Ã£o de conteÃºdo (design + texto)?',
                    'HÃ¡ uma estratÃ©gia de conteÃºdo definida?',
                    'Vai responder comentÃ¡rios/mensagens ou quer que o freelancer cuide disso?',
                ],
                'example'  => 'GestÃ£o completa do Instagram e Facebook de restaurante. 5 publicaÃ§Ãµes/semana por rede, incluindo design e legenda. Precisamos de crescimento orgÃ¢nico e interaÃ§Ã£o com seguidores. NÃ£o temos estratÃ©gia definida â€” precisamos que o freelancer proponha.',
                'tips'     => 'Partilhe exemplos de marcas que admira nas redes sociais para alinhar o estilo visual.',
                'keywords' => ['redes sociais', 'instagram', 'facebook', 'linkedin', 'social media', 'conteÃºdo'],
            ],
            'EdiÃ§Ã£o de imagens e vÃ­deos' => [
                'icon'     => 'film',
                'questions' => [
                    'Que tipo de ediÃ§Ã£o: imagens, vÃ­deo, motion graphics ou animaÃ§Ã£o?',
                    'Qual o volume (nÃºmero de peÃ§as) e duraÃ§Ã£o dos vÃ­deos?',
                    'Para que plataforma serÃ¡ o conteÃºdo (YouTube, Instagram, TV)?',
                    'Tem material bruto (filmagens, fotos) ou precisa que o freelancer se encarregue?',
                    'Qual o prazo de entrega?',
                ],
                'example'  => 'EdiÃ§Ã£o de 4 vÃ­deos por mÃªs para canal YouTube de educaÃ§Ã£o. Cada vÃ­deo: 10-15 minutos, incluindo abertura animada, cortes, legendas e fundo musical. Temos o vÃ­deo bruto gravado. Precisamos de entrega em 7 dias apÃ³s receber o material.',
                'tips'     => 'Envie um exemplo do resultado final que procura (vÃ­deo referÃªncia) para alinhar expectativas.',
                'keywords' => ['vÃ­deo', 'ediÃ§Ã£o', 'premiere', 'after effects', 'motion', 'animaÃ§Ã£o', 'youtube'],
            ],
            'Consultoria em TI, negÃ³cios, finanÃ§as e RH' => [
                'icon'     => 'briefcase',
                'questions' => [
                    'Na qual Ã¡rea precisa de consultoria (TI, negÃ³cios, finanÃ§as, RH)?',
                    'Qual o problema especÃ­fico que precisa resolver?',
                    'A consultoria Ã© pontual ou mensal (retainer)?',
                    'Precisa de relatÃ³rio/entregÃ¡vel especÃ­fico?',
                    'Quantas horas semanais estima precisar?',
                ],
                'example'  => 'Consultoria financeira para PME do sector de construÃ§Ã£o. Precisamos de anÃ¡lise de fluxo de caixa, re-estruturaÃ§Ã£o de custos e projecÃ§Ãµes para 12 meses. Consultoria pontual com entrega de relatÃ³rio detalhado em 2 semanas. Estimamos 20 horas de trabalho.',
                'tips'     => 'Seja especÃ­fico sobre o deliverable esperado: relatÃ³rio, plano de acÃ§Ã£o, apresentaÃ§Ã£o, etc.',
                'keywords' => ['consultoria', 'ti', 'negÃ³cios', 'finanÃ§as', 'rh', 'estratÃ©gia', 'anÃ¡lise'],
            ],
        ];
    }

    public static function get(string $service_type): ?array
    {
        $templates = self::templates();
        return $templates[$service_type] ?? null;
    }

    /**
     * Returns a generic template for unknown/custom service types.
     */
    public static function generic(): array
    {
        return [
            'questions' => [
                'Qual Ã© o resultado esperado ao fim do projeto?',
                'Quais sÃ£o as principais funcionalidades ou entregas?',
                'Qual Ã© o pÃºblico-alvo ou utilizador final?',
                'Tem referÃªncias ou exemplos de projetos similares?',
                'Qual Ã© o prazo desejado para conclusÃ£o?',
            ],
            'example'  => 'Preciso de um profissional para [descreva o projeto]. O objectivo Ã© [resultado esperado]. O pÃºblico-alvo Ã© [descreva]. As principais entregas sÃ£o: [liste]. Prazo: [data/perÃ­odo]. ReferÃªncias: [links ou exemplos].',
            'tips'     => 'Quanto mais detalhes fornecer sobre o objetivo final, mais facilmente encontrarÃ¡ o profissional certo.',
            'keywords' => [],
        ];
    }
}

