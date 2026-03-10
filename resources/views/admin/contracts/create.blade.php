@extends('layouts.dashboard')

@section('dashboard-title', 'Novo Contrato/Parceria')

@section('dashboard-content')
    <form method="POST" action="{{ route('admin.comercial.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
            <label class="block mb-1 font-medium">Parceiro</label>
            <input type="text" name="partner_name" class="form-input w-full" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-medium">Tipo</label>
            <input type="text" name="type" class="form-input w-full" required placeholder="Fornecedor, Cliente, Parceria...">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-medium">Status</label>
            <select name="status" class="form-input w-full">
                <option value="ativo">Ativo</option>
                <option value="pendente">Pendente</option>
                <option value="encerrado">Encerrado</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-medium">Data de Início</label>
            <input type="date" name="start_date" class="form-input w-full">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-medium">Data de Fim</label>
            <input type="date" name="end_date" class="form-input w-full">
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
            <p class="text-xs text-gray-400 mt-1">PDF até 8MB</p>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-medium">Notas</label>
            <textarea name="notes" class="form-input w-full"></textarea>
        </div>
        <button type="submit" class="btn-primary">Salvar</button>
        <a href="{{ route('admin.comercial.index') }}" class="btn-outline ml-2">Cancelar</a>
    </form>
@endsection
