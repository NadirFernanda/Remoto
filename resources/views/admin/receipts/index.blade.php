@extends('layouts.dashboard')

@section('dashboard-title', 'Recibos')

@section('dashboard-content')

@if(session('success'))
<div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm flex items-center gap-2">
    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif

{{-- ══════════════════════════════════════════════════════
     SECÇÃO 1 — Comprovativos de Pagamento Escrow
══════════════════════════════════════════════════════ --}}
<div class="mb-8">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-base font-bold text-gray-800">Comprovativos de Pagamento (Escrow)</h2>
            <p class="text-xs text-gray-400 mt-0.5">Ordens pagas pelos clientes — mesmo recibo que o cliente vê</p>
        </div>
    </div>

    {{-- Pesquisa --}}
    <form method="GET" action="{{ route('admin.recibos.index') }}" class="mb-3">
        <input type="hidden" name="page" value="1">
        <div class="relative max-w-xs">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/></svg>
            <input type="text" name="sq" value="{{ $serviceSearch }}"
                placeholder="Título ou cliente..."
                class="w-full pl-9 pr-4 py-2 text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition">
        </div>
    </form>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        @if($services->isEmpty())
            <div class="py-10 text-center text-gray-400 text-sm">
                @if($serviceSearch)
                    Nenhum resultado para "{{ $serviceSearch }}".
                @else
                    Ainda não há ordens pagas.
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Ordem</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Título</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Cliente</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Freelancer</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Valor</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Estado</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Data</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($services as $service)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 text-gray-400 whitespace-nowrap">#{{ $service->id }}</td>
                        <td class="px-5 py-3 font-medium text-gray-800 max-w-[200px] truncate">{{ $service->titulo }}</td>
                        <td class="px-5 py-3 text-gray-600 whitespace-nowrap">{{ $service->cliente?->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-600 whitespace-nowrap">{{ $service->freelancer?->name ?? '—' }}</td>
                        <td class="px-5 py-3 font-semibold text-gray-800 whitespace-nowrap">{{ money_aoa($service->valor ?? 0) }}</td>
                        <td class="px-5 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                {{ match($service->status) {
                                    'completed','concluido','delivered' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                    'in_progress','accepted','em_andamento','em andamento' => 'bg-sky-50 text-sky-700 border border-sky-200',
                                    'cancelled','cancelado' => 'bg-red-50 text-red-700 border border-red-200',
                                    default => 'bg-amber-50 text-amber-700 border border-amber-200',
                                } }}">
                                {{ match($service->status) {
                                    'published'       => 'Publicado',
                                    'accepted'        => 'Aceite',
                                    'negotiating'     => 'Em Negociação',
                                    'in_progress','em_andamento','em andamento' => 'Em Andamento',
                                    'delivered'       => 'Entregue',
                                    'completed','concluido' => 'Concluído',
                                    'cancelled','cancelado' => 'Cancelado',
                                    default           => $service->status,
                                } }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-500 whitespace-nowrap">{{ $service->created_at->format('d/m/Y') }}</td>
                        <td class="px-5 py-3 whitespace-nowrap">
                            <a href="{{ route('admin.service.receipt', $service) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-white whitespace-nowrap"
                               style="background:linear-gradient(135deg,#0070ff,#00baff);">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Ver Comprovativo
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            @if($services->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $services->appends(['sq' => $serviceSearch])->links() }}
            </div>
            @endif
        @endif
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     SECÇÃO 2 — Recibos Manuais do Admin
══════════════════════════════════════════════════════ --}}
<div>
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-base font-bold text-gray-800">Recibos Manuais</h2>
            <p class="text-xs text-gray-400 mt-0.5">Recibos criados manualmente pelo administrador</p>
        </div>
        <a href="{{ route('admin.recibos.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-bold text-white shadow"
           style="background:linear-gradient(135deg,#0070ff,#00baff);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Novo Recibo
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        @if($receipts->isEmpty())
            <div class="py-10 text-center text-gray-400">
                <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm">Ainda não há recibos manuais gerados.</p>
                <a href="{{ route('admin.recibos.create') }}" class="mt-3 inline-block text-sm font-semibold text-[#0070ff] hover:underline">Criar o primeiro</a>
            </div>
        @else
            <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Número</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Utilizador</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Nome / NIF</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Valor</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Data</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Gerado por</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($receipts as $receipt)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 font-mono font-semibold text-[#0070ff] whitespace-nowrap">{{ $receipt->receipt_number }}</td>
                        <td class="px-5 py-3">
                            @if($receipt->user)
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                                        style="background:linear-gradient(135deg,#0070ff,#00baff);">
                                        {{ strtoupper(substr($receipt->user->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-medium text-gray-700 truncate max-w-[120px]">{{ $receipt->user->name }}</p>
                                        <p class="text-xs text-gray-400 truncate max-w-[120px]">{{ $receipt->user->email }}</p>
                                    </div>
                                </div>
                            @else
                                <span class="text-xs text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <p class="text-gray-700">{{ $receipt->nome ?: '—' }}</p>
                            @if($receipt->nif)
                                <p class="text-xs text-gray-400">{{ $receipt->nif }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3 font-semibold text-gray-800 whitespace-nowrap">
                            @if($receipt->valor !== null)
                                {{ money_aoa($receipt->valor) }}
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-500 whitespace-nowrap">{{ $receipt->created_at->format('d/m/Y') }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $receipt->creator?->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.recibos.show', $receipt) }}"
                               class="inline-flex items-center gap-1 text-xs font-semibold text-[#0070ff] hover:underline">
                                Ver / Imprimir
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            @if($receipts->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $receipts->links() }}
            </div>
            @endif
        @endif
    </div>
</div>

@endsection
