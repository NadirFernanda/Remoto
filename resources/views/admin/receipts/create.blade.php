@extends('layouts.dashboard')

@section('dashboard-title', 'Gerar Recibo')

@section('dashboard-content')
<div class="max-w-2xl mx-auto">

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 text-red-700 rounded-xl border border-red-200 text-sm">
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.recibos.store') }}" enctype="multipart/form-data"
          class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        @csrf

        {{-- Header do formulário --}}
        <div class="px-6 py-4 border-b border-gray-100" style="background:linear-gradient(135deg,#f0f9ff,#e0f2fe);">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white" style="background:linear-gradient(135deg,#0070ff,#00baff);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">Dados do Recibo</p>
                    <p class="text-xs text-gray-500">Todos os campos são opcionais</p>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-5">

            {{-- Secção: Identificação do Cliente --}}
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Identificação do cliente</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                    <input type="text" name="nome" value="{{ old('nome') }}"
                           placeholder="Nome completo ou empresa"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIF</label>
                    <input type="text" name="nif" value="{{ old('nif') }}"
                           placeholder="Número de identificação fiscal"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                    <input type="text" name="telefone" value="{{ old('telefone') }}"
                           placeholder="+244 9XX XXX XXX"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Endereço</label>
                    <input type="text" name="endereco" value="{{ old('endereco') }}"
                           placeholder="Rua, cidade, província"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Secção: Período --}}
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Período (opcional)</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data de Início</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data de Fim</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Notas --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notas / Descrição</label>
                <textarea name="notes" rows="3"
                          placeholder="Descreva o serviço, produto ou qualquer observação relevante..."
                          class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] resize-none">{{ old('notes') }}</textarea>
            </div>

            {{-- Documento --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Documento anexo (PDF)</label>
                <div class="flex items-center gap-3">
                    <input type="file" name="documento" id="doc-input" accept="application/pdf" class="hidden"
                           onchange="document.getElementById('doc-name').textContent = this.files[0] ? this.files[0].name : 'Nenhum ficheiro'">
                    <button type="button" onclick="document.getElementById('doc-input').click()"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 bg-gray-50 hover:bg-gray-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        Escolher PDF
                    </button>
                    <span id="doc-name" class="text-xs text-gray-400">Nenhum ficheiro</span>
                </div>
                <p class="text-xs text-gray-400 mt-1">PDF até 8 MB</p>
            </div>

        </div>

        {{-- Botões --}}
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-end gap-3">
            <a href="{{ route('admin.comercial.index') }}"
               class="px-5 py-2 rounded-xl text-sm font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 transition">
                Cancelar
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2 rounded-xl text-sm font-bold text-white shadow transition"
                    style="background:linear-gradient(135deg,#0070ff,#00baff);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Gerar Recibo
            </button>
        </div>

    </form>
</div>
@endsection
