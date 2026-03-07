<div class="flex items-center gap-3 mt-3 pt-2 border-t border-gray-100">
    <button wire:click="openForm('{{ $item->category }}', {{ $item->id }})"
            class="text-xs text-gray-500 hover:text-[#00baff] flex items-center gap-1">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Editar
    </button>
    <button wire:click="toggleFeatured({{ $item->id }})"
            class="text-xs {{ $item->featured ? 'text-yellow-500' : 'text-gray-400 hover:text-yellow-500' }} flex items-center gap-1">
        <svg class="w-3.5 h-3.5" fill="{{ $item->featured ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
        </svg>
        {{ $item->featured ? 'Destaque' : 'Destacar' }}
    </button>
    <button wire:click="delete({{ $item->id }})"
            wire:confirm="Remover este item do portfólio?"
            class="text-xs text-gray-400 hover:text-red-500 flex items-center gap-1 ml-auto">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
        Remover
    </button>
</div>
