<div>
    {{-- ─── Filtros ─────────────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-end gap-3 mb-6">
        {{-- Período rápido --}}
        <div class="flex items-center gap-2">
            <span class="text-xs text-gray-500">Período:</span>
            @foreach(['week' => 'Semana', 'month' => 'Mês', 'year' => 'Ano'] as $val => $lbl)
                <button wire:click="$set('period', '{{ $val }}')"
                    class="px-3 py-1.5 rounded-[10px] text-xs border transition
                        {{ $period === $val ? 'bg-[#00baff] text-white border-[#00baff]' : 'bg-white text-gray-600 border-gray-200 hover:border-[#00baff] hover:text-[#00baff]' }}">
                    {{ $lbl }}</button>
            @endforeach
        </div>
        {{-- Status --}}
        <select wire:model.live="status" class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs text-gray-600 focus:ring-2 focus:ring-[#00baff] focus:outline-none">
            <option value="">Todos os status</option>
            <option value="saque_solicitado">Pendentes</option>
            <option value="saque_aprovado">Aprovados</option>
            <option value="saque_rejeitado">Rejeitados</option>
        </select>
        {{-- Pesquisa --}}
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nome ou email…"
            class="border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-600 focus:ring-2 focus:ring-[#00baff] focus:outline-none w-48" />
        {{-- Datas --}}
        <input type="date" wire:model.live="dateStart" class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs text-gray-600 focus:ring-2 focus:ring-[#00baff] focus:outline-none" />
        <span class="text-xs text-gray-400">a</span>
        <input type="date" wire:model.live="dateEnd" class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs text-gray-600 focus:ring-2 focus:ring-[#00baff] focus:outline-none" />
        {{-- Export --}}
        <div class="flex items-center gap-2 ml-auto">
            <a href="{{ route('admin.reports.withdrawals.csv', ['period' => $period, 'status' => $status, 'search' => $search, 'date_start' => $dateStart, 'date_end' => $dateEnd]) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs border border-gray-200 bg-white text-gray-600 hover:border-[#00baff] hover:text-[#00baff] transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                CSV
            </a>
            <a href="{{ route('admin.reports.withdrawals.pdf', ['period' => $period, 'status' => $status, 'search' => $search, 'date_start' => $dateStart, 'date_end' => $dateEnd]) }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs border border-gray-200 bg-white text-gray-600 hover:border-red-500 hover:text-red-600 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                PDF
            </a>
        </div>
    </div>

    {{-- ─── Resumo por Status ────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        @php
            $pendentes  = $resumo['saque_solicitado'] ?? null;
            $aprovados  = $resumo['saque_aprovado']   ?? null;
            $rejeitados = $resumo['saque_rejeitado']  ?? null;
        @endphp
        <div class="bg-white rounded-2xl border border-yellow-100 p-5">
            <p class="text-xs text-yellow-600 font-medium uppercase tracking-wide mb-1">Pendentes</p>
            <p class="text-xl font-bold text-yellow-700">{{ $pendentes->total_pedidos ?? 0 }} pedidos</p>
            <p class="text-sm text-yellow-500 mt-1">{{ money_aoa($pendentes->total_valor ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-green-100 p-5">
            <p class="text-xs text-green-600 font-medium uppercase tracking-wide mb-1">Aprovados</p>
            <p class="text-xl font-bold text-green-700">{{ $aprovados->total_pedidos ?? 0 }} pedidos</p>
            <p class="text-sm text-green-500 mt-1">{{ money_aoa($aprovados->total_valor ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-red-100 p-5">
            <p class="text-xs text-red-500 font-medium uppercase tracking-wide mb-1">Rejeitados</p>
            <p class="text-xl font-bold text-red-600">{{ $rejeitados->total_pedidos ?? 0 }} pedidos</p>
            <p class="text-sm text-red-400 mt-1">{{ money_aoa($rejeitados->total_valor ?? 0) }}</p>
        </div>
    </div>

    {{-- ─── Tabela ───────────────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-gray-200 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Nome</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Origem</th>
                    <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 uppercase">Valor</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                    @php
                        $statusColor = match($log->tipo) {
                            'saque_solicitado' => 'bg-yellow-100 text-yellow-700',
                            'saque_aprovado'   => 'bg-green-100 text-green-700',
                            'saque_rejeitado'  => 'bg-red-100 text-red-600',
                            default            => 'bg-gray-100 text-gray-600',
                        };
                        $statusLabel = match($log->tipo) {
                            'saque_solicitado' => 'Pendente',
                            'saque_aprovado'   => 'Aprovado',
                            'saque_rejeitado'  => 'Rejeitado',
                            default            => $log->tipo,
                        };
                        $origem = match(optional($log->user)->role) {
                            'freelancer' => 'Freelancer',
                            'creator'    => 'Creator',
                            'cliente'    => 'Cliente',
                            default      => ucfirst(optional($log->user)->role ?? '—'),
                        };
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 text-sm text-gray-800">{{ optional($log->user)->name ?? '—' }}</td>
                        <td class="py-3 px-4 text-xs text-gray-500">{{ optional($log->user)->email ?? '—' }}</td>
                        <td class="py-3 px-4 text-xs text-gray-500 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3 px-4">
                            <span class="text-[11px] px-2 py-0.5 rounded-full font-medium {{ $statusColor }}">{{ $statusLabel }}</span>
                        </td>
                        <td class="py-3 px-4 text-xs text-gray-500">{{ $origem }}</td>
                        <td class="py-3 px-4 text-sm text-right font-semibold text-gray-800">{{ money_aoa(abs($log->valor)) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-sm text-gray-400">Nenhum saque encontrado para o período seleccionado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $logs->links() }}</div>
</div>
