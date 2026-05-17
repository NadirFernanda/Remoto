<div class="max-w-5xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-[#00c8ff] to-[#0033cc] rounded-2xl p-6 text-white flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-2xl font-extrabold">Ajuste Manual de Saldo</h2>
            <p class="text-sm text-white/90 mt-1">Credita ou debita a carteira de qualquer utilizador. Toda a acção fica registada na auditoria.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white font-semibold px-4 py-2 rounded-xl">
            ← Voltar
        </a>
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Formulário de ajuste --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-6 space-y-5">
        <h3 class="font-bold text-slate-800 text-lg">Novo Ajuste</h3>

        {{-- Pesquisa de utilizador --}}
        @if (!$userId)
            <div class="space-y-2">
                <label class="block text-sm font-medium text-slate-700">Pesquisar utilizador</label>
                <input
                    wire:model.debounce.400ms="searchUser"
                    type="text"
                    placeholder="Nome ou email..."
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0055ff]"
                />
                @if ($searchUser && $users->count())
                    <div class="border border-gray-200 rounded-xl divide-y divide-gray-100 shadow-sm">
                        @foreach ($users as $u)
                            <button wire:click="selectUser({{ $u->id }}, '{{ addslashes($u->name) }}')"
                                class="w-full text-left px-4 py-2.5 hover:bg-slate-50 text-sm flex justify-between items-center">
                                <span class="font-medium">{{ $u->name }}</span>
                                <span class="text-slate-400 text-xs">{{ $u->email }} · {{ $u->role }}</span>
                            </button>
                        @endforeach
                    </div>
                @elseif (strlen($searchUser) >= 2)
                    <p class="text-sm text-slate-400">Nenhum utilizador encontrado.</p>
                @endif
            </div>
        @else
            <div class="flex items-center justify-between bg-slate-50 border border-slate-200 rounded-xl px-4 py-3">
                <span class="text-sm font-semibold text-slate-800">{{ $userName }}</span>
                <button wire:click="clearUser" class="text-xs text-red-500 hover:underline">Mudar</button>
            </div>
        @endif

        {{-- Tipo de Ajuste --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Tipo de Ajuste</label>
            <select
                wire:model="adjustmentType"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0055ff]"
            >
                <option value="wallet">Carteira (Saldo Disponível)</option>
                <option value="revenue">Receita (Total Ganho)</option>
            </select>
        </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Valor (positivo = crédito, negativo = débito)
                </label>
                <input
                    wire:model="amount"
                    type="number"
                    step="0.01"
                    placeholder="ex: 5000 ou -2000"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0055ff] @error('amount') border-red-400 @enderror"
                />
                @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tipo</label>
                <div class="border border-gray-200 rounded-xl px-3 py-2 text-sm bg-slate-50">
                    @if ($amount > 0)
                        <span class="text-emerald-700 font-semibold">Crédito (+{{ number_format(abs($amount), 2) }} Kz)</span>
                    @elseif ($amount < 0)
                        <span class="text-red-600 font-semibold">Débito (−{{ number_format(abs($amount), 2) }} Kz)</span>
                    @else
                        <span class="text-slate-400">Preenche o valor</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Motivo --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">
                Motivo <span class="text-slate-400 font-normal">(obrigatório, mín. 10 caracteres)</span>
            </label>
            <textarea
                wire:model="reason"
                rows="3"
                placeholder="Descreve o motivo do ajuste de forma clara. Ex: Compensação por falha técnica no pagamento #1234."
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0055ff] @error('reason') border-red-400 @enderror"
            ></textarea>
            @error('reason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <button
            wire:click="applyAdjustment"
            wire:loading.attr="disabled"
            class="bg-[#0055ff] hover:bg-[#0033cc] disabled:opacity-60 text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition"
        >
            <span wire:loading.remove>Aplicar Ajuste</span>
            <span wire:loading>A processar...</span>
        </button>
    </div>

    {{-- Histórico de ajustes --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-5 space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="font-bold text-slate-800">Histórico de Ajustes Manuais</h3>
            <select wire:model="periodFilter" class="border border-gray-200 rounded-xl px-3 py-1.5 text-sm">
                <option value="week">Esta semana</option>
                <option value="month">Este mês</option>
                <option value="year">Este ano</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="p-3 text-left">Utilizador</th>
                        <th class="p-3 text-left">Valor</th>
                        <th class="p-3 text-left">Descrição</th>
                        <th class="p-3 text-left">Data</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr class="border-t border-slate-100">
                            <td class="p-3">{{ $log->user->name ?? '—' }}</td>
                            <td class="p-3 font-semibold {{ $log->valor >= 0 ? 'text-emerald-700' : 'text-red-600' }}">
                                {{ $log->valor >= 0 ? '+' : '' }}{{ number_format($log->valor, 0, ',', '.') }} Kz
                            </td>
                            <td class="p-3 text-slate-600 max-w-xs truncate">{{ $log->descricao }}</td>
                            <td class="p-3 text-slate-500">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-4 text-center text-slate-400">Nenhum ajuste no período.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div>{{ $logs->links() }}</div>
    </div>
</div>
