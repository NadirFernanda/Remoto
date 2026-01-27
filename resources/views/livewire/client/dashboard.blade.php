<div class="p-6 bg-gray-50 rounded shadow max-w-6xl mx-auto mt-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <h2 class="text-2xl font-bold text-cyan-700">Painel do Cliente</h2>
        <div class="flex gap-2">
            <a href="{{ route('client.profile') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded transition-all">Perfil</a>
            <a href="{{ route('client.settings') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded transition-all">Configurações</a>
            <a href="{{ route('client.publish') }}" class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-2 px-4 rounded transition-all">+ Novo Pedido</a>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="md:col-span-1">
            @livewire('client.finance-panel')
        </div>
        <div class="md:col-span-1">
            @livewire('notification-panel')
        </div>
        <div class="md:col-span-1">
            @livewire('client.review-panel')
        </div>
    </div>
    <h3 class="text-lg font-semibold mb-2">Seus últimos pedidos</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-left border bg-white rounded">
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
                            <a href="{{ route('freelancer.service.review', $order->id) }}" class="text-blue-600 hover:underline">Detalhes</a>
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
</div>
