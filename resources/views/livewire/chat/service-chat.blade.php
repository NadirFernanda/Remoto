<div class="min-h-screen bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center py-6 px-2">
    <div class="w-full max-w-2xl flex flex-col bg-white rounded-2xl shadow-2xl overflow-hidden" style="height: 80vh; min-height: 520px;">

        {{-- Header --}}
        <div class="flex items-center gap-3 px-5 py-4 bg-gradient-to-r from-[#0ea5e9] to-[#0284c7] text-white shadow-sm flex-shrink-0">
            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-lg font-bold">
                {{ strtoupper(substr($service->titulo ?? 'S', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="font-semibold text-base leading-tight truncate">{{ $service->titulo ?? 'Chat do Servico' }}</div>
                <div class="text-xs text-blue-100 mt-0.5">
                    @php
                        $statusLabels = ['negotiating'=>'Em negociação','accepted'=>'Aceite','in_progress'=>'Em andamento','delivered'=>'Entregue','completed'=>'Concluido'];
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
        </div>

        {{-- Messages area: wire:ignore impede ServiceChat de tentar re-inicializar ChatMessages durante morph de upload --}}
        <div wire:ignore class="flex-1 overflow-hidden">
            <livewire:chat.chat-messages :service="$service" />
        </div>

        @error('mensagem') <div class="px-4 py-1 text-xs text-red-500 bg-red-50">{{ $message }}</div> @enderror

        {{-- Input bar --}}
        <div class="flex-shrink-0 border-t border-slate-200 bg-white px-3 py-3">
            @if($chat_bloqueado)
                <div class="flex items-center justify-center gap-2 py-2 text-slate-400 text-sm">
                    &#128274; Chat disponivel apos aceitacao do servico
                </div>
            @else
                <form wire:submit="enviarMensagem" class="flex items-end gap-2">

                    {{-- Attach button - Livewire WithFileUploads (igual ao portfolio/foto de perfil) --}}
                    <label class="flex-shrink-0 cursor-pointer group" title="Anexar ficheiro">
                        <div class="w-10 h-10 rounded-full bg-slate-100 group-hover:bg-[#0ea5e9]/10 flex items-center justify-center transition">
                            @if($chatFile)
                                <svg class="w-5 h-5 text-[#0ea5e9]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                            @else
                                <svg class="w-5 h-5 text-slate-400 group-hover:text-[#0ea5e9] transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                            @endif
                        </div>
                        <input type="file" wire:model="chatFile" style="position:absolute;width:1px;height:1px;opacity:0;overflow:hidden">
                    </label>

                    {{-- Upload progress via Livewire --}}
                    <div wire:loading wire:target="chatFile" class="text-xs text-[#0ea5e9] flex-shrink-0">A carregar...</div>

                    {{-- File preview badge (renderizado pelo servidor após upload) --}}
                    @if($chatFile)
                        <div class="flex items-center gap-1.5 bg-[#0ea5e9]/10 text-[#0284c7] text-xs font-medium px-3 py-1.5 rounded-full flex-shrink-0 max-w-[180px]">
                            <span>&#128204;</span>
                            <span class="truncate">{{ $chatFile->getClientOriginalName() }}</span>
                        </div>
                    @endif

                    @error('chatFile')
                        <div class="text-xs text-red-500 flex-shrink-0 max-w-[140px] truncate">{{ $message }}</div>
                    @enderror

                    <div class="flex-1 relative">
                        <input type="text"
                               wire:model="mensagem"
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

                <div id="emoji-picker" style="display:none; position:absolute; bottom:80px; right:80px; z-index:1000;"></div>
                <script src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js" type="module"></script>
                <script>
                function toggleEmojiPicker() {
                    const picker = document.getElementById('emoji-picker');
                    picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
                    if (picker.style.display === 'block' && !picker.hasChildNodes()) {
                        const ep = document.createElement('emoji-picker');
                        ep.addEventListener('emoji-click', e => {
                            const input = document.getElementById('mensagemInput');
                            input.value += e.detail.unicode;
                            input.dispatchEvent(new Event('input', { bubbles: true }));
                            picker.style.display = 'none';
                        });
                        picker.appendChild(ep);
                    }
                }
                document.addEventListener('click', function(e) {
                    const picker = document.getElementById('emoji-picker');
                    if (picker && !picker.contains(e.target) && !e.target.closest('[onclick="toggleEmojiPicker()"]')) {
                        picker.style.display = 'none';
                    }
                });
                </script>
            @endif
        </div>
    </div>
</div>
<script>
let lastMsgCount = document.querySelectorAll('#chat-messages > div').length;
window.addEventListener('livewire:update', function() {
    const msgs = document.querySelectorAll('#chat-messages > div');
    if (msgs.length > lastMsgCount) {
        const audio = new Audio('https://cdn.pixabay.com/audio/2022/07/26/audio_124bfae6c2.mp3');
        audio.play().catch(()=>{});
    }
    lastMsgCount = msgs.length;
    const el = document.getElementById('chat-messages');
    if (el) el.scrollTop = el.scrollHeight;
});
if (Notification && Notification.permission !== 'granted') Notification.requestPermission();
</script>
