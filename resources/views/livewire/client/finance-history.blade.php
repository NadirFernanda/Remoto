<div class="p-6">
    <h2 class="text-2xl font-bold text-cyan-700 mb-6">Histórico de Transações</h2>

    <div class="flex flex-col md:flex-row md:items-end md:justify-between mb-6 gap-4">
        <form class="grid grid-cols-1 md:grid-cols-4 gap-4 w-full">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Status</label>
            <select wire:model="filter_status" class="w-full border rounded px-2 py-1">
                <option value="">Todos</option>
                <option value="concluido">Concluído</option>
                <option value="completed">Completed</option>
                <option value="em andamento">Em andamento</option>
                <option value="in_progress">In Progress</option>
                <option value="publicado">Publicado</option>
                <option value="published">Published</option>
                <option value="cancelado">Cancelado</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Tipo</label>
            <select wire:model="filter_type" class="w-full border rounded px-2 py-1">
                <option value="">Todos</option>
                <option value="entrada">Entrada</option>
                <option value="saida">Saída</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Data Inicial</label>
            <input type="date" wire:model="filter_date_start" class="w-full border rounded px-2 py-1" />
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Data Final</label>
            <input type="date" wire:model="filter_date_end" class="w-full border rounded px-2 py-1" />
        </div>

        </form>
        <div class="w-full md:w-1/4 flex justify-center md:justify-end items-end">
            <a href="{{ route('client.finance.exportCsv', [
                'status' => $filter_status,
                'type' => $filter_type,
                'date_start' => $filter_date_start,
                'date_end' => $filter_date_end
            ]) }}"
               class="btn-primary w-full text-center" target="_blank">Exportar CSV</a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left bg-white rounded-lg shadow border border-gray-200">
            <thead>
                <tr class="bg-[#f5f7fa] text-gray-700">
                    <th class="py-3 px-4 font-semibold">ID</th>
                    <th class="py-3 px-4 font-semibold">Título</th>
                    <th class="py-3 px-4 font-semibold">Valor</th>
                    <th class="py-3 px-4 font-semibold">Status</th>
                    <th class="py-3 px-4 font-semibold">Data</th>
                    <th class="py-3 px-4 font-semibold">Recibo</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $t)
                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                        <td class="py-3 px-4">{{ $t->id }}</td>
                        <td class="py-3 px-4">{{ $t->titulo ?? '-' }}</td>
                        <td class="py-3 px-4">{{ money_aoa($t->valor) }}</td>
                        <td class="py-3 px-4">
                            <span class="status-pill status-published">
                                @if($t->status === 'concluido' || $t->status === 'completed') Concluído
                                @elseif($t->status === 'em andamento' || $t->status === 'in_progress') Em andamento
                                @elseif($t->status === 'publicado' || $t->status === 'published') Publicado
                                @elseif($t->status === 'cancelado' || $t->status === 'cancelled') Cancelado
                                @else {{ ucfirst($t->status) }}
                                @endif
                            </span>
                        </td>
                        <td class="py-3 px-4">{{ $t->created_at->format('d/m/Y') }}</td>
                        <td class="py-3 px-4">
                            <a href="{{ route('client.receipt.download', $t->id) }}" class="btn-primary xs" target="_blank">Baixar Recibo</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-3 px-4 text-gray-500">Nenhuma transação encontrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
