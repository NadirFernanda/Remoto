<div class="max-w-5xl mx-auto space-y-6">
    <div class="bg-gradient-to-r from-[#00baff] to-[#0095cc] rounded-2xl p-6 text-white">
        <a href="{{ route('freelancer.dashboard') }}" class="inline-flex items-center text-white/90 hover:text-white text-sm font-semibold mb-3">
            &larr; Voltar ao dashboard
        </a>
        <h2 class="text-2xl font-extrabold">Configurações da Conta</h2>
        <p class="text-sm text-white/90 mt-1">Gerencie preferências de notificação e segurança da conta.</p>
    </div>

    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white border border-gray-200 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-slate-900">Notificações</h3>
            <p class="text-sm text-slate-500 mt-1">Escolha se quer receber e-mails de novos projectos.</p>

            <div class="mt-4">
                <label class="block text-sm font-semibold text-slate-700 mb-2">E-mails de novos projectos</label>
                <select wire:model="notify_new_project_email" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-[#00baff]/40 focus:border-[#00baff]">
                    <option value="1">Activado</option>
                    <option value="0">Desactivado</option>
                </select>
            </div>
        </div>

        <div class="bg-white border border-amber-200 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-amber-700">Desativar Conta</h3>
            <p class="text-sm text-slate-500 mt-1">Suspende acesso imediato. Reativação apenas via suporte.</p>

            <div class="mt-4">
                <input wire:model.defer="deactivatePassword" type="password" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm" placeholder="Confirme a palavra-passe">
                @error('deactivatePassword')
                    <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <button wire:click="deactivateAccount" class="mt-4 inline-flex items-center rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-4 py-2 transition">
                Desativar conta
            </button>
        </div>
    </div>

    <div class="bg-white border border-red-200 rounded-2xl p-6">
        <h3 class="text-lg font-bold text-red-700">Remover Conta</h3>
        <p class="text-sm text-slate-500 mt-1">Ação permanente e irreversível. Digite REMOVER para confirmar.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4">
            <div>
                <input wire:model.defer="deletePassword" type="password" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm" placeholder="Confirme a palavra-passe">
                @error('deletePassword')
                    <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <input wire:model.defer="deleteConfirmation" type="text" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm" placeholder="Digite REMOVER">
                @error('deleteConfirmation')
                    <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <button wire:click="deleteAccount" class="mt-4 inline-flex items-center rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 transition">
            Remover conta
        </button>
    </div>
</div>
