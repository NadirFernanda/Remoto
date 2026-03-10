<div class="container mx-auto px-4 py-8 max-w-xl">
    <h2 class="text-xl font-bold text-cyan-600 mb-4">Entrega do Serviço</h2>
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="mb-2">
            <span class="font-semibold">Título:</span> {{ $service->titulo }}
        </div>
        <div class="mb-2">
            <span class="font-semibold">Status:</span>
            @php
                $statusLabels = [
                    'published' => 'Publicado',
                    'accepted' => 'Aceite',
                    'in_progress' => 'Em andamento',
                    'delivered' => 'Entregue',
                    'completed' => 'Concluído',
                    'cancelled' => 'Cancelado',
                ];
            @endphp
            <span class="px-2 py-1 rounded text-xs font-bold
                @if($service->status === 'in_progress') bg-yellow-100 text-yellow-700
                @elseif($service->status === 'delivered') bg-blue-100 text-blue-700
                @elseif($service->status === 'completed') bg-green-100 text-green-700
                @endif">
                {{ $statusLabels[$service->status] ?? ucfirst($service->status) }}
            </span>
        </div>
        <form wire:submit.prevent="entregarServico" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="block font-semibold mb-2">Ficheiro de entrega</label>
                <x-file-input wire:model="entrega_arquivo" label="📎 Seleccionar ficheiro de entrega" loading-target="entrega_arquivo">
                    @error('entrega_arquivo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </x-file-input>
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-2">Mensagem (opcional)</label>
                <input type="text" wire:model.defer="entrega_mensagem" class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                @error('entrega_mensagem') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="w-full bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-3 rounded-lg transition-all duration-150">Entregar serviço</button>
        </form>
        @if(session('success'))
            <div class="mt-4 p-2 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif
    </div>
</div>
