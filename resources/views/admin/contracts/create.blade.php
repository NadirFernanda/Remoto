@extends('layouts.dashboard')

@section('dashboard-title', 'Novo Contrato/Parceria')

@section('dashboard-content')
    <h2 class="text-2xl font-bold text-center mb-6">Novo Contrato/Parceria</h2>
    <form method="POST" action="{{ route('admin.comercial.store') }}" enctype="multipart/form-data" class="max-w-xl mx-auto bg-white rounded-2xl shadow p-8 space-y-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Parceiro</label>
                <input type="text" name="partner_name" class="w-full rounded-lg border border-gray-300 focus:border-[#00baff] focus:ring-2 focus:ring-[#00baff]/20 px-4 py-2 text-base transition" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tipo</label>
                <input type="text" name="type" class="w-full rounded-lg border border-gray-300 focus:border-[#00baff] focus:ring-2 focus:ring-[#00baff]/20 px-4 py-2 text-base transition" required placeholder="Fornecedor, Cliente, Parceria...">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full rounded-lg border border-gray-300 focus:border-[#00baff] focus:ring-2 focus:ring-[#00baff]/20 px-4 py-2 text-base transition">
                    <option value="ativo">Ativo</option>
                    <option value="pendente">Pendente</option>
                    <option value="encerrado">Encerrado</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Data de Início</label>
                <input type="date" name="start_date" class="w-full rounded-lg border border-gray-300 focus:border-[#00baff] focus:ring-2 focus:ring-[#00baff]/20 px-4 py-2 text-base transition">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Data de Fim</label>
                <input type="date" name="end_date" class="w-full rounded-lg border border-gray-300 focus:border-[#00baff] focus:ring-2 focus:ring-[#00baff]/20 px-4 py-2 text-base transition">
            </div>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Documento (PDF)</label>
            <div class="flex items-center gap-3">
                <input type="file" name="documento" id="doc-input" accept="application/pdf" class="hidden" onchange="document.getElementById('doc-file-name').textContent = this.files[0] ? this.files[0].name : 'Nenhum arquivo selecionado';">
                <button type="button" onclick="document.getElementById('doc-input').click();" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-[#00baff] text-[#00baff] bg-white hover:bg-[#00baff]/10 cursor-pointer text-base font-medium transition">
                    <span class="text-lg">📄</span> Escolher arquivo
                </button>
                <span id="doc-file-name" class="text-sm text-gray-500">Nenhum arquivo selecionado</span>
            </div>
            <p class="text-xs text-gray-400 mt-1">PDF até 8MB</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Notas</label>
            <textarea name="notes" class="w-full rounded-lg border border-gray-300 focus:border-[#00baff] focus:ring-2 focus:ring-[#00baff]/20 px-4 py-2 text-base transition" rows="3"></textarea>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <button type="submit" class="bg-[#00baff] hover:bg-[#009ad6] text-white font-semibold px-6 py-2 rounded-lg shadow transition">Salvar</button>
            <a href="{{ route('admin.comercial.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-2 rounded-lg shadow transition">Cancelar</a>
        </div>
    </form>
@endsection
