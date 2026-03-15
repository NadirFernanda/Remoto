@extends('layouts.main')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-16 text-gray-700">
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Política de Cookies</h1>
    <p class="text-sm text-gray-500 mb-10">Última actualização: março de 2026</p>

    <section class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-3">1. O que são cookies?</h2>
        <p>Cookies são pequenos ficheiros de texto que os websites guardam no seu dispositivo (computador, telemóvel ou tablet) quando os visita. Permitem que o site se "lembre" de si e das suas preferências entre visitas.</p>
    </section>

    <section class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-3">2. Que cookies utilizamos?</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm border border-gray-200 rounded-xl overflow-hidden">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Tipo</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Nome</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Finalidade</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Duração</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr>
                        <td class="px-4 py-3 font-medium text-[#0f172a]">Essencial</td>
                        <td class="px-4 py-3 text-gray-500 font-mono text-xs">laravel_session</td>
                        <td class="px-4 py-3">Mantém a sessão do utilizador autenticado</td>
                        <td class="px-4 py-3">Sessão</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="px-4 py-3 font-medium text-[#0f172a]">Essencial</td>
                        <td class="px-4 py-3 text-gray-500 font-mono text-xs">XSRF-TOKEN</td>
                        <td class="px-4 py-3">Protecção contra ataques CSRF em formulários</td>
                        <td class="px-4 py-3">Sessão</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-medium text-[#0f172a]">Essencial</td>
                        <td class="px-4 py-3 text-gray-500 font-mono text-xs">remember_web_*</td>
                        <td class="px-4 py-3">Mantém o utilizador autenticado ao usar "Lembrar-me"</td>
                        <td class="px-4 py-3">30 dias</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="text-sm text-gray-500 mt-3">Não utilizamos cookies de seguimento ou publicidade de terceiros.</p>
    </section>

    <section class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-3">3. Cookies essenciais</h2>
        <p>Os cookies listados acima são <strong>estritamente necessários</strong> para o funcionamento seguro da plataforma (autenticação, formulários, segurança). Não é possível desactivá-los sem comprometer a funcionalidade do site.</p>
    </section>

    <section class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-3">4. Como gerir ou eliminar cookies?</h2>
        <p class="mb-3">Pode gerir ou apagar os cookies nas definições do seu browser:</p>
        <ul class="list-disc pl-5 space-y-1">
            <li><strong>Chrome:</strong> Definições → Privacidade e segurança → Cookies</li>
            <li><strong>Firefox:</strong> Definições → Privacidade e segurança → Cookies e dados de sites</li>
            <li><strong>Safari:</strong> Preferências → Privacidade → Gerir dados dos sites</li>
            <li><strong>Edge:</strong> Definições → Privacidade, pesquisa e serviços → Cookies</li>
        </ul>
        <p class="mt-3 text-sm text-gray-500">Atenção: bloquear todos os cookies pode impedir o acesso a partes da plataforma que requerem autenticação.</p>
    </section>

    <section class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-3">5. Contacto</h2>
        <p>Para questões sobre esta política, contacte-nos em <a href="mailto:contacto@24horas.ao" class="text-[#00baff] hover:underline">contacto@24horas.ao</a>.</p>
    </section>
</div>
@endsection
