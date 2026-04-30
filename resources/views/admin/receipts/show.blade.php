@extends('layouts.dashboard')

@section('dashboard-title', 'Recibo ' . $recibo->receipt_number)

@section('dashboard-content')

@if(session('success'))
<div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm flex items-center gap-2">
    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif

{{-- Acções --}}
<div class="flex items-center gap-3 mb-5 no-print">
    <a href="{{ route('admin.recibos.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Todos os recibos
    </a>
    <span class="text-gray-300">|</span>
    <button onclick="window.print()"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white shadow"
            style="background:linear-gradient(135deg,#0070ff,#00baff);">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        Imprimir / Guardar PDF
    </button>
    <form method="POST" action="{{ route('admin.recibos.destroy', $recibo) }}" class="ml-auto"
          onsubmit="return confirm('Eliminar este recibo permanentemente?')">
        @csrf @method('DELETE')
        <button type="submit" class="inline-flex items-center gap-1.5 text-sm text-red-500 hover:text-red-700 font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Eliminar
        </button>
    </form>
</div>

{{-- Recibo imprimível --}}
<div class="receipt-card bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden max-w-2xl mx-auto" id="receipt-print">

    {{-- Cabeçalho da empresa --}}
    <div style="background:linear-gradient(135deg,#0052cc,#0a1228);padding:2rem 2.5rem 1.5rem;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;">
            <div>
                <div style="font-size:1.4rem;font-weight:800;color:#fff;letter-spacing:-.02em;">24 HORAS</div>
                <div style="font-size:.75rem;color:rgba(255,255,255,.6);margin-top:.2rem;">Plataforma de Freelancers · Angola</div>
            </div>
            <div style="text-align:right;">
                <div style="display:inline-block;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:8px;padding:.3rem .8rem;">
                    <div style="font-size:.65rem;color:rgba(255,255,255,.6);text-transform:uppercase;letter-spacing:.08em;">Recibo</div>
                    <div style="font-size:1rem;font-weight:800;color:#fff;margin-top:.1rem;">{{ $recibo->receipt_number }}</div>
                </div>
                <div style="font-size:.72rem;color:rgba(255,255,255,.5);margin-top:.5rem;">
                    Emitido em {{ $recibo->created_at->format('d/m/Y') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Dados do cliente --}}
    @if($recibo->nome || $recibo->nif || $recibo->telefone || $recibo->endereco)
    <div style="padding:1.5rem 2.5rem;border-bottom:1px solid #f1f5f9;">
        <p style="font-size:.65rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:.75rem;">Dados do Cliente</p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem 2rem;">
            @if($recibo->nome)
            <div>
                <div style="font-size:.7rem;color:#94a3b8;">Nome</div>
                <div style="font-size:.9rem;font-weight:600;color:#1e293b;">{{ $recibo->nome }}</div>
            </div>
            @endif
            @if($recibo->nif)
            <div>
                <div style="font-size:.7rem;color:#94a3b8;">NIF</div>
                <div style="font-size:.9rem;font-weight:600;color:#1e293b;">{{ $recibo->nif }}</div>
            </div>
            @endif
            @if($recibo->telefone)
            <div>
                <div style="font-size:.7rem;color:#94a3b8;">Telefone</div>
                <div style="font-size:.9rem;font-weight:600;color:#1e293b;">{{ $recibo->telefone }}</div>
            </div>
            @endif
            @if($recibo->endereco)
            <div>
                <div style="font-size:.7rem;color:#94a3b8;">Endereço</div>
                <div style="font-size:.9rem;font-weight:600;color:#1e293b;">{{ $recibo->endereco }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Período --}}
    @if($recibo->start_date || $recibo->end_date)
    <div style="padding:1.25rem 2.5rem;border-bottom:1px solid #f1f5f9;background:#fafafa;">
        <p style="font-size:.65rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:.75rem;">Período</p>
        <div style="display:flex;gap:2rem;">
            @if($recibo->start_date)
            <div>
                <div style="font-size:.7rem;color:#94a3b8;">Início</div>
                <div style="font-size:.9rem;font-weight:600;color:#1e293b;">{{ $recibo->start_date->format('d/m/Y') }}</div>
            </div>
            @endif
            @if($recibo->end_date)
            <div>
                <div style="font-size:.7rem;color:#94a3b8;">Fim</div>
                <div style="font-size:.9rem;font-weight:600;color:#1e293b;">{{ $recibo->end_date->format('d/m/Y') }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Notas --}}
    @if($recibo->notes)
    <div style="padding:1.25rem 2.5rem;border-bottom:1px solid #f1f5f9;">
        <p style="font-size:.65rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:.5rem;">Descrição / Notas</p>
        <p style="font-size:.88rem;color:#334155;line-height:1.6;white-space:pre-line;">{{ $recibo->notes }}</p>
    </div>
    @endif

    {{-- Documento anexo --}}
    @if($recibo->document_path)
    <div class="no-print" style="padding:1rem 2.5rem;border-bottom:1px solid #f1f5f9;">
        <a href="{{ Storage::url($recibo->document_path) }}" target="_blank"
           style="display:inline-flex;align-items:center;gap:.5rem;font-size:.82rem;font-weight:600;color:#0070ff;text-decoration:none;">
            <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
            </svg>
            Ver documento anexo (PDF)
        </a>
    </div>
    @endif

    {{-- Rodapé --}}
    <div style="padding:1rem 2.5rem;background:#f8fafc;display:flex;align-items:center;justify-content:space-between;">
        <span style="font-size:.72rem;color:#94a3b8;">Gerado por {{ $recibo->creator?->name ?? 'Administrador' }}</span>
        <span style="font-size:.72rem;color:#cbd5e1;">24horas.ao</span>
    </div>

</div>

<style>
@media print {
    .no-print { display: none !important; }
    body * { visibility: hidden; }
    #receipt-print, #receipt-print * { visibility: visible; }
    #receipt-print { position: absolute; left: 0; top: 0; width: 100%; border: none !important; box-shadow: none !important; border-radius: 0 !important; }
}
</style>

@endsection
