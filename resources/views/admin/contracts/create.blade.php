@extends('layouts.dashboard')

@section('dashboard-title', '')

@section('dashboard-content')
    <h2 class="text-2xl font-bold text-center mb-6 text-white">Novo Contrato/Parceria</h2>
    @if ($errors->any())
        <div class="max-w-xl mx-auto mb-4 p-3 bg-rose-500/10 text-rose-200 rounded-lg border border-rose-400/20">
            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('admin.comercial.store') }}" enctype="multipart/form-data" class="max-w-xl mx-auto bg-slate-900/70 border border-white/10 rounded-2xl shadow p-8 space-y-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-2">Parceiro</label>
                <input type="text" name="partner_name" value="{{ old('partner_name') }}" class="w-full rounded-lg border border-white/10 bg-slate-950/60 text-white focus:border-[#00baff] focus:ring-2 focus:ring-[#00baff]/20 px-4 py-2 text-base transition" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-2">Tipo</label>
                <input type="text" name="type" value="{{ old('type') }}" class="w-full rounded-lg border border-white/10 bg-slate-950/60 text-white focus:border-[#00baff] focus:ring-2 focus:ring-[#00baff]/20 px-4 py-2 text-base transition" required placeholder="Fornecedor, Cliente, Parceria...">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-2">Status</label>
                <select name="status" class="w-full rounded-lg border border-white/10 bg-slate-950/60 text-white focus:border-[#00baff] focus:ring-2 focus:ring-[#00baff]/20 px-4 py-2 text-base transition">
                    <option value="ativo" @selected(old('status')==='ativo')>Activo</option>
                    <option value="pendente" @selected(old('status', 'pendente')==='pendente')>Pendente</option>
                    <option value="encerrado" @selected(old('status')==='encerrado')>Encerrado</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-2">Data de Início</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}" class="w-full rounded-lg border border-white/10 bg-slate-950/60 text-white focus:border-[#00baff] focus:ring-2 focus:ring-[#00baff]/20 px-4 py-2 text-base transition">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-2">Data de Fim</label>
                <input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full rounded-lg border border-white/10 bg-slate-950/60 text-white focus:border-[#00baff] focus:ring-2 focus:ring-[#00baff]/20 px-4 py-2 text-base transition">
            </div>
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-300 mb-2">Documento (PDF)</label>
            <div class="flex items-center gap-3">
                <input type="file" name="documento" id="doc-input" accept="application/pdf" class="hidden" onchange="document.getElementById('doc-file-name').textContent = this.files[0] ? this.files[0].name : 'Nenhum ficheiro seleccionado';">
                <button type="button" onclick="document.getElementById('doc-input').click();" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-[#00baff] text-[#00baff] bg-slate-900/60 hover:bg-[#00baff]/10 cursor-pointer text-base font-medium transition">
                    @include('components.icon', ['name' => 'file', 'class' => 'w-4 h-4'])
                    Escolher ficheiro
                </button>
                <span id="doc-file-name" class="text-sm text-slate-400">Nenhum ficheiro seleccionado</span>
            </div>
            <p class="text-xs text-slate-500 mt-1">PDF até 8MB</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-300 mb-2">Notas</label>
            <textarea name="notes" class="w-full rounded-lg border border-white/10 bg-slate-950/60 text-white focus:border-[#00baff] focus:ring-2 focus:ring-[#00baff]/20 px-4 py-2 text-base transition" rows="3">{{ old('notes') }}</textarea>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <button type="submit" class="bg-[#00baff] hover:bg-[#009ad6] text-white font-semibold px-6 py-2 rounded-lg shadow transition">Guardar</button>
            <a href="{{ route('admin.comercial.index') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold px-6 py-2 rounded-lg shadow transition">Cancelar</a>
        </div>
    </form>
@endsection
