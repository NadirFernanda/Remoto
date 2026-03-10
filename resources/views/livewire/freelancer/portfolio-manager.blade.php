@php
$categoryIcons = [
    'imagem'         => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
    'documento'      => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    'link'           => 'M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14',
    'certificacao'   => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
    'estudo_de_caso' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
];
@endphp

<div class="light-page min-h-screen pt-8 pb-12">
<div class="max-w-5xl mx-auto px-4">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Meu Portfólio</h1>
            <p class="text-sm text-gray-500 mt-1">Apresente os seus trabalhos, certificações e estudos de caso</p>
        </div>
        <button wire:click="openForm('imagem')" class="btn-eq btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Adicionar item
        </button>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 text-green-700 border border-green-200 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- ===== ADD / EDIT FORM ===== --}}
    @if($showForm)
    <div class="bg-white border rounded-2xl shadow-sm p-6 mb-8">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-semibold text-gray-800">{{ $editingId ? 'Editar item' : 'Novo item' }}</h2>
            <button wire:click="resetForm" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Category tabs --}}
        <div class="flex flex-wrap gap-2 mb-5">
            @foreach($categories as $key => $label)
            <button wire:click="$set('tab', '{{ $key }}')"
                    type="button"
                    class="px-3 py-1.5 rounded-full text-sm font-medium border transition
                        {{ $tab === $key ? 'bg-[#00baff] text-white border-[#00baff]' : 'bg-white text-gray-600 border-gray-200 hover:border-[#00baff]' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>

        <form wire:submit.prevent="save" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
                <input wire:model.defer="title" type="text" class="block w-full border rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-[#00baff]" placeholder="Ex: Redesign de identidade visual">
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            @if(in_array($tab, ['imagem', 'documento']))
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Ficheiro {{ $tab === 'imagem' ? '(JPG, PNG, GIF — máx. 8MB)' : '(PDF, DOCX, etc. — máx. 20MB)' }}
                    {{ $editingId ? '(deixe em branco para manter o actual)' : '*' }}
                </label>
                <x-file-input
                    wire:model="file"
                    accept="{{ $tab === 'imagem' ? 'image/*' : '.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx' }}"
                    label="{{ $tab === 'imagem' ? '🖼 Escolher imagem' : '📄 Escolher documento' }}"
                    loading-target="file"
                >
                    @error('file') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                </x-file-input>
            </div>
            @endif

            @if($tab === 'link')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">URL *</label>
                <input wire:model.defer="external_url" type="url" class="block w-full border rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-[#00baff]" placeholder="https://...">
                @error('external_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            @endif

            @if($tab === 'certificacao')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Emitido por *</label>
                    <input wire:model.defer="issuer" type="text" class="block w-full border rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-[#00baff]" placeholder="Ex: Google, Coursera, Udemy">
                    @error('issuer') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ano de emissão</label>
                    <input wire:model.defer="issued_year" type="number" min="1990" max="{{ date('Y') }}" class="block w-full border rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-[#00baff]" placeholder="{{ date('Y') }}">
                    @error('issued_year') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ $tab === 'estudo_de_caso' ? 'Descrição do caso (contexto, desafio, solução, resultado)' : 'Descrição' }}
                </label>
                <textarea wire:model.defer="description" rows="{{ $tab === 'estudo_de_caso' ? 6 : 3 }}"
                    class="block w-full border rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-[#00baff]"
                    placeholder="{{ $tab === 'estudo_de_caso' ? 'Descreva o projecto: contexto, desafio enfrentado, solução desenvolvida e resultados obtidos...' : 'Descrição opcional' }}"></textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700">
                <input wire:model.defer="featured" type="checkbox" class="rounded text-[#00baff]">
                Destacar este item no meu perfil
            </label>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-eq btn-primary">
                    {{ $editingId ? 'Guardar alterações' : 'Adicionar ao portfólio' }}
                </button>
                <button type="button" wire:click="resetForm" class="btn-eq btn-outline">Cancelar</button>
            </div>
        </form>
    </div>
    @endif

    {{-- ===== PORTFOLIO ITEMS ===== --}}
    @php $totalItems = $items->flatten()->count(); @endphp

    @if($totalItems === 0 && !$showForm)
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <svg class="w-16 h-16 text-gray-200 mb-4" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-500 font-medium">O seu portfólio está vazio</p>
            <p class="text-sm text-gray-400 mt-1">Adicione imagens, documentos, links e certificações</p>
            <button wire:click="openForm('imagem')" class="mt-4 btn-eq btn-primary">Começar agora</button>
        </div>
    @else
        @foreach($categories as $catKey => $catLabel)
            @if(isset($items[$catKey]) && $items[$catKey]->count())
            <div class="mb-8">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-5 h-5 text-[#00baff]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $categoryIcons[$catKey] }}"/>
                    </svg>
                    <h2 class="font-semibold text-gray-800">{{ $catLabel }}</h2>
                    <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">{{ $items[$catKey]->count() }}</span>
                    <button wire:click="openForm('{{ $catKey }}')" class="ml-auto text-xs text-[#00baff] hover:underline">+ Adicionar</button>
                </div>

                @if($catKey === 'imagem')
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($items[$catKey] as $item)
                    <div class="bg-white border rounded-xl overflow-hidden shadow-sm group relative">
                        @if($item->featured)
                            <span class="absolute top-2 left-2 z-10 text-[10px] bg-yellow-400 text-white px-2 py-0.5 rounded-full font-semibold">Destaque</span>
                        @endif
                        @if($item->media_path)
                            <img src="{{ asset('storage/' . $item->media_path) }}" alt="{{ $item->title }}" class="w-full h-40 object-cover">
                        @else
                            <div class="w-full h-40 bg-gray-100 flex items-center justify-center text-gray-300">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                        <div class="p-3">
                            <div class="font-medium text-sm text-gray-800 truncate">{{ $item->title }}</div>
                            @if($item->description)
                                <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ $item->description }}</p>
                            @endif
                            @include('livewire.freelancer.portfolio-item-actions', ['item' => $item])
                        </div>
                    </div>
                    @endforeach
                </div>

                @elseif(in_array($catKey, ['estudo_de_caso', 'certificacao']))
                <div class="space-y-3">
                    @foreach($items[$catKey] as $item)
                    <div class="bg-white border rounded-xl p-4 shadow-sm relative">
                        @if($item->featured)
                            <span class="absolute top-3 right-3 text-[10px] bg-yellow-400 text-white px-2 py-0.5 rounded-full font-semibold">Destaque</span>
                        @endif
                        <div class="font-medium text-gray-800">{{ $item->title }}</div>
                        @if($catKey === 'certificacao' && $item->issuer)
                            <div class="text-sm text-[#00baff] mt-0.5">{{ $item->issuer }}{{ $item->issued_year ? ' · '.$item->issued_year : '' }}</div>
                        @endif
                        @if($item->description)
                            <p class="text-sm text-gray-600 mt-2 whitespace-pre-line">{{ $item->description }}</p>
                        @endif
                        @include('livewire.freelancer.portfolio-item-actions', ['item' => $item])
                    </div>
                    @endforeach
                </div>

                @else
                <div class="space-y-2">
                    @foreach($items[$catKey] as $item)
                    <div class="bg-white border rounded-xl p-4 shadow-sm flex items-start gap-3">
                        <svg class="w-5 h-5 text-[#00baff] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $categoryIcons[$catKey] }}"/>
                        </svg>
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-sm text-gray-800">{{ $item->title }}</div>
                            @if($item->external_url)
                                <a href="{{ $item->external_url }}" target="_blank" rel="noopener noreferrer"
                                   class="text-xs text-[#00baff] hover:underline truncate block mt-0.5">
                                    {{ $item->external_url }}
                                </a>
                            @endif
                            @if($item->media_path)
                                <a href="{{ asset('storage/' . $item->media_path) }}" target="_blank"
                                   class="text-xs text-[#00baff] hover:underline mt-0.5 inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Ver ficheiro
                                </a>
                            @endif
                            @if($item->description)
                                <p class="text-xs text-gray-500 mt-1">{{ $item->description }}</p>
                            @endif
                        </div>
                        @include('livewire.freelancer.portfolio-item-actions', ['item' => $item])
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endif
        @endforeach
    @endif

</div>
</div>
