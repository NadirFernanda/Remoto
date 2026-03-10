<div>
    <div class="flex flex-col md:flex-row gap-3 mb-6">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Pesquisar por título..." class="border rounded px-3 py-2 text-sm w-full md:w-72">
        <select wire:model.live="statusFilter" class="border rounded px-3 py-2 text-sm">
            <option value="">Todos os status</option>
            <option value="published">Publicado</option>
            <option value="in_progress">Em andamento</option>
            <option value="delivered">Entregue</option>
            <option value="cancelled">Cancelado</option>
        </select>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="py-2 px-4 text-left">ID</th>
                    <th class="py-2 px-4 text-left">Título</th>
                    <th class="py-2 px-4 text-left">Cliente</th>
                    <th class="py-2 px-4 text-left">Status</th>
                    <th class="py-2 px-4 text-left">Taxa</th>
                    <th class="py-2 px-4 text-left">Criado em</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-2 px-4 text-gray-400">{{ $service->id }}</td>
                    <td class="py-2 px-4 font-medium">{{ $service->titulo }}</td>
                    <td class="py-2 px-4">{{ $service->cliente->name ?? '—' }}</td>
                    <td class="py-2 px-4">
                        <span class="px-2 py-0.5 rounded text-xs font-semibold
                            {{ $service->status === 'delivered' ? 'bg-green-100 text-green-700' :
                               ($service->status === 'in_progress' ? 'bg-blue-100 text-blue-700' :
                               ($service->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')) }}">
                            {{ match($service->status) {
                                'published' => 'Publicado',
                                'in_progress' => 'Em andamento',
                                'delivered' => 'Entregue',
                                'cancelled' => 'Cancelado',
                                default => $service->status
                            } }}
                        </span>
                    </td>
                    <td class="py-2 px-4">{{ money_aoa($service->taxa ?? 0) }}</td>
                    <td class="py-2 px-4 text-gray-500">{{ $service->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-6 text-center text-gray-400">Nenhum serviço encontrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $services->links() }}
    </div>
</div>
