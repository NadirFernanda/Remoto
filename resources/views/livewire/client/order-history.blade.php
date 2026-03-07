<div class="min-h-screen" style="background: #071422;">
    <div class="max-w-4xl mx-auto px-4 py-10">

        {{-- Header --}}
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('client.dashboard') }}"
               class="flex items-center justify-center w-10 h-10 rounded-full border border-white/10 text-white/60 hover:text-white hover:border-white/30 transition-all">
                @include('components.icon', ['name' => 'arrow-left', 'class' => 'w-5 h-5'])
            </a>
            <div>
                <h1 class="text-2xl font-bold text-white">Meus Pedidos</h1>
                <p class="text-sm text-white/40 mt-0.5">Acompanhe o status dos seus projetos</p>
            </div>
        </div>

        {{-- Stats bar --}}
        @php
            $total     = $orders->count();
            $publicado = $orders->where('status', 'published')->count();
            $andamento = $orders->whereIn('status', ['em_andamento', 'em andamento', 'in_progress'])->count();
            $concluido = $orders->where('status', 'concluido')->count();
        @endphp
        <div class="grid grid-cols-3 gap-3 mb-8">
            <div class="rounded-2xl p-4 text-center" style="background: rgba(0,186,255,0.08); border: 1px solid rgba(0,186,255,0.15);">
                <div class="text-2xl font-bold text-[#00baff]">{{ $total }}</div>
                <div class="text-xs text-white/50 mt-1">Total</div>
            </div>
            <div class="rounded-2xl p-4 text-center" style="background: rgba(234,179,8,0.08); border: 1px solid rgba(234,179,8,0.15);">
                <div class="text-2xl font-bold text-yellow-400">{{ $publicado + $andamento }}</div>
                <div class="text-xs text-white/50 mt-1">Em aberto</div>
            </div>
            <div class="rounded-2xl p-4 text-center" style="background: rgba(34,197,94,0.08); border: 1px solid rgba(34,197,94,0.15);">
                <div class="text-2xl font-bold text-green-400">{{ $concluido }}</div>
                <div class="text-xs text-white/50 mt-1">Concluídos</div>
            </div>
        </div>

        {{-- Orders list --}}
        <div class="space-y-3">
            @forelse($orders as $order)
                @php
                    $s = $order->status;
                    if ($s === 'published') {
                        $badge = ['label' => 'Publicado',    'color' => 'bg-[#00baff]/15 text-[#00baff] border-[#00baff]/30'];
                        $dot   = 'bg-[#00baff]';
                    } elseif (in_array($s, ['em_andamento','em andamento','in_progress'])) {
                        $badge = ['label' => 'Em andamento', 'color' => 'bg-yellow-400/15 text-yellow-300 border-yellow-400/30'];
                        $dot   = 'bg-yellow-400';
                    } elseif ($s === 'concluido') {
                        $badge = ['label' => 'Concluído',    'color' => 'bg-green-400/15 text-green-300 border-green-400/30'];
                        $dot   = 'bg-green-400';
                    } elseif ($s === 'cancelado') {
                        $badge = ['label' => 'Cancelado',    'color' => 'bg-red-400/15 text-red-300 border-red-400/30'];
                        $dot   = 'bg-red-400';
                    } else {
                        $badge = ['label' => ucfirst($s),    'color' => 'bg-white/10 text-white/50 border-white/10'];
                        $dot   = 'bg-white/30';
                    }
                @endphp

                <a href="{{ route('client.service.cancel', $order->id) }}"
                   class="group flex items-center gap-4 rounded-2xl px-5 py-4 transition-all hover:scale-[1.01]"
                   style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.07);">

                    {{-- Icon / number --}}
                    <div class="flex-shrink-0 w-11 h-11 rounded-xl flex items-center justify-center font-bold text-sm text-white/60"
                         style="background: rgba(255,255,255,0.06);">
                        #{{ $order->id }}
                    </div>

                    {{-- Title + date --}}
                    <div class="flex-1 min-w-0">
                        <div class="text-white font-semibold truncate group-hover:text-[#00baff] transition-colors">
                            {{ $order->titulo ?? 'Sem título' }}
                        </div>
                        <div class="text-xs text-white/35 mt-0.5 flex items-center gap-1.5">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $order->created_at->format('d/m/Y') }}
                            @if($order->valor)
                                <span class="mx-1 opacity-30">·</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                                {{ number_format($order->valor, 0, ',', '.') }} Kz
                            @endif
                        </div>
                    </div>

                    {{-- Status badge --}}
                    <div class="flex-shrink-0 flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border {{ $badge['color'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>
                            {{ $badge['label'] }}
                        </span>
                        <svg class="w-4 h-4 text-white/20 group-hover:text-white/50 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
            @empty
                <div class="rounded-2xl p-12 text-center" style="background: rgba(255,255,255,0.03); border: 1px dashed rgba(255,255,255,0.08);">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background: rgba(0,186,255,0.08);">
                        <svg class="w-8 h-8 text-[#00baff]/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p class="text-white/40 font-medium">Nenhum pedido encontrado</p>
                    <p class="text-white/25 text-sm mt-1">Os seus projetos aparecerão aqui após a criação</p>
                    <a href="{{ route('client.briefing') }}"
                       class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 rounded-full text-sm font-semibold text-white transition-all"
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
               class="inline-flex items-center gap-2 px-6 py-3 rounded-full text-sm font-semibold text-white transition-all hover:opacity-90"
               style="background: #00baff;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Novo pedido
            </a>
        </div>
        @endif

    </div>
</div>
