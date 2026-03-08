<div>
    <h1 class="text-2xl font-bold mb-6 text-red-600">Meus Pedidos de Reembolso</h1>
    <table class="min-w-full bg-white rounded-2xl border border-gray-200">
        <thead class="bg-red-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Projeto</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Motivo</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Status</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Data</th>
            </tr>
        </thead>
        <tbody>
            @forelse($refunds as $refund)
                <tr class="border-b">
                    <td class="px-4 py-2">{{ $refund->service->titulo ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $refund->reason }}</td>
                    <td class="px-4 py-2">
                        @if($refund->status === 'pending')
                            <span class="inline-block px-2 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs font-semibold">Em análise</span>
                        @elseif($refund->status === 'approved')
                            <span class="inline-block px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">Aprovado</span>
                        @elseif($refund->status === 'rejected')
                            <span class="inline-block px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">Recusado</span>
                        @else
                            <span class="inline-block px-2 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-semibold">{{ ucfirst($refund->status) }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-2">{{ $refund->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center py-4 text-[#888]">Nenhum pedido de reembolso encontrado.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
