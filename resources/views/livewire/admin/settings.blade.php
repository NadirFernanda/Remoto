<div>
    @if($savedMsg)
        <div class="mb-5 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl text-sm">{{ $savedMsg }}</div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-200 p-6 max-w-lg">
        <h2 class="text-base font-semibold text-gray-700 mb-5">Configurações da Plataforma</h2>

        <div class="mb-4">
            <label class="block text-xs text-gray-500 mb-1">Nome do Site</label>
            <input wire:model="siteName" type="text"
                class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
        </div>

        <div class="mb-4">
            <label class="block text-xs text-gray-500 mb-1">E-mail da Plataforma</label>
            <input wire:model="siteEmail" type="email"
                class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
        </div>

        <div class="mb-5">
            <label class="block text-xs text-gray-500 mb-1">Modo de Manutenção</label>
            <select wire:model="maintenanceMode"
                class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
                <option value="0">Desactivado</option>
                <option value="1">Activado</option>
            </select>
        </div>

        <button wire:click="save" class="btn-primary w-full">Guardar Configurações</button>
    </div>
</div>
