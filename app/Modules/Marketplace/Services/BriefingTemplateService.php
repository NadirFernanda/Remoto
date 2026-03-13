<?php

namespace App\Modules\Marketplace\Services;

class BriefingTemplateService
{
    public static function templates(): array
    {
        return [
            'Desenvolvimento de sites e sistemas web' => [
                'icon'      => 'code',
                'questions' => [
                    'Qual o objetivo principal do site (institucional, vendas, portfólio, blog)?',
                    'Quantas páginas/seções o site deve ter?',
                    'Há referências de sites que você gosta?',
                    'Quem é o público-alvo?',
                    'Precisa de integração com algum sistema (pagamento, CRM, API)?',
                ],
                'example'  => 'Preciso de um site institucional para minha empresa de consultoria. O site deve ter 5 páginas: Home, Sobre, Serviços, Blog e Contato. Referências: exemplo.com. Público B2B, profissionais de 30-50 anos. Precisa de formulário de contacto e integração com Google Analytics.',
                'tips'     => 'Quanto mais detalhes você fornecer (tecnologia preferida, integrações, prazo), mais rápido encontrará o freelancer ideal.',
                'keywords' => ['web', 'site', 'sistema', 'desenvolvimento', 'php', 'laravel', 'react', 'html', 'css'],
            ],
            'Criação de lojas virtuais (e-commerce)' => [
                'icon'      => 'shopping-cart',
                'questions' => [
                    'Quantos produtos pretende vender inicialmente?',
                    'Quais métodos de pagamento precisa (cartão, PayPal, transferência)?',
                    'Tem domínio e hospedagem ou precisa de indicação?',
                    'Prefere alguma plataforma (WooCommerce, Shopify, personalizada)?',
                    'Precisa de gestão de stock/inventário?',
                ],
                'example'  => 'Precisamos de uma loja virtual para vender roupas femininas. Inicialmente teremos 50 produtos. Precisamos de pagamento por cartão, transferência bancária e referência. Preferência por WooCommerce. Precisamos de gestão de stock e envio de e-mails automáticos.',
                'tips'     => 'Especifique a plataforma preferida e os métodos de pagamento disponíveis no seu país para agilizar a proposta.',
                'keywords' => ['e-commerce', 'loja', 'woocommerce', 'shopify', 'magento', 'vendas', 'stock', 'pagamento'],
            ],
            'Desenvolvimento de aplicações mobile' => [
                'icon'      => 'phone',
                'questions' => [
                    'O app deve funcionar no Android, iOS ou ambos?',
                    'Qual é a funcionalidade principal do app?',
                    'Precisa de login/cadastro de utilizadores?',
                    'Há backend (servidor) existente ou precisa ser criado do zero?',
                    'Tem design/protótipo ou precisa que o freelancer crie?',
                ],
                'example'  => 'App Android e iOS para delivery de refeições. Funcionamento: utilizador regista, navega restaurantes, faz pedido e paga. Precisa de backend com API REST. Temos design no Figma. O diferencial é o chat ao vivo com o restaurante.',
                'tips'     => 'Descreva os fluxos principais (telas) e se já tem um design. Isso reduz muito o tempo e custo.',
                'keywords' => ['app', 'mobile', 'android', 'ios', 'flutter', 'react native', 'swift', 'kotlin'],
            ],
            'Design gráfico (logos, banners, identidade visual)' => [
                'icon'      => 'palette',
                'questions' => [
                    'Precisa de logo, identidade completa ou material específico?',
                    'Qual o estilo visual preferido (moderno, minimalista, clássico)?',
                    'Tem cores ou fontes de referência?',
                    'Quais arquivos de entrega precisa (AI, PDF, PNG)?',
                    'Para que plataformas será usado (redes sociais, impressão, web)?',
                ],
                'example'  => 'Preciso de identidade visual completa para startup de tecnologia. Incluindo: logo, paleta de cores, tipografia, cartão de visita e assinatura de e-mail. Estilo moderno e minimalista. Referências: Stripe, Notion. Cores: tons de azul e branco. Entrega em AI, PDF e PNG.',
                'tips'     => 'Envie referências (sites, marcas que admira) para o freelancer entender melhor o estilo desejado.',
                'keywords' => ['design', 'logo', 'identidade visual', 'branding', 'photoshop', 'illustrator', 'figma'],
            ],
            'Redação de textos, artigos e blogs' => [
                'icon'      => 'pencil',
                'questions' => [
                    'Qual o tema e objetivo do conteúdo?',
                    'Qual o tom/voz pretendida (formal, informal, técnica)?',
                    'Quantos artigos/textos e qual o tamanho médio (palavras)?',
                    'Precisa de optimização SEO? Se sim, quais palavras-chave?',
                    'Tem guia de estilo ou brand voice estabelecida?',
                ],
                'example'  => 'Preciso de 10 artigos mensais para blog de finanças pessoais. Cada artigo com 800-1200 palavras, tom informal mas educativo, optimizados para SEO com palavras-chave fornecidas. Público: portugueses com 25-40 anos que querem aprender a poupar.',
                'tips'     => 'Forneça exemplos de artigos que gosta para orientar o tom e estilo do conteúdo.',
                'keywords' => ['redação', 'copywriting', 'conteúdo', 'seo', 'blog', 'artigo', 'texto'],
            ],
            'Marketing digital (SEO, Google Ads, Facebook Ads)' => [
                'icon'      => 'chart-bar',
                'questions' => [
                    'Qual plataforma: Google Ads, Meta Ads, SEO orgânico ou tudo?',
                    'Qual o orçamento mensal para anúncios?',
                    'Qual o produto/serviço a promover?',
                    'Há campanhas ativas? Quais os resultados actuais?',
                    'Qual o objectivo: leads, vendas, brand awareness?',
                ],
                'example'  => 'Precisamos de gestão mensal de Google Ads e Meta Ads para clínica dentária em Luanda. Orçamento: 500 USD/mês em anúncios. Objectivo: 50 novos leads por mês. Não temos campanhas activas. KPI principal: custo por lead.',
                'tips'     => 'Inclua o orçamento de anúncios e o custo máximo que aceita pagar por cada lead/venda.',
                'keywords' => ['marketing', 'seo', 'google ads', 'facebook ads', 'meta ads', 'tráfego', 'leads', 'campanhas'],
            ],
            'Gestão de redes sociais' => [
                'icon'      => 'share',
                'questions' => [
                    'Quais redes sociais precisam de gestão (Instagram, Facebook, LinkedIn)?',
                    'Quantas publicações por semana?',
                    'Precisa de criação de conteúdo (design + texto)?',
                    'Há uma estratégia de conteúdo definida?',
                    'Vai responder comentários/mensagens ou quer que o freelancer cuide?',
                ],
                'example'  => 'Gestão completa do Instagram e Facebook de restaurante. 5 publicações/semana por rede, incluindo design e legenda. Precisamos de crescimento orgânico e interação com seguidores. Não temos estratégia definida.',
                'tips'     => 'Partilhe exemplos de marcas que admira nas redes sociais para alinhar o estilo visual.',
                'keywords' => ['redes sociais', 'instagram', 'facebook', 'linkedin', 'social media', 'conteúdo'],
            ],
            'Edição de imagens e vídeos' => [
                'icon'      => 'film',
                'questions' => [
                    'Que tipo de edição: imagens, vídeo, motion graphics ou animação?',
                    'Qual o volume (número de peças) e duração dos vídeos?',
                    'Para que plataforma será o conteúdo (YouTube, Instagram, TV)?',
                    'Tem material bruto (filmagens, fotos) ou precisa que o freelancer se encarregue?',
                    'Qual o prazo de entrega?',
                ],
                'example'  => 'Edição de 4 vídeos por mês para canal YouTube de educação. Cada vídeo: 10-15 minutos, incluindo abertura animada, cortes, legendas e fundo musical. Temos o vídeo bruto gravado.',
                'tips'     => 'Envie um exemplo do resultado final que procura (vídeo referência) para alinhar expectativas.',
                'keywords' => ['vídeo', 'edição', 'premiere', 'after effects', 'motion', 'animação', 'youtube'],
            ],
            'Consultoria em TI, negócios, finanças e RH' => [
                'icon'      => 'briefcase',
                'questions' => [
                    'Na qual área precisa de consultoria (TI, negócios, finanças, RH)?',
                    'Qual o problema específico que precisa resolver?',
                    'A consultoria é pontual ou mensal (retainer)?',
                    'Precisa de relatório/entregável específico?',
                    'Quantas horas semanais estima precisar?',
                ],
                'example'  => 'Consultoria financeira para PME do sector de construção. Precisamos de análise de fluxo de caixa, re-estruturação de custos e projeções para 12 meses.',
                'tips'     => 'Seja específico sobre o deliverable esperado: relatório, plano de acção, apresentação, etc.',
                'keywords' => ['consultoria', 'ti', 'negócios', 'finanças', 'rh', 'estratégia', 'análise'],
            ],
        ];
    }

    public static function get(string $service_type): ?array
    {
        return self::templates()[$service_type] ?? null;
    }

    public static function generic(): array
    {
        return [
            'questions' => [
                'Qual é o resultado esperado ao fim do projeto?',
                'Quais são as principais funcionalidades ou entregas?',
                'Qual é o público-alvo ou utilizador final?',
                'Tem referências ou exemplos de projetos similares?',
                'Qual é o prazo desejado para conclusão?',
            ],
            'example'  => 'Preciso de um profissional para [descreva o projeto]. O objectivo é [resultado esperado].',
            'tips'     => 'Quanto mais detalhes fornecer sobre o objetivo final, mais facilmente encontrará o profissional certo.',
            'keywords' => [],
        ];
    }
}
