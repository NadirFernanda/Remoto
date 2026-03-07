<div class="container mx-auto px-4 py-8 max-w-xl">
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded shadow text-center font-semibold">
            {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded shadow text-center font-semibold">
            {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div class="mb-4 p-3 bg-blue-100 text-blue-700 rounded shadow text-center font-semibold">
            {{ session('info') }}
        </div>
    @endif
    <a href="{{ route('client.orders') }}" class="inline-flex items-center gap-2 text-cyan-600 hover:text-cyan-800 font-bold text-sm bg-white border border-cyan-100 rounded-full px-4 py-2 shadow transition mb-4">
        @include('components.icon', ['name' => 'arrow-left', 'class' => 'w-5 h-5'])
        Voltar
    </a>
    <h2 class="text-xl font-bold text-cyan-600 mb-4">Detalhes do Pedido</h2>
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="mb-2 flex items-center gap-2">
            <span class="font-semibold">Título:</span> <span id="service-title">{{ $service->titulo }}</span>
            <button type="button" onclick="document.getElementById('editTitleModal').showModal()" class="ml-2 px-2 py-1 text-xs bg-cyan-100 text-cyan-800 rounded hover:bg-cyan-200">Editar título</button>
        </div>

        <dialog id="editTitleModal" class="rounded-lg shadow-lg p-0">
            <form method="dialog" class="p-6 bg-white rounded-lg flex flex-col gap-4" onsubmit="event.preventDefault(); window.submitEditTitle()">
                <h3 class="font-bold text-cyan-700 text-lg mb-2">Editar título do pedido</h3>
                <input type="text" id="newTitleInput" value="{{ $service->titulo }}" maxlength="100" class="border border-cyan-400 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none" required>
                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="document.getElementById('editTitleModal').close()" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300">Cancelar</button>
                    <button type="submit" class="px-3 py-1 rounded bg-cyan-500 text-white hover:bg-cyan-600">Salvar</button>
                </div>
            </form>
        </dialog>
        <script>
        function submitEditTitle(e) {
            if (e) e.preventDefault();
            const input = document.getElementById('newTitleInput');
            const modal = document.getElementById('editTitleModal');
            const titleSpan = document.getElementById('service-title');
            const csrfMeta = document.querySelector('meta[name=csrf-token]');
            if (!input || !modal || !titleSpan) {
                alert('Elementos do formulário não encontrados. Recarregue a página.');
                return;
            }
            if (!csrfMeta) {
                alert('CSRF token não encontrado. Recarregue a página.');
                return;
            }
            const newTitle = input.value;
            fetch(window.location.pathname + '/edit-title', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfMeta.content
                },
                body: JSON.stringify({ titulo: newTitle })
            }).then(resp => resp.json()).then(data => {
                if (data.success) {
                    titleSpan.innerText = newTitle;
                    modal.close();
                } else {
                    alert('Erro ao salvar título!');
                }
            });
        }
        window.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('#editTitleModal form');
            if (form) {
                form.addEventListener('submit', submitEditTitle);
            }
        });
        </script>
        <div class="mb-2">
            <span class="font-semibold">Briefing:</span>
            @php
                $briefing = $service->briefing;
                $briefingArray = @json_decode($briefing, true);
            @endphp
            <div class="mt-2 text-gray-800 whitespace-pre-line">
                @if(is_array($briefingArray))
                    @if(isset($briefingArray['title']))<div><b>Título:</b> {{ $briefingArray['title'] }}</div>@endif
                    @if(isset($briefingArray['business_type']))<div><b>Tipo de negócio:</b> {{ $briefingArray['business_type'] }}</div>@endif
                    @if(isset($briefingArray['necessity']))<div><b>Necessidade:</b> {{ $briefingArray['necessity'] }}</div>@endif
                @else
                    {{ $briefing }}
                @endif
            </div>
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
            <div class="action-row mt-4">
                    <a href="{{ route('service.chat', $service->id) }}" class="btn-eq btn-outline relative focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:ring-offset-2">
                        @include('components.icon', ['name' => 'chat', 'class' => 'w-4 h-4'])
                        <span>Acessar chat do serviço</span>
                        @livewire('chat.chat-badge', ['serviceId' => $service->id], key('chat-badge-'.$service->id))
                    </a>
            </div>
        @else
            <div class="text-gray-500">Este pedido não pode mais ser cancelado.</div>
        @endif
    </div>
</div>
