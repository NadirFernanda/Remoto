<div class="max-w-6xl mx-auto space-y-6">
    <div class="bg-gradient-to-r from-[#00c8ff] to-[#0033cc] rounded-2xl p-6 text-white flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-2xl font-extrabold">Painel de Reembolsos</h2>
            <p class="text-sm text-white/90 mt-1">Acompanhe pedidos, estados e aprovacoes. Pode aprovar reembolsos parciais.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white font-semibold px-4 py-2 rounded-xl">
            ← Voltar ao Dashboard
        </a>
    </div>

    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if (session('info'))
        <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-xl px-4 py-3 text-sm">{{ session('info') }}</div>
    @endif

    <div class="bg-white border border-gray-200 rounded-2xl p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <input type="text" wire:model.debounce.500ms="search" placeholder="Pesquisar motivo..." class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm" />
            <select wire:model="status" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
                <option value="">Todos status</option>
                <option value="pending">Pendente</option>
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
                    <th class="p-3 text-left">Projecto</th>
                    <th class="p-3 text-left">Motivo</th>
                    <th class="p-3 text-left">Proposta cliente</th>
                    <th class="p-3 text-left">Valor a reembolsar</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Data</th>
                    <th class="p-3 text-left">Provas</th>
                    <th class="p-3 text-left">Acoes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($refunds as $refund)
                    <tr wire:key="refund-{{ $refund->id }}" class="border-t border-slate-100">
                        <td class="p-3">{{ $refund->id }}</td>
                        <td class="p-3">{{ $refund->user->name ?? '-' }}</td>
                        <td class="p-3 max-w-[140px] truncate text-slate-500">{{ $refund->service->titulo ?? '-' }}</td>
                        <td class="p-3 max-w-[160px] truncate">{{ $refund->reason }}</td>
                        <td class="p-3 text-slate-600">
                            {{ $refund->proposta_cliente ? number_format($refund->proposta_cliente, 0, ',', '.') . ' Kz' : '—' }}
                        </td>
                        <td class="p-3">
                            @if ($refund->status === 'pending')
                                <input
                                    type="number"
                                    wire:model="valoresReembolso.{{ $refund->id }}"
                                    min="0"
                                    max="{{ $refund->service?->valor ?? 9999999 }}"
                                    step="0.01"
                                    class="w-28 border border-gray-200 rounded-lg px-2 py-1 text-xs"
                                />
                                <span class="text-slate-400 text-xs ml-1">Kz</span>
                            @else
                                {{ $refund->valor_reembolso !== null ? number_format($refund->valor_reembolso, 0, ',', '.') . ' Kz' : '—' }}
                            @endif
                        </td>
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
                                <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($refund->proof) }}" target="_blank" class="text-[#0055ff] hover:underline text-xs">Ver ficheiro</a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="p-3">
                            @if($refund->status === 'pending')
                                <button wire:click="approve({{ $refund->id }})"
                                    wire:loading.attr="disabled"
                                    class="px-3 py-1 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold">
                                    Aprovar
                                </button>
                                <button wire:click="reject({{ $refund->id }})"
                                    class="px-3 py-1 rounded-lg bg-red-500 hover:bg-red-600 text-white text-xs font-semibold ml-2">
                                    Rejeitar
                                </button>
                            @else
                                <span class="text-slate-400 text-xs">Processado</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="p-4 text-center text-slate-500">Nenhum reembolso encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $refunds->links() }}</div>
</div>
