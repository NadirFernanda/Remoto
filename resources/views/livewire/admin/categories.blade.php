<div class="space-y-5">

    {{-- Alerts --}}
    @if($savedMsg)
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl text-sm">{{ $savedMsg }}</div>
    @endif

    {{-- Header: search + new button --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="flex-1">
            <input wire:model.live.debounce.300ms="search" type="text"
                placeholder="Pesquisar categoria..."
                class="w-full sm:max-w-xs border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
        </div>
        @if(!$showForm)
            <button wire:click="openCreate"
                class="inline-flex items-center gap-2 px-5 py-2 rounded-lg bg-[#00baff] hover:bg-[#009ad6] text-white font-semibold text-sm shadow transition">
                <svg width="16" height="16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 2v12M2 8h12" stroke="white" stroke-width="2.2" stroke-linecap="round"/></svg>
                Nova Categoria
            </button>
        @endif
    </div>

    {{-- Create / Edit Form --}}
    @if($showForm)
        <div class="bg-white rounded-2xl border border-[#00baff]/40 p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">
                {{ $editId ? 'Editar Categoria' : 'Nova Categoria' }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Nome <span class="text-red-400">*</span></label>
                    <input wire:model="name" type="text" maxlength="100"
                        class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] @error('name') border-red-400 @enderror"
                        placeholder="Ex: Design Gráfico">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Ícone (emoji)</label>
                    <input wire:model="icon" type="text" maxlength="20"
                        class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]"
                        placeholder="🎨">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Descrição</label>
                    <input wire:model="description" type="text" maxlength="255"
                        class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]"
                        placeholder="Breve descrição da categoria...">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Ordenação</label>
                    <input wire:model="sort_order" type="number" min="0"
                        class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
                    <p class="text-xs text-gray-400 mt-0.5">Menor número → aparece primeiro.</p>
                </div>
                <div class="flex items-center gap-3 mt-5">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input wire:model="active" type="checkbox" class="sr-only peer">
                        <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-5 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#00baff]"></div>
                        <span class="ml-2 text-sm text-gray-600">Activa</span>
                    </label>
                </div>
            </div>
            <div class="flex gap-3 mt-5">
                <button wire:click="save" wire:loading.attr="disabled"
                    class="btn-primary">
                    <span wire:loading.remove wire:target="save">{{ $editId ? 'Actualizar' : 'Criar Categoria' }}</span>
                    <span wire:loading wire:target="save">A guardar...</span>
                </button>
                <button wire:click="cancelForm" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium transition">
                    Cancelar
                </button>
            </div>
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="bg-[#f4fbfd] text-[#00baff] uppercase text-xs tracking-wider">
                    <th class="py-3 px-5 text-left font-semibold">Categoria</th>
                    <th class="py-3 px-5 text-left font-semibold">Descrição</th>
                    <th class="py-3 px-5 text-center font-semibold">Ordem</th>
                    <th class="py-3 px-5 text-center font-semibold">Estado</th>
                    <th class="py-3 px-5 text-left font-semibold">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                    <tr class="border-b last:border-0 hover:bg-[#f8fdff] transition">
                        <td class="py-3 px-5">
                            <div class="flex items-center gap-2">
                                @if($cat->icon)
                                    <span class="text-xl leading-none">{{ $cat->icon }}</span>
                                @endif
                                <span class="font-medium text-gray-900">{{ $cat->name }}</span>
                            </div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $cat->slug }}</div>
                        </td>
                        <td class="py-3 px-5 text-gray-500 max-w-xs">
                            {{ $cat->description ?: '—' }}
                        </td>
                        <td class="py-3 px-5 text-center text-gray-500">{{ $cat->sort_order }}</td>
                        <td class="py-3 px-5 text-center">
                            <button wire:click="toggleActive({{ $cat->id }})"
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold transition
                                    {{ $cat->active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                {{ $cat->active ? 'Activa' : 'Inactiva' }}
                            </button>
                        </td>
                        <td class="py-3 px-5 whitespace-nowrap">
                            <button wire:click="openEdit({{ $cat->id }})"
                                class="inline-flex items-center gap-1 text-yellow-600 hover:text-yellow-800 font-medium transition text-xs">
                                ✏️ Editar
                            </button>
                            <button wire:click="delete({{ $cat->id }})"
                                wire:confirm="Tem a certeza que quer remover a categoria «{{ $cat->name }}»?"
                                class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 font-medium transition text-xs ml-3">
                                🗑️ Remover
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <span class="text-4xl">🗂️</span>
                                @if($search)
                                    Nenhuma categoria encontrada para «{{ $search }}».
                                @else
                                    Ainda não há categorias. Crie a primeira!
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="text-xs text-gray-400">
        Total: {{ $categories->count() }} {{ $categories->count() === 1 ? 'categoria' : 'categorias' }}
    </p>
</div>
