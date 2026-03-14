@extends('layouts.main')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-16 text-gray-700">
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Política de Privacidade</h1>
    <p class="text-sm text-gray-500 mb-10">Última actualização: março de 2026</p>

    <section class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-3">1. Responsável pelo tratamento</h2>
        <p>
            O responsável pelo tratamento dos seus dados pessoais é a
            <strong class="text-gray-900">{{ config('app.name') }}</strong>,
            com sede em Luanda, Angola, contactável através de
            <a href="mailto:{{ config('mail.from.address') }}" class="text-[#00baff] hover:underline">
                {{ config('mail.from.address') }}
            </a>.
        </p>
    </section>

    <section class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-3">2. Dados recolhidos</h2>
        <ul class="list-disc pl-5 space-y-1">
            <li>Dados de identificação: nome, endereço de e-mail, número de telefone.</li>
            <li>Dados de perfil: foto, portfolio, competências e historial de serviços.</li>
            <li>Dados de transação: pagamentos, facturas e histórico de carteira.</li>
            <li>Dados técnicos: endereço IP, tipo de browser, páginas visitadas (via cookies analíticos, apenas com consentimento).</li>
        </ul>
    </section>

    <section class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-3">3. Finalidade e base legal</h2>
        <ul class="list-disc pl-5 space-y-1">
            <li><strong class="text-gray-900">Execução contratual</strong> — prestação dos serviços da plataforma (Art. 6.º, n.º 1, al. b) RGPD / Art. 7.º Lei 22/11).</li>
            <li><strong class="text-gray-900">Obrigação legal</strong> — cumprimento de obrigações fiscais e KYC (Art. 6.º, n.º 1, al. c) RGPD).</li>
            <li><strong class="text-gray-900">Consentimento</strong> — envio de comunicações de marketing e cookies analíticos (Art. 6.º, n.º 1, al. a) RGPD).</li>
            <li><strong class="text-gray-900">Interesse legítimo</strong> — segurança da plataforma e prevenção de fraude (Art. 6.º, n.º 1, al. f) RGPD).</li>
        </ul>
    </section>

    <section class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-3">4. Cookies</h2>
        <p class="mb-2">Utilizamos dois tipos de cookies:</p>
        <ul class="list-disc pl-5 space-y-1">
            <li><strong class="text-gray-900">Essenciais</strong> — necessários para autenticação e segurança da sessão. Não podem ser desactivados.</li>
            <li><strong class="text-gray-900">Analíticos</strong> — recolhem dados de navegação de forma anónima para melhorar a plataforma. Apenas activados com o seu consentimento.</li>
        </ul>
        <p class="mt-2">Pode alterar as suas preferências a qualquer momento clicando em «Apenas essenciais» no banner de cookies.</p>
    </section>

    <section class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-3">5. Partilha de dados</h2>
        <p>
            Os seus dados não são vendidos a terceiros. Podem ser partilhados com prestadores de serviços técnicos (processamento de pagamentos, envio de e-mails) sujeitos a contratos de tratamento de dados compatíveis com a legislação aplicável.
        </p>
    </section>

    <section class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-3">6. Transferências internacionais</h2>
        <p>
            Alguns dos nossos fornecedores estão localizados fora de Angola. Nestas situações garantimos salvaguardas adequadas (cláusulas contratuais-tipo ou decisão de adequação da Comissão Europeia).
        </p>
    </section>

    <section class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-3">7. Os seus direitos</h2>
        <p class="mb-2">Ao abrigo da Lei n.º 22/11 e do RGPD, tem direito a:</p>
        <ul class="list-disc pl-5 space-y-1">
            <li>Aceder, rectificar ou apagar os seus dados pessoais.</li>
            <li>Solicitar a portabilidade dos dados.</li>
            <li>Opor-se ao tratamento ou solicitar a sua limitação.</li>
            <li>Retirar o consentimento a qualquer momento, sem prejuízo da licitude do tratamento anterior.</li>
        </ul>
        <p class="mt-2">
            Para exercer estes direitos, contacte-nos em
            <a href="mailto:{{ config('mail.from.address') }}" class="text-[#00baff] hover:underline">
                {{ config('mail.from.address') }}
            </a>.
        </p>
    </section>

    <section class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-3">8. Conservação dos dados</h2>
        <p>
            Os dados são conservados pelo período necessário à prestação dos serviços e, posteriormente, pelo período mínimo exigido por obrigações legais (regra geral, 5 anos para dados fiscais).
        </p>
    </section>

    <section>
        <h2 class="text-xl font-semibold text-gray-900 mb-3">9. Actualizações</h2>
        <p>
            Esta política pode ser actualizada periodicamente. Em caso de alterações materiais, notificamo-lo por e-mail ou através de um aviso na plataforma.
        </p>
    </section>
</div>
@endsection
