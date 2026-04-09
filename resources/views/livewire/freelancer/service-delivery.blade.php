<div class="container mx-auto px-4 py-8 max-w-xl">
    <h2 class="text-xl font-bold text-cyan-600 mb-4">Entrega do Serviço</h2>

    {{-- ── Resumo do projecto ── --}}
    @php
        $statusLabels = [
            'published'   => 'Publicado',
            'negotiating' => 'Em negociação',
            'accepted'    => 'Aceite',
            'in_progress' => 'Em andamento',
            'delivered'   => 'Entregue',
            'completed'   => 'Concluído',
            'cancelled'   => 'Cancelado',
        ];
        $typeLabels = [
            'direct_invite' => 'Contratação directa',
            'marketplace'   => 'Marketplace',
        ];
    @endphp
    <div class="bg-white rounded-lg shadow p-6 mb-6 border-l-4 border-cyan-400">
        <h3 class="text-base font-bold text-gray-700 mb-4 flex items-center gap-2">
            <span class="text-cyan-500">📋</span> Resumo do Projecto
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">

            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Título</p>
                <p class="font-semibold text-gray-800">{{ $service->titulo ?? '—' }}</p>
            </div>

            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Status</p>
                <span class="px-2 py-1 rounded text-xs font-bold
                    @if($service->status === 'in_progress') bg-yellow-100 text-yellow-700
                    @elseif($service->status === 'delivered')  bg-blue-100  text-blue-700
                    @elseif($service->status === 'completed')  bg-green-100 text-green-700
                    @elseif($service->status === 'accepted')   bg-cyan-100  text-cyan-700
                    @else bg-gray-100 text-gray-600 @endif">
                    {{ $statusLabels[$service->status] ?? ucfirst($service->status) }}
                </span>
            </div>

            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Tipo de projecto</p>
                <p class="text-gray-700">{{ $typeLabels[$service->service_type] ?? ucfirst($service->service_type ?? '—') }}</p>
            </div>

            @if($service->categoria)
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Categoria</p>
                <p class="text-gray-700">{{ $service->categoria }}</p>
            </div>
            @endif

            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Valor a receber</p>
                <p class="font-bold text-green-600 text-base">
                    Kz {{ number_format($service->valor_liquido ?: $service->valor * 0.90, 2, ',', '.') }}
                </p>
            </div>

            @if($service->valor)
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Valor bruto do projecto</p>
                <p class="text-gray-700">Kz {{ number_format($service->valor, 2, ',', '.') }}</p>
            </div>
            @endif

            @if($service->prazo)
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Prazo de entrega</p>
                <p class="text-gray-700">{{ $service->prazo }}</p>
            </div>
            @endif

            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Data de início</p>
                <p class="text-gray-700">{{ $service->created_at?->format('d/m/Y') ?? '—' }}</p>
            </div>

            @if($service->cliente)
            <div class="sm:col-span-2">
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Cliente</p>
                <p class="text-gray-700">{{ $service->cliente->name ?? '—' }}</p>
            </div>
            @endif

            @if($service->briefing)
            <div class="sm:col-span-2">
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Briefing / Descrição</p>
                <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ $service->briefing }}</p>
            </div>
            @endif

        </div>

        {{-- Acesso rápido ao chat --}}
        <div class="mt-4 pt-4 border-t border-gray-100 flex justify-end">
            <a href="{{ route('service.chat', $service->id) }}"
               class="inline-flex items-center gap-1 text-xs text-cyan-600 hover:text-cyan-800 font-semibold">
                💬 Ir para o chat do projecto
            </a>
        </div>
    </div>

    {{-- ── Formulário de entrega ── --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-base font-bold text-gray-700 mb-4 flex items-center gap-2">
            <span class="text-cyan-500">📤</span> {{ $service->status === 'delivered' ? 'Re-entregar Serviço' : 'Submeter Entrega' }}
        </h3>
        @if($service->status === 'delivered')
        <div class="mb-4 p-3 bg-orange-50 border border-orange-200 rounded-lg text-orange-700 text-sm">
            <p class="font-semibold">⚠️ Este projecto já foi entregue e aguarda aprovação do cliente.</p>
            <p class="mt-1">Se o cliente pediu revisões, pode submeter uma nova entrega abaixo. O pagamento só será libertado quando o cliente aprovar.</p>
        </div>
        @endif
        <form wire:submit.prevent="entregarServico" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="block font-semibold mb-2">Ficheiro de entrega</label>
                <x-file-input wire:model="entrega_arquivo" label="📎 Seleccionar ficheiro de entrega" loading-target="entrega_arquivo">
                    @error('entrega_arquivo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </x-file-input>
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-2">Mensagem (opcional)</label>
                <textarea wire:model.defer="entrega_mensagem" rows="4"
                    class="w-full border border-cyan-500 rounded px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none resize-y"
                    placeholder="Descreva o que foi feito, instruções de uso, versões entregues..."></textarea>
                @error('entrega_mensagem') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <button type="submit"
                wire:loading.attr="disabled"
                wire:target="entregarServico"
                class="w-full bg-cyan-500 hover:bg-cyan-600 disabled:opacity-60 text-white font-bold py-3 rounded-lg transition-all duration-150">
                <span wire:loading.remove wire:target="entregarServico">Entregar serviço</span>
                <span wire:loading wire:target="entregarServico">A enviar...</span>
            </button>
        </form>
        @if(session('success'))
            <div class="mt-4 p-2 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif
    </div>
</div>
