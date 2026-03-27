<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-sky-50/40 pb-16">

    {{-- ── Header ── --}}
    <div class="bg-white border-b border-slate-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#00baff] to-blue-600 flex items-center justify-center shadow-lg shadow-sky-200">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800 leading-tight">Serviços</h1>
                    <p class="text-sm text-slate-500">Acompanhe o estado dos pedidos e entregas</p>
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
                    <option value="published">Publicado</option>
                    <option value="in_progress">Em andamento</option>
                    <option value="delivered">Entregue</option>
                    <option value="cancelled">Cancelado</option>
                </select>
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
                        <tr class="bg-slate-50/80">
                            <th class="py-3.5 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">ID</th>
                            <th class="py-3.5 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Título</th>
                            <th class="py-3.5 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Cliente</th>
                            <th class="py-3.5 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="py-3.5 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Taxa</th>
                            <th class="py-3.5 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Criado em</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($services as $service)
                        <tr class="hover:bg-sky-50/30 transition-colors">
                            <td class="py-3 px-4 text-slate-400">#{{ $service->id }}</td>
                            <td class="py-3 px-4 font-medium text-slate-800">{{ $service->titulo }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $service->cliente->name ?? '—' }}</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $service->status === 'delivered' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' :
                                       ($service->status === 'in_progress' ? 'bg-sky-50 text-sky-700 border border-sky-200' :
                                       ($service->status === 'cancelled' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-amber-50 text-amber-700 border border-amber-200')) }}">
                                    {{ match($service->status) {
                                        'published' => 'Publicado',
                                        'in_progress' => 'Em andamento',
                                        'delivered' => 'Entregue',
                                        'cancelled' => 'Cancelado',
                                        default => $service->status
                                    } }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-700">{{ money_aoa($service->taxa ?? 0) }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ $service->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400">Nenhum serviço encontrado.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $services->links() }}
            </div>
        </div>
    </div>
</div>
