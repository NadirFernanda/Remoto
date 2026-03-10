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
            <input type="file" name="documento" accept="application/pdf" class="form-input w-full">
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
