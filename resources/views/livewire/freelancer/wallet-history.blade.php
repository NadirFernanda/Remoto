<div class="bg-white rounded shadow p-6 mt-6">
    <h2 class="font-semibold text-xl mb-4 text-[#222]">Histórico de Movimentações</h2>
    <div class="mb-4 flex items-center gap-2">
        <label for="tipo" class="text-sm text-[#888]">Filtrar por tipo:</label>
        <select wire:model="tipo" id="tipo" class="pub-select">
            <option value="">Todos</option>
            @foreach($tipos as $t)
                <option value="{{ $t }}">{{ ucfirst(str_replace('_', ' ', $t)) }}</option>
            @endforeach
        </select>
    </div>
    <table class="min-w-full bg-white rounded shadow">
        <thead>
            <tr class="bg-gray-50 text-[#222]">
                <th class="py-2 px-4">Data</th>
                <th class="py-2 px-4">Tipo</th>
                <th class="py-2 px-4">Valor</th>
                <th class="py-2 px-4">Descrição</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td class="py-2 px-4">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    <td class="py-2 px-4">{{ ucfirst(str_replace('_', ' ', $log->tipo)) }}</td>
                    <td class="py-2 px-4">Kz {{ number_format($log->valor, 2, ',', '.') }}</td>
                    <td class="py-2 px-4">{{ $log->descricao }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="py-8 px-4 text-center">
                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.5rem;">
                            @include('components.icon', ['name' => 'plus', 'class' => 'w-12 h-12'])
                            <span style="color: #222; font-weight: 600; font-size: 1.1rem;">Nenhuma movimentação encontrada</span>
                            <span style="color: #888; font-size: 0.98rem;">Você ainda não possui registros de entrada ou saída na sua carteira.</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
