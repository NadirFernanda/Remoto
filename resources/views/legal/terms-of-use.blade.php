@extends('layouts.main')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-16 text-gray-700">
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Termos de Uso</h1>
    <p class="text-sm text-gray-500 mb-10">Última actualização: março de 2026</p>

    <section class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-3">1. Aceitação dos termos</h2>
        <p>
            Ao aceder ou utilizar a plataforma <strong class="text-gray-900">24h_Remoto</strong> ("<strong>Plataforma</strong>"),
            disponível em <strong>24horas.ao</strong>, concorda com os presentes Termos de Uso e com a nossa
            <a href="{{ route('legal.privacy') }}" class="text-[#00baff] hover:underline">Política de Privacidade</a>.
            Se não concordar, deverá cessar a utilização imediatamente.
        </p>
    </section>

    <section class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-3">2. Descrição do serviço</h2>
        <p>
            A 24h_Remoto é um marketplace digital que conecta clientes angolanos e freelancers para a prestação de
            serviços digitais e remotos. A plataforma actua exclusivamente como intermediária e não é parte nos
            contratos celebrados entre clientes e freelancers.
        </p>
    </section>

    <section class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-3">3. Elegibilidade</h2>
        <ul class="list-disc pl-5 space-y-1">
            <li>Para criar uma conta é necessário ter 18 anos ou mais.</li>
            <li>As informações fornecidas no registo devem ser verdadeiras e actualizadas.</li>
            <li>Cada utilizador só pode possuir uma conta pessoal.</li>
            <li>É proibido ceder ou partilhar credenciais de acesso com terceiros.</li>
        </ul>
    </section>

    <section class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-3">4. Responsabilidades do utilizador</h2>
        <ul class="list-disc pl-5 space-y-1">
            <li>Utilizar a plataforma de forma lícita e de boa-fé.</li>
            <li>Não publicar conteúdo falso, enganoso, ofensivo ou que viole direitos de terceiros.</li>
            <li>Não tentar aceder a áreas restritas ou interferir com o funcionamento técnico da plataforma.</li>
            <li>Cumprir os prazos e termos acordados em cada projecto.</li>
            <li>Pagar os valores devidos de forma segura através dos meios disponibilizados.</li>
        </ul>
    </section>

    <section class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-3">5. Pagamentos e comissões</h2>
        <p class="mb-2">
            Todos os pagamentos são processados através da plataforma com recurso a sistema de <em>escrow</em>
            (depósito em garantia). Os fundos só são libertados ao freelancer após confirmação de conclusão pelo cliente.
        </p>
        <p>
            A 24h_Remoto cobra uma comissão sobre cada transação bem-sucedida. A taxa actual encontra-se indicada
            na secção de preços da plataforma e pode ser alterada com aviso prévio de 15 dias.
        </p>
    </section>

    <section class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-3">6. Disputa e resolução de conflitos</h2>
        <p>
            Em caso de disputa entre cliente e freelancer, ambas as partes podem activar o Centro de Disputas
            da plataforma. A 24h_Remoto reserva-se o direito de mediar a situação e tomar decisões vinculativas
            com base nas evidências fornecidas, incluindo mensagens, ficheiros e histórico de entregas.
        </p>
    </section>

    <section class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-3">7. Propriedade intelectual</h2>
        <p>
            O trabalho entregue pelo freelancer é, salvo acordo expresso em contrário, propriedade do cliente
            após o pagamento integral. A 24h_Remoto não reivindica qualquer direito sobre os trabalhos criados
            entre os utilizadores.
        </p>
    </section>

    <section class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-3">8. Conteúdo proibido</h2>
        <ul class="list-disc pl-5 space-y-1">
            <li>Conteúdo ilegal, pornográfico, difamatório ou discriminatório.</li>
            <li>Serviços que violem legislação angolana ou internacional.</li>
            <li>Transacções fora da plataforma para contornar comissões.</li>
            <li>Uso de bots, automação ou scraping não autorizado.</li>
        </ul>
    </section>

    <section class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-3">9. Suspensão e encerramento de conta</h2>
        <p>
            A 24h_Remoto pode suspender ou encerrar contas que violem estes termos, sem aviso prévio em casos
            graves. O utilizador pode encerrar a sua conta a qualquer momento contactando o suporte.
            Os saldos em carteira serão processados conforme a política de saques vigente.
        </p>
    </section>

    <section class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-3">10. Limitação de responsabilidade</h2>
        <p>
            A 24h_Remoto não se responsabiliza por perdas ou danos resultantes de falhas técnicas,
            interrupciones de serviço, actos de terceiros ou incumprimento por parte dos utilizadores.
            A responsabilidade máxima da plataforma limita-se ao valor das comissões cobradas na transação
            em causa.
        </p>
    </section>

    <section class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-3">11. Alterações aos termos</h2>
        <p>
            Reservamo-nos o direito de actualizar estes Termos de Uso a qualquer momento. As alterações
            serão publicadas nesta página com a respectiva data de actualização. O uso continuado da
            plataforma após publicação implica aceitação das novas condições.
        </p>
    </section>

    <section class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-3">12. Contacto</h2>
        <p>
            Para questões relativas a estes termos, contacte-nos através de
            <a href="mailto:contacto@24horas.ao" class="text-[#00baff] hover:underline">contacto@24horas.ao</a>.
        </p>
    </section>
</div>
@endsection
