<div class="light-page min-h-screen pb-16">
<div class="max-w-6xl mx-auto px-4">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-[#00baff]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Minha Loja
            </h1>
            <p class="text-sm text-gray-500 mt-1">Gerencie e venda os seus infoprodutos digitais</p>
        </div>
        <button wire:click="openCreate"
            class="inline-flex items-center gap-2 px-4 py-2 bg-[#00baff] text-white rounded-xl font-medium text-sm hover:bg-[#009ad6] transition shadow">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Novo Infoproduto
        </button>
    </div>

    {{-- Feedback --}}
    @if($feedback)
        <div class="mb-6 px-4 py-3 rounded-xl text-sm font-medium
            {{ $feedbackType === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
            {{ $feedback }}
        </div>
    @endif

    {{-- Wallet summary --}}
    <div class="bg-white rounded-2xl border shadow-sm p-5 mb-8 flex flex-wrap gap-6 items-center">
        <div>
            <div class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Saldo disponível</div>
            <div class="text-2xl font-bold text-green-600">Kz {{ number_format($wallet->saldo ?? 0, 2, ',', '.') }}</div>
        </div>
        <div class="text-gray-200 hidden sm:block">|</div>
        <div>
            <div class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Custo do patrocínio</div>
            <div class="text-lg font-semibold text-gray-700">600 Kz <span class="text-xs text-gray-400 font-normal">/ dia</span></div>
        </div>
        <div class="text-gray-200 hidden sm:block">|</div>
        <div>
            <div class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Comissão da plataforma</div>
            <div class="text-lg font-semibold text-gray-700">20% <span class="text-xs text-gray-400 font-normal">por venda</span></div>
        </div>
        <div class="text-gray-200 hidden sm:block">|</div>
        <div>
            <div class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Preço mínimo</div>
            <div class="text-lg font-semibold text-gray-700">5.000 Kz</div>
        </div>
        <a href="{{ route('loja.index') }}" target="_blank"
            class="ml-auto flex items-center gap-1.5 text-sm text-[#00baff] hover:underline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            Ver Loja pública
        </a>
    </div>

    {{-- Product form --}}
    @if($showForm)
    <div class="bg-white rounded-2xl border shadow-sm p-6 mb-8">
        <h2 class="text-lg font-bold text-gray-800 mb-5">
            {{ $editingId ? 'Editar Infoproduto' : 'Novo Infoproduto' }}
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            {{-- Título --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Título <span class="text-red-500">*</span></label>
                <input type="text" wire:model="titulo" maxlength="200"
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#00baff]/40 focus:border-[#00baff]"
                    placeholder="Ex: Guia Completo de Marketing Digital">
                @error('titulo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Tipo --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo <span class="text-red-500">*</span></label>
                <select wire:model="tipo" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#00baff]/40 focus:border-[#00baff]">
                    <option value="ebook">E-book (PDF)</option>
                    <option value="audio">Áudio</option>
                    <option value="literatura_digital">Literatura Digital</option>
                    <option value="outro">Outro</option>
                </select>
                @error('tipo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Preço --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Preço (Kz) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-sm text-gray-400">Kz</span>
                    <input type="number" wire:model="preco" min="5000" step="100"
                        class="w-full border rounded-lg pl-9 pr-3 py-2 text-sm focus:ring-2 focus:ring-[#00baff]/40 focus:border-[#00baff]"
                        placeholder="5000">
                </div>
                <p class="text-xs text-gray-400 mt-1">Mínimo: 5.000 Kz</p>
                @error('preco') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Capa --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Imagem de Capa {{ !$editingId ? '<span class="text-red-500">*</span>' : '(opcional para atualizar)' }}
                </label>
                <input type="file" wire:model="capa" accept="image/*"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-[#00baff]/10 file:text-[#00baff] hover:file:bg-[#00baff]/20">
                <p class="text-xs text-gray-400 mt-1">JPG, PNG ou WEBP • máx. 4MB</p>
                @error('capa') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Ficheiro --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Ficheiro do Produto {{ !$editingId ? '<span class="text-red-500">*</span>' : '(opcional para atualizar)' }}
                </label>
                <input type="file" wire:model="arquivo"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-[#00baff]/10 file:text-[#00baff] hover:file:bg-[#00baff]/20">
                <p class="text-xs text-gray-400 mt-1">PDF, MP3, MP4, ZIP, etc. • máx. 100MB</p>
                @error('arquivo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Descrição --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Descrição <span class="text-red-500">*</span></label>
                <textarea wire:model="descricao" rows="5" maxlength="5000"
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#00baff]/40 focus:border-[#00baff]"
                    placeholder="Descreva o conteúdo, o que o comprador irá aprender ou obter, público alvo..."></textarea>
                @error('descricao') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Loading indicator --}}
        <div wire:loading wire:target="capa,arquivo" class="mt-3 text-sm text-[#00baff]">
            <svg class="inline w-4 h-4 animate-spin mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            Enviando ficheiro...
        </div>

        <div class="flex items-center gap-3 mt-6">
            <button wire:click="saveProduto" wire:loading.attr="disabled"
                class="px-5 py-2.5 bg-[#00baff] text-white rounded-xl text-sm font-semibold hover:bg-[#009ad6] transition disabled:opacity-50">
                <span wire:loading.remove wire:target="saveProduto">{{ $editingId ? 'Atualizar' : 'Criar Produto' }}</span>
                <span wire:loading wire:target="saveProduto">A processar...</span>
            </button>
            <button wire:click="cancelForm" class="px-4 py-2.5 text-sm text-gray-600 hover:text-gray-900 transition">
                Cancelar
            </button>
        </div>
    </div>
    @endif

    {{-- Products list --}}
    @if($produtos->isEmpty() && !$showForm)
    <div class="bg-white rounded-2xl border shadow-sm p-12 text-center">
        <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
        </svg>
        <p class="text-gray-500 font-medium">Ainda não tem infoprodutos na loja</p>
        <p class="text-sm text-gray-400 mt-1">Clique em "Novo Infoproduto" para começar a vender</p>
    </div>
    @else
    <div class="grid grid-cols-1 gap-5">
        @foreach($produtos as $produto)
        <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
            <div class="flex flex-col sm:flex-row">
                {{-- Cover --}}
                @if($produto->capa_path)
                <div class="sm:w-36 h-32 sm:h-auto flex-shrink-0">
                    <img src="{{ Storage::disk('public')->url($produto->capa_path) }}"
                        alt="{{ $produto->titulo }}"
                        class="w-full h-full object-cover">
                </div>
                @else
                <div class="sm:w-36 h-32 sm:h-auto flex-shrink-0 bg-gradient-to-br from-[#00baff]/10 to-[#00baff]/30 flex items-center justify-center">
                    <svg class="w-12 h-12 text-[#00baff]/40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                @endif

                {{-- Content --}}
                <div class="flex-1 p-5">
                    <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                        <div>
                            <h3 class="font-semibold text-gray-900 text-base">{{ $produto->titulo }}</h3>
                            <div class="flex flex-wrap gap-2 mt-1.5">
                                {{-- Type badge --}}
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-[#00baff]/10 text-[#00baff]">
                                    {{ $produto->tipoLabel() }}
                                </span>
                                {{-- Status badge --}}
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    @if($produto->status === 'ativo') bg-green-100 text-green-700
                                    @elseif($produto->status === 'em_moderacao') bg-yellow-100 text-yellow-700
                                    @elseif($produto->status === 'inativo') bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-700 @endif">
                                    {{ $produto->statusLabel() }}
                                </span>
                                {{-- Sponsored badge --}}
                                @if($produto->isPatrocinado())
                                    @php $pat = $produto->patrocinioAtivo(); @endphp
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        Patrocinado até {{ $pat->data_fim->format('d/m/Y') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xl font-bold text-gray-900">Kz {{ number_format($produto->preco, 0, ',', '.') }}</div>
                            <div class="text-xs text-gray-400">{{ $produto->compras_count }} venda(s)</div>
                        </div>
                    </div>

                    <p class="text-sm text-gray-500 line-clamp-2 mb-4">{{ $produto->descricao }}</p>

                    {{-- Actions --}}
                    <div class="flex flex-wrap gap-2">
                        {{-- Sponsor button (only if active) --}}
                        @if($produto->status === 'ativo')
                        <button wire:click="openSponsor({{ $produto->id }})"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg
                                {{ $produto->isPatrocinado() ? 'bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100' : 'bg-[#00baff]/10 text-[#00baff] hover:bg-[#00baff]/20' }} transition">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            {{ $produto->isPatrocinado() ? 'Renovar Patrocínio' : 'Patrocinar' }}
                        </button>
                        @endif

                        {{-- Share link --}}
                        @if($produto->status === 'ativo')
                        <button x-data
                            x-on:click='navigator.clipboard.writeText("{{ route("loja.show", $produto->slug) }}"); $el.textContent = "✓ Link copiado!"; setTimeout(()=>$el.textContent="Copiar Link", 2000)'
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                            Copiar Link
                        </button>

                        <a href="{{ route('loja.show', $produto->slug) }}" target="_blank"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            Ver Produto
                        </a>
                        @endif

                        <button wire:click="openEdit({{ $produto->id }})"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Editar
                        </button>

                        <button wire:click="deleteProduto({{ $produto->id }})"
                            wire:confirm="Tem certeza que deseja excluir este produto?"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Excluir
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>

{{-- ═══ SPONSOR MODAL ═══════════════════════════════════════════════════════ --}}
@if($showSponsorModal)
<div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
    wire:click.self="cancelarSponsor">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            Patrocinar Infoproduto
        </h3>

        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-5 text-sm text-amber-800">
            <strong>Como funciona:</strong> O patrocínio faz o seu produto aparecer em destaque para todos os utilizadores da loja, de forma progressiva até à data de terme.
        </div>

        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-2">Número de dias</label>
            <div class="flex items-center gap-3">
                <button wire:click="$set('dias', {{ max(1, $dias - 1) }})"
                    class="w-9 h-9 flex items-center justify-center rounded-lg border hover:bg-gray-100 text-lg font-bold transition">−</button>
                <input type="number" wire:model.live="dias" min="1" max="365"
                    class="w-24 text-center border rounded-lg py-2 text-sm font-semibold focus:ring-2 focus:ring-amber-300 focus:border-amber-400">
                <button wire:click="$set('dias', {{ min(365, $dias + 1) }})"
                    class="w-9 h-9 flex items-center justify-center rounded-lg border hover:bg-gray-100 text-lg font-bold transition">+</button>
            </div>
            <p class="text-xs text-gray-400 mt-1">600 Kz × {{ $dias }} dia(s)</p>
        </div>

        {{-- Cost summary --}}
        <div class="bg-gray-50 rounded-xl p-4 mb-5">
            <div class="flex justify-between text-sm mb-2">
                <span class="text-gray-600">Custo por dia</span>
                <span class="font-medium">600 Kz</span>
            </div>
            <div class="flex justify-between text-sm mb-2">
                <span class="text-gray-600">Duração</span>
                <span class="font-medium">{{ $dias }} dia(s)</span>
            </div>
            <div class="border-t pt-2 flex justify-between font-bold text-base">
                <span>Total a debitar</span>
                <span class="text-amber-600">Kz {{ number_format($this->valorPatrocinio(), 0, ',', '.') }}</span>
            </div>
            <div class="text-xs text-gray-400 mt-1 text-right">
                Saldo: Kz {{ number_format($wallet->saldo ?? 0, 0, ',', '.') }}
            </div>
        </div>

        @if(($wallet->saldo ?? 0) < $this->valorPatrocinio())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm mb-4">
            Saldo insuficiente para este patrocínio.
        </div>
        @endif

        <div class="flex gap-3">
            <button wire:click="confirmarPatrocinio"
                @disabled(($wallet->saldo ?? 0) < $this->valorPatrocinio())
                class="flex-1 py-2.5 bg-amber-500 text-white rounded-xl font-semibold text-sm hover:bg-amber-600 transition disabled:opacity-50">
                Confirmar Patrocínio
            </button>
            <button wire:click="cancelarSponsor"
                class="px-5 py-2.5 text-sm text-gray-600 hover:text-gray-900 transition">
                Cancelar
            </button>
        </div>
    </div>
</div>
@endif

</div>
