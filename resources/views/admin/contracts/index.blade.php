@extends('layouts.dashboard')

@section('dashboard-title')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h2 class="text-3xl font-bold text-gray-900">Gestão Comercial</h2>
        <a href="{{ route('admin.comercial.create') }}" class="inline-flex items-center gap-2 px-5 py-2 rounded-lg bg-[#00baff] hover:bg-[#009ad6] text-white font-semibold shadow transition text-base">
            <span class="text-lg">
                <svg width="22" height="22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11 4v14M4 11h14" stroke="white" stroke-width="2.5" stroke-linecap="round"/></svg>
            </span>
            Novo Contrato/Parceria
        </a>
    </div>
@endsection

@section('dashboard-content')
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg border border-green-200 shadow-sm">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs uppercase text-gray-500 tracking-wide">Registos filtrados</p>
            <p class="text-3xl font-semibold text-gray-900">{{ $contracts->total() }}</p>
            <p class="text-xs text-gray-400">Total respeitando os filtros seleccionados</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs uppercase text-gray-500 tracking-wide">Activos</p>
            <p class="text-3xl font-semibold text-emerald-600">{{ $statusTotals['ativo'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs uppercase text-gray-500 tracking-wide">Pendente</p>
            <p class="text-3xl font-semibold text-amber-500">{{ $statusTotals['pendente'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs uppercase text-gray-500 tracking-wide">Encerrados</p>
            <p class="text-3xl font-semibold text-gray-500">{{ $statusTotals['encerrado'] }}</p>
        </div>
    </div>

    <form method="GET" class="bg-white rounded-2xl border border-gray-200 p-4 mb-5 space-y-4">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
            <div class="lg:col-span-2">
                <label class="block text-xs text-gray-500 mb-1">Pesquisar parceiro, tipo ou notas</label>
                <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Pesquisar termo livre"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Tipo</label>
                <input list="contract-types" name="type" value="{{ $filters['type'] }}" placeholder="Fornecedor, Cliente..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
                    <option value="">Todos os status</option>
                    <option value="ativo" @selected($filters['status'] === 'ativo')>Activo</option>
                    <option value="pendente" @selected($filters['status'] === 'pendente')>Pendente</option>
                    <option value="encerrado" @selected($filters['status'] === 'encerrado')>Encerrado</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Início (De)</label>
                <input type="date" name="start_date_from" value="{{ $filters['start_date_from'] }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Início (Até)</label>
                <input type="date" name="start_date_to" value="{{ $filters['start_date_to'] }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Fim (De)</label>
                <input type="date" name="end_date_from" value="{{ $filters['end_date_from'] }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Fim (Até)</label>
                <input type="date" name="end_date_to" value="{{ $filters['end_date_to'] }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="submit" class="bg-[#00baff] hover:bg-[#009ad6] text-white font-semibold px-5 py-2 rounded-lg text-sm shadow transition">Filtrar</button>
            <a href="{{ route('admin.comercial.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-5 py-2 rounded-lg text-sm shadow transition">Limpar</a>
        </div>
        <datalist id="contract-types">
            @foreach($types as $type)
                <option value="{{ $type }}"></option>
            @endforeach
        </datalist>
    </form>

    <div class="overflow-x-auto rounded-2xl shadow bg-white">
        <table class="min-w-full text-base">
            <thead>
                <tr class="bg-[#f4fbfd] text-[#00baff] uppercase text-xs tracking-wider">
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
                <tr class="border-b last:border-0 hover:bg-[#f8fdff] transition">
                    <td class="py-3 px-5 font-medium text-gray-900">{{ $contract->partner_name }}</td>
                    <td class="py-3 px-5">{{ ucfirst($contract->type) }}</td>
                    <td class="py-3 px-5">
                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-bold
                            @if($contract->status=='ativo') bg-green-100 text-green-700
                            @elseif($contract->status=='pendente') bg-yellow-100 text-yellow-700
                            @else bg-gray-200 text-gray-700 @endif">
                            {{ ucfirst($contract->status) }}
                        </span>
                    </td>
                    <td class="py-3 px-5">{{ $contract->start_date ? \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') : '—' }}</td>
                    <td class="py-3 px-5">{{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') : '—' }}</td>
                    <td class="py-3 px-5 text-sm text-gray-500">
                        @if($contract->notes)
                            {{ \Illuminate\Support\Str::limit($contract->notes, 70) }}
                        @else
                            <span class="text-gray-400">Sem notas registadas.</span>
                        @endif
                    </td>
                    <td class="py-3 px-5 text-sm">
                        @if($contract->document_path)
                            <a href="{{ asset('storage/' . $contract->document_path) }}" target="_blank" class="text-emerald-600 hover:text-emerald-800 font-medium">Abrir PDF</a>
                        @else
                            <span class="text-gray-400">Sem documento</span>
                        @endif
                    </td>
                    <td class="py-3 px-5 whitespace-nowrap flex flex-wrap gap-2">
                        <a href="{{ route('admin.comercial.show', $contract) }}" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 font-medium transition"><span>🔎</span>Ver</a>
                        <a href="{{ route('admin.comercial.edit', $contract) }}" class="inline-flex items-center gap-1 text-yellow-600 hover:text-yellow-800 font-medium transition"><span>✏️</span>Editar</a>
                        <form action="{{ route('admin.comercial.destroy', $contract) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 font-medium transition" onclick="return confirm('Remover este contrato/parceria?')"><span>🗑️</span>Remover</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-8 text-center text-gray-400 text-lg">
                        <div class="flex flex-col items-center gap-2">
                            <span class="text-5xl">📄</span>
                            Nenhum contrato/parceria registado.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $contracts->links() }}</div>
@endsection
