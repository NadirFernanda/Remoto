<div class="container mx-auto py-10 px-2 flex justify-center items-center min-h-[80vh] bg-gradient-to-b from-[#f8fcff] to-[#eaf6fa]">
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
    <div class="pub-card" style="max-width:42rem;width:100%;padding:2rem;margin:0 auto 2rem;">
        <h2 class="text-2xl font-extrabold text-center mb-6 text-[#00baff] tracking-tight">Detalhes do Serviço</h2>
        <div class="mb-4 flex items-center gap-3">
            <div style="width:56px;height:56px;border-radius:12px;background:linear-gradient(135deg,#00baff,#0077cc);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:900;font-size:1.4rem;flex-shrink:0;">{{ strtoupper(mb_substr($service->titulo,0,1)) }}</div>
            <div>
                <div class="font-bold text-lg text-[#222]">{{ $service->titulo }}</div>
            </div>
        </div>
        <div class="mb-4">
            <span class="font-semibold text-[#00baff]">Briefing:</span>
            @php
                $labels = [
                    'title' => 'Título',
                    'business_type' => 'Tipo de negócio',
                    'necessity' => 'Descrição do serviço',
                ];
            @endphp
            @php $briefingDecoded = json_decode($service->briefing, true); @endphp
            @if(is_array($briefingDecoded))
                <ul class="list-disc ml-6 text-gray-700 mt-2">
                    @foreach($briefingDecoded as $key => $value)
                        <li><span class="capitalize font-semibold">{{ $labels[$key] ?? str_replace('_', ' ', $key) }}:</span> {{ $value }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-700 mt-2">{{ $service->briefing }}</p>
            @endif
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            @if(auth()->user() && $service->cliente_id === auth()->id())
                <div>
                    <span class="font-semibold">Valor:</span> <span class="text-cyan-700 font-bold">{{ number_format($service->valor, 2, ',', '.') }} Kz</span>
                </div>
                <div>
                    <span class="font-semibold">Taxa:</span> <span class="text-yellow-600 font-bold">{{ number_format($service->taxa, 2, ',', '.') }}%</span>
                </div>
            @endif
            <div>
                <span class="font-semibold">Valor líquido:</span> <span class="text-green-600 font-bold">{{ number_format($service->valor_liquido, 2, ',', '.') }} Kz</span>
            </div>
            <div>
                <span class="font-semibold">Status:</span>
                @php
                    $statusLabels = [
                        'published' => 'Publicado',
                        'accepted' => 'Aceite',
                        'in_progress' => 'Em andamento',
                        'delivered' => 'Entregue',
                        'completed' => 'Concluído',
                        'cancelled' => 'Cancelado',
                    ];
                @endphp
                <span class="status-badge status-{{ $service->status }} ml-2">{{ $statusLabels[$service->status] ?? ucfirst($service->status) }}</span>
            </div>
        </div>
        @if(auth()->user() && $service->cliente_id !== auth()->id())
        <x-action-toolbar class="mt-6">
            <form wire:submit.prevent="acceptService" class="m-0">
                <button type="submit" class="btn-eq small" aria-label="Aceitar serviço">
                    @include('components.icon', ['name' => 'check', 'class' => 'w-4 h-4'])
                    <span>Aceitar serviço</span>
                </button>
            </form>

            <button type="button" wire:click="showProposalModal" class="btn-eq btn-outline small" aria-haspopup="dialog">
                @include('components.icon', ['name' => 'dots', 'class' => 'w-4 h-4'])
                <span>Enviar proposta</span>
            </button>

            <a href="{{ route('service.chat', $service->id) }}" class="btn-eq btn-outline relative" title="Aceder ao chat do serviço">
                @include('components.icon', ['name' => 'chat', 'class' => 'w-4 h-4'])
                <span>Chat</span>
                @livewire('chat.chat-badge', ['serviceId' => $service->id], key('chat-badge-'.$service->id))
            </a>

            @if($service->status === 'completed')
                <a href="{{ route('service.review.leave', $service->id) }}" class="btn-eq btn-primary">
                    @include('components.icon', ['name' => 'star', 'class' => 'w-4 h-4'])
                    <span>Avaliar serviço</span>
                </a>
            @endif

            @if(!in_array($service->status, ['cancelled', 'published']))
                <a href="{{ route('service.dispute', $service->id) }}" class="btn-eq btn-outline text-red-600 border-red-400 hover:bg-red-50">
                    @include('components.icon', ['name' => 'flag', 'class' => 'w-4 h-4'])
                    <span>Abrir disputa</span>
                </a>
            @endif
        </x-action-toolbar>

        @if($proposalModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                <div class="bg-white rounded-lg w-full max-w-2xl p-6 shadow-xl">
                    <h3 class="text-lg font-bold mb-3">Enviar proposta</h3>
                    <form wire:submit.prevent="sendProposal">
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700">Mensagem</label>
                            <textarea wire:model.defer="proposalMessage" class="mt-1 block w-full border rounded p-2" rows="5"></textarea>
                            @error('proposalMessage') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700">Valor líquido (opcional) — valor que irá receber</label>
                            <input type="number" step="0.01" wire:model.defer="proposalValue" class="mt-1 block w-48 border rounded p-2" />
                            @error('proposalValue') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" wire:click="$set('proposalModal', false)" class="btn-eq btn-outline">Cancelar</button>
                            <button type="submit" class="btn-eq btn-primary">Enviar proposta</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
        {{-- chat link consolidated into action toolbar above to avoid duplicate controls --}}
        @endif
        @if(session('success'))
            <div class="mt-6 p-3 bg-green-50 text-green-700 rounded-lg text-center font-semibold shadow">{{ session('success') }}</div>
        @endif
        @if(session('info'))
            <div class="mt-6 p-3 bg-blue-50 text-blue-700 rounded-lg text-center font-semibold shadow">{{ session('info') }}</div>
        @endif
    </div>
</div>
