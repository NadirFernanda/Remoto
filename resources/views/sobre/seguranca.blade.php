@extends('layouts.main')

@section('content')
<div class="pub-page">
    <div class="pub-container--md" style="padding-top:2.5rem;padding-bottom:4rem;">

        <div class="pub-hero" style="margin-bottom:2.5rem;">
            <div class="pub-hero-label">Segurança</div>
            <h1 class="pub-hero-title">A sua segurança é a nossa prioridade</h1>
            <p class="pub-hero-sub">Medidas técnicas e processos humanos para proteger cada transação, dado pessoal e interação na plataforma.</p>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 mb-6 border border-[#e6f3fa] transition hover:shadow-2xl">
            <h2 class="text-2xl md:text-3xl font-extrabold text-[#0f172a] mb-4">Pagamentos protegidos por escrow</h2>
            <p class="text-[#475569] text-lg leading-relaxed">Quando um cliente aceita uma proposta, o valor do projecto é retido na plataforma. O freelancer só recebe após o cliente aprovar a entrega — ou após resolução de eventuais disputas. Nenhum pagamento é libertado antes da confirmação bilateral.</p>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 mb-6 border border-[#e6f3fa] transition hover:shadow-2xl">
            <h2 class="text-2xl md:text-3xl font-extrabold text-[#0f172a] mb-4">Verificação de identidade (KYC)</h2>
            <p class="text-[#475569] text-lg leading-relaxed">Todos os utilizadores que desejam sacar fundos ou atingir limites de transação mais elevados passam pelo nosso processo de verificação KYC (Know Your Customer). Isso garante que apenas pessoas reais e identificadas operam na plataforma.</p>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 mb-6 border border-[#e6f3fa] transition hover:shadow-2xl">
            <h2 class="text-2xl md:text-3xl font-extrabold text-[#0f172a] mb-6">Camadas de protecção técnica</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <div class="bg-[#f8fafc] rounded-2xl p-6 border border-[#e2e8f0]">
                    <div class="text-[#00baff] font-extrabold mb-2 text-lg">HTTPS / TLS</div>
                    <p class="text-[#64748b] text-base leading-relaxed m-0">Toda a comunicação entre o seu browser e os nossos servidores é cifrada com TLS 1.3.</p>
                </div>
                <div class="bg-[#f8fafc] rounded-2xl p-6 border border-[#e2e8f0]">
                    <div class="text-[#00baff] font-extrabold mb-2 text-lg">Autenticação 2FA</div>
                    <p class="text-[#64748b] text-base leading-relaxed m-0">Activa a verificação em dois passos para proteger o acesso à sua conta mesmo que a palavra-passe seja comprometida.</p>
                </div>
                <div class="bg-[#f8fafc] rounded-2xl p-6 border border-[#e2e8f0]">
                    <div class="text-[#00baff] font-extrabold mb-2 text-lg">Logs de auditoria</div>
                    <p class="text-[#64748b] text-base leading-relaxed m-0">Cada acção crítica (login, transferência, alteração de dados) fica registada com timestamp e endereço IP para rastreabilidade.</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 mb-6 border border-[#e6f3fa] transition hover:shadow-2xl">
            <h2 class="text-2xl md:text-3xl font-extrabold text-[#0f172a] mb-4">Centro de disputas</h2>
            <p class="text-[#475569] text-lg leading-relaxed">Se surgir um conflito entre cliente e freelancer, a nossa equipa de mediação analisa as provas apresentadas por ambas as partes e arbitra uma resolução justa. O processo é documentado e registado para transparência total.</p>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 border border-[#e6f3fa] transition hover:shadow-2xl">
            <h2 class="text-2xl md:text-3xl font-extrabold text-[#0f172a] mb-4">Denuncie actividade suspeita</h2>
            <p class="text-[#475569] text-lg leading-relaxed">Identificou comportamento suspeito, phishing ou tentativa de fraude? Contacte-nos imediatamente através do e-mail <strong>seguranca@remoto.ao</strong> ou use o botão "Denunciar" disponível em qualquer perfil ou mensagem.</p>
        </div>

    </div>
</div>
@endsection
