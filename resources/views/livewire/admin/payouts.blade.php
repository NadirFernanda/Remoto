<div>
    {{-- ─── Filters ────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 mb-5">
        <input wire:model.live.debounce.400ms="search" type="text"
            placeholder="Pesquisar utilizador..."
            class="border border-gray-200 rounded-[10px] px-3 py-2 text-sm w-full sm:w-56 focus:outline-none focus:ring-2 focus:ring-[#0055ff]/30 focus:border-[#0055ff]">
        <div class="flex gap-2">
            @foreach(['week' => 'Semana', 'month' => 'Mês', 'year' => 'Ano'] as $val => $label)
                <button wire:click="$set('period', '{{ $val }}')"
                    class="flex-1 sm:flex-none px-3 py-1.5 rounded-[10px] text-xs border transition
                        {{ $period === $val
                            ? 'bg-[#0055ff] text-white border-[#0055ff]'
                            : 'bg-white text-gray-600 border-gray-200 hover:border-[#0055ff] hover:text-[#0055ff]' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 text-green-800 rounded-[10px] border border-green-200 text-sm">{{ session('success') }}</div>
    @endif

    {{-- ─── Pendentes ──────────────────────────────────────────── --}}
    @if($pendentes->count() > 0)
    <div class="mb-6">
        <div class="flex items-center justify-between flex-wrap gap-2 mb-3">
            <h3 class="text-sm font-bold text-orange-700 flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-orange-100 text-orange-700 text-xs font-bold">{{ $pendentes->count() }}</span>
                Saques Pendentes — aguardam aprovação
            </h3>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.payouts.bank-file.excel') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[10px] text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H8a2 2 0 01-2-2V5a2 2 0 012-2h6l6 6v11a2 2 0 01-2 2z"/></svg>
                    <span class="hidden sm:inline">Exportar para Excel (Banco)</span>
                    <span class="sm:hidden">Excel</span>
                </a>
                <a href="{{ route('admin.payouts.bank-file.csv') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[10px] text-xs font-semibold bg-white text-gray-600 border border-gray-200 hover:border-emerald-300 hover:text-emerald-700 transition">
                    CSV
                </a>
            </div>
        </div>

        {{-- Mobile: cartões empilhados --}}
        <div class="grid gap-3 lg:hidden">
            @foreach($pendentes as $log)
            @php $bank = $log->user?->freelancerProfile; @endphp
            <div wire:key="payout-card-{{ $log->id }}" class="rounded-2xl border border-orange-200 bg-white p-4">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-gray-800 truncate">{{ $log->user->name ?? '—' }}</p>
                        <p class="text-xs text-gray-400">{{ $log->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <p class="text-base font-extrabold text-orange-600 whitespace-nowrap">{{ money_aoa(abs($log->valor), false) }}</p>
                </div>

                <p class="text-xs text-gray-500 mb-3 leading-relaxed">{{ $log->descricao ?? '—' }}</p>

                <div class="rounded-xl bg-gray-50 border border-gray-100 p-3 mb-3">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide mb-1">Conta bancária</p>
                    @if($bank?->hasBankAccount())
                        <p class="text-xs font-semibold text-gray-700">{{ $bank->bank_name }}</p>
                        <p class="text-xs text-gray-600">{{ $bank->bank_account_holder }}</p>
                        <p class="text-xs font-mono text-gray-400 break-all">{{ $bank->bank_account_number }}</p>
                    @else
                        <span class="text-xs text-red-500 font-semibold">Sem conta registada</span>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    <button wire:click="aprovarSaque({{ $log->id }})"
                        wire:confirm="Aprovar este saque de {{ money_aoa(abs($log->valor), false) }}?"
                        class="flex-1 py-2 rounded-[10px] bg-green-100 text-green-700 border border-green-300 hover:bg-green-600 hover:text-white text-xs font-semibold transition inline-flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 13l4 4L19 3"/></svg>
                        Aprovar
                    </button>
                    <button wire:click="rejeitarSaque({{ $log->id }})"
                        wire:confirm="Rejeitar e devolver o valor ao freelancer?"
                        class="flex-1 py-2 rounded-[10px] bg-red-100 text-red-700 border border-red-300 hover:bg-red-600 hover:text-white text-xs font-semibold transition inline-flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 18L18 6M6 6l12 12"/></svg>
                        Rejeitar
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Desktop: tabela --}}
        <div class="hidden lg:block rounded-2xl border border-orange-200 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-orange-50">
                    <tr>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Freelancer</th>
                        <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 uppercase">Valor Solicitado</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Descrição</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Conta bancária</th>
                        <th class="py-3 px-4 text-center text-xs font-semibold text-gray-500 uppercase">Acção</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-orange-100">
                    @foreach($pendentes as $log)
                    @php $bank = $log->user?->freelancerProfile; @endphp
                    <tr wire:key="payout-{{ $log->id }}" class="bg-white hover:bg-orange-50">
                        <td class="py-3 px-4 text-xs text-gray-400 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3 px-4 text-sm font-medium text-gray-700">{{ $log->user->name ?? '—' }}</td>
                        <td class="py-3 px-4 text-sm text-right font-bold text-orange-600">{{ money_aoa(abs($log->valor), false) }}</td>
                        <td class="py-3 px-4 text-xs text-gray-500">{{ $log->descricao ?? '—' }}</td>
                        <td class="py-3 px-4 text-xs text-gray-600">
                            @if($bank?->hasBankAccount())
                                <div class="font-semibold">{{ $bank->bank_name }}</div>
                                <div>{{ $bank->bank_account_holder }}</div>
                                <div class="font-mono text-gray-400">{{ $bank->bank_account_number }}</div>
                            @else
                                <span class="text-red-500 font-semibold">Sem conta registada</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button wire:click="aprovarSaque({{ $log->id }})"
                                    wire:confirm="Aprovar este saque de {{ money_aoa(abs($log->valor), false) }}?"
                                    class="px-3 py-1 rounded-[8px] bg-green-100 text-green-700 border border-green-300 hover:bg-green-600 hover:text-white text-xs font-semibold transition inline-flex items-center justify-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 13l4 4L19 3"/></svg>
                                    Aprovar
                                </button>
                                <button wire:click="rejeitarSaque({{ $log->id }})"
                                    wire:confirm="Rejeitar e devolver o valor ao freelancer?"
                                    class="px-3 py-1 rounded-[8px] bg-red-100 text-red-700 border border-red-300 hover:bg-red-600 hover:text-white text-xs font-semibold transition inline-flex items-center justify-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 18L18 6M6 6l12 12"/></svg>
                                    Rejeitar
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ─── KPI ────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-6 flex items-center gap-3 flex-wrap">
        <span class="text-xs text-gray-500">Total Aprovado (período):</span>
        <span class="text-xl font-bold text-red-500">{{ money_aoa($totalAprovado, false) }}</span>
    </div>

    {{-- ─── Histórico ──────────────────────────────────────────── --}}
    <h3 class="text-sm font-semibold text-gray-600 mb-3">Histórico de Saques</h3>

    {{-- Mobile: cartões empilhados --}}
    <div class="grid gap-3 lg:hidden">
        @forelse($logs as $log)
            <div class="rounded-2xl border border-gray-200 bg-white p-4">
                <div class="flex items-start justify-between gap-3 mb-1.5">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $log->user->name ?? '—' }}</p>
                        <p class="text-xs text-gray-400">{{ $log->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <p class="text-base font-bold whitespace-nowrap {{ $log->tipo === 'saque_rejeitado' ? 'text-gray-400 line-through' : 'text-red-500' }}">
                        {{ money_aoa(abs($log->valor), false) }}
                    </p>
                </div>
                <div class="mb-2">
                    @if($log->tipo === 'saque_aprovado')
                        <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-semibold">Aprovado</span>
                    @elseif($log->tipo === 'saque_rejeitado')
                        <span class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-semibold">Rejeitado</span>
                    @endif
                </div>
                <p class="text-xs text-gray-500 leading-relaxed">{{ $log->descricao ?? '—' }}</p>
            </div>
        @empty
            <div class="rounded-2xl border border-gray-200 bg-white py-10 text-center text-sm text-gray-400">
                Sem saques para o período seleccionado.
            </div>
        @endforelse
    </div>

    {{-- Desktop: tabela --}}
    <div class="hidden lg:block rounded-2xl border border-gray-200 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Freelancer</th>
                    <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 uppercase">Valor</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Estado</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Descrição</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 text-xs text-gray-400 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">{{ $log->user->name ?? '—' }}</td>
                        <td class="py-3 px-4 text-sm text-right font-medium
                            {{ $log->tipo === 'saque_rejeitado' ? 'text-gray-400 line-through' : 'text-red-500' }}">
                            {{ money_aoa(abs($log->valor), false) }}
                        </td>
                        <td class="py-3 px-4">
                            @if($log->tipo === 'saque_aprovado')
                                <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-semibold">Aprovado</span>
                            @elseif($log->tipo === 'saque_rejeitado')
                                <span class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-semibold">Rejeitado</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-xs text-gray-500">{{ $log->descricao ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-sm text-gray-400">Sem saques para o período seleccionado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $logs->links() }}</div>
</div>
