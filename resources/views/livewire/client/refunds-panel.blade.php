<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-rose-50/40 pb-16">

    {{-- ── Hero Header ── --}}
    <div class="bg-white border-b border-slate-100 shadow-sm">
        <div class="max-w-5xl mx-auto px-6 py-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-rose-500 to-red-600 flex items-center justify-center shadow-lg shadow-rose-200">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800 leading-tight">Meus Pedidos de Reembolso</h1>
                <p class="text-sm text-slate-500">Acompanhe o estado dos pedidos enviados</p>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-6 pt-8">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <span class="text-sm font-semibold text-slate-700">Lista de reembolsos</span>
                <span class="text-xs text-slate-400">{{ $refunds->total() }} resultado(s)</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-rose-50/60">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Projecto</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Motivo</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($refunds as $refund)
                            <tr class="hover:bg-rose-50/30 transition-colors">
                                <td class="px-4 py-3 text-slate-700">{{ $refund->service->titulo ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $refund->reason }}</td>
                                <td class="px-4 py-3">
                                    @if($refund->status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-semibold border border-amber-200">Em análise</span>
                                    @elseif($refund->status === 'approved')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-200">Aprovado</span>
                                    @elseif($refund->status === 'rejected')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-red-50 text-red-700 text-xs font-semibold border border-red-200">Recusado</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold border border-slate-200">{{ ucfirst($refund->status) }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-500">{{ $refund->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-10 text-slate-400">Nenhum pedido de reembolso encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $refunds->links() }}
            </div>
        </div>
    </div>
</div>
