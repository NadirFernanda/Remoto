<div class="min-h-screen pb-16">

    {{-- Header --}}
    <div class="rounded-2xl p-6 text-white mb-8">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold leading-tight">Histórico de Pagamentos</h1>
                    <p class="text-sm text-white/80 mt-0.5">Consulte todas as suas transações e descarregue recibos</p>
                </div>
            </div>
            <a href="{{ route('client.dashboard') }}"
                class="flex-shrink-0 inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Voltar
            </a>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-end gap-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 flex-1">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Status</label>
                    <select wire:model="filter_status"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-700 bg-white focus:ring-2 focus:ring-sky-200 focus:border-sky-400 outline-none transition">
                        <option value="">Todos</option>
                        <option value="draft">Rascunho</option>
                        <option value="payment_pending">Pagamento Pendente</option>
                        <option value="published">Publicado</option>
                        <option value="negotiating">Em Negociação</option>
                        <option value="accepted">Aceite</option>
                        <option value="in_progress">Em Andamento</option>
                        <option value="em_moderacao">Em Moderação</option>
                        <option value="delivered">Entregue</option>
                        <option value="completed">Concluído</option>
                        <option value="cancelled">Cancelado</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tipo</label>
                    <select wire:model="filter_type"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-700 bg-white focus:ring-2 focus:ring-sky-200 focus:border-sky-400 outline-none transition">
                        <option value="">Todos</option>
                        <option value="entrada">Entrada</option>
                        <option value="saida">Saída</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Data Inicial</label>
                    <input type="date" wire:model="filter_date_start"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-700 bg-white focus:ring-2 focus:ring-sky-200 focus:border-sky-400 outline-none transition"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Data Final</label>
                    <input type="date" wire:model="filter_date_end"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-700 bg-white focus:ring-2 focus:ring-sky-200 focus:border-sky-400 outline-none transition"/>
                </div>
            </div>

            <a href="{{ route('client.finance.exportCsv', [
                    'status'     => $filter_status,
                    'type'       => $filter_type,
                    'date_start' => $filter_date_start,
                    'date_end'   => $filter_date_end,
                ]) }}"
                target="_blank"
                class="flex items-center gap-2 hover: hover: text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-md shadow-sky-200/40 transition-all whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exportar CSV
            </a>
        </div>
    </div>

    {{-- Tabela --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="py-3.5 px-5 font-semibold text-slate-600 text-xs uppercase tracking-wide">#</th>
                        <th class="py-3.5 px-5 font-semibold text-slate-600 text-xs uppercase tracking-wide">Título</th>
                        <th class="py-3.5 px-5 font-semibold text-slate-600 text-xs uppercase tracking-wide">Valor</th>
                        <th class="py-3.5 px-5 font-semibold text-slate-600 text-xs uppercase tracking-wide">Status</th>
                        <th class="py-3.5 px-5 font-semibold text-slate-600 text-xs uppercase tracking-wide">Data</th>
                        <th class="py-3.5 px-5 font-semibold text-slate-600 text-xs uppercase tracking-wide">Recibo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($transactions as $t)
                        @php
                            $statusLabel = match($t->status) {
                                'draft'           => 'Rascunho',
                                'payment_pending' => 'Pag. Pendente',
                                'published'       => 'Publicado',
                                'negotiating'     => 'Em Negociação',
                                'accepted'        => 'Aceite',
                                'in_progress'     => 'Em Andamento',
                                'em_moderacao'    => 'Em Moderação',
                                'delivered'       => 'Entregue',
                                'completed'       => 'Concluído',
                                'cancelled'       => 'Cancelado',
                                default           => ucfirst($t->status),
                            };
                            $statusStyle = match($t->status) {
                                'completed'       => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
                                'in_progress'     => 'bg-sky-50 text-sky-700 ring-1 ring-sky-200',
                                'published'       => 'bg-blue-50 text-blue-700 ring-1 ring-blue-200',
                                'accepted'        => 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200',
                                'negotiating'     => 'bg-violet-50 text-violet-700 ring-1 ring-violet-200',
                                'delivered'       => 'bg-teal-50 text-teal-700 ring-1 ring-teal-200',
                                'em_moderacao'    => 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
                                'payment_pending' => 'bg-orange-50 text-orange-700 ring-1 ring-orange-200',
                                'cancelled'       => 'bg-red-50 text-red-600 ring-1 ring-red-200',
                                'draft'           => 'bg-slate-100 text-slate-500 ring-1 ring-slate-200',
                                default           => 'bg-slate-100 text-slate-500 ring-1 ring-slate-200',
                            };
                        @endphp
                        <tr class="hover:bg-sky-50/30 transition-colors">
                            <td class="py-4 px-5 text-slate-400 font-mono text-xs">#{{ $t->id }}</td>
                            <td class="py-4 px-5 font-medium text-slate-800">{{ $t->titulo ?? '—' }}</td>
                            <td class="py-4 px-5 font-semibold text-slate-800">{{ money_aoa($t->valor) }}</td>
                            <td class="py-4 px-5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusStyle }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="py-4 px-5 text-slate-500">{{ $t->created_at->format('d/m/Y') }}</td>
                            <td class="py-4 px-5">
                                <a href="{{ route('client.receipt.download', $t->id) }}" target="_blank"
                                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-sky-600 hover:text-sky-800 hover:underline transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Baixar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-slate-500 text-sm font-medium">Nenhuma transação encontrada</p>
                                    <p class="text-slate-400 text-xs">Tente ajustar os filtros acima</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
