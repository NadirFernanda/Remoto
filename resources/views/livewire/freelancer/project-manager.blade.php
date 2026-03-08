<div>
    <h1 class="text-2xl font-bold mb-6 text-[#00baff]">Meus Projetos</h1>
    <div class="flex flex-wrap gap-4 mb-6">
        @foreach(['accepted' => 'Em Andamento', 'in_progress' => 'Em Progresso', 'delivered' => 'Entregue', 'completed' => 'Concluído'] as $key => $label)
            <span class="inline-block px-4 py-2 rounded-lg text-xs font-semibold bg-[#e0f7fa] text-[#00baff]">
                {{ $label }}: {{ $statusCounts[$key] ?? 0 }}
            </span>
        @endforeach
    </div>
    <div class="flex flex-col sm:flex-row gap-4 mb-4">
        <input type="text" wire:model.debounce.500ms="search" placeholder="Buscar por título..." class="w-full sm:w-1/3 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#00baff]">
        <select wire:model="status" class="w-full sm:w-1/4 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#00baff]">
            <option value="">Todos os status</option>
            <option value="accepted">Em Andamento</option>
            <option value="in_progress">Em Progresso</option>
            <option value="delivered">Entregue</option>
            <option value="completed">Concluído</option>
        </select>
    </div>
    <div class="overflow-x-auto bg-white rounded-2xl border border-gray-200 p-5">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-[#e0f7fa]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($projects as $project)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $project->titulo ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-block px-2 py-1 rounded-full text-xs font-bold
                                @if($project->status === 'completed') bg-green-100 text-green-700
                                @elseif($project->status === 'in_progress') bg-yellow-100 text-yellow-700
                                @elseif($project->status === 'delivered') bg-blue-100 text-blue-700
                                @else bg-gray-100 text-gray-700 @endif">
                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">Kz {{ number_format($project->valor, 2, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $project->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap flex flex-wrap gap-2">
                            <a href="{{ route('service.chat', ['service' => $project->id]) }}" class="px-2 py-1 bg-[#e0f7fa] text-[#00baff] rounded hover:bg-[#00baff] hover:text-white text-xs font-semibold" title="Chat">Chat</a>
                            @if(in_array($project->status, ['in_progress','delivered','completed']))
                                <a href="{{ route('service.dispute', ['service' => $project->id]) }}" class="px-2 py-1 bg-red-100 text-red-700 rounded hover:bg-red-700 hover:text-white text-xs font-semibold" title="Disputar">Disputar</a>
                            @endif
                            @if($project->status === 'completed')
                                <a href="{{ route('service.review.leave', ['service' => $project->id]) }}" class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded hover:bg-yellow-700 hover:text-white text-xs font-semibold" title="Avaliar">Avaliar</a>
                            @endif
                            <a href="{{ route('freelancer.service.delivery', ['service' => $project->id]) }}" class="px-2 py-1 bg-green-100 text-green-700 rounded hover:bg-green-700 hover:text-white text-xs font-semibold" title="Entregar">Entregar</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-[#888]">Nenhum projeto encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $projects->links() }}
        </div>
    </div>
</div>
