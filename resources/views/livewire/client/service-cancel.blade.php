<div class="container mx-auto px-4 py-8 max-w-xl">
    <h2 class="text-xl font-bold text-cyan-600 mb-4">Cancelar Pedido</h2>
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="mb-2">
            <span class="font-semibold">Título:</span> {{ $service->titulo }}
        </div>
        <div class="mb-2">
            <span class="font-semibold">Briefing:</span>
            <ul class="list-disc ml-6 text-gray-700">
                <li><span class="font-semibold">Tipo de negócio:</span> {{ json_decode($service->briefing, true)['business_type'] ?? '' }}</li>
                <li><span class="font-semibold">Público-alvo:</span> {{ json_decode($service->briefing, true)['target_audience'] ?? '' }}</li>
                <li><span class="font-semibold">Estilo desejado:</span> {{ json_decode($service->briefing, true)['style'] ?? '' }}</li>
                <li><span class="font-semibold">Cores preferidas:</span> {{ json_decode($service->briefing, true)['colors'] ?? '' }}</li>
                <li><span class="font-semibold">Onde será utilizado:</span> {{ json_decode($service->briefing, true)['usage'] ?? '' }}</li>
            </ul>
        </div>
        <div class="mb-2">
            <span class="font-semibold">Valor:</span> <span class="text-cyan-700 font-bold">{{ number_format($service->valor, 2, ',', '.') }} Kz</span>
        </div>
        <div class="mb-2">
            <span class="font-semibold">Taxa:</span> <span class="text-yellow-600 font-bold">{{ number_format($service->taxa, 2, ',', '.') }}%</span>
        </div>
        <div class="mb-2">
            <span class="font-semibold">Valor líquido:</span> <span class="text-green-600 font-bold">{{ number_format($service->valor_liquido, 2, ',', '.') }} Kz</span>
        </div>
        <div class="mb-2">
            <span class="font-semibold">Status:</span>
            <span class="px-2 py-1 rounded text-xs font-bold
                @if($service->status === 'published') bg-gray-200 text-gray-700
                @elseif($service->status === 'cancelled') bg-red-100 text-red-700
                @endif">
                @if($service->status === 'published') Publicado
                @elseif($service->status === 'cancelled') Cancelado
                @elseif($service->status === 'accepted') Aceito
                @elseif($service->status === 'in_progress') Em andamento
                @elseif($service->status === 'delivered') Entregue
                @elseif($service->status === 'completed') Concluído
                @else {{ $service->status }}
                @endif
            </span>
        </div>
        @if($service->status === 'published')
            <div class="flex gap-4">
                <form wire:submit.prevent="cancelService">
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded">Cancelar pedido</button>
                </form>
                <a href="{{ route('client.briefing', ['edit' => $service->id]) }}" class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-2 px-6 rounded flex items-center justify-center">Editar pedido</a>
            </div>
        @else
            <div class="text-gray-500">Este pedido não pode mais ser cancelado.</div>
        @endif
    </div>
</div>
