<div
    id="chat-messages"
    wire:poll.8s
    class="flex-1 overflow-y-auto px-4 py-4 space-y-3 bg-slate-50 h-full"
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
        @endphp
        @if($msg->conteudo || $msg->anexo)
        <div wire:key="msg-{{ $msg->id }}" class="flex items-end gap-2 {{ $isMine ? 'justify-end' : 'justify-start' }}">
            @if(!$isMine)
                <img src="{{ $avatar }}" alt="{{ $name }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0 shadow">
            @endif
            <div class="max-w-[72%] flex flex-col {{ $isMine ? 'items-end' : 'items-start' }}">
                @if(!$isMine)
                    <span class="text-xs text-slate-400 mb-1 ml-1">{{ $name }}</span>
                @endif
                <div class="relative px-4 py-2.5 rounded-2xl shadow-sm {{ $isMine ? 'bg-[#0ea5e9] text-white rounded-br-sm' : 'bg-white text-slate-800 rounded-bl-sm border border-slate-100' }}">

                    @if($msg->anexo)
                        @if($isImage)
                            <a href="{{ asset('storage/anexos/' . $msg->anexo) }}" target="_blank" class="block mb-1">
                                <img src="{{ asset('storage/anexos/' . $msg->anexo) }}" alt="{{ $displayName }}" class="max-h-48 max-w-full rounded-xl shadow">
                            </a>
                        @elseif($isAudio)
                            <audio controls class="w-full max-w-xs mb-1 rounded-lg">
                                <source src="{{ asset('storage/anexos/' . $msg->anexo) }}">
                            </audio>
                        @else
                            <a href="{{ asset('storage/anexos/' . $msg->anexo) }}" target="_blank"
                               class="flex items-center gap-2 mb-1 px-3 py-2 rounded-xl {{ $isMine ? 'bg-white/20 hover:bg-white/30' : 'bg-slate-100 hover:bg-slate-200' }} transition">
                                <span class="text-2xl">&#128196;</span>
                                <span class="text-sm font-medium truncate max-w-[200px]">{{ $displayName }}</span>
                                <span class="text-xs opacity-60 uppercase ml-auto">{{ strtoupper($ext) }}</span>
                            </a>
                        @endif
                    @endif

                    @if($msg->conteudo)
                        <p class="text-sm leading-relaxed whitespace-pre-wrap break-words">{{ $msg->conteudo }}</p>
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
