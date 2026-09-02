@extends('layouts.dashboard')

@section('dashboard-title')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h2 class="text-3xl font-bold text-white">Gestão Comercial</h2>
        <div class="flex items-center gap-3 flex-wrap">
            <a href="{{ route('admin.recibos.create') }}" class="inline-flex items-center gap-2 px-5 py-2 rounded-lg border border-[#00baff] text-[#00baff] bg-slate-900/60 hover:bg-[#00baff]/10 font-semibold shadow-sm transition text-base">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Gerar Recibo
            </a>
            <a href="{{ route('admin.comercial.create') }}" class="inline-flex items-center gap-2 px-5 py-2 rounded-lg bg-[#00baff] hover:bg-[#009ad6] text-white font-semibold shadow transition text-base">
                <span class="text-lg">
                    <svg width="22" height="22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11 4v14M4 11h14" stroke="white" stroke-width="2.5" stroke-linecap="round"/></svg>
                </span>
                Novo Contrato/Parceria
            </a>
        </div>
    </div>
@endsection

@section('dashboard-content')
    @if(session('success'))
        <div class="mb-4 p-3 bg-emerald-500/10 text-emerald-200 rounded-lg border border-emerald-400/20 shadow-sm">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-400">Registos filtrados</p>
            <p class="text-3xl font-semibold text-white">{{ $contracts->total() }}</p>
            <p class="text-xs text-slate-400">Total respeitando os filtros seleccionados</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-400">Activos</p>
            <p class="text-3xl font-semibold text-emerald-300">{{ $statusTotals['ativo'] }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-400">Pendente</p>
            <p class="text-3xl font-semibold text-amber-300">{{ $statusTotals['pendente'] }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-400">Encerrados</p>
            <p class="text-3xl font-semibold text-slate-300">{{ $statusTotals['encerrado'] }}</p>
        </div>
    </div>

    <form method="GET" class="rounded-2xl border border-white/10 bg-slate-900/70 p-4 mb-5 space-y-4">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
            <div class="lg:col-span-2">
                <label class="block text-xs text-slate-400 mb-1">Pesquisar parceiro, tipo ou notas</label>
                <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Pesquisar termo livre"
                    class="w-full border border-white/10 bg-slate-950/60 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1">Tipo</label>
                <input list="contract-types" name="type" value="{{ $filters['type'] }}" placeholder="Fornecedor, Cliente..."
                    class="w-full border border-white/10 bg-slate-950/60 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1">Status</label>
                <select name="status" class="w-full border border-white/10 bg-slate-950/60 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
                    <option value="">Todos os status</option>
                    <option value="ativo" @selected($filters['status'] === 'ativo')>Activo</option>
                    <option value="pendente" @selected($filters['status'] === 'pendente')>Pendente</option>
                    <option value="encerrado" @selected($filters['status'] === 'encerrado')>Encerrado</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs text-slate-400 mb-1">Início (De)</label>
                <input type="date" name="start_date_from" value="{{ $filters['start_date_from'] }}"
                    class="w-full border border-white/10 bg-slate-950/60 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1">Início (Até)</label>
                <input type="date" name="start_date_to" value="{{ $filters['start_date_to'] }}"
                    class="w-full border border-white/10 bg-slate-950/60 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1">Fim (De)</label>
                <input type="date" name="end_date_from" value="{{ $filters['end_date_from'] }}"
                    class="w-full border border-white/10 bg-slate-950/60 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1">Fim (Até)</label>
                <input type="date" name="end_date_to" value="{{ $filters['end_date_to'] }}"
                    class="w-full border border-white/10 bg-slate-950/60 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="submit" class="bg-[#00baff] hover:bg-[#009ad6] text-white font-semibold px-5 py-2 rounded-lg text-sm shadow transition">Filtrar</button>
            <a href="{{ route('admin.comercial.index') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold px-5 py-2 rounded-lg text-sm shadow transition">Limpar</a>
        </div>
        <datalist id="contract-types">
            @foreach($types as $type)
                <option value="{{ $type }}"></option>
            @endforeach
        </datalist>
    </form>

    <div class="overflow-x-auto rounded-2xl shadow bg-slate-900/70 border border-white/10">
        <table class="min-w-full text-base">
            <thead>
                <tr class="bg-slate-950/60 text-[#00baff] uppercase text-xs tracking-wider">
                    <th class="py-3 px-5 text-left font-semibold">Parceiro</th>
                    <th class="py-3 px-5 text-left font-semibold">Tipo</th>
                    <th class="py-3 px-5 text-left font-semibold">Status</th>
                    <th class="py-3 px-5 text-left font-semibold">Início</th>
                    <th class="py-3 px-5 text-left font-semibold">Fim</th>
                    <th class="py-3 px-5 text-left font-semibold">Notas</th>
                    <th class="py-3 px-5 text-left font-semibold">Documento</th>
                    <th class="py-3 px-5 text-left font-semibold">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contracts as $contract)
                <tr class="border-b border-white/10 last:border-0 hover:bg-slate-800/60 transition">
                    <td class="py-3 px-5 font-medium text-white">{{ $contract->partner_name }}</td>
                    <td class="py-3 px-5 text-slate-200">{{ ucfirst($contract->type) }}</td>
                    <td class="py-3 px-5">
                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-bold @if($contract->status=='ativo') bg-emerald-500/15 text-emerald-300 border border-emerald-400/20 @elseif($contract->status=='pendente') bg-amber-500/15 text-amber-300 border border-amber-400/20 @else bg-slate-700 text-slate-200 border border-slate-600 @endif">
                            {{ ucfirst($contract->status) }}
                        </span>
                    </td>
                    <td class="py-3 px-5 text-slate-300">{{ $contract->start_date ? \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') : '—' }}</td>
                    <td class="py-3 px-5 text-slate-300">{{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') : '—' }}</td>
                    <td class="py-3 px-5 text-sm text-slate-400">
                        @if($contract->notes)
                            {{ \Illuminate\Support\Str::limit($contract->notes, 70) }}
                        @else
                            <span class="text-slate-500">Sem notas registadas.</span>
                        @endif
                    </td>
                    <td class="py-3 px-5 text-sm">
                        @if($contract->document_path)
                            <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($contract->document_path) }}" target="_blank" class="text-emerald-300 hover:text-emerald-200 font-medium">Abrir PDF</a>
                        @else
                            <span class="text-slate-500">Sem documento</span>
                        @endif
                    </td>
                    <td class="py-3 px-5 whitespace-nowrap flex flex-wrap gap-2">
                        <a href="{{ route('admin.comercial.show', $contract) }}" class="inline-flex items-center gap-1 text-sky-300 hover:text-sky-200 font-medium transition">
                            @include('components.icon', ['name' => 'search', 'class' => 'w-3.5 h-3.5'])
                            Ver
                        </a>
                        <a href="{{ route('admin.comercial.edit', $contract) }}" class="inline-flex items-center gap-1 text-amber-300 hover:text-amber-200 font-medium transition">
                            @include('components.icon', ['name' => 'pencil', 'class' => 'w-3.5 h-3.5'])
                            Editar
                        </a>
                        <form action="{{ route('admin.comercial.destroy', $contract) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1 text-rose-300 hover:text-rose-200 font-medium transition" onclick="return confirm('Remover este contrato/parceria?')">
                                @include('components.icon', ['name' => 'trash', 'class' => 'w-3.5 h-3.5'])
                                Remover
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-8 text-center text-slate-400 text-lg">
                        <div class="flex flex-col items-center gap-2">
                            @include('components.icon', ['name' => 'file', 'class' => 'w-10 h-10'])
                            Nenhum contrato/parceria registado.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6 text-slate-300">{{ $contracts->links() }}</div>
@endsection
