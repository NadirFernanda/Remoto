<div class="p-6 bg-white rounded shadow max-w-2xl mx-auto mt-8">
    <h2 class="text-2xl font-bold mb-4">Patrocínio de Perfil</h2>
    <div class="mb-4">
        <label class="block text-gray-700 font-semibold">Status do patrocínio:</label>
        @php
            $sponsorshipStatusLabels = [
                'aprovado' => 'Aprovado',
                'em análise' => 'Em análise',
                'recusado' => 'Recusado',
                'pendente' => 'Pendente',
            ];
        @endphp
        <span class="inline-block px-3 py-1 rounded-full text-white {{ $status === 'aprovado' ? 'bg-green-500' : ($status === 'em análise' ? 'bg-yellow-500' : ($status === 'recusado' ? 'bg-red-500' : 'bg-gray-400')) }}">
            {{ $sponsorshipStatusLabels[$status] ?? ucfirst($status) }}
        </span>
    </div>
    <div class="mb-4">
        <button wire:click="solicitarPatrocinio" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none" @if($status === 'em análise' || $status === 'aprovado') disabled @endif>
            Solicitar Patrocínio
        </button>
        @if($feedback)
            <div class="text-green-600 text-sm mt-2">{{ $feedback }}</div>
        @endif
    </div>
    <div>
        <label class="block text-gray-700 font-semibold mb-2">Histórico de solicitações:</label>
        <table class="w-full text-left border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="py-2 px-3">Data</th>
                    <th class="py-2 px-3">Valor</th>
                    <th class="py-2 px-3">Descrição</th>
                </tr>
            </thead>
            <tbody>
                @forelse($history as $item)
                    <tr>
                        <td class="py-2 px-3">{{ \Carbon\Carbon::parse($item['created_at'])->format('d/m/Y') }}</td>
                        <td class="py-2 px-3">
                            @if(is_null($item['amount']))
                                -
                            @elseif($item['amount'] == 0)
                                Gratuito
                            @else
                                {{ money_aoa($item['amount']) }}
                            @endif
                        </td>
                        <td class="py-2 px-3">{{ $item['description'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-2 px-3 text-gray-500">Nenhum histórico encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
