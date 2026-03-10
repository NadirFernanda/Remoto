@extends('layouts.dashboard')

@section('dashboard-title', 'Editar Contrato/Parceria')

@section('dashboard-content')
    <form method="POST" action="{{ route('admin.comercial.update', $contract) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block mb-1 font-medium">Parceiro</label>
            <input type="text" name="partner_name" class="form-input w-full" value="{{ $contract->partner_name }}" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-medium">Tipo</label>
            <input type="text" name="type" class="form-input w-full" value="{{ $contract->type }}" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-medium">Status</label>
            <select name="status" class="form-input w-full">
                <option value="ativo" @if($contract->status=='ativo') selected @endif>Ativo</option>
                <option value="pendente" @if($contract->status=='pendente') selected @endif>Pendente</option>
                <option value="encerrado" @if($contract->status=='encerrado') selected @endif>Encerrado</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-medium">Data de Início</label>
            <input type="date" name="start_date" class="form-input w-full" value="{{ $contract->start_date }}">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-medium">Data de Fim</label>
            <input type="date" name="end_date" class="form-input w-full" value="{{ $contract->end_date }}">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-medium">Documento (PDF)</label>
            <div class="relative">
                <input type="file" name="documento" id="doc-input" accept="application/pdf" class="opacity-0 absolute inset-0 w-full h-full cursor-pointer" onchange="document.getElementById('doc-file-name').textContent = this.files[0] ? this.files[0].name : 'Nenhum arquivo selecionado';">
                <button type="button" onclick="document.getElementById('doc-input').click();" class="inline-flex items-center gap-2 px-4 py-2 rounded border border-[#00baff] text-[#00baff] bg-white hover:bg-[#00baff]/5 cursor-pointer text-sm font-medium transition">
                    📄 Escolher arquivo
                </button>
                <span id="doc-file-name" class="ml-2 text-sm text-gray-500">Nenhum arquivo selecionado</span>
            </div>
            @if($contract->document_path)
                <a href="{{ asset('storage/' . $contract->document_path) }}" target="_blank" class="text-blue-600 underline text-xs mt-1 inline-block">Ver documento atual</a>
            @endif
            <p class="text-xs text-gray-400 mt-1">PDF até 8MB</p>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-medium">Notas</label>
            <textarea name="notes" class="form-input w-full">{{ $contract->notes }}</textarea>
        </div>
        <button type="submit" class="btn-primary">Salvar</button>
        <a href="{{ route('admin.comercial.index') }}" class="btn-outline ml-2">Cancelar</a>
    </form>
@endsection
