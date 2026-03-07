@extends('layouts.main')

@section('content')
<div class="light-page min-h-screen pt-8 pb-12">
    <div class="container mx-auto px-4 py-8 flex flex-col items-center">
        <h2 class="text-xl font-bold text-cyan-600 mb-2">O que você precisa?</h2>
        <p class="text-sm text-gray-600 mb-4">Descreva seu pedido para que possamos encontrar o freelancer ideal.</p>
        <div class="w-full max-w-md bg-white rounded-lg shadow p-6">
            <form wire:submit.prevent="submitBriefing">
            <div class="mb-6">
                <label class="block font-bold text-lg mb-2 text-cyan-700">Título do pedido <span class="text-red-500">*</span></label>
                <input type="text" wire:model.defer="title1" maxlength="100" required autocomplete="off" class="w-full border-2 border-cyan-500 rounded-lg px-4 py-3 text-lg font-semibold focus:ring-2 focus:ring-cyan-500 focus:outline-none bg-cyan-50" placeholder="Título único do pedido">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-2">Tipo de serviço</label>
                <select wire:model="business_type1" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    <option value="">Selecione o tipo de serviço</option>
                    <option value="Desenvolvimento de sites e sistemas web">Desenvolvimento de sites e sistemas web</option>
                    <option value="Criação de lojas virtuais (e-commerce)">Criação de lojas virtuais (e-commerce)</option>
                    <option value="Desenvolvimento de aplicativos mobile">Desenvolvimento de aplicativos mobile</option>
                    <option value="Design gráfico (logos, banners, identidade visual)">Design gráfico (logos, banners, identidade visual)</option>
                    <option value="Edição de imagens e vídeos">Edição de imagens e vídeos</option>
                    <option value="Redação de textos, artigos e blogs">Redação de textos, artigos e blogs</option>
                    <option value="Tradução e revisão de textos">Tradução e revisão de textos</option>
                    <option value="Marketing digital (SEO, Google Ads, Facebook Ads)">Marketing digital (SEO, Google Ads, Facebook Ads)</option>
                    <option value="Gestão de redes sociais">Gestão de redes sociais</option>
                    <option value="Produção de conteúdo para redes sociais">Produção de conteúdo para redes sociais</option>
                    <option value="Criação de apresentações e materiais institucionais">Criação de apresentações e materiais institucionais</option>
                    <option value="Ilustração e animação">Ilustração e animação</option>
                    <option value="Modelagem 3D e renderização">Modelagem 3D e renderização</option>
                    <option value="Suporte administrativo e atendimento ao cliente">Suporte administrativo e atendimento ao cliente</option>
                    <option value="Consultoria em TI, negócios, finanças e RH">Consultoria em TI, negócios, finanças e RH</option>
                    <option value="Suporte técnico remoto">Suporte técnico remoto</option>
                    <option value="Data entry (digitação e organização de dados)">Data entry (digitação e organização de dados)</option>
                    <option value="Automação de processos (scripts, bots, RPA)">Automação de processos (scripts, bots, RPA)</option>
                    <option value="Voice-over e locução profissional">Voice-over e locução profissional</option>
                    <option value="Criação de cursos e materiais educacionais">Criação de cursos e materiais educacionais</option>
                    <option value="Desenvolvimento de plugins e integrações">Desenvolvimento de plugins e integrações</option>
                    <option value="Testes de software e QA">Testes de software e QA</option>
                    <option value="Pesquisa de mercado e análise de dados">Pesquisa de mercado e análise de dados</option>
                    <option value="Serviços jurídicos e contábeis online">Serviços jurídicos e contábeis online</option>
                    <option value="Criação de campanhas de e-mail marketing">Criação de campanhas de e-mail marketing</option>
                    <option value="Outro">Outro</option>
                </select>
                @if($business_type1 === 'Outro')
                    <input type="text" wire:model.defer="business_type1_outro" class="w-full border border-cyan-500 rounded px-3 py-2 mt-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none" placeholder="Descreva o tipo de serviço">
                @endif
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-2">Qual é a sua necessidade?</label>
                <input type="text" wire:model.defer="necessity1" autocomplete="off" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none" placeholder="Necessidade específica">
            </div>
            {{-- Botão 'Gerar descrição inteligente' removido --}}
            @if($generated_description)
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Descrição sugerida</label>
                    <textarea wire:model.defer="generated_description" rows="5" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none"></textarea>
                </div>
            @endif
            <div class="flex justify-end">
                <button type="submit" class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-2 px-4 rounded">Finalizar</button>
            </div>
        </form>
        </div>
    </div>
</div>
@endsection
