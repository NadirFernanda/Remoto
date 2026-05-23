<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-sky-50/40 pb-16">

    {{-- ── Header ── --}}
    <div class="bg-gradient-to-r from-[#00c8ff] to-[#0033cc] rounded-2xl p-6 text-white mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold leading-tight">Novo Pedido</h1>
                <p class="text-sm text-white/80 mt-0.5">Descreva o seu projecto e encontre o freelancer ideal</p>
            </div>
        </div>
    </div>

    {{-- ── Progress bar ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm px-6 py-5 mb-8">
        <div class="flex items-center gap-2">
            @foreach([1 => 'Tipo de Serviço', 2 => 'Detalhes', 3 => 'Revisão'] as $n => $label)
                <div class="flex items-center {{ $loop->last ? '' : 'flex-1' }}">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold shadow-sm transition-all
                            {{ $step > $n  ? 'bg-gradient-to-br from-emerald-400 to-emerald-500 text-white' :
                               ($step === $n ? 'bg-gradient-to-br from-[#00c8ff] to-[#0055ff] text-white shadow-sky-200/60' :
                                              'bg-slate-100 text-slate-400') }}">
                            @if($step > $n)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            @else
                                {{ $n }}
                            @endif
                        </div>
                        <span class="text-sm font-semibold {{ $step >= $n ? 'text-slate-800' : 'text-slate-400' }} hidden sm:inline">{{ $label }}</span>
                    </div>
                    @if(!$loop->last)
                        <div class="flex-1 h-1 mx-3 rounded-full {{ $step > $n ? 'bg-gradient-to-r from-[#00c8ff] to-[#0055ff]' : 'bg-slate-100' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── Alerts ── --}}
    @if(session()->has('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ═══════════════════════════════════════════
         STEP 1: Tipo de serviço
    ════════════════════════════════════════════ --}}
    @if($step === 1)
    <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <h2 class="text-lg font-bold text-slate-800 mb-1">Que tipo de serviço precisa?</h2>
            <p class="text-sm text-slate-500 mb-6">Escolha a categoria mais próxima do seu projecto</p>

            @error('business_type1')
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 rounded-xl text-sm">{{ $message }}</div>
            @enderror

            @php
                $icons = [
                    'Desenvolvimento de sites e sistemas web' => ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/><path d="M7 8l3 3-3 3"/><path d="M13 14h4"/></svg>', 'short' => 'Dev. Web'],
                    'Criação de lojas virtuais (e-commerce)'  => ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>', 'short' => 'E-commerce'],
                    'Desenvolvimento de aplicações mobile'   => ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>', 'short' => 'App Mobile'],
                    'Design gráfico (logos, banners, identidade visual)' => ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/><line x1="12" y1="2" x2="12" y2="9"/><line x1="12" y1="15" x2="12" y2="22"/><line x1="2" y1="12" x2="9" y2="12"/><line x1="15" y1="12" x2="22" y2="12"/></svg>', 'short' => 'Design Gráfico'],
                    'Redação de textos, artigos e blogs'      => ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>', 'short' => 'Redação'],
                    'Marketing digital (SEO, Google Ads, Facebook Ads)'  => ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>', 'short' => 'Marketing Digital'],
                    'Gestão de redes sociais'                 => ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>', 'short' => 'Redes Sociais'],
                    'Edição de imagens e vídeos'              => ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="2.18"/><path d="M7 2v20M17 2v20M2 12h20M2 7h5M2 17h5M17 17h5M17 7h5"/></svg>', 'short' => 'Vídeo & Imagem'],
                    'Consultoria em TI, negócios, finanças e RH' => ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>', 'short' => 'Consultoria'],
                    'Outro' => ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>', 'short' => 'Outro'],
                ];
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-6">
                @foreach($allCategories as $cat)
                    @php
                        $meta     = $icons[$cat] ?? ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>', 'short' => $cat];
                        $selected = $business_type1 === $cat;
                    @endphp
                    <button type="button" wire:click="$set('business_type1', '{{ $cat }}')"
                        class="flex flex-col items-center gap-3 p-5 rounded-2xl border-2 transition-all text-center cursor-pointer
                            {{ $selected
                                ? 'border-sky-400 bg-gradient-to-br from-sky-50 to-blue-50 shadow-md shadow-sky-100'
                                : 'border-slate-200 bg-white hover:border-sky-300 hover:bg-sky-50/60' }}">
                        <div class="w-10 h-10 {{ $selected ? 'text-sky-600' : 'text-slate-600' }} transition-colors">
                            {!! $meta['icon'] !!}
                        </div>
                        <span class="text-xs font-semibold leading-tight {{ $selected ? 'text-sky-700' : 'text-slate-700' }}">{{ $meta['short'] }}</span>
                    </button>
                @endforeach
            </div>

            @if($business_type1 === 'Outro')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Descreva o tipo de serviço</label>
                    <input type="text" wire:model.defer="business_type1_outro"
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm bg-white text-slate-800 focus:ring-2 focus:ring-sky-200 focus:border-sky-400 outline-none transition"
                        placeholder="Ex: Tradução de contratos jurídicos">
                </div>
            @endif
        </div>

        @if($business_type1 && $business_type1 !== 'Outro' && !empty($currentTemplate))
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4">
                <p class="text-xs font-bold text-amber-700 uppercase tracking-wide mb-2">💡 Dica para o seu briefing</p>
                <p class="text-sm text-amber-800">{{ $currentTemplate['tips'] ?? '' }}</p>
            </div>
        @endif

        <div class="flex justify-end">
            <button type="button" wire:click="goToStep2"
                class="inline-flex items-center gap-2 bg-gradient-to-r from-[#00c8ff] to-[#0055ff] hover:from-sky-400 hover:to-blue-600 text-white font-semibold px-8 py-3 rounded-xl transition-all shadow-md shadow-sky-200/40">
                Continuar
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════
         STEP 2: Detalhes
    ════════════════════════════════════════════ --}}
    @if($step === 2)
    <div class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Formulário --}}
            <div class="lg:col-span-2 space-y-5">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <h2 class="text-lg font-bold text-slate-800 mb-1">Descreva o seu projecto</h2>
                    <p class="text-sm text-slate-500 mb-5">Quanto mais detalhe, melhores serão as propostas que vai receber</p>

                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Título do pedido <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model.defer="title1" maxlength="100"
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm bg-white text-slate-800 focus:ring-2 focus:ring-sky-200 focus:border-sky-400 outline-none transition @error('title1') border-red-400 @enderror"
                            placeholder="Ex: Site institucional para empresa de consultoria">
                        @error('title1')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Descrição detalhada <span class="text-red-500">*</span>
                        </label>
                        @if(!empty($currentTemplate['example']))
                            <p class="text-xs text-slate-400 mb-2">Exemplo: <em>{{ $currentTemplate['example'] }}</em></p>
                        @endif
                        <textarea wire:model.defer="necessity1" rows="7" maxlength="2000"
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm bg-white text-slate-800 focus:ring-2 focus:ring-sky-200 focus:border-sky-400 outline-none resize-none transition @error('necessity1') border-red-400 @enderror"
                            placeholder="Descreva o que precisa, objectivos, funcionalidades desejadas..."></textarea>
                        @error('necessity1')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Dicas --}}
            @if(!empty($currentTemplate))
            <div class="lg:col-span-1 space-y-4">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-7 h-7 rounded-lg bg-sky-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-sky-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"/></svg>
                        </div>
                        <p class="text-xs font-bold text-sky-700 uppercase tracking-wide">Perguntas guia</p>
                    </div>
                    <ul class="space-y-3">
                        @foreach($currentTemplate['questions'] ?? [] as $q)
                            <li class="flex items-start gap-2.5 text-xs text-slate-600">
                                <span class="mt-0.5 flex-shrink-0 w-5 h-5 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center text-xs font-bold">{{ $loop->iteration }}</span>
                                {{ $q }}
                            </li>
                        @endforeach
                    </ul>
                </div>
                @if(!empty($currentTemplate['tips']))
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4">
                    <p class="text-xs font-bold text-amber-700 uppercase tracking-wide mb-1">💡 Dica</p>
                    <p class="text-xs text-amber-800 leading-relaxed">{{ $currentTemplate['tips'] }}</p>
                </div>
                @endif
            </div>
            @endif
        </div>

        <div class="flex justify-between">
            <button type="button" wire:click="prevStep"
                class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-800 font-medium px-6 py-3 rounded-xl border border-slate-200 bg-white hover:border-slate-300 transition-all text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Voltar
            </button>
            <button type="button" wire:click="goToStep3"
                class="inline-flex items-center gap-2 bg-gradient-to-r from-[#00c8ff] to-[#0055ff] hover:from-sky-400 hover:to-blue-600 text-white font-semibold px-8 py-3 rounded-xl transition-all shadow-md shadow-sky-200/40">
                Gerar descrição
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════
         STEP 3: Revisão
    ════════════════════════════════════════════ --}}
    @if($step === 3)
    <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-2xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Revisão do pedido</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Revise e edite a descrição antes de submeter</p>
                </div>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Título do pedido</label>
                <div class="bg-slate-50 rounded-xl px-4 py-3 text-sm text-slate-800 font-medium border border-slate-100">{{ $title1 }}</div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Descrição completa
                    <span class="text-slate-400 font-normal">(editável)</span>
                </label>
                <textarea wire:model="generated_description" rows="8"
                    class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 bg-slate-50 focus:ring-2 focus:ring-sky-200 focus:border-sky-400 outline-none resize-none transition"></textarea>
                @error('generated_description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="bg-sky-50 border border-sky-200 rounded-2xl p-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-sky-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm text-sky-800">
                <strong>O que acontece a seguir?</strong> Após submeter, poderá definir o orçamento e o pedido será publicado para que freelancers enviem propostas.
            </p>
        </div>

        <div class="flex justify-between">
            <button type="button" wire:click="prevStep"
                class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-800 font-medium px-6 py-3 rounded-xl border border-slate-200 bg-white hover:border-slate-300 transition-all text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Voltar
            </button>
            <button type="button" wire:click="submitBriefing" wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 bg-gradient-to-r from-[#00c8ff] to-[#0055ff] hover:from-sky-400 hover:to-blue-600 text-white font-semibold px-8 py-3 rounded-xl transition-all shadow-md shadow-sky-200/40 disabled:opacity-60">
                <span wire:loading.remove>
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Publicar pedido
                </span>
                <span wire:loading class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    A publicar...
                </span>
            </button>
        </div>
    </div>
    @endif

</div>
