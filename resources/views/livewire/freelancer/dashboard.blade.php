<div class="container mx-auto px-4 py-8">
    <h2 class="text-2xl font-bold text-cyan-600 mb-6">Dashboard do Freelancer</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-gray-700 mb-2">Saldo disponível</div>
            <div class="text-2xl font-bold text-green-600">{{ number_format($saldo_disponivel, 2, ',', '.') }} Kz</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-gray-700 mb-2">Saldo pendente</div>
            <div class="text-2xl font-bold text-yellow-600">{{ number_format($saldo_pendente, 2, ',', '.') }} Kz</div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-cyan-700 mb-4">Pedidos recebidos</h3>
        <table class="w-full text-left">
            <thead>
                <tr class="text-gray-500 text-sm">
                    <th class="py-2">Título</th>
                    <th class="py-2">Status</th>
                    <th class="py-2">Valor</th>
                    <th class="py-2">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                    <tr class="border-t">
                        <td class="py-2">{{ $service->titulo }}</td>
                        <td class="py-2">
                            <span class="px-2 py-1 rounded text-xs font-bold
                                @if($service->status === 'published') bg-gray-200 text-gray-700
                                @elseif($service->status === 'accepted') bg-cyan-100 text-cyan-700
                                @elseif($service->status === 'in_progress') bg-yellow-100 text-yellow-700
                                @elseif($service->status === 'delivered') bg-blue-100 text-blue-700
                                @elseif($service->status === 'completed') bg-green-100 text-green-700
                                @endif">
                                {{ ucfirst($service->status) }}
                            </span>
                        </td>
                        <td class="py-2">{{ number_format($service->valor, 2, ',', '.') }} Kz</td>
                        <td class="py-2">
                            <a href="#" class="text-cyan-600 hover:underline font-semibold">Ver detalhes</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-4 text-center text-gray-400">Nenhum pedido encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
