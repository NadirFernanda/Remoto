@extends('layouts.main')

@section('content')
<div class="pub-page">
    <div class="pub-container--md" style="padding-top:2.5rem;padding-bottom:4rem;">

        <div class="pub-hero" style="margin-bottom:2.5rem;">
            <div class="pub-hero-label">Segurança</div>
            <h1 class="pub-hero-title">A sua segurança é a nossa prioridade</h1>
            <p class="pub-hero-sub">Medidas técnicas e processos humanos para proteger cada transação, dado pessoal e interação na plataforma.</p>
        </div>

        <div class="pub-card" style="margin-bottom:1.5rem;">
            <h2 style="font-size:1.2rem;font-weight:800;color:#0f172a;margin-bottom:.75rem;">Pagamentos protegidos por escrow</h2>
            <p style="color:#475569;line-height:1.7;">Quando um cliente aceita uma proposta, o valor do projeto é retido na plataforma. O freelancer só recebe após o cliente aprovar a entrega — ou após resolução de eventuais disputas. Nenhum pagamento é liberado antes da confirmação bilateral.</p>
        </div>

        <div class="pub-card" style="margin-bottom:1.5rem;">
            <h2 style="font-size:1.2rem;font-weight:800;color:#0f172a;margin-bottom:.75rem;">Verificação de identidade (KYC)</h2>
            <p style="color:#475569;line-height:1.7;">Todos os utilizadores que desejam sacar fundos ou atingir limites de transação mais elevados passam pelo nosso processo de verificação KYC (Know Your Customer). Isso garante que apenas pessoas reais e identificadas operam na plataforma.</p>
        </div>

        <div class="pub-card" style="margin-bottom:1.5rem;">
            <h2 style="font-size:1.2rem;font-weight:800;color:#0f172a;margin-bottom:1rem;">Camadas de proteção técnica</h2>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem;">
                <div style="background:#f8fafc;border-radius:12px;padding:1.25rem;border:1px solid #e2e8f0;">
                    <div style="color:#00baff;font-weight:800;margin-bottom:.35rem;">HTTPS / TLS</div>
                    <p style="color:#64748b;font-size:.9rem;margin:0;line-height:1.55;">Toda a comunicação entre o seu browser e os nossos servidores é cifrada com TLS 1.3.</p>
                </div>
                <div style="background:#f8fafc;border-radius:12px;padding:1.25rem;border:1px solid #e2e8f0;">
                    <div style="color:#00baff;font-weight:800;margin-bottom:.35rem;">Autenticação 2FA</div>
                    <p style="color:#64748b;font-size:.9rem;margin:0;line-height:1.55;">Activa a verificação em dois passos para proteger o acesso à sua conta mesmo que a palavra-passe seja comprometida.</p>
                </div>
                <div style="background:#f8fafc;border-radius:12px;padding:1.25rem;border:1px solid #e2e8f0;">
                    <div style="color:#00baff;font-weight:800;margin-bottom:.35rem;">Logs de auditoria</div>
                    <p style="color:#64748b;font-size:.9rem;margin:0;line-height:1.55;">Cada acção crítica (login, transferência, alteração de dados) fica registada com timestamp e endereço IP para rastreabilidade.</p>
                </div>
            </div>
        </div>

        <div class="pub-card" style="margin-bottom:1.5rem;">
            <h2 style="font-size:1.2rem;font-weight:800;color:#0f172a;margin-bottom:.75rem;">Centro de disputas</h2>
            <p style="color:#475569;line-height:1.7;">Se surgir um conflito entre cliente e freelancer, a nossa equipa de mediação analisa as provas apresentadas por ambas as partes e arbitra uma resolução justa. O processo é documentado e registado para transparência total.</p>
        </div>

        <div class="pub-card">
            <h2 style="font-size:1.2rem;font-weight:800;color:#0f172a;margin-bottom:.75rem;">Denuncie atividade suspeita</h2>
            <p style="color:#475569;line-height:1.7;">Identificou comportamento suspeito, phishing ou tentativa de fraude? Contacte-nos imediatamente através do e-mail <strong>seguranca@remoto.ao</strong> ou use o botão "Denunciar" disponível em qualquer perfil ou mensagem.</p>
        </div>

    </div>
</div>
@endsection
