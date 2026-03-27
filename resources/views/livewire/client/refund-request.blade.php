<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-rose-50/40 pb-16">

    {{-- ── Hero Header ── --}}
    <div class="bg-white border-b border-slate-100 shadow-sm">
        <div class="max-w-4xl mx-auto px-6 py-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-rose-500 to-red-600 flex items-center justify-center shadow-lg shadow-rose-200">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800 leading-tight">Solicitar Reembolso</h1>
                    <p class="text-sm text-slate-500">Envie o pedido e anexe provas, se necessário</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-6 pt-8">
        @if (session('success'))
            <div class="mb-6 px-4 py-3 rounded-2xl text-sm font-medium border border-emerald-200 bg-emerald-50 text-emerald-700 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <form wire:submit.prevent="submit" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1 text-slate-700">Pedido/Projecto</label>
                    <select wire:model="service_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-rose-200 focus:border-rose-400">
                        <option value="">Seleccione...</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}">{{ $service->titulo }} ({{ money_aoa($service->valor) }})</option>
                        @endforeach
                    </select>
                    @error('service_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-slate-700">Motivo do Reembolso</label>
                    <input type="text" wire:model="reason" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-rose-200 focus:border-rose-400" maxlength="255">
                    @error('reason') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-slate-700">Detalhes</label>
                    <textarea wire:model="details" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-rose-200 focus:border-rose-400" rows="4"></textarea>
                    @error('details') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-slate-700">Provas/Anexos (opcional)</label>
                    <input type="file" wire:model="evidence" multiple class="w-full text-sm">
                    @error('evidence.*') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    <div wire:loading wire:target="evidence" class="text-xs text-slate-400 mt-1">A carregar ficheiros...</div>
                </div>
                <div class="pt-2">
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-500 to-red-600 hover:opacity-90 text-white text-sm font-semibold transition shadow-md shadow-rose-200">
                        Enviar Pedido
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
