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
    <div class="overflow-x-auto rounded-2xl shadow bg-white">
        <table class="min-w-full text-base">
            <thead>
                <tr class="bg-[#f4fbfd] text-[#00baff] uppercase text-xs tracking-wider">
                    <th class="py-3 px-5 text-left font-semibold">Parceiro</th>
                    <th class="py-3 px-5 text-left font-semibold">Tipo</th>
                    <th class="py-3 px-5 text-left font-semibold">Status</th>
                    <th class="py-3 px-5 text-left font-semibold">Início</th>
                    <th class="py-3 px-5 text-left font-semibold">Fim</th>
                    <th class="py-3 px-5 text-left font-semibold">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contracts as $contract)
                <tr class="border-b last:border-0 hover:bg-[#f8fdff] transition">
                    <td class="py-3 px-5">{{ $contract->partner_name }}</td>
                    <td class="py-3 px-5">{{ ucfirst($contract->type) }}</td>
                    <td class="py-3 px-5">
                        <span class="inline-block px-2 py-1 rounded-full text-xs font-bold
                            @if($contract->status=='ativo') bg-green-100 text-green-700
                            @elseif($contract->status=='pendente') bg-yellow-100 text-yellow-700
                            @else bg-gray-200 text-gray-700 @endif">
                            {{ ucfirst($contract->status) }}
                        </span>
                    </td>
                    <td class="py-3 px-5">{{ $contract->start_date }}</td>
                    <td class="py-3 px-5">{{ $contract->end_date }}</td>
                    <td class="py-3 px-5 whitespace-nowrap">
                        <a href="{{ route('admin.comercial.show', $contract) }}" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 font-medium transition"><span>🔎</span>Ver</a>
                        <a href="{{ route('admin.comercial.edit', $contract) }}" class="inline-flex items-center gap-1 text-yellow-600 hover:text-yellow-800 font-medium ml-3 transition"><span>✏️</span>Editar</a>
                        <form action="{{ route('admin.comercial.destroy', $contract) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 font-medium ml-3 transition" onclick="return confirm('Remover este contrato/parceria?')"><span>🗑️</span>Remover</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-gray-400 text-lg">
                        <div class="flex flex-col items-center gap-2">
                            <span class="text-5xl">📄</span>
                            Nenhum contrato/parceria cadastrado.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $contracts->links() }}</div>
@endsection
