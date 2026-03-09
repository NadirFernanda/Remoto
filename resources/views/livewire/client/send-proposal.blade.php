<div>
    @if($show)
        <div x-data="{ open: true, focusables: [], firstFocusable: null, lastFocusable: null, showCharError: false, init() { this.$nextTick(() => { this.focusables = Array.from(this.$el.querySelectorAll('a,button,input,textarea,select,[tabindex]')).filter(el => el.getAttribute('tabindex') !== '-1' && !el.hasAttribute('disabled')); this.firstFocusable = this.focusables[0] || null; this.lastFocusable = this.focusables[this.focusables.length - 1] || null; if (this.firstFocusable) this.firstFocusable.focus(); }); }, handleTab(e) { if (this.focusables.length === 0) return; if (e.shiftKey) { if (document.activeElement === this.firstFocusable) { e.preventDefault(); this.lastFocusable.focus(); } } else { if (document.activeElement === this.lastFocusable) { e.preventDefault(); this.firstFocusable.focus(); } } } }"
            x-init="init()"
            x-cloak
            @keydown.window.escape="$wire.close()"
            @keydown.window="if ($event.key === 'Tab') handleTab($event)"
            x-effect="document.body.classList.toggle('overflow-hidden', open)"
            class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" @click="$wire.close()" x-transition.opacity></div>
            <div class="relative bg-white rounded-2xl shadow-xl z-10 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto" x-transition:enter="transform transition ease-in-out duration-200" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100" x-transition:leave="transform transition ease-in-out duration-150" x-transition:leave-start="scale-100 opacity-100" x-transition:leave-end="scale-95 opacity-0">
                <div class="flex items-center justify-between p-6 pb-4 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900">Enviar proposta</h3>
                    <button type="button" wire:click="close" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Valor (opcional)</label>
                    <input data-focus-start
                           type="text"
                           inputmode="decimal"
                           pattern="^\d*(?:[\.,]\d{0,2})?$"
                           wire:model.defer="value"
                           @input="
                               // strip letters and allow only digits, comma or dot; normalize comma to dot
                               let cleaned = $el.value.replace(/[^0-9\.,]/g, '');
                               if (/[^0-9\.,]/.test($el.value)) { showCharError = true; setTimeout(()=> showCharError = false, 1500); }
                               cleaned = cleaned.replace(/,/g, '.');
                               const parts = cleaned.split('.');
                               if (parts.length > 2) cleaned = parts.shift() + '.' + parts.join('');
                               $el.value = cleaned;
                               // notify input event so Livewire/alpine update
                               $el.dispatchEvent(new Event('input'));
                           "
                           @keydown="
                               // allow control keys and navigate keys
                               const allowed = ['Backspace','Delete','ArrowLeft','ArrowRight','Tab','Enter'];
                               if (allowed.includes($event.key)) return;
                               if (!/^[0-9\.,]$/.test($event.key)) {
                                   $event.preventDefault(); showCharError = true; setTimeout(()=> showCharError = false, 1200);
                               }
                           "
                           @beforeinput="if ($event.data && /[^0-9\.,]/.test($event.data)) { $event.preventDefault(); showCharError = true; setTimeout(()=> showCharError = false, 1200); }"
                           @paste.prevent="
                               const pasted = ($event.clipboardData || window.clipboardData).getData('text') || '';
                               let cleaned = pasted.replace(/[^0-9\.,]/g, '').replace(/,/g, '.');
                               const parts = cleaned.split('.'); if (parts.length > 2) cleaned = parts.shift() + '.' + parts.join('');
                               $el.value = cleaned; $el.dispatchEvent(new Event('input'));
                           "
                           class="block w-full rounded-lg border border-gray-200 py-2 px-3">
                    <p x-show="showCharError" x-cloak class="mt-1 text-sm text-red-600">Não é possível digitar letras no campo Valor.</p>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Mensagem</label>
                    <textarea wire:model.defer="message" rows="4" class="block w-full rounded-lg border border-gray-200 py-2 px-3"></textarea>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Anexos (opcional)</label>
                    <x-file-input wire:model="attachments" accept=".pdf,.doc,.docx,.xls,.xlsx,image/*,.zip" multiple label="📎 Adicionar anexos" loading-target="attachments">
                        @error('attachments') <div class="text-red-600 text-xs">{{ $message }}</div> @enderror
                        @error('attachments.*') <div class="text-red-600 text-xs">{{ $message }}</div> @enderror
                        <p class="text-xs text-gray-500">pdf, doc, xlsx, png, jpg, zip · máx. 5 ficheiros · 5 MB cada</p>
                    </x-file-input>
                </div>
                <div class="mt-4 flex items-center justify-end gap-3">
                    <button type="button" class="btn-outline" wire:click.prevent="close">Cancelar</button>
                    <button type="button" wire:click="send" class="btn-primary">Enviar proposta</button>
                </div>
                </div>{{-- /p-6 --}}
            </div>
        </div>
    @endif
</div>
