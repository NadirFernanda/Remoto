<div class="chat-outer min-h-screen bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center py-6 px-2">
    <div class="chat-window w-full max-w-2xl flex flex-col bg-white rounded-2xl shadow-2xl overflow-hidden" style="height: 80vh; min-height: 400px; max-height: 100dvh;">

        {{-- Header --}}
        <div class="chat-header flex items-center gap-3 px-5 py-4 bg-gradient-to-r from-[#0ea5e9] to-[#0284c7] text-white shadow-sm flex-shrink-0">
            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-lg font-bold">
                {{ strtoupper(substr($service->titulo ?? 'S', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="font-semibold text-base leading-tight truncate">{{ $service->titulo ?? 'Chat do Servico' }}</div>
                <div class="text-xs text-blue-100 mt-0.5">
                    @php
                        $statusLabels = ['published'=>'Publicado','negotiating'=>'Em negociação','accepted'=>'Aceite','in_progress'=>'Em andamento','delivered'=>'Entregue','completed'=>'Concluído','cancelled'=>'Cancelado'];
                    @endphp
                    {{ $statusLabels[$service->status] ?? ucfirst($service->status) }}
                </div>
            </div>
            @if($chat_bloqueado)
                <span class="text-xs bg-white/20 rounded-full px-3 py-1">Bloqueado</span>
            @elseif($service->status === 'negotiating')
                <span class="flex items-center gap-1 text-xs bg-amber-400/30 rounded-full px-3 py-1"><span class="w-2 h-2 rounded-full bg-amber-300 inline-block"></span> Em negociação</span>
            @else
                <span class="flex items-center gap-1 text-xs bg-white/20 rounded-full px-3 py-1"><span class="w-2 h-2 rounded-full bg-green-300 inline-block"></span> Activo</span>
            @endif

            @if($mostrarBotaoValor || $mostrarBotaoFreelancerValor)
            <div class="chat-header-actions flex gap-2 flex-shrink-0">
                @if($mostrarBotaoValor)
                    <button wire:click="abrirModalValor"
                            class="chat-header-btn"
                            style="display:flex;align-items:center;gap:.35rem;padding:.3rem .75rem;border-radius:.5rem;background:#ff2d55;color:#fff;font-size:.75rem;font-weight:700;border:none;cursor:pointer;white-space:nowrap;box-shadow:0 2px 8px rgba(255,45,85,.45);"
                            title="Inserir valor adicional acordado com o freelancer">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12M6 12h12"/>
                        </svg>
                        Inserir Valor
                    </button>
                @endif
                @if($mostrarBotaoFreelancerValor)
                    <button type="button"
                            @click="window.dispatchEvent(new CustomEvent('open-propor-valor-modal'))"
                            class="chat-header-btn"
                            style="display:flex;align-items:center;gap:.35rem;padding:.3rem .75rem;border-radius:.5rem;background:#10b981;color:#fff;font-size:.75rem;font-weight:700;border:none;cursor:pointer;white-space:nowrap;box-shadow:0 2px 8px rgba(16,185,129,.45);"
                            title="Propor um valor ao cliente">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12M6 12h12"/>
                        </svg>
                        Propor Valor
                    </button>
                @endif
            </div>
            @endif
        </div>

        {{-- Flash: sucesso --}}
        @if(session('chat_success'))
            <div class="px-4 py-2 text-sm text-green-700 bg-green-50 border-b border-green-100 flex items-center gap-2 flex-shrink-0">
                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ session('chat_success') }}
            </div>
        @endif

        {{-- Flash: erro --}}
        @if(session('chat_error'))
            <div class="px-4 py-2 text-sm text-red-700 bg-red-50 border-b border-red-100 flex items-center gap-2 flex-shrink-0">
                <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                {{ session('chat_error') }}
            </div>
        @endif

        {{-- Messages area --}}
        <div
            id="chat-messages"
            class="flex-1 overflow-y-auto px-4 py-4 space-y-3 bg-slate-50"
            x-data
            x-init="$el.scrollTop = $el.scrollHeight"
            @scroll-bottom.window="$nextTick(() => { $el.scrollTop = $el.scrollHeight })"
        >
            @forelse($messages as $msg)
                @php
                    $isMine = $msg->user_id === auth()->id();
                    $name = $msg->user->name ?? 'Utilizador';
                    $avatar = $msg->user ? $msg->user->avatarUrl() : asset('img/default-avatar.svg');
                    $ext = $msg->anexo ? strtolower(pathinfo($msg->anexo, PATHINFO_EXTENSION)) : null;
                    $isImage = in_array($ext, ['jpg','jpeg','png','gif','bmp','webp']);
                    $isAudio = in_array($ext, ['mp3','wav','ogg','m4a','aac']);
                    $displayName = $msg->nome_original_anexo ?? $msg->anexo;
                    $shortName = mb_strlen($displayName) > 36 ? mb_substr($displayName, 0, 33) . '…' : $displayName;
                @endphp
                @if($msg->conteudo || $msg->anexo)
                <div wire:key="msg-{{ $msg->id }}" style="display:flex;align-items:flex-end;gap:8px;width:100%;" class="{{ $isMine ? 'justify-end' : 'justify-start' }}">
                    @if(!$isMine)
                        <img src="{{ $avatar }}" alt="{{ $name }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0 shadow">
                    @endif
                    <div class="flex flex-col {{ $isMine ? 'items-end' : 'items-start' }}" style="min-width:0;max-width:min(72%, calc(100vw - 110px));">
                        @if(!$isMine)
                            <span class="text-xs text-slate-400 mb-1 ml-1">{{ $name }}</span>
                        @endif
                        <div class="relative px-4 py-2.5 rounded-2xl shadow-sm {{ $isMine ? 'bg-[#0ea5e9] text-white rounded-br-sm' : 'bg-white text-slate-800 rounded-bl-sm border border-slate-100' }}" style="overflow:hidden;word-break:break-word;max-width:100%;box-sizing:border-box;">

                            @if($msg->anexo)
                                @if($isImage)
                                    <a href="{{ asset('storage/anexos/' . $msg->anexo) }}" target="_blank" class="block mb-1">
                                        <img src="{{ asset('storage/anexos/' . $msg->anexo) }}" alt="{{ $displayName }}" class="max-h-48 max-w-full rounded-xl shadow">
                                    </a>
                                @elseif($isAudio)
                                    <audio controls style="display:block;width:100%;max-width:100%;box-sizing:border-box;" class="mb-1 rounded-lg">
                                        <source src="{{ asset('storage/anexos/' . $msg->anexo) }}">
                                    </audio>
                                @else
                                    <a href="{{ asset('storage/anexos/' . $msg->anexo) }}" target="_blank"
                                       title="{{ $displayName }}"
                                       style="display:flex;align-items:center;gap:.5rem;margin-bottom:.25rem;padding:.5rem .75rem;border-radius:.75rem;text-decoration:none;background:{{ $isMine ? 'rgba(255,255,255,.15)' : '#f1f5f9' }};max-width:100%;overflow:hidden;box-sizing:border-box;">
                                        <span style="font-size:1.25rem;flex-shrink:0;">&#128196;</span>
                                        <span style="font-size:.8rem;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;min-width:0;">{{ $shortName }}</span>
                                        <span style="font-size:.7rem;opacity:.65;text-transform:uppercase;flex-shrink:0;margin-left:.25rem;">{{ strtoupper($ext) }}</span>
                                    </a>
                                @endif
                            @endif

                            @if($msg->conteudo)
                                <p class="text-sm leading-relaxed whitespace-pre-wrap break-words">{{ $msg->conteudo }}</p>
                                {{-- Inline pay button for proposal messages (client view only) --}}
                                @if($mostrarBotaoValor && !$isMine)
                                    @php
                                        preg_match('/Proposta de valor: ([\.\d]+,\d{2}) Kz/', $msg->conteudo, $propostaMatch);
                                    @endphp
                                    @if(!empty($propostaMatch[1]))
                                    <div class="mt-2 pt-2 border-t border-slate-200">
                                        <button wire:click="abrirModalComValor('{{ $propostaMatch[1] }}')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-[#ff2d55] text-white shadow hover:opacity-90 transition active:scale-95"
                                                title="Confirmar este valor e efectuar o pagamento">
                                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            Aceitar & Pagar {{ $propostaMatch[1] }} Kz
                                        </button>
                                    </div>
                                    @endif
                                @endif
                            @endif

                            <span class="block text-right text-[10px] mt-1 {{ $isMine ? 'text-blue-100' : 'text-slate-400' }}">
                                {{ $msg->created_at->format('H:i') }}
                            </span>
                        </div>
                    </div>
                    @if($isMine)
                        <img src="{{ $avatar }}" alt="{{ $name }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0 shadow">
                    @endif
                </div>
                @endif
            @empty
                <div class="flex flex-col items-center justify-center h-full text-slate-400 py-16">
                    <svg class="w-12 h-12 mb-3 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/></svg>
                    <p class="font-medium">Nenhuma mensagem ainda.</p>
                    <p class="text-sm mt-1">Seja o primeiro a escrever!</p>
                </div>
            @endforelse
        </div>

        @error('mensagem') <div class="px-4 py-1 text-xs text-red-500 bg-red-50">{{ $message }}</div> @enderror

        {{-- Input bar --}}
        <div class="flex-shrink-0 border-t border-slate-200 bg-white px-3 py-3">
            @if($chat_bloqueado)
                <div class="flex items-center justify-center gap-2 py-2 text-slate-400 text-sm">
                    @if($service->status === 'completed')
                        &#9989; Projeto concluído — o chat está em modo de leitura
                    @else
                        &#128274; Chat disponível após aceitação do serviço
                    @endif
                </div>
            @else
                <form wire:submit.prevent
                      class="flex items-end gap-2"
                      x-data="chatInput"
                      x-on:chat-file-cleared.window="clear()"
                      @submit.prevent="submit()">

                    {{-- Attach button --}}
                    <label class="flex-shrink-0 cursor-pointer group" title="Anexar ficheiro">
                        <div class="w-10 h-10 rounded-full bg-slate-100 group-hover:bg-[#0ea5e9]/10 flex items-center justify-center transition">
                            <svg x-show="hasFile || uploading" class="w-5 h-5 text-[#0ea5e9]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                            <svg x-show="!hasFile && !uploading" class="w-5 h-5 text-slate-400 group-hover:text-[#0ea5e9] transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        </div>
                        <input type="file" x-ref="fileInput"
                               @change="handleFile($event)"
                               style="position:absolute;width:1px;height:1px;opacity:0;overflow:hidden">
                    </label>

                    {{-- Upload progress --}}
                    <div x-show="uploading" x-cloak class="text-xs text-[#0ea5e9] flex-shrink-0">A carregar...</div>

                    {{-- File preview badge --}}
                    <div x-show="hasFile" x-cloak
                         class="flex items-center gap-1.5 bg-[#0ea5e9]/10 text-[#0284c7] text-xs font-medium px-3 py-1.5 rounded-full flex-shrink-0 max-w-[180px]">
                        <span>&#128204;</span>
                        <span class="truncate" x-text="fileName"></span>
                        <button type="button" @click="cancelFile()" class="ml-1 text-[#0284c7] hover:text-red-500 transition leading-none font-bold">&times;</button>
                    </div>

                    @error('chatFile')
                        <div class="text-xs text-red-500 flex-shrink-0 max-w-[180px] truncate">{{ $message }}</div>
                    @enderror
                    <div x-show="uploadError" x-cloak class="text-xs text-red-500 flex-shrink-0 max-w-[180px] truncate" x-text="uploadError"></div>

                    <div class="flex-1 relative">
                        <input type="text"
                               wire:model.defer="mensagem"
                               id="mensagemInput"
                               class="w-full bg-slate-100 rounded-full px-4 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-[#0ea5e9]/40 placeholder-slate-400"
                               placeholder="Escreva uma mensagem...">
                        <button type="button"
                                onclick="toggleEmojiPicker()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-yellow-400 transition text-lg leading-none">
                            &#128522;
                        </button>
                    </div>

                    <button type="submit"
                        class="flex-shrink-0 w-10 h-10 rounded-full bg-[#0ea5e9] hover:bg-[#0284c7] text-white flex items-center justify-center shadow transition active:scale-95">
                        <svg class="w-5 h-5 rotate-45 -mr-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                    </button>
                </form>

                <div id="emoji-picker" wire:ignore style="display:none; position:absolute; bottom:60px; right:0.5rem; z-index:1000; max-width:calc(100vw - 1rem);"></div>

                @assets
                <script src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js" type="module"></script>
                @endassets

                @script
                <script>
                if (!window.__chatInputRegistered) {
                    Alpine.data('chatInput', () => ({
                        hasFile: false,
                        fileName: '',
                        uploading: false,
                        uploadError: '',
                        pendingPath: '',
                        pendingOriginal: '',
                        init() {},
                        submit() {
                            this.$wire.enviarMensagem(this.pendingPath, this.pendingOriginal);
                            this.clear();
                        },
                        handleFile(event) {
                            const file = event.target.files[0];
                            if (!file) return;
                            this.uploadError = '';
                            if (file.size > 10 * 1024 * 1024) {
                                this.uploadError = 'Ficheiro demasiado grande (máx 10 MB).';
                                if (this.$refs.fileInput) this.$refs.fileInput.value = '';
                                return;
                            }
                            this.uploading = true;
                            this.fileName = file.name;
                            const formData = new FormData();
                            formData.append('file', file);
                            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
                            fetch('/chat/upload-file', {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                                body: formData,
                            })
                            .then(r => r.json())
                            .then(data => {
                                this.uploading = false;
                                if (data.error) {
                                    this.uploadError = data.error;
                                    this.hasFile = false;
                                    this.fileName = '';
                                    if (this.$refs.fileInput) this.$refs.fileInput.value = '';
                                    return;
                                }
                                this.pendingPath = data.filename;
                                this.pendingOriginal = data.original;
                                this.hasFile = true;
                            })
                            .catch(() => {
                                this.uploading = false;
                                this.hasFile = false;
                                this.fileName = '';
                                this.uploadError = 'Erro ao carregar ficheiro. Tente novamente.';
                                if (this.$refs.fileInput) this.$refs.fileInput.value = '';
                            });
                        },
                        cancelFile() {
                            this.pendingPath = '';
                            this.pendingOriginal = '';
                            this.hasFile = false;
                            this.fileName = '';
                            this.uploadError = '';
                            if (this.$refs.fileInput) this.$refs.fileInput.value = '';
                        },
                        clear() {
                            this.pendingPath = '';
                            this.pendingOriginal = '';
                            this.hasFile = false;
                            this.fileName = '';
                            this.uploading = false;
                            this.uploadError = '';
                            if (this.$refs.fileInput) this.$refs.fileInput.value = '';
                        }
                    }));
                    window.__chatInputRegistered = true;
                }

                if (!window.toggleEmojiPicker) {
                    window.toggleEmojiPicker = function () {
                        const picker = document.getElementById('emoji-picker');
                        if (!picker) return;

                        picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
                        if (picker.style.display === 'block' && !picker.hasChildNodes()) {
                            const ep = document.createElement('emoji-picker');
                            ep.addEventListener('emoji-click', e => {
                                const input = document.getElementById('mensagemInput');
                                if (!input) return;
                                input.value += e.detail.unicode;
                                input.dispatchEvent(new Event('input', { bubbles: true }));
                                picker.style.display = 'none';
                            });
                            picker.appendChild(ep);
                        }
                    };
                }

                if (!window.__chatEmojiCloseHandlerRegistered) {
                    document.addEventListener('click', function(e) {
                        const picker = document.getElementById('emoji-picker');
                        if (picker && !picker.contains(e.target) && !e.target.closest('[onclick="toggleEmojiPicker()"]')) {
                            picker.style.display = 'none';
                        }
                    });
                    window.__chatEmojiCloseHandlerRegistered = true;
                }
                </script>
                @endscript
            @endif
        </div>
    </div>

        {{-- Modais fora do chat-window mas dentro do root Livewire (.chat-outer) --}}
        {{-- para que $wire esteja disponível no scope Alpine dos formulários --}}
        <div wire:key="modal-propor-valor"
             x-data="{ open: false }"
             @open-propor-valor-modal.window="open = true"
             @close-propor-valor-modal.window="open = false"
             x-show="open" x-cloak
             style="display:flex;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(15,23,42,.72);backdrop-filter:blur(5px);">
            <div style="background:#fff;border-radius:1.25rem;padding:1.75rem 1.75rem 1.5rem;width:100%;max-width:420px;box-shadow:0 24px 64px rgba(0,0,0,.28);margin:1rem;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
                    <h3 style="font-size:1.05rem;font-weight:700;color:#0f172a;margin:0;">Propor Valor ao Cliente</h3>
                    <button type="button"
                            wire:click="fecharModalProporValor"
                            @click="open = false"
                            style="background:none;border:none;cursor:pointer;color:#94a3b8;padding:.25rem;line-height:1;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:.75rem;padding:.75rem 1rem;margin-bottom:1rem;font-size:.8rem;color:#166534;line-height:1.5;">
                    &#9432; O valor será enviado como mensagem no chat. O cliente poderá confirmar e efectuar o pagamento.
                </div>
                <form style="margin:0;" wire:submit="enviarPropostaValor">
                    <div style="margin-bottom:.75rem;">
                        <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.4rem;">Valor proposto (Kz)</label>
                        <input wire:model="valorProposto"
                               type="text"
                               inputmode="decimal"
                               placeholder="Ex.: 50000"
                               style="width:100%;border:1.5px solid #e2e8f0;border-radius:.65rem;padding:.65rem .85rem;font-size:.95rem;color:#0f172a;outline:none;box-sizing:border-box;transition:border-color .15s;"
                               onfocus="this.style.borderColor='#10b981'"
                               onblur="this.style.borderColor='#e2e8f0'">
                        @error('valorProposto')
                            <p style="margin:.35rem 0 0;font-size:.75rem;color:#ef4444;">{{ $message }}</p>
                        @enderror
                    </div>
                    <div style="display:flex;gap:.75rem;">
                        <button type="button"
                                wire:click="fecharModalProporValor"
                                @click="open = false"
                                style="flex:1;padding:.65rem;border-radius:.65rem;border:1.5px solid #e2e8f0;background:#fff;color:#64748b;font-size:.85rem;font-weight:600;cursor:pointer;">
                            Cancelar
                        </button>
                        <button type="submit"
                                wire:loading.attr="disabled"
                                wire:target="enviarPropostaValor"
                                style="flex:2;padding:.65rem;border-radius:.65rem;border:none;background:#10b981;color:#fff;font-size:.85rem;font-weight:700;cursor:pointer;box-shadow:0 2px 12px rgba(16,185,129,.35);">
                            <span wire:loading.remove wire:target="enviarPropostaValor">Enviar Proposta</span>
                            <span wire:loading wire:target="enviarPropostaValor">A enviar...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal: Inserir Valor --}}
        @php $bd = $this->extraBreakdown; @endphp
        <div wire:key="modal-inserir-valor"
             x-data="{ open: false }"
             @open-valor-modal.window="open = true"
             @close-valor-modal.window="open = false"
             x-show="open" x-cloak
             style="display:flex;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(15,23,42,.72);backdrop-filter:blur(5px);">
            <div style="background:#fff;border-radius:1.25rem;padding:1.75rem 1.75rem 1.5rem;width:100%;max-width:430px;box-shadow:0 24px 64px rgba(0,0,0,.28);margin:1rem;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
                    <h3 style="font-size:1.05rem;font-weight:700;color:#0f172a;margin:0;">
                        {{ $bd['is_negotiating'] ? 'Confirmar Valor Acordado' : 'Inserir Valor Acordado' }}
                    </h3>
                    <button type="button" wire:click="fecharModalValor" @click="open = false" style="background:none;border:none;cursor:pointer;color:#94a3b8;padding:.25rem;line-height:1;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                @if($bd['is_negotiating'])
                    <div style="background:#fefce8;border:1px solid #fde68a;border-radius:.75rem;padding:.75rem 1rem;margin-bottom:1rem;font-size:.8rem;color:#92400e;line-height:1.5;">
                        &#9432; Após confirmar o pagamento, o projecto passará automaticamente para <strong>Em andamento</strong>.
                    </div>
                @else
                    <div style="background:#f8fafc;border-radius:.75rem;padding:.85rem 1rem;margin-bottom:1rem;display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-size:.8rem;color:#64748b;">Valor actual do projecto</span>
                        <span style="font-size:.95rem;font-weight:700;color:#0284c7;">{{ number_format($bd['atual'], 2, ',', '.') }} Kz</span>
                    </div>
                @endif
                <div style="margin-bottom:.75rem;">
                    <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.4rem;">
                        {{ $bd['is_negotiating'] ? 'Valor acordado (Kz)' : 'Novo valor total acordado (Kz)' }}
                    </label>
                    <input wire:model.blur="novoValorTotal"
                           type="number"
                           min="0"
                           step="0.01"
                           placeholder="Ex.: 80000"
                           style="width:100%;border:1.5px solid #e2e8f0;border-radius:.65rem;padding:.65rem .85rem;font-size:.95rem;color:#0f172a;outline:none;box-sizing:border-box;transition:border-color .15s;"
                           onfocus="this.style.borderColor='#0ea5e9'"
                           onblur="this.style.borderColor='#e2e8f0'">
                    @error('novoValorTotal')
                        <p style="margin:.35rem 0 0;font-size:.75rem;color:#ef4444;">{{ $message }}</p>
                    @enderror
                </div>
                @if($bd['extra'] > 0)
                <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:.75rem;padding:.85rem 1rem;margin-bottom:1.1rem;font-size:.82rem;">
                    <div style="display:flex;justify-content:space-between;color:#475569;padding:.15rem 0;">
                        <span>{{ $bd['is_negotiating'] ? 'Valor do projecto' : 'Valor extra acordado' }}</span>
                        <span style="font-weight:600;">{{ number_format($bd['extra'], 2, ',', '.') }} Kz</span>
                    </div>
                    <div style="border-top:1px solid #bae6fd;margin:.5rem 0;"></div>
                    <div style="display:flex;justify-content:space-between;color:#0284c7;font-weight:700;font-size:.88rem;padding:.1rem 0;">
                        <span>Total a pagar</span>
                        <span>{{ number_format($bd['total_cliente'], 2, ',', '.') }} Kz</span>
                    </div>
                </div>
                @endif
                <div style="display:flex;gap:.75rem;">
                    <button type="button" wire:click="fecharModalValor" @click="open = false"
                            style="flex:1;padding:.65rem;border-radius:.65rem;border:1.5px solid #e2e8f0;background:#fff;color:#64748b;font-size:.85rem;font-weight:600;cursor:pointer;">
                        Cancelar
                    </button>
                    <button type="button" wire:click="pagarValorExtra"
                            wire:loading.attr="disabled"
                            wire:target="pagarValorExtra"
                            style="flex:2;padding:.65rem;border-radius:.65rem;border:none;background:#ff2d55;color:#fff;font-size:.85rem;font-weight:700;cursor:pointer;box-shadow:0 2px 12px rgba(255,45,85,.35);">
                        <span wire:loading.remove wire:target="pagarValorExtra">
                            {{ $bd['is_negotiating'] ? 'Confirmar & Iniciar Projecto' : 'Confirmar Pagamento' }}
                        </span>
                        <span wire:loading wire:target="pagarValorExtra">A processar...</span>
                    </button>
                </div>
            </div>
        </div>

</div>