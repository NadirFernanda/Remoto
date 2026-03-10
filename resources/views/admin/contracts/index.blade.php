@extends('layouts.dashboard')

@section('dashboard-title', 'Gestão Comercial')

@section('dashboard-actions')
    <a href="{{ route('admin.comercial.create') }}" class="btn-primary">Novo Contrato/Parceria</a>
@endsection

@section('dashboard-content')
    <div class="mb-4">
        @if(session('success'))
            <div class="p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="py-2 px-4 text-left">Parceiro</th>
                    <th class="py-2 px-4 text-left">Tipo</th>
                    <th class="py-2 px-4 text-left">Status</th>
                    <th class="py-2 px-4 text-left">Início</th>
                    <th class="py-2 px-4 text-left">Fim</th>
                    <th class="py-2 px-4 text-left">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contracts as $contract)
                <tr class="border-b">
                    <td class="py-2 px-4">{{ $contract->partner_name }}</td>
                    <td class="py-2 px-4">{{ ucfirst($contract->type) }}</td>
                    <td class="py-2 px-4">{{ ucfirst($contract->status) }}</td>
                    <td class="py-2 px-4">{{ $contract->start_date }}</td>
                    <td class="py-2 px-4">{{ $contract->end_date }}</td>
                    <td class="py-2 px-4">
                        <a href="{{ route('admin.comercial.show', $contract) }}" class="text-blue-600 hover:underline">Ver</a>
                        <a href="{{ route('admin.comercial.edit', $contract) }}" class="text-yellow-600 hover:underline ml-2">Editar</a>
                        <form action="{{ route('admin.comercial.destroy', $contract) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline ml-2" onclick="return confirm('Remover este contrato/parceria?')">Remover</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="py-4 text-center text-gray-500">Nenhum contrato/parceria cadastrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $contracts->links() }}</div>
@endsection
