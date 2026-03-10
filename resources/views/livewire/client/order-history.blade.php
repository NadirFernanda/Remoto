<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 py-10">

        {{-- Header --}}
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('client.dashboard') }}"
               class="flex items-center justify-center w-10 h-10 rounded-full border border-gray-200 text-gray-400 hover:text-gray-700 hover:border-gray-400 transition-all bg-white shadow-sm">
                @include('components.icon', ['name' => 'arrow-left', 'class' => 'w-5 h-5'])
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900">Meus Pedidos</h1>
                <p class="text-sm text-gray-400 mt-0.5">Acompanhe o estado dos seus projectos</p>
            </div>
            <a href="{{ route('client.projects') }}" class="btn-primary text-xs self-center">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776"/></svg>
                Gerir Projectos
            </a>
        </div>

        {{-- Stats bar --}}
        @php
            $total     = $orders->count();
            $publicado = $orders->where('status', 'published')->count();
            $andamento = $orders->whereIn('status', ['em_andamento', 'em andamento', 'in_progress'])->count();
            $concluido = $orders->where('status', 'concluido')->count();
        @endphp
        <div class="grid grid-cols-3 gap-3 mb-8">
            <div class="bg-white rounded-2xl p-4 text-center shadow-sm border border-gray-100">
                <div class="text-2xl font-bold text-[#00baff]">{{ $total }}</div>
                <div class="text-xs text-gray-400 mt-1">Total</div>
            </div>
            <div class="bg-white rounded-2xl p-4 text-center shadow-sm border border-gray-100">
                <div class="text-2xl font-bold text-yellow-500">{{ $publicado + $andamento }}</div>
                <div class="text-xs text-gray-400 mt-1">Em aberto</div>
            </div>
            <div class="bg-white rounded-2xl p-4 text-center shadow-sm border border-gray-100">
                <div class="text-2xl font-bold text-green-500">{{ $concluido }}</div>
                <div class="text-xs text-gray-400 mt-1">Concluídos</div>
            </div>
        </div>

        {{-- Orders list --}}
        <div class="space-y-3">
            @forelse($orders as $order)
                @php
                    $s = $order->status;
                    if ($s === 'published') {
                        $badge = ['label' => 'Publicado',    'color' => 'bg-cyan-50 text-cyan-700 border-cyan-200'];
                        $dot   = 'bg-[#00baff]';
                    } elseif (in_array($s, ['em_andamento','em andamento','in_progress'])) {
                        $badge = ['label' => 'Em andamento', 'color' => 'bg-yellow-50 text-yellow-700 border-yellow-200'];
                        $dot   = 'bg-yellow-400';
                    } elseif ($s === 'concluido') {
                        $badge = ['label' => 'Concluído',    'color' => 'bg-green-50 text-green-700 border-green-200'];
                        $dot   = 'bg-green-500';
                    } elseif ($s === 'cancelado') {
                        $badge = ['label' => 'Cancelado',    'color' => 'bg-red-50 text-red-600 border-red-200'];
                        $dot   = 'bg-red-500';
                    } else {
                        $badge = ['label' => ucfirst($s),    'color' => 'bg-gray-100 text-gray-500 border-gray-200'];
                        $dot   = 'bg-gray-400';
                    }
                @endphp

                <a href="{{ route('client.service.cancel', $order->id) }}"
                   class="group flex items-center gap-4 bg-white rounded-2xl px-5 py-4 shadow-sm border border-gray-100 transition-all hover:shadow-md hover:border-cyan-100 hover:-translate-y-0.5">

                    {{-- Number badge --}}
                    <div class="flex-shrink-0 w-11 h-11 rounded-xl flex items-center justify-center font-bold text-sm text-gray-400 bg-gray-50 border border-gray-100">
                        #{{ $order->id }}
                    </div>

                    {{-- Title + date --}}
                    <div class="flex-1 min-w-0">
                        <div class="text-gray-800 font-semibold truncate group-hover:text-[#00baff] transition-colors">
                            {{ $order->titulo ?? 'Sem título' }}
                        </div>
                        <div class="text-xs text-gray-400 mt-0.5 flex items-center gap-1.5">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $order->created_at->format('d/m/Y') }}
                            @if($order->valor)
                                <span class="mx-1 text-gray-200">·</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                                {{ number_format($order->valor, 0, ',', '.') }} Kz
                            @endif
                        </div>
                    </div>

                    {{-- Status badge + arrow --}}
                    <div class="flex-shrink-0 flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border {{ $badge['color'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>
                            {{ $badge['label'] }}
                        </span>
                        <svg class="w-4 h-4 text-gray-300 group-hover:text-[#00baff] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
            @empty
                <div class="bg-white rounded-2xl p-12 text-center border border-dashed border-gray-200">
                    <div class="w-16 h-16 rounded-2xl bg-cyan-50 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-[#00baff]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p class="text-gray-500 font-medium">Nenhum pedido encontrado</p>
                    <p class="text-gray-400 text-sm mt-1">Os seus projectos aparecerão aqui após a criação</p>
                    <a href="{{ route('client.briefing') }}"
                       class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 rounded-full text-sm font-semibold text-white transition-all hover:opacity-90"
                       style="background: #00baff;">
                        Criar primeiro pedido
                    </a>
                </div>
            @endforelse
        </div>

        {{-- New order CTA --}}
        @if($orders->count() > 0)
        <div class="mt-6 text-center">
            <a href="{{ route('client.briefing') }}"
               class="btn-eq btn-primary inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold transition-all"
               style="min-width: 180px;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Novo pedido
            </a>
        </div>
        @endif

    </div>
</div>
