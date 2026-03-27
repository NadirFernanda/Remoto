@extends('layouts.dashboard')

@section('dashboard-title')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h2 class="text-3xl font-bold text-gray-900">Detalhes do Contrato/Parceria</h2>
        <div class="flex gap-3">
            <a href="{{ route('admin.comercial.edit', $contract) }}"
               class="inline-flex items-center gap-2 px-5 py-2 rounded-lg border border-[#00baff] text-[#00baff] hover:bg-[#00baff]/10 font-semibold text-sm transition">
                ✏️ Editar
            </a>
            <a href="{{ route('admin.comercial.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm transition">
                ← Voltar
            </a>
        </div>
    </div>
@endsection

@section('dashboard-content')
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wide mb-1">Parceiro</dt>
                <dd class="text-base font-semibold text-gray-900">{{ $contract->partner_name }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wide mb-1">Tipo</dt>
                <dd class="text-base text-gray-800">{{ ucfirst($contract->type) }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wide mb-1">Status</dt>
                <dd>
                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-bold
                        @if($contract->status=='ativo') bg-green-100 text-green-700
                        @elseif($contract->status=='pendente') bg-yellow-100 text-yellow-700
                        @else bg-gray-200 text-gray-600 @endif">
                        {{ ucfirst($contract->status) }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wide mb-1">Documento</dt>
                <dd>
                    @if($contract->document_path)
                        <a href="{{ asset('storage/' . $contract->document_path) }}" target="_blank"
                           class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-800 font-medium text-sm">
                            📄 Abrir PDF
                        </a>
                    @else
                        <span class="text-gray-400 text-sm">Sem documento anexado</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wide mb-1">Data de Início</dt>
                <dd class="text-base text-gray-800">
                    {{ $contract->start_date ? \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') : '—' }}
                </dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wide mb-1">Data de Fim</dt>
                <dd class="text-base text-gray-800">
                    {{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') : '—' }}
                </dd>
            </div>
            <div class="md:col-span-2">
                <dt class="text-xs uppercase text-gray-500 tracking-wide mb-1">Notas</dt>
                <dd class="text-sm text-gray-700 whitespace-pre-line">
                    {{ $contract->notes ?: '—' }}
                </dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wide mb-1">Criado em</dt>
                <dd class="text-sm text-gray-500">{{ $contract->created_at->format('d/m/Y H:i') }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-gray-500 tracking-wide mb-1">Última atualização</dt>
                <dd class="text-sm text-gray-500">{{ $contract->updated_at->format('d/m/Y H:i') }}</dd>
            </div>
        </dl>
    </div>
@endsection
