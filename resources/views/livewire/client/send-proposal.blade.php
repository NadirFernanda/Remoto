<div>
    @if($show)
        <div
            x-data="{ handleTab(e) { const els = Array.from($el.querySelectorAll('a,button,input,textarea,select,[tabindex]')).filter(el => el.getAttribute('tabindex') !== '-1' && !el.hasAttribute('disabled')); const first = els[0]; const last = els[els.length-1]; if (!first) return; if (e.shiftKey) { if (document.activeElement === first) { e.preventDefault(); last.focus(); } } else { if (document.activeElement === last) { e.preventDefault(); first.focus(); } } } }"
            x-init="$nextTick(() => { const f = $el.querySelector('[autofocus]'); if(f) f.focus(); document.body.classList.add('overflow-hidden'); })"
            @keydown.window.escape="$wire.close()"
            @keydown.window="if ($event.key === 'Tab') handleTab($event)"
            class="fixed inset-0 z-50 flex items-center justify-center px-4">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/50" @click="$wire.close()"></div>

            {{-- Modal --}}
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">

                {{-- Cabeçalho --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Enviar proposta</h3>
                        <p class="text-sm text-gray-500 mt-0.5">Descreva o projeto e o que pretende do freelancer</p>
                    </div>
                    <button type="button" wire:click="close" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Corpo com scroll --}}
                <div class="overflow-y-auto flex-1 px-6 py-5 space-y-5">

                    {{-- Título --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Título do projeto <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            wire:model="title"
                            autofocus
                            maxlength="120"
                            placeholder="Ex: Desenvolvimento de landing page em WordPress"
                            class="block w-full rounded-xl border border-gray-200 py-2.5 px-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition @error('title') border-red-400 @enderror">
                        @error('title')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Mensagem --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Descrição / mensagem <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            wire:model="message"
                            rows="5"
                            maxlength="5000"
                            placeholder="Descreva o projeto, os requisitos, prazos e qualquer detalhe relevante para o freelancer..."
                            class="block w-full rounded-xl border border-gray-200 py-2.5 px-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition resize-none @error('message') border-red-400 @enderror"></textarea>
                        @error('message')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Orçamento --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Orçamento estimado <span class="text-gray-400 font-normal">(opcional)</span>
                        </label>
                        <div x-data="{ showCharError: false }" class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium pointer-events-none">AOA</span>
                            <input
                                type="text"
                                inputmode="decimal"
                                wire:model="value"
                                @input="
                                    let v = $el.value.replace(/[^0-9\.,]/g, '');
                                    if (/[^0-9\.,]/.test($el.value)) { showCharError = true; setTimeout(()=> showCharError = false, 1500); }
                                    v = v.replace(/,/g, '.'); const p = v.split('.');
                                    if (p.length > 2) v = p.shift() + '.' + p.join('');
                                    $el.value = v; $el.dispatchEvent(new Event('input'));
                                "
                                @keydown="
                                    const allowed = ['Backspace','Delete','ArrowLeft','ArrowRight','Tab','Enter'];
                                    if (allowed.includes($event.key)) return;
                                    if (!/^[0-9\.,]$/.test($event.key)) { $event.preventDefault(); showCharError = true; setTimeout(()=> showCharError = false, 1200); }
                                "
                                placeholder="0.00"
                                class="block w-full rounded-xl border border-gray-200 pl-14 pr-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                            <p x-show="showCharError" x-cloak class="mt-1 text-xs text-red-600">Apenas números permitidos.</p>
                        </div>
                        <p class="mt-1.5 text-xs text-gray-400">O valor é indicativo. A plataforma cobra 10% de comissão sobre o valor final.</p>
                    </div>

                    {{-- Anexos --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Anexos <span class="text-gray-400 font-normal">(opcional)</span>
                        </label>
                        <x-file-input wire:model="attachments" accept=".pdf,.doc,.docx,.xls,.xlsx,image/*,.zip" multiple label="📎 Adicionar ficheiros" loading-target="attachments">
                            @error('attachments') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                            @error('attachments.*') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </x-file-input>
                        <p class="mt-1 text-xs text-gray-400">pdf, doc, xlsx, imagens, zip · máx. 5 ficheiros · 5 MB cada</p>
                    </div>

                </div>

                {{-- Rodapé --}}
                <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100 flex-shrink-0 bg-gray-50 rounded-b-2xl">
                    <p class="text-xs text-gray-400">O freelancer poderá aceitar ou recusar a proposta.</p>
                    <div class="flex gap-3">
                        <button type="button" wire:click="close" class="btn-outline text-sm px-4 py-2">Cancelar</button>
                        <button type="button" wire:click="send"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-60 cursor-not-allowed"
                            class="btn-primary text-sm px-5 py-2">
                            <span wire:loading.remove wire:target="send">Enviar proposta</span>
                            <span wire:loading wire:target="send">A enviar...</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    @endif
</div>
