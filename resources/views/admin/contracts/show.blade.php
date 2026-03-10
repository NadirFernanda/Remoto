@extends('layouts.dashboard')

@section('dashboard-title', 'Detalhes do Contrato/Parceria')

@section('dashboard-content')
    <div class="mb-4">
        <a href="{{ route('admin.comercial.edit', $contract) }}" class="btn-outline">Editar</a>
        <a href="{{ route('admin.comercial.index') }}" class="btn-outline ml-2">Voltar</a>
    </div>
    <div class="bg-white rounded shadow p-6">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <dt class="font-semibold">Parceiro</dt>
                <dd>{{ $contract->partner_name }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Tipo</dt>
                <dd>{{ ucfirst($contract->type) }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Status</dt>
                <dd>{{ ucfirst($contract->status) }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Data de Início</dt>
                <dd>{{ $contract->start_date }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Data de Fim</dt>
                <dd>{{ $contract->end_date }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Documento</dt>
                <dd>
                    @if($contract->document_path)
                        <a href="{{ asset('storage/' . $contract->document_path) }}" target="_blank" class="text-blue-600 underline">Ver PDF</a>
                    @else
                        <span class="text-gray-400">Nenhum documento</span>
                    @endif
                </dd>
            </div>
            <div class="md:col-span-2">
                <dt class="font-semibold">Notas</dt>
                <dd>{{ $contract->notes }}</dd>
            </div>
        </dl>
    </div>
@endsection
