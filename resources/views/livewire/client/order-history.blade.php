<div class="p-6 bg-white rounded shadow max-w-3xl mx-auto mt-8">
    <h2 class="text-2xl font-bold mb-4">Histórico de Pedidos</h2>
    <table class="w-full text-left border">
        <thead>
            <tr class="bg-gray-100">
                <th class="py-2 px-3">ID</th>
                <th class="py-2 px-3">Título</th>
                <th class="py-2 px-3">Status</th>
                <th class="py-2 px-3">Data</th>
                <th class="py-2 px-3">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td class="py-2 px-3">{{ $order->id }}</td>
                    <td class="py-2 px-3">{{ $order->titulo ?? '-' }}</td>
                    <td class="py-2 px-3">
                        <span class="inline-block px-3 py-1 rounded-full text-white {{ $order->status === 'concluido' ? 'bg-green-500' : ($order->status === 'em andamento' ? 'bg-yellow-500' : 'bg-gray-400') }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="py-2 px-3">{{ $order->created_at->format('d/m/Y') }}</td>
                    <td class="py-2 px-3">
                        <a href="{{ route('client.service.cancel', $order->id) }}" class="text-blue-600 hover:underline">Detalhes</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-2 px-3 text-gray-500">Nenhum pedido encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
