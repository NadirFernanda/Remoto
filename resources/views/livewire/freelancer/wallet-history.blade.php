<div class="bg-white border border-gray-200 rounded-2xl p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h2 class="font-bold text-xl text-slate-900">Histórico de Movimentações</h2>
            <p class="text-sm text-slate-500">Acompanhe entradas e saídas na sua carteira.</p>
        </div>
        <div class="flex items-center gap-2">
            <label for="tipo" class="text-sm text-slate-500">Filtrar por tipo:</label>
            <select wire:model="tipo" id="tipo" class="border border-gray-200 rounded-xl px-3 py-2 text-sm">
                <option value="">Todos</option>
                @foreach($tipos as $t)
                    <option value="{{ $t }}">{{ ucfirst(str_replace('_', ' ', $t)) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="py-3 px-4 text-left">Data</th>
                    <th class="py-3 px-4 text-left">Tipo</th>
                    <th class="py-3 px-4 text-left">Valor</th>
                    <th class="py-3 px-4 text-left">Descrição</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr class="border-t border-slate-100">
                        <td class="py-3 px-4 text-slate-600">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3 px-4 text-slate-700">{{ ucfirst(str_replace('_', ' ', $log->tipo)) }}</td>
                        <td class="py-3 px-4 font-semibold text-emerald-600">Kz {{ number_format($log->valor, 2, ',', '.') }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ $log->descricao }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-8 px-4 text-center">
                            <div class="flex flex-col items-center justify-center gap-2">
                                @include('components.icon', ['name' => 'plus', 'class' => 'w-12 h-12'])
                                <span class="text-slate-900 font-semibold text-base">Nenhuma movimentação encontrada</span>
                                <span class="text-slate-500 text-sm">Ainda nao possui registos de entrada ou saida na sua carteira.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
