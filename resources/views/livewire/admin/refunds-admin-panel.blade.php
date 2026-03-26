<div class="max-w-6xl mx-auto space-y-6">
    <div class="bg-gradient-to-r from-[#00baff] to-[#0095cc] rounded-2xl p-6 text-white flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-2xl font-extrabold">Painel de Reembolsos</h2>
            <p class="text-sm text-white/90 mt-1">Acompanhe pedidos, estados e aprovacoes.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white font-semibold px-4 py-2 rounded-xl">
            ← Voltar ao Dashboard
        </a>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <input type="text" wire:model.debounce.500ms="search" placeholder="Pesquisar motivo..." class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm" />
            <select wire:model="status" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
                <option value="">Todos status</option>
                <option value="pendente">Pendente</option>
                <option value="aprovado">Aprovado</option>
                <option value="rejeitado">Rejeitado</option>
            </select>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="p-3 text-left">ID</th>
                    <th class="p-3 text-left">Cliente</th>
                    <th class="p-3 text-left">Motivo</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Data</th>
                    <th class="p-3 text-left">Provas</th>
                    <th class="p-3 text-left">Acoes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($refunds as $refund)
                    <tr class="border-t border-slate-100">
                        <td class="p-3">{{ $refund->id }}</td>
                        <td class="p-3">{{ $refund->user->name ?? '-' }}</td>
                        <td class="p-3">{{ $refund->reason }}</td>
                        <td class="p-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{
                                $refund->status === 'aprovado' ? 'bg-emerald-100 text-emerald-700' :
                                ($refund->status === 'rejeitado' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700')
                            }}">
                                {{ ucfirst($refund->status) }}
                            </span>
                        </td>
                        <td class="p-3">{{ $refund->created_at->format('d/m/Y H:i') }}</td>
                        <td class="p-3">
                            @if($refund->proof)
                                <a href="{{ asset('storage/'.$refund->proof) }}" target="_blank" class="text-[#00baff] hover:underline">Ver ficheiro</a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="p-3">
                            @if($refund->status === 'pendente')
                                <button wire:click="approve({{ $refund->id }})" class="px-3 py-1 rounded-lg bg-emerald-500 text-white text-xs font-semibold">Aprovar</button>
                                <button wire:click="reject({{ $refund->id }})" class="px-3 py-1 rounded-lg bg-red-500 text-white text-xs font-semibold ml-2">Rejeitar</button>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-4 text-center text-slate-500">Nenhum reembolso encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $refunds->links() }}</div>
</div>
