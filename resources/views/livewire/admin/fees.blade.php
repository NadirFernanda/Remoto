<div class="space-y-6">

    @if($savedMsg)
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl text-sm">{{ $savedMsg }}</div>
    @endif
    @if($errorMsg)
        <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm">{{ $errorMsg }}</div>
    @endif

    {{-- ── Projetos / Serviços ── --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-1">Projetos & Serviços Freelancer</h3>
        <p class="text-xs text-gray-400 mb-4">Taxa cobrada ao cliente sobre o valor do projecto + comissão retida ao freelancer na entrega.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs text-gray-500 mb-1">
                    Taxa do Cliente (%)
                    <span class="ml-1 text-gray-400">— adicionado ao valor do projecto</span>
                </label>
                <div class="flex items-center gap-2">
                    <input wire:model="serviceClientFeeRate" type="number" step="0.1" min="0" max="100"
                        class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] @error('serviceClientFeeRate') border-red-400 @enderror">
                    <span class="text-gray-500 text-sm font-medium">%</span>
                </div>
                @error('serviceClientFeeRate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">
                    Comissão da Plataforma s/ Freelancer (%)
                    <span class="ml-1 text-gray-400">— retido ao freelancer na entrega</span>
                </label>
                <div class="flex items-center gap-2">
                    <input wire:model="serviceFreelancerFeeRate" type="number" step="0.1" min="0" max="100"
                        class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] @error('serviceFreelancerFeeRate') border-red-400 @enderror">
                    <span class="text-gray-500 text-sm font-medium">%</span>
                </div>
                @error('serviceFreelancerFeeRate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-3 bg-gray-50 rounded-lg px-3 py-2">
            Exemplo (projecto 50.000 Kz): cliente paga
            <strong>{{ number_format(50000 * (1 + $serviceClientFeeRate / 100), 0, ',', '.') }} Kz</strong>,
            freelancer recebe <strong>{{ number_format(50000 * (1 - $serviceFreelancerFeeRate / 100), 0, ',', '.') }} Kz</strong>.
        </p>
    </div>

    {{-- ── Loja (Infoprodutos) ── --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-1">Loja — Infoprodutos</h3>
        <p class="text-xs text-gray-400 mb-4">Comissão da plataforma sobre cada venda de infoproduto (ebook, áudio, etc.).</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Comissão da Plataforma (%)</label>
                <div class="flex items-center gap-2">
                    <input wire:model="lojaFeeRate" type="number" step="0.1" min="0" max="100"
                        class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] @error('lojaFeeRate') border-red-400 @enderror">
                    <span class="text-gray-500 text-sm font-medium">%</span>
                </div>
                @error('lojaFeeRate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Custo de Destaque (Patrocínio) por Dia (AOA)</label>
                <div class="flex items-center gap-2">
                    <input wire:model="patrocinioDiario" type="number" step="1" min="0"
                        class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] @error('patrocinioDiario') border-red-400 @enderror">
                    <span class="text-gray-500 text-sm font-medium">Kz/dia</span>
                </div>
                @error('patrocinioDiario') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-3 bg-gray-50 rounded-lg px-3 py-2">
            Num produto de 10.000 Kz: plataforma fica
            <strong>{{ number_format(10000 * $lojaFeeRate / 100, 0, ',', '.') }} Kz</strong>,
            criador recebe <strong>{{ number_format(10000 * (1 - $lojaFeeRate / 100), 0, ',', '.') }} Kz</strong>.
        </p>
    </div>

    {{-- ── Assinaturas de Criadores ── --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-1">Assinaturas de Criadores</h3>
        <p class="text-xs text-gray-400 mb-4">Comissão da plataforma sobre cada assinatura paga a um criador de conteúdo.</p>
        <div class="max-w-xs">
            <label class="block text-xs text-gray-500 mb-1">Comissão da Plataforma (%)</label>
            <div class="flex items-center gap-2">
                <input wire:model="subscriptionFeeRate" type="number" step="0.1" min="0" max="100"
                    class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] @error('subscriptionFeeRate') border-red-400 @enderror">
                <span class="text-gray-500 text-sm font-medium">%</span>
            </div>
            @error('subscriptionFeeRate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- ── Programa de Afiliados ── --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-1">Programa de Afiliados</h3>
        <p class="text-xs text-gray-400 mb-4">Comissão em AOA creditada ao afiliado por cada novo utilizador que se regista com o código desse afiliado.</p>
        <div class="max-w-xs">
            <label class="block text-xs text-gray-500 mb-1">Comissão por Registo (AOA)</label>
            <div class="flex items-center gap-2">
                <input wire:model="affiliateSignupCommission" type="number" step="1" min="0"
                    class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] @error('affiliateSignupCommission') border-red-400 @enderror">
                <span class="text-gray-500 text-sm font-medium">Kz</span>
            </div>
            @error('affiliateSignupCommission') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- ── Saques ── --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-1">Saques (Levantamentos)</h3>
        <p class="text-xs text-gray-400 mb-4">Taxas aplicadas sobre cada pedido de saque de carteira.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Taxa Fixa por Saque (AOA)</label>
                <div class="flex items-center gap-2">
                    <input wire:model="withdrawFeeFixed" type="number" step="0.01" min="0"
                        class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] @error('withdrawFeeFixed') border-red-400 @enderror">
                    <span class="text-gray-500 text-sm font-medium">Kz</span>
                </div>
                @error('withdrawFeeFixed') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Taxa Percentual por Saque (%)</label>
                <div class="flex items-center gap-2">
                    <input wire:model="withdrawFeePercent" type="number" step="0.01" min="0" max="100"
                        class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] @error('withdrawFeePercent') border-red-400 @enderror">
                    <span class="text-gray-500 text-sm font-medium">%</span>
                </div>
                @error('withdrawFeePercent') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-3 bg-gray-50 rounded-lg px-3 py-2">
            Num saque de 10.000 Kz: taxa total =
            <strong>{{ number_format($withdrawFeeFixed + (10000 * $withdrawFeePercent / 100), 2, ',', '.') }} Kz</strong>.
        </p>
    </div>

    <div class="flex justify-end">
        <button wire:click="save" wire:loading.attr="disabled"
            class="btn-primary min-w-[180px]">
            <span wire:loading.remove wire:target="save">Guardar Todas as Taxas</span>
            <span wire:loading wire:target="save">A guardar...</span>
        </button>
    </div>
</div>
