<div class="container mx-auto px-4 py-8 max-w-xl">
    <h2 class="text-xl font-bold text-cyan-600 mb-4">Detalhes do Serviço</h2>
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="mb-2">
            <span class="font-semibold">Título:</span> {{ $service->titulo }}
        </div>
        <div class="mb-2">
            <span class="font-semibold">Briefing:</span>
            <ul class="list-disc ml-6 text-gray-700">
                @foreach(json_decode($service->briefing, true) as $key => $value)
                    <li><span class="capitalize">{{ str_replace('_', ' ', $key) }}:</span> {{ $value }}</li>
                @endforeach
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
        <div class="mb-4">
            <span class="font-semibold">Status:</span>
            <span class="px-2 py-1 rounded text-xs font-bold
                @if($service->status === 'published') bg-gray-200 text-gray-700
                @elseif($service->status === 'accepted') bg-cyan-100 text-cyan-700
                @elseif($service->status === 'in_progress') bg-yellow-100 text-yellow-700
                @elseif($service->status === 'delivered') bg-blue-100 text-blue-700
                @elseif($service->status === 'completed') bg-green-100 text-green-700
                @endif">
                {{ ucfirst($service->status) }}
            </span>
        </div>
        @if(auth()->user() && $service->cliente_id !== auth()->id())
        <div class="flex gap-4">
            <form wire:submit.prevent="acceptService">
                <button type="submit" class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-2 px-6 rounded">Aceitar serviço</button>
            </form>
            <form wire:submit.prevent="refuseService">
                <button type="submit" class="bg-gray-200 text-gray-700 font-bold py-2 px-6 rounded">Recusar</button>
            </form>
        </div>
        @endif
        @if(session('success'))
            <div class="mt-4 p-2 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif
        @if(session('info'))
            <div class="mt-4 p-2 bg-blue-100 text-blue-700 rounded">{{ session('info') }}</div>
        @endif
    </div>
</div>
