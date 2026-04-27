<div class="space-y-6">

    @if($savedMsg)
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl text-sm">{{ $savedMsg }}</div>
    @endif
    @if($errorMsg)
        <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm">{{ $errorMsg }}</div>
    @endif

    {{-- ── 0. Meu Perfil ── --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <div class="mb-5 pb-3 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-800">Meu Perfil</h2>
            <p class="text-xs text-gray-400 mt-1">Actualize o seu nome, e-mail e senha de acesso.</p>
        </div>

        @if($profileMsg)
            <div class="mb-4 px-4 py-3 rounded-xl text-sm font-medium border
                {{ $profileMsgType === 'success' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                {{ $profileMsg }}
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-gray-500 mb-1">Nome completo <span class="text-red-500">*</span></label>
                <input wire:model="profileName" type="text" required placeholder="O seu nome real"
                    class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] @error('profileName') border-red-400 @enderror">
                @error('profileName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">E-mail de acesso <span class="text-red-500">*</span></label>
                <input wire:model="profileEmail" type="email" required
                    class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] @error('profileEmail') border-red-400 @enderror">
                @error('profileEmail') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div></div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Nova senha <span class="text-gray-400">(deixe em branco para manter)</span></label>
                <input wire:model="profilePassword" type="password" placeholder="mínimo 10 caracteres"
                    class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] @error('profilePassword') border-red-400 @enderror">
                @error('profilePassword') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Confirmar nova senha</label>
                <input wire:model="profilePasswordConfirm" type="password" placeholder="repetir senha"
                    class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
            </div>
        </div>

        <button wire:click="saveProfile" wire:loading.attr="disabled"
            class="px-5 py-2 bg-gradient-to-r from-[#00baff] to-blue-600 text-white rounded-xl text-sm font-semibold hover:opacity-90 transition disabled:opacity-50">
            <span wire:loading.remove wire:target="saveProfile">Guardar Perfil</span>
            <span wire:loading wire:target="saveProfile">A guardar...</span>
        </button>
    </div>

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

    {{-- ── 3. Configurações de Saques ── --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <div class="mb-6 pb-3 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-800">3. Configurações de Saques</h2>
            <p class="text-xs text-gray-400 mt-1">Controlo do processamento, limites e alertas de liquidez para saques da carteira.</p>
        </div>

        {{-- Processamento --}}
        <div class="mb-6">
            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Processamento de Saques</p>
            <div class="flex flex-col gap-2">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input wire:model="withdrawalProcessing" type="radio" value="automatic"
                        class="w-4 h-4 accent-[#00baff] cursor-pointer">
                    <span class="text-sm text-gray-700 group-hover:text-[#00baff] transition-colors">
                        <strong class="font-medium">a.</strong> Processar Automaticamente
                    </span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input wire:model="withdrawalProcessing" type="radio" value="manual"
                        class="w-4 h-4 accent-[#00baff] cursor-pointer">
                    <span class="text-sm text-gray-700 group-hover:text-[#00baff] transition-colors">
                        <strong class="font-medium">b.</strong> Processar Manualmente
                    </span>
                </label>
            </div>
            @error('withdrawalProcessing') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Limite mínimo --}}
        <div class="border-t border-gray-100 pt-5 mb-6">
            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Limite Mínimo de Saque</p>
            <div class="flex flex-col gap-2">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input wire:model="withdrawalMinAmount" type="radio" value="20000"
                        class="w-4 h-4 accent-[#00baff] cursor-pointer">
                    <span class="text-sm text-gray-700 group-hover:text-[#00baff] transition-colors">
                        <strong class="font-medium">a.</strong> 20.000,00 Kz
                    </span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input wire:model="withdrawalMinAmount" type="radio" value="60000"
                        class="w-4 h-4 accent-[#00baff] cursor-pointer">
                    <span class="text-sm text-gray-700 group-hover:text-[#00baff] transition-colors">
                        <strong class="font-medium">b.</strong> 60.000,00 Kz
                    </span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input wire:model="withdrawalMinAmount" type="radio" value="0"
                        class="w-4 h-4 accent-[#00baff] cursor-pointer">
                    <span class="text-sm text-gray-700 group-hover:text-[#00baff] transition-colors">
                        <strong class="font-medium">c.</strong> Sem limite mínimo
                    </span>
                </label>
            </div>
            @error('withdrawalMinAmount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Alerta de Liquidez --}}
        <div class="border-t border-gray-100 pt-5 mb-6">
            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Alerta de Liquidez</p>
            <p class="text-xs text-gray-400 mb-3">Notificar o administrador quando o total de saques pendentes atingir:</p>
            <div class="flex flex-col gap-2">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input wire:model="withdrawalLiquidityAlert" type="radio" value="500000"
                        class="w-4 h-4 accent-[#00baff] cursor-pointer">
                    <span class="text-sm text-gray-700 group-hover:text-[#00baff] transition-colors">
                        <strong class="font-medium">a.</strong> Alerta a partir de 500.000,00 Kz pendentes
                    </span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input wire:model="withdrawalLiquidityAlert" type="radio" value="1000000"
                        class="w-4 h-4 accent-[#00baff] cursor-pointer">
                    <span class="text-sm text-gray-700 group-hover:text-[#00baff] transition-colors">
                        <strong class="font-medium">b.</strong> Alerta a partir de 1.000.000,00 Kz pendentes
                    </span>
                </label>
            </div>
            @error('withdrawalLiquidityAlert') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Métodos de pagamento --}}
        <div class="border-t border-gray-100 pt-5">
            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Métodos de Pagamento Aceites</p>
            <p class="text-xs text-gray-400 mb-3">Seleccione todos os métodos disponíveis para saques:</p>
            <div class="flex flex-col gap-2">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input wire:model="withdrawalMethods" type="checkbox" value="bank_transfer"
                        class="w-4 h-4 accent-[#00baff] rounded cursor-pointer">
                    <span class="text-sm text-gray-700 group-hover:text-[#00baff] transition-colors">
                        <strong class="font-medium">a.</strong> Transferência Bancária
                    </span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input wire:model="withdrawalMethods" type="checkbox" value="visa"
                        class="w-4 h-4 accent-[#00baff] rounded cursor-pointer">
                    <span class="text-sm text-gray-700 group-hover:text-[#00baff] transition-colors">
                        <strong class="font-medium">b.</strong> Gateway de Pagamento — VISA
                    </span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input wire:model="withdrawalMethods" type="checkbox" value="other"
                        class="w-4 h-4 accent-[#00baff] rounded cursor-pointer">
                    <span class="text-sm text-gray-700 group-hover:text-[#00baff] transition-colors">
                        <strong class="font-medium">c.</strong> Outro
                    </span>
                </label>
            </div>
            @error('withdrawalMethods') <p class="text-red-500 text-xs mt-1">Seleccione pelo menos um método.</p> @enderror
            @error('withdrawalMethods.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- ── 4. Notificações e Alertas ── --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <div class="mb-6 pb-3 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-800">4. Notificações e Alertas</h2>
            <p class="text-xs text-gray-400 mt-1">Configure os alertas para administradores e notificações automáticas para utilizadores.</p>
        </div>

        {{-- Alertas para o Admin --}}
        <div class="mb-6">
            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Alertas para o Administrador</p>
            <div class="flex flex-col gap-2">
                @php
                    $adminAlertOptions = [
                        'pending_withdrawals_24h' => ['label' => 'Saques pendentes há mais de 24 horas',       'letter' => 'a'],
                        'suspicious_withdrawal'   => ['label' => 'Tentativa de saque suspeita detectada',      'letter' => 'b'],
                        'config_risk'             => ['label' => 'Alerta quando uma configuração pode causar problemas', 'letter' => 'c'],
                        'change_history'          => ['label' => 'Histórico de alterações das configurações',  'letter' => 'd'],
                        'help_tooltips'           => ['label' => 'Mensagens de ajuda explicando cada configuração', 'letter' => 'e'],
                    ];
                @endphp
                @foreach($adminAlertOptions as $value => $opt)
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input wire:model="adminAlerts" type="checkbox" value="{{ $value }}"
                        class="w-4 h-4 accent-[#00baff] rounded cursor-pointer">
                    <span class="text-sm text-gray-700 group-hover:text-[#00baff] transition-colors">
                        <strong class="font-medium">{{ $opt['letter'] }}.</strong> {{ $opt['label'] }}
                    </span>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Canal de alerta --}}
        <div class="border-t border-gray-100 pt-5 mb-6">
            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Canal de Alerta</p>
            <div class="flex flex-col gap-2">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input wire:model="adminAlertChannels" type="checkbox" value="email"
                        class="w-4 h-4 accent-[#00baff] rounded cursor-pointer">
                    <span class="text-sm text-gray-700 group-hover:text-[#00baff] transition-colors"><strong class="font-medium">a.</strong> E-mail</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input wire:model="adminAlertChannels" type="checkbox" value="sms"
                        class="w-4 h-4 accent-[#00baff] rounded cursor-pointer">
                    <span class="text-sm text-gray-700 group-hover:text-[#00baff] transition-colors"><strong class="font-medium">b.</strong> SMS</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input wire:model="adminAlertChannels" type="checkbox" value="system"
                        class="w-4 h-4 accent-[#00baff] rounded cursor-pointer">
                    <span class="text-sm text-gray-700 group-hover:text-[#00baff] transition-colors"><strong class="font-medium">c.</strong> Sistema (notificação interna)</span>
                </label>
            </div>
            @error('adminAlertChannels') <p class="text-red-500 text-xs mt-1">Seleccione pelo menos um canal.</p> @enderror
        </div>

        {{-- Notificações para Utilizadores --}}
        <div class="border-t border-gray-100 pt-5">
            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Notificações para Utilizadores</p>
            <div class="flex flex-col gap-2">
                @php
                    $userNotifOptions = [
                        'withdrawal_processed'  => ['label' => 'Notificar quando o saque for processado',       'letter' => 'a'],
                        'value_retained'        => ['label' => 'Notificar quando o valor for retido',           'letter' => 'b'],
                        'dispute'               => ['label' => 'Notificar quando houver uma disputa',           'letter' => 'c'],
                        'weekly_earnings'       => ['label' => 'Resumo semanal dos ganhos',                     'letter' => 'd'],
                        'fee_change_notice'     => ['label' => 'Aviso antes de alteração de taxas',             'letter' => 'e'],
                    ];
                @endphp
                @foreach($userNotifOptions as $value => $opt)
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input wire:model="userNotifications" type="checkbox" value="{{ $value }}"
                        class="w-4 h-4 accent-[#00baff] rounded cursor-pointer">
                    <span class="text-sm text-gray-700 group-hover:text-[#00baff] transition-colors">
                        <strong class="font-medium">{{ $opt['letter'] }}.</strong> {{ $opt['label'] }}
                    </span>
                </label>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── 5. Relatórios e Exportações ── --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <div class="mb-6 pb-3 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-800">5. Relatórios e Exportações</h2>
            <p class="text-xs text-gray-400 mt-1">Automatize o envio de relatórios financeiros e configure formatos e destinatários.</p>
        </div>

        {{-- Tipos de relatório --}}
        <div class="mb-6">
            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Envio Automático de Relatórios</p>
            <div class="flex flex-col gap-3">
                <label class="flex items-start gap-3 cursor-pointer group">
                    <input wire:model="reportWithdrawalDaily" type="checkbox" value="1" true-value="1" false-value="0"
                        class="w-4 h-4 mt-0.5 accent-[#00baff] rounded cursor-pointer">
                    <div>
                        <span class="text-sm font-medium text-gray-700 group-hover:text-[#00baff] transition-colors"><strong>a.</strong> Relatório de Saques</span>
                        <p class="text-xs text-gray-400">Enviar diariamente por e-mail</p>
                    </div>
                </label>
                <label class="flex items-start gap-3 cursor-pointer group">
                    <input wire:model="reportCommissionMonthly" type="checkbox" value="1" true-value="1" false-value="0"
                        class="w-4 h-4 mt-0.5 accent-[#00baff] rounded cursor-pointer">
                    <div>
                        <span class="text-sm font-medium text-gray-700 group-hover:text-[#00baff] transition-colors"><strong>b.</strong> Relatório de Comissões</span>
                        <p class="text-xs text-gray-400">Enviar mensalmente por e-mail</p>
                    </div>
                </label>
                <label class="flex items-start gap-3 cursor-pointer group">
                    <input wire:model="reportTax" type="checkbox" value="1" true-value="1" false-value="0"
                        class="w-4 h-4 mt-0.5 accent-[#00baff] rounded cursor-pointer">
                    <div>
                        <span class="text-sm font-medium text-gray-700 group-hover:text-[#00baff] transition-colors"><strong>c.</strong> Relatório de Impostos</span>
                        <p class="text-xs text-gray-400">Gerado e enviado automaticamente</p>
                    </div>
                </label>
            </div>
        </div>

        {{-- Email destino --}}
        <div class="border-t border-gray-100 pt-5 mb-6">
            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">
                d. Enviar Todos os Relatórios Para
            </label>
            <input wire:model="reportEmail" type="email" placeholder="contabilidade@24horas.ao"
                class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] @error('reportEmail') border-red-400 @enderror">
            @error('reportEmail') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Formatos --}}
        <div class="border-t border-gray-100 pt-5">
            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Formatos de Exportação</p>
            <div class="flex flex-wrap gap-4">
                @foreach(['csv' => 'CSV', 'excel' => 'Excel (.xlsx)', 'pdf' => 'PDF'] as $val => $lbl)
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input wire:model="reportFormats" type="checkbox" value="{{ $val }}"
                        class="w-4 h-4 accent-[#00baff] rounded cursor-pointer">
                    <span class="text-sm text-gray-700 group-hover:text-[#00baff] transition-colors">{{ $lbl }}</span>
                </label>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── 6. Dashboard Personalizado ── --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <div class="mb-6 pb-3 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-800">6. Dashboard Personalizado</h2>
            <p class="text-xs text-gray-400 mt-1">Escolha quais widgets exibir no painel de controlo do administrador.</p>
        </div>

        <div class="flex flex-col gap-3">
            @php
                $widgetOptions = [
                    'top_freelancers'    => ['label' => 'Top 10 Freelancers por Faturamento',    'letter' => 'a'],
                    'top_creators'       => ['label' => 'Top 10 Criadores por Assinantes',        'letter' => 'b'],
                    'top_products'       => ['label' => 'Produtos mais vendidos (Infoprodutos)',   'letter' => 'c'],
                    'withdrawal_heatmap' => ['label' => 'Mapa de calor de saques por região',     'letter' => 'd'],
                ];
            @endphp
            @foreach($widgetOptions as $value => $opt)
            <label class="flex items-center gap-3 cursor-pointer group">
                <input wire:model="dashboardWidgets" type="checkbox" value="{{ $value }}"
                    class="w-4 h-4 accent-[#00baff] rounded cursor-pointer">
                <span class="text-sm text-gray-700 group-hover:text-[#00baff] transition-colors">
                    <strong class="font-medium">{{ $opt['letter'] }}.</strong> {{ $opt['label'] }}
                </span>
            </label>
            @endforeach
        </div>
    </div>

    {{-- ── 7. Configurações Gerais ── --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <div class="mb-5 pb-3 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-800">7. Configurações Gerais</h2>
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
