<div class="container mx-auto px-4 py-8 max-w-xl">
    <a href="{{ route('client.orders') }}" class="inline-flex items-center gap-2 text-cyan-600 hover:text-cyan-800 font-bold text-sm bg-white border border-cyan-100 rounded-full px-4 py-2 shadow transition mb-4">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Voltar
    </a>
    <h2 class="text-xl font-bold text-cyan-600 mb-4">Detalhes do Pedido</h2>
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="mb-2">
            <span class="font-semibold">Título:</span> {{ $service->titulo }}
        </div>
        <div class="mb-2">
            <span class="font-semibold">Briefing:</span>
            @php
                $briefing = $service->briefing;
                $briefingArray = @json_decode($briefing, true);
            @endphp
            @if(is_string($briefing) && !$briefingArray)
                <div class="mt-2 text-gray-800 whitespace-pre-line">{{ $briefing }}</div>
            @else
                <ul class="list-disc ml-6 text-gray-700">
                    <li><span class="font-semibold">Tipo de negócio:</span> {{ $briefingArray['business_type'] ?? '' }}</li>
                    <li><span class="font-semibold">Público-alvo:</span> {{ $briefingArray['target_audience'] ?? '' }}</li>
                    <li><span class="font-semibold">Estilo desejado:</span> {{ $briefingArray['style'] ?? '' }}</li>
                    <li><span class="font-semibold">Cores preferidas:</span> {{ $briefingArray['colors'] ?? '' }}</li>
                    <li><span class="font-semibold">Onde será utilizado:</span> {{ $briefingArray['usage'] ?? '' }}</li>
                </ul>
            @endif
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
