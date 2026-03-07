<div class="min-h-screen bg-gray-50 py-10">
<div class="max-w-4xl mx-auto px-4">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('client.dashboard') }}"
           class="flex items-center justify-center w-10 h-10 rounded-full border border-gray-200 bg-white shadow-sm text-gray-400 hover:text-gray-700 hover:border-gray-400 transition-all">
            @include('components.icon', ['name' => 'arrow-left', 'class' => 'w-5 h-5'])
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $edit ? 'Editar pedido' : 'Novo pedido' }}</h1>
            <p class="text-sm text-gray-400 mt-0.5">Descreva seu projecto e encontre o freelancer ideal</p>
        </div>
    </div>

    {{-- Progress bar --}}
    <div class="flex items-center gap-2 mb-8">
        @foreach([1 => 'Tipo', 2 => 'Detalhes', 3 => 'Revisão'] as $n => $label)
            <div class="flex items-center {{ $loop->last ? '' : 'flex-1' }}">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-all
                        {{ $step >= $n ? 'bg-[#00baff] text-white' : 'bg-gray-200 text-gray-500' }}">
                        @if($step > $n)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        @else
                            {{ $n }}
                        @endif
                    </div>
                    <span class="text-sm font-medium {{ $step >= $n ? 'text-gray-800' : 'text-gray-400' }}">{{ $label }}</span>
                </div>
                @if(!$loop->last)
                    <div class="flex-1 h-0.5 mx-3 {{ $step > $n ? 'bg-[#00baff]' : 'bg-gray-200' }}"></div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Alerts --}}
    @if (session()->has('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- ─── STEP 1: Service type picker ─────────────────────────────── --}}
    @if($step === 1)
    <div>
        <h2 class="text-lg font-semibold text-gray-800 mb-1">Que tipo de serviço você precisa?</h2>
        <p class="text-sm text-gray-400 mb-6">Escolha a categoria mais próxima do seu projecto</p>

        @error('business_type1')
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 rounded-xl text-sm">{{ $message }}</div>
        @enderror

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-6">
            @php
                $icons = [
                    'Desenvolvimento de sites e sistemas web' => ['icon' => '💻', 'short' => 'Desenvolvimento Web'],
                    'Criação de lojas virtuais (e-commerce)'  => ['icon' => '🛒', 'short' => 'E-commerce'],
                    'Desenvolvimento de aplicativos mobile'   => ['icon' => '📱', 'short' => 'App Mobile'],
                    'Design gráfico (logos, banners, identidade visual)' => ['icon' => '🎨', 'short' => 'Design Gráfico'],
                    'Redação de textos, artigos e blogs'      => ['icon' => '✏️', 'short' => 'Redação'],
                    'Marketing digital (SEO, Google Ads, Facebook Ads)'  => ['icon' => '📊', 'short' => 'Marketing Digital'],
                    'Gestão de redes sociais'                 => ['icon' => '📣', 'short' => 'Redes Sociais'],
                    'Edição de imagens e vídeos'              => ['icon' => '🎬', 'short' => 'Edição de Vídeo'],
                    'Consultoria em TI, negócios, finanças e RH' => ['icon' => '🧠', 'short' => 'Consultoria'],
                    'Outro' => ['icon' => '➕', 'short' => 'Outro'],
                ];
            @endphp
            @foreach($allCategories as $cat)
                @php
                    $meta = $icons[$cat] ?? ['icon' => '📋', 'short' => $cat];
                    $selected = $business_type1 === $cat;
                @endphp
                <button type="button"
                        wire:click="$set('business_type1', '{{ $cat }}')"
                        class="flex flex-col items-center gap-2 p-4 rounded-2xl border-2 transition-all text-center cursor-pointer
                               {{ $selected ? 'border-[#00baff] bg-cyan-50 shadow-md' : 'border-gray-200 bg-white hover:border-cyan-300 hover:bg-cyan-50' }}">
                    <span class="text-3xl">{{ $meta['icon'] }}</span>
                    <span class="text-xs font-semibold leading-tight text-gray-700">{{ $meta['short'] }}</span>
                </button>
            @endforeach
        </div>

        {{-- Show "Outro" text input --}}
        @if($business_type1 === 'Outro')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Descreva o tipo de serviço</label>
                <input type="text" wire:model.defer="business_type1_outro"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-cyan-400 focus:border-transparent outline-none"
                       placeholder="Ex: Tradução de contratos jurídicos">
            </div>
        @endif

        @if($business_type1 && $business_type1 !== 'Outro' && !empty($currentTemplate))
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-6">
                <p class="text-xs font-semibold text-amber-700 uppercase tracking-wide mb-2">💡 Dica para o seu briefing</p>
                <p class="text-sm text-amber-800">{{ $currentTemplate['tips'] ?? '' }}</p>
            </div>
        @endif

        <div class="flex justify-end mt-4">
            <button type="button" wire:click="goToStep2"
                    class="bg-[#00baff] hover:bg-cyan-500 text-white font-semibold px-8 py-3 rounded-xl transition-all shadow-sm">
                Continuar →
            </button>
        </div>
    </div>
    @endif

    {{-- ─── STEP 2: Details form with template hints ─────────────────── --}}
    @if($step === 2)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: form --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Descreva o seu projecto</h2>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Título do pedido <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model.defer="title1" maxlength="100"
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-cyan-400 focus:border-transparent outline-none @error('title1') border-red-400 @enderror"
                           placeholder="Ex: Site institucional para empresa de consultoria">
                    @error('title1')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Descrição detalhada <span class="text-red-500">*</span>
                    </label>
                    @if(!empty($currentTemplate['example']))
                        <p class="text-xs text-gray-400 mb-2">Exemplo: <em>{{ $currentTemplate['example'] }}</em></p>
                    @endif
                    <textarea wire:model.defer="necessity1" rows="6" maxlength="2000"
                              class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-cyan-400 focus:border-transparent outline-none resize-none @error('necessity1') border-red-400 @enderror"
                              placeholder="Descreva o que precisa, objectivos, funcionalidades desejadas..."></textarea>
                    @error('necessity1')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Público-alvo</label>
                        <input type="text" wire:model.defer="target_audience1"
                               class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-cyan-400 focus:border-transparent outline-none"
                               placeholder="Ex: Empresas B2B, jovens adultos...">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Estilo preferido</label>
                        <input type="text" wire:model.defer="style1"
                               class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-cyan-400 focus:border-transparent outline-none"
                               placeholder="Ex: Moderno, minimalista, corporativo...">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Prazo desejado</label>
                        <input type="text" wire:model.defer="deadline"
                               class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-cyan-400 focus:border-transparent outline-none"
                               placeholder="Ex: 2 semanas, até 30/04...">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Orçamento estimado</label>
                        <input type="text" wire:model.defer="budget_range"
                               class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-cyan-400 focus:border-transparent outline-none"
                               placeholder="Ex: 50.000 – 100.000 Kz">
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: template hints --}}
        @if(!empty($currentTemplate))
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <p class="text-xs font-bold text-[#00baff] uppercase tracking-wide mb-3">📋 Perguntas guia</p>
                <ul class="space-y-2.5">
                    @foreach($currentTemplate['questions'] ?? [] as $q)
                        <li class="flex items-start gap-2 text-xs text-gray-600">
                            <span class="mt-0.5 flex-shrink-0 w-4 h-4 rounded-full bg-cyan-100 text-[#00baff] flex items-center justify-center text-xs font-bold">{{ $loop->iteration }}</span>
                            {{ $q }}
                        </li>
                    @endforeach
                </ul>
            </div>
            @if(!empty($currentTemplate['tips']))
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4">
                <p class="text-xs font-bold text-amber-700 uppercase tracking-wide mb-1">💡 Dica</p>
                <p class="text-xs text-amber-800">{{ $currentTemplate['tips'] }}</p>
            </div>
            @endif
        </div>
        @endif
    </div>

    <div class="flex justify-between mt-6">
        <button type="button" wire:click="prevStep"
                class="flex items-center gap-2 text-gray-500 hover:text-gray-800 font-medium px-6 py-3 rounded-xl border border-gray-200 bg-white hover:border-gray-300 transition-all">
            ← Voltar
        </button>
        <button type="button" wire:click="goToStep3"
                class="bg-[#00baff] hover:bg-cyan-500 text-white font-semibold px-8 py-3 rounded-xl transition-all shadow-sm">
            Gerar descrição →
        </button>
    </div>
    @endif

    {{-- ─── STEP 3: Preview + submit ─────────────────────────────────── --}}
    @if($step === 3)
    <div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Descrição gerada</h2>
                    <p class="text-xs text-gray-400">Revise e edite se necessário antes de submeter</p>
                </div>
            </div>

            <div class="mb-3">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Título do pedido</label>
                <div class="bg-gray-50 rounded-xl px-4 py-3 text-sm text-gray-800 font-medium border border-gray-100">{{ $title1 }}</div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Descrição completa <span class="text-gray-400 font-normal">(editável)</span></label>
                <textarea wire:model="generated_description" rows="8"
                          class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 focus:ring-2 focus:ring-cyan-400 focus:border-transparent outline-none resize-none bg-gray-50"></textarea>
                @error('generated_description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="bg-cyan-50 border border-cyan-200 rounded-2xl p-4 mb-6">
            <p class="text-sm text-cyan-800">
                <strong>O que acontece a seguir?</strong> Após submeter, você definirá o orçamento e o pedido será publicado para que freelancers enviem propostas.
            </p>
        </div>

        <div class="flex justify-between">
            <button type="button" wire:click="prevStep"
                    class="flex items-center gap-2 text-gray-500 hover:text-gray-800 font-medium px-6 py-3 rounded-xl border border-gray-200 bg-white hover:border-gray-300 transition-all">
                ← Voltar
            </button>
            <button type="button" wire:click="submitBriefing"
                    wire:loading.attr="disabled"
                    class="bg-[#00baff] hover:bg-cyan-500 text-white font-semibold px-8 py-3 rounded-xl transition-all shadow-sm disabled:opacity-60">
                <span wire:loading.remove>✓ Publicar pedido</span>
                <span wire:loading>A publicar...</span>
            </button>
        </div>
    </div>
    @endif

</div>
</div>
            @if (session()->has('success_message'))
                <div class="mb-4 p-2 bg-green-100 text-green-700 rounded text-center text-sm">
                    {{ session('success_message') }}
                </div>
            @endif
            <div x-data="{ show: false, message: '' }"
                 x-on:show-success.window="show = true; message = $event.detail; setTimeout(() => show = false, 4000)"
                 x-show="show"
                 x-transition
                 class="mb-4 p-2 bg-green-100 text-green-700 rounded text-center text-sm">
                <span x-text="message"></span>
            </div>
            <form wire:submit.prevent="submitBriefing">
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Título do pedido <span class="text-red-500">*</span></label>
                    <input type="text" wire:model.defer="title1" maxlength="100" autocomplete="off" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none @error('title1') border-red-500 @enderror" placeholder="Título único do pedido">
                    @error('title1')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="mb-4" x-data="{ isOutro: {{ $business_type1 === 'Outro' ? 'true' : 'false' }} }">
                    <label class="block font-semibold mb-2">Tipo de serviço</label>
                    <select wire:model="business_type1" @change="isOutro = $event.target.value === 'Outro'" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
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
                    <div x-show="isOutro" x-transition class="mt-3">
                        <label class="block font-semibold mb-2">Descreva o tipo de serviço</label>
                        <input type="text" wire:model="business_type1_outro" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none @error('business_type1_outro') border-red-500 @enderror" placeholder="Descreva o tipo de serviço">
                        @error('business_type1_outro')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block font-semibold mb-2">Descrição do serviço <span class="text-red-500">*</span></label>
                    <input type="text" wire:model.defer="necessity1" autocomplete="off" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none @error('necessity1') border-red-500 @enderror" placeholder="Necessidade específica">
                    @error('necessity1')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
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
