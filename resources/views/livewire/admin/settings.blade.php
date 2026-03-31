<div class="space-y-6">

    @if($savedMsg)
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl text-sm">{{ $savedMsg }}</div>
    @endif
    @if($errorMsg)
        <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm">{{ $errorMsg }}</div>
    @endif

    {{-- ── 1. Configurações de Marca e Comunicação ── --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <div class="mb-5 pb-3 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-800">1. Configurações de Marca e Comunicação</h2>
            <p class="text-xs text-gray-400 mt-1">Identidade visual e dados de contacto exibidos em extractos, comprovativos e comunicações.</p>
        </div>

        {{-- a. Nome da plataforma --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-500 mb-1">
                a. Nome da Plataforma
            </label>
            <input wire:model="siteName" type="text" placeholder="ex. 24Horas Remoto"
                class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] @error('siteName') border-red-400 @enderror">
            @error('siteName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- b. Saldo mínimo da carteira digital --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-500 mb-1">
                b. Saldo Mínimo da Carteira Digital (Kz)
            </label>
            <input wire:model="walletMinBalance" type="number" min="0" step="1" placeholder="0"
                class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] @error('walletMinBalance') border-red-400 @enderror">
            <p class="text-xs text-gray-400 mt-1">Saldo mínimo exigido para operações na carteira digital.</p>
            @error('walletMinBalance') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- c. Logomarca no extrato de movimento --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-500 mb-1">
                c. Logomarca no Extrato de Movimento
            </label>

            @if($brandLogoPath)
                <div class="mb-2 flex items-center gap-3">
                    <img src="{{ Storage::url($brandLogoPath) }}" alt="Logo actual"
                        class="h-12 w-auto object-contain border border-gray-200 rounded-lg p-1 bg-gray-50">
                    <span class="text-xs text-gray-400">Logo actual</span>
                </div>
            @endif

            @if($brandLogo)
                <div class="mb-2 flex items-center gap-3">
                    <img src="{{ $brandLogo->temporaryUrl() }}" alt="Pré-visualização"
                        class="h-12 w-auto object-contain border border-[#00baff]/40 rounded-lg p-1 bg-gray-50">
                    <span class="text-xs text-[#00baff]">Pré-visualização — será guardada ao clicar em Guardar</span>
                </div>
            @endif

            <input wire:model="brandLogo" type="file" accept="image/*"
                class="block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-[#00baff]/10 file:text-[#00baff] hover:file:bg-[#00baff]/20 cursor-pointer">
            <p class="text-xs text-gray-400 mt-1">Formatos aceites: JPG, PNG, SVG, WEBP. Máximo 2 MB.</p>
            @error('brandLogo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- d. E-mail de suporte financeiro --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-500 mb-1">
                d. E-mail de Suporte Financeiro
            </label>
            <input wire:model="financialSupportEmail" type="email" placeholder="financeiro@24horasremoto.ao"
                class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] @error('financialSupportEmail') border-red-400 @enderror">
            <p class="text-xs text-gray-400 mt-1">Exibido em comprovativos e extractos de pagamento.</p>
            @error('financialSupportEmail') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- e. Texto no comprovante --}}
        <div class="mb-0">
            <label class="block text-xs font-medium text-gray-500 mb-1">
                e. Texto no Comprovante de Pagamento
            </label>
            <textarea wire:model="receiptText" rows="3"
                placeholder="Pagamento processado pela 24Horas Remoto."
                class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] resize-none @error('receiptText') border-red-400 @enderror"></textarea>
            <p class="text-xs text-gray-400 mt-1">Rodapé exibido em comprovativos e extractos de pagamento. Máximo 500 caracteres.</p>
            @error('receiptText') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- ── 2. Configuração de Prazos e Retenção ── --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <div class="mb-6 pb-3 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-800">2. Configuração de Prazos e Retenção</h2>
            <p class="text-xs text-gray-400 mt-1">Define quando os pagamentos são liberados para cada tipo de prestador.</p>
        </div>

        {{-- Freelancers --}}
        <div class="mb-6">
            <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Freelancers</h3>
            <p class="text-xs text-gray-500 mb-3">a. Liberação após a conclusão do projecto para o Freelancer:</p>
            <div class="flex flex-col gap-2">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input wire:model="freelancerPaymentRelease" type="radio" value="immediate"
                        class="w-4 h-4 accent-[#00baff] cursor-pointer">
                    <span class="text-sm text-gray-700 group-hover:text-[#00baff] transition-colors">Imediata</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input wire:model="freelancerPaymentRelease" type="radio" value="after_confirmation"
                        class="w-4 h-4 accent-[#00baff] cursor-pointer">
                    <span class="text-sm text-gray-700 group-hover:text-[#00baff] transition-colors">Apenas após a confirmação do contratante</span>
                </label>
            </div>
            @error('freelancerPaymentRelease') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="border-t border-gray-100 pt-5 mb-6">
            {{-- Criadores --}}
            <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Criadores</h3>
            <p class="text-xs text-gray-500 mb-3">a. Liberação de pagamento para Criadores:</p>
            <div class="flex flex-col gap-2">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input wire:model="creatorPaymentRelease" type="radio" value="immediate"
                        class="w-4 h-4 accent-[#00baff] cursor-pointer">
                    <span class="text-sm text-gray-700 group-hover:text-[#00baff] transition-colors">Imediata</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input wire:model="creatorPaymentRelease" type="radio" value="day_26"
                        class="w-4 h-4 accent-[#00baff] cursor-pointer">
                    <span class="text-sm text-gray-700 group-hover:text-[#00baff] transition-colors">Após o dia 26 de cada mês</span>
                </label>
            </div>
            @error('creatorPaymentRelease') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="border-t border-gray-100 pt-5">
            {{-- Infoprodutos --}}
            <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Infoprodutos</h3>
            <p class="text-xs text-gray-500 mb-3">a. Liberação de pagamento ao produtor após a venda:</p>
            <div class="flex flex-col gap-2">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input wire:model="infoprodutoPaymentRelease" type="radio" value="immediate"
                        class="w-4 h-4 accent-[#00baff] cursor-pointer">
                    <span class="text-sm text-gray-700 group-hover:text-[#00baff] transition-colors">Imediato</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input wire:model="infoprodutoPaymentRelease" type="radio" value="7_days"
                        class="w-4 h-4 accent-[#00baff] cursor-pointer">
                    <span class="text-sm text-gray-700 group-hover:text-[#00baff] transition-colors">Após 7 dias</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input wire:model="infoprodutoPaymentRelease" type="radio" value="14_days"
                        class="w-4 h-4 accent-[#00baff] cursor-pointer">
                    <span class="text-sm text-gray-700 group-hover:text-[#00baff] transition-colors">Após 14 dias</span>
                </label>
            </div>
            @error('infoprodutoPaymentRelease') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- ── 3. Configurações Gerais ── --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <div class="mb-5 pb-3 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-800">3. Configurações Gerais</h2>
            <p class="text-xs text-gray-400 mt-1">E-mail da plataforma e modo de manutenção.</p>
        </div>

        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-500 mb-1">E-mail da Plataforma</label>
            <input wire:model="siteEmail" type="email"
                class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] @error('siteEmail') border-red-400 @enderror">
            @error('siteEmail') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-0">
            <label class="block text-xs font-medium text-gray-500 mb-1">Modo de Manutenção</label>
            <select wire:model="maintenanceMode"
                class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
                <option value="0">Desactivado</option>
                <option value="1">Activado</option>
            </select>
        </div>
    </div>

    <div class="flex justify-end">
        <button wire:click="save" wire:loading.attr="disabled"
            class="btn-primary px-8 disabled:opacity-60 disabled:cursor-not-allowed">
            <span wire:loading.remove wire:target="save">Guardar Configurações</span>
            <span wire:loading wire:target="save">A guardar…</span>
        </button>
    </div>
</div>
