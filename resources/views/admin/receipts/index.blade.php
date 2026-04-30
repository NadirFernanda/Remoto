@extends('layouts.dashboard')

@section('dashboard-title', 'Recibos')

@section('dashboard-content')

@if(session('success'))
<div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm flex items-center gap-2">
    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif

<div class="flex items-center justify-between mb-5">
    <a href="{{ route('admin.comercial.index') }}" class="text-sm text-gray-500 hover:text-gray-700 font-medium flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Gestão Comercial
    </a>
    <a href="{{ route('admin.recibos.create') }}"
       class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-bold text-white shadow"
       style="background:linear-gradient(135deg,#0070ff,#00baff);">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Gerar Recibo
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    @if($receipts->isEmpty())
        <div class="py-16 text-center text-gray-400">
            <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-sm">Ainda não há recibos gerados.</p>
            <a href="{{ route('admin.recibos.create') }}" class="mt-3 inline-block text-sm font-semibold text-[#0070ff] hover:underline">Gerar o primeiro recibo</a>
        </div>
    @else
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Número</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Nome</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">NIF</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Data</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Gerado por</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($receipts as $receipt)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3 font-mono font-semibold text-[#0070ff]">{{ $receipt->receipt_number }}</td>
                    <td class="px-5 py-3 text-gray-700">{{ $receipt->nome ?: '—' }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $receipt->nif ?: '—' }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $receipt->created_at->format('d/m/Y') }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $receipt->creator?->name ?? '—' }}</td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('admin.recibos.show', $receipt) }}"
                           class="inline-flex items-center gap-1 text-xs font-semibold text-[#0070ff] hover:underline">
                            Ver / Imprimir
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($receipts->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $receipts->links() }}
        </div>
        @endif
    @endif
</div>

@endsection
