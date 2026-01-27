<div class="container mx-auto px-4 py-8 max-w-xl">
    <h2 class="text-xl font-bold text-cyan-600 mb-4">Chat do Serviço</h2>
    <div class="bg-white rounded-lg shadow p-6 mb-6 h-96 flex flex-col">
        <div id="chat-messages" class="flex-1 overflow-y-auto mb-4" x-data x-init="$nextTick(() => $el.scrollTop = $el.scrollHeight)">
            @forelse($messages as $msg)
                <div class="mb-2 flex {{ $msg->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="px-4 py-2 rounded-lg {{ $msg->user_id === auth()->id() ? 'bg-cyan-500 text-white' : 'bg-gray-100 text-gray-800' }}">
                        <span>{{ $msg->conteudo }}</span>
                        <span class="block text-xs text-gray-400 mt-1">{{ $msg->created_at->format('H:i') }}</span>
                    </div>
                </div>
            @empty
                <div class="text-gray-400 text-center">Nenhuma mensagem ainda.</div>
            @endforelse
        </div>
        @if($chat_bloqueado)
            <div class="absolute inset-0 bg-white bg-opacity-80 flex flex-col items-center justify-center z-10">
                <svg class="w-10 h-10 text-cyan-500 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0-1.104.896-2 2-2s2 .896 2 2-.896 2-2 2-2-.896-2-2zm0 0V7m0 4v4m0 0h4m-4 0H8"/></svg>
                <span class="text-cyan-700 font-semibold">Chat disponível após aceitação do serviço</span>
            </div>
        @endif
        <form wire:submit.prevent="enviarMensagem" class="flex mt-2" @submit.window="$nextTick(() => document.getElementById('chat-messages').scrollTop = document.getElementById('chat-messages').scrollHeight)">
            <input type="text" wire:model.defer="mensagem" class="flex-1 border border-cyan-500 rounded-l px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none" placeholder="Digite sua mensagem..." @if($chat_bloqueado) disabled @endif>
            <button type="submit" class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold px-6 rounded-r disabled:bg-gray-200" @if($chat_bloqueado) disabled @endif>
                Enviar
            </button>
        </form>
    </div>
</div>
