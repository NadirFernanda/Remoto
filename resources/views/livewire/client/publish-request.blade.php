<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-sky-50/40 pb-16">

    {{-- ── Header ── --}}
    <div class="bg-gradient-to-r from-[#00c8ff] to-[#0033cc] rounded-2xl p-6 text-white mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold leading-tight">O que precisa?</h1>
                <p class="text-sm text-white/80 mt-0.5">Descreva o seu pedido de forma clara e objectiva</p>
            </div>
        </div>
    </div>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">

            <form wire:submit.prevent="submit" class="space-y-5">
                <div>
                    <label for="title" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Título do pedido <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" wire:model.defer="title" maxlength="100" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 bg-white focus:ring-2 focus:ring-sky-200 focus:border-sky-400 outline-none transition"
                        placeholder="Ex: Site institucional para minha empresa">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="need" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Descreva a sua necessidade <span class="text-red-500">*</span>
                    </label>
                    <textarea id="need" wire:model.defer="need" rows="5" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 bg-white focus:ring-2 focus:ring-sky-200 focus:border-sky-400 outline-none transition resize-none"
                        placeholder="Ex: Preciso de um logotipo moderno para a minha empresa de tecnologia..."></textarea>
                    @error('need') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-[#00c8ff] to-[#0055ff] hover:from-sky-400 hover:to-blue-600 text-white font-bold py-3.5 rounded-xl transition-all shadow-md shadow-sky-200/40 flex items-center justify-center gap-2">
                    Continuar
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </form>
        </div>
    </div>

</div>
