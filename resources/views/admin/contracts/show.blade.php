@extends('layouts.dashboard')

@section('dashboard-title')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h2 class="text-3xl font-bold text-white">Detalhes do Contrato/Parceria</h2>
        <div class="flex gap-3 flex-wrap">
            <a href="{{ route('admin.comercial.edit', $contract) }}"
               class="inline-flex items-center gap-2 px-5 py-2 rounded-lg border border-[#00baff] text-[#00baff] hover:bg-[#00baff]/10 font-semibold text-sm transition">
                @include('components.icon', ['name' => 'pencil', 'class' => 'w-4 h-4'])
                Editar
            </a>
            <a href="{{ route('admin.comercial.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2 rounded-lg bg-white/5 border border-white/10 text-slate-200 hover:bg-white/10 font-semibold text-sm transition">
                @include('components.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4'])
                Voltar
            </a>
        </div>
    </div>
@endsection

@section('dashboard-content')
    <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-6 shadow-sm">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <dt class="text-xs uppercase tracking-wide mb-1 text-slate-400">Parceiro</dt>
                <dd class="text-base font-semibold text-white">{{ $contract->partner_name }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide mb-1 text-slate-400">Tipo</dt>
                <dd class="text-base text-slate-200">{{ ucfirst($contract->type) }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide mb-1 text-slate-400">Status</dt>
                <dd>
                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-bold @if($contract->status=='ativo') bg-emerald-500/15 text-emerald-300 border border-emerald-400/20 @elseif($contract->status=='pendente') bg-amber-500/15 text-amber-300 border border-amber-400/20 @else bg-slate-700 text-slate-200 border border-slate-600 @endif">
                        {{ ucfirst($contract->status) }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide mb-1 text-slate-400">Documento</dt>
                <dd>
                    @if($contract->document_path)
                        <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($contract->document_path) }}" target="_blank"
                           class="inline-flex items-center gap-1 text-emerald-300 hover:text-emerald-200 font-medium text-sm">
                            @include('components.icon', ['name' => 'file-text', 'class' => 'w-4 h-4'])
                            Abrir PDF
                        </a>
                    @else
                        <span class="text-slate-400 text-sm">Sem documento anexado</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide mb-1 text-slate-400">Data de Início</dt>
                <dd class="text-base text-slate-200">
                    {{ $contract->start_date ? \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') : '—' }}
                </dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide mb-1 text-slate-400">Data de Fim</dt>
                <dd class="text-base text-slate-200">
                    {{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') : '—' }}
                </dd>
            </div>
            <div class="md:col-span-2">
                <dt class="text-xs uppercase tracking-wide mb-1 text-slate-400">Notas</dt>
                <dd class="text-sm text-slate-300 whitespace-pre-line">
                    {{ $contract->notes ?: '—' }}
                </dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide mb-1 text-slate-400">Criado em</dt>
                <dd class="text-sm text-slate-400">{{ $contract->created_at->format('d/m/Y H:i') }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide mb-1 text-slate-400">Última atualização</dt>
                <dd class="text-sm text-slate-400">{{ $contract->updated_at->format('d/m/Y H:i') }}</dd>
            </div>
        </dl>
    </div>
@endsection
