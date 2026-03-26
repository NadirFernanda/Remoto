<div class="max-w-5xl mx-auto space-y-6">
    <div class="bg-gradient-to-r from-[#00baff] to-[#0095cc] rounded-2xl p-6 text-white">
        <h2 class="text-2xl font-extrabold">Patrocinio de Perfil</h2>
        <p class="text-sm text-white/90 mt-1">Destaque o seu perfil com patrocinio.</p>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-slate-700">Status do patrocinio</p>
                @php
                    $sponsorshipStatusLabels = [
                        'aprovado' => 'Aprovado',
                        'em análise' => 'Em analise',
                        'recusado' => 'Recusado',
                        'pendente' => 'Pendente',
                    ];
                @endphp
                <span class="mt-2 inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold text-white {{ $status === 'aprovado' ? 'bg-emerald-500' : ($status === 'em análise' ? 'bg-amber-500' : ($status === 'recusado' ? 'bg-red-500' : 'bg-slate-400')) }}">
                    {{ $sponsorshipStatusLabels[$status] ?? ucfirst($status) }}
                </span>
            </div>
            <div>
                <button wire:click="solicitarPatrocinio" class="px-4 py-2 rounded-xl bg-[#00baff] text-white font-semibold hover:bg-[#009ad6] transition" @if($status === 'em análise' || $status === 'aprovado') disabled @endif>
                    Solicitar patrocinio
                </button>
                @if($feedback)
                    <div class="text-emerald-600 text-sm mt-2">{{ $feedback }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <p class="text-sm font-semibold text-slate-700">Historico de solicitacoes</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="py-3 px-4">Data</th>
                        <th class="py-3 px-4">Valor</th>
                        <th class="py-3 px-4">Descricao</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $item)
                        <tr class="border-t border-slate-100">
                            <td class="py-3 px-4">{{ \Carbon\Carbon::parse($item['created_at'])->format('d/m/Y') }}</td>
                            <td class="py-3 px-4 font-semibold text-emerald-600">
                                @if(is_null($item['amount']))
                                    -
                                @elseif($item['amount'] == 0)
                                    Gratuito
                                @else
                                    {{ money_aoa($item['amount']) }}
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-600">{{ $item['description'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-4 px-4 text-slate-500">Nenhum historico encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
