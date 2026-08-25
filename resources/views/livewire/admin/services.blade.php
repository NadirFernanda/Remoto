<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-sky-50/40 pb-16"
    x-data="{
        selected: [],
        toggleAll(checked, ids) {
            this.selected = checked ? [...ids] : [];
        },
        bulkUrl() {
            return '{{ route('admin.services.receipts.bulk') }}?ids=' + this.selected.join(',');
        }
    }">

    {{-- ── Header ── --}}
    <div class="bg-white border-b border-slate-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#00c8ff] to-blue-600 flex items-center justify-center shadow-lg shadow-sky-200">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800 leading-tight">Serviços</h1>
                    <p class="text-sm text-slate-500">Acompanhe o estado dos projectos e entregas</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 pt-8">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4 mb-6">
            <div class="flex flex-col md:flex-row gap-3">
                <div class="relative flex-1 min-w-[220px]">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/></svg>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Pesquisar por título..."
                        class="w-full pl-9 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition">
                </div>
                <select wire:model.live="statusFilter"
                    class="px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition text-slate-700 min-w-[180px]">
                    <option value="">Todos os status</option>
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
        </div>

        {{-- Bulk action bar --}}
        <div x-show="selected.length > 0"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="bg-gradient-to-r from-[#00c8ff] to-[#0033cc] rounded-2xl px-5 py-3.5 mb-4 flex items-center justify-between gap-4 shadow-md shadow-sky-200/40">
            <span class="text-white text-sm font-semibold" x-text="selected.length + ' serviço(s) seleccionado(s)'"></span>
            <div class="flex items-center gap-3">
                <button @click="selected = []"
                    class="text-white/70 hover:text-white text-xs font-medium transition">
                    Limpar
                </button>
                <a :href="bulkUrl()" target="_blank"
                    class="inline-flex items-center gap-2 bg-white text-[#0055ff] text-sm font-bold px-4 py-2 rounded-xl hover:bg-sky-50 transition shadow">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Imprimir Recibos Seleccionados
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <span class="text-sm font-semibold text-slate-700">Lista de serviços</span>
                <span class="text-xs text-slate-400">{{ $services->total() }} resultado(s)</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="py-3.5 px-4 w-10">
                                <input type="checkbox"
                                    class="w-4 h-4 rounded border-slate-300 text-sky-500 focus:ring-sky-300 cursor-pointer"
                                    @change="toggleAll($event.target.checked, [{{ $services->pluck('id')->implode(',') }}])">
                            </th>
                            <th class="py-3.5 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">ID</th>
                            <th class="py-3.5 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Título</th>
                            <th class="py-3.5 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Cliente</th>
                            <th class="py-3.5 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="py-3.5 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Taxa</th>
                            <th class="py-3.5 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Criado em</th>
                            <th class="py-3.5 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Recibo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($services as $service)
                        <tr class="hover:bg-sky-50/30 transition-colors" :class="selected.includes({{ $service->id }}) ? 'bg-sky-50/60' : ''">
                            <td class="py-3 px-4">
                                <input type="checkbox" :value="{{ $service->id }}" x-model="selected"
                                    class="w-4 h-4 rounded border-slate-300 text-sky-500 focus:ring-sky-300 cursor-pointer">
                            </td>
                            <td class="py-3 px-4 text-slate-400">#{{ $service->id }}</td>
                            <td class="py-3 px-4 font-medium text-slate-800">{{ $service->titulo }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $service->cliente->name ?? '—' }}</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ match($service->status) {
                                        'completed', 'delivered' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                        'in_progress', 'accepted' => 'bg-sky-50 text-sky-700 border border-sky-200',
                                        'cancelled' => 'bg-red-50 text-red-700 border border-red-200',
                                        'negotiating' => 'bg-violet-50 text-violet-700 border border-violet-200',
                                        'em_moderacao', 'payment_pending' => 'bg-orange-50 text-orange-700 border border-orange-200',
                                        'draft' => 'bg-slate-100 text-slate-500 border border-slate-200',
                                        default => 'bg-amber-50 text-amber-700 border border-amber-200',
                                    } }}">
                                    {{ match($service->status) {
                                        'draft'           => 'Rascunho',
                                        'payment_pending' => 'Pagamento Pendente',
                                        'published'       => 'Publicado',
                                        'negotiating'     => 'Em Negociação',
                                        'accepted'        => 'Aceite',
                                        'in_progress'     => 'Em Andamento',
                                        'em_moderacao'    => 'Em Moderação',
                                        'delivered'       => 'Entregue',
                                        'completed'       => 'Concluído',
                                        'cancelled'       => 'Cancelado',
                                        default           => $service->status,
                                    } }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-700">{{ money_aoa($service->taxa ?? 0) }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ $service->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4">
                                @php
                                    $paid = ['published','accepted','negotiating','in_progress','em_andamento','em andamento','delivered','revision_requested','completed','concluido','cancelled','cancelado'];
                                @endphp
                                @if(in_array($service->status, $paid))
                                    <a href="{{ route('admin.service.receipt', $service) }}" target="_blank"
                                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-sky-600 hover:text-sky-800 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                        Ver
                                    </a>
                                @else
                                    <span class="text-xs text-slate-300">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-slate-400">Nenhum serviço encontrado.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                {{ $services->links() }}
            </div>
        </div>
    </div>
</div>
