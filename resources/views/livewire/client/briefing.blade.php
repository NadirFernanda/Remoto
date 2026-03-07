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
                    'Desenvolvimento de sites e sistemas web' => ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/><path d="M7 8l3 3-3 3"/><path d="M13 14h4"/></svg>', 'short' => 'Dev. Web'],
                    'Criação de lojas virtuais (e-commerce)'  => ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>', 'short' => 'E-commerce'],
                    'Desenvolvimento de aplicativos mobile'   => ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>', 'short' => 'App Mobile'],
                    'Design gráfico (logos, banners, identidade visual)' => ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/><line x1="12" y1="2" x2="12" y2="9"/><line x1="12" y1="15" x2="12" y2="22"/><line x1="2" y1="12" x2="9" y2="12"/><line x1="15" y1="12" x2="22" y2="12"/></svg>', 'short' => 'Design Gráfico'],
                    'Redação de textos, artigos e blogs'      => ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>', 'short' => 'Redação'],
                    'Marketing digital (SEO, Google Ads, Facebook Ads)'  => ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>', 'short' => 'Marketing Digital'],
                    'Gestão de redes sociais'                 => ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>', 'short' => 'Redes Sociais'],
                    'Edição de imagens e vídeos'              => ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="2.18"/><path d="M7 2v20M17 2v20M2 12h20M2 7h5M2 17h5M17 17h5M17 7h5"/></svg>', 'short' => 'Vídeo & Imagem'],
                    'Consultoria em TI, negócios, finanças e RH' => ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>', 'short' => 'Consultoria'],
                    'Outro' => ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>', 'short' => 'Outro'],
                ];
            @endphp
            @foreach($allCategories as $cat)
                @php
                    $meta = $icons[$cat] ?? ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>', 'short' => $cat];
                    $selected = $business_type1 === $cat;
                @endphp
                <button type="button"
                        wire:click="$set('business_type1', '{{ $cat }}')"
                        class="flex flex-col items-center gap-3 p-5 rounded-2xl border-2 transition-all text-center cursor-pointer
                               {{ $selected ? 'border-[#00baff] bg-[#e8f9ff] shadow-md' : 'border-gray-100 bg-white hover:border-[#00baff] hover:bg-[#f0fbff]' }}">
                    <div class="w-10 h-10 text-[#00baff]">
                        {!! $meta['icon'] !!}
                    </div>
                    <span class="text-xs font-semibold leading-tight {{ $selected ? 'text-[#00baff]' : 'text-gray-600' }}">{{ $meta['short'] }}</span>
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
