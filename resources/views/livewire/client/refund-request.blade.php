<div>
    <h1 class="text-2xl font-bold mb-6 text-red-600">Solicitar Reembolso</h1>
    @if (session('success'))
        <div class="bg-green-100 text-green-700 rounded-lg px-4 py-2 mb-4">{{ session('success') }}</div>
    @endif
    <form wire:submit.prevent="submit" enctype="multipart/form-data" class="space-y-4 max-w-lg">
        <div>
            <label class="block text-sm font-medium mb-1">Pedido/Projeto</label>
            <select wire:model="service_id" class="w-full border rounded-lg px-3 py-2">
                <option value="">Selecione...</option>
                @foreach($services as $service)
                    <option value="{{ $service->id }}">{{ $service->titulo }} ({{ money_aoa($service->valor) }})</option>
                @endforeach
            </select>
            @error('service_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Motivo do Reembolso</label>
            <input type="text" wire:model="reason" class="w-full border rounded-lg px-3 py-2" maxlength="255">
            @error('reason') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Detalhes</label>
            <textarea wire:model="details" class="w-full border rounded-lg px-3 py-2" rows="4"></textarea>
            @error('details') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Provas/Anexos (opcional)</label>
            <input type="file" wire:model="evidence" multiple class="w-full">
            @error('evidence.*') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            <div wire:loading wire:target="evidence" class="text-xs text-gray-500 mt-1">Carregando arquivos...</div>
        </div>
        <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-red-700 transition">Enviar Pedido</button>
    </form>
</div>
