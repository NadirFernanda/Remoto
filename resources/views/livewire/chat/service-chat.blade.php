<div class="container mx-auto px-4 py-8 max-w-3xl">
    <h2 class="text-xl font-bold text-cyan-600 mb-4">Chat do Serviço</h2>
    <div class="bg-white rounded-lg shadow p-8 mb-6 min-h-[32rem] flex flex-col">
        <form wire:submit.prevent="enviarMensagem" class="flex mt-2 gap-2 items-center" enctype="multipart/form-data" @submit.window="$nextTick(() => document.getElementById('chat-messages').scrollTop = document.getElementById('chat-messages').scrollHeight)">
            <input type="text" wire:model.defer="mensagem" id="mensagemInput" class="flex-1 border border-cyan-500 rounded-l px-3 py-2 focus:ring-2 focus:ring-cyan-500 focus:outline-none" placeholder="Digite sua mensagem..." @if($chat_bloqueado) disabled @endif>
            <label class="flex flex-col items-center cursor-pointer">
                <span class="border border-cyan-300 rounded px-2 py-1 text-xs bg-white hover:bg-cyan-50 transition whitespace-nowrap">
                    @if($anexo)
                        📎 {{ $anexo->getClientOriginalName() }}
                    @else
                        📎 Anexar ficheiro
                    @endif
                </span>
                <input type="file" wire:model="anexo" class="sr-only" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar,.csv,audio/*">
                <div wire:loading wire:target="anexo" class="text-xs text-cyan-500 mt-0.5">A enviar...</div>
            </label>
            <button type="button" onclick="toggleEmojiPicker()" class="bg-yellow-200 text-yellow-900 rounded px-2 py-1 text-xl">😊</button>
            <button type="submit" class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold px-8 rounded-r disabled:bg-gray-200" style="min-width: 100px;" @if($chat_bloqueado) disabled @endif>
                Enviar
            </button>
        </form>
        <div id="emoji-picker" style="display:none; position:absolute; z-index:1000;"></div>
        <script src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js" type="module"></script>
        <script>
        function toggleEmojiPicker() {
            const picker = document.getElementById('emoji-picker');
            if (picker.style.display === 'none') {
                picker.style.display = 'block';
                if (!picker.hasChildNodes()) {
                    const emojiPicker = document.createElement('emoji-picker');
                    emojiPicker.addEventListener('emoji-click', event => {
                        const input = document.getElementById('mensagemInput');
                        input.value += event.detail.unicode;
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                        picker.style.display = 'none';
                    });
                    picker.appendChild(emojiPicker);
                }
            } else {
                picker.style.display = 'none';
            }
        }
        document.addEventListener('click', function(e) {
            const picker = document.getElementById('emoji-picker');
            if (picker && !picker.contains(e.target) && e.target.className !== 'bg-yellow-200 text-yellow-900 rounded px-2 py-1 text-xl') {
                picker.style.display = 'none';
            }
        });
        </script>
        @error('anexo') <div class="mt-1 text-xs text-red-500">{{ $message }}</div> @enderror
        @error('mensagem') <div class="mt-1 text-xs text-red-500">{{ $message }}</div> @enderror
        <!-- Exibir anexos nas mensagens -->
        <div id="chat-messages" class="flex-1 overflow-y-auto mb-4" x-data x-init="$nextTick(() => $el.scrollTop = $el.scrollHeight)" wire:poll.3s="atualizarMensagens">
            @forelse($messages as $msg)
                @php
                    $isClient = $msg->user_id === $service->cliente_id;
                @endphp
                <div class="mb-2 flex items-end {{ $msg->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                    @if($msg->user_id !== auth()->id())
                        <div class="w-8 h-8 rounded-full overflow-hidden mr-2 flex-shrink-0">
                            <img src="{{ $msg->user ? $msg->user->avatarUrl() : asset('img/default-avatar.svg') }}" alt="{{ $msg->user->name ?? 'User' }}" class="w-full h-full object-cover">
                        </div>
                    @endif
                    <div class="px-4 py-2 rounded-lg
                        @if($isClient)
                            bg-green-500 text-white
                        @else
                            bg-cyan-500 text-white
                        @endif
                    ">
                        @if($msg->anexo)
                            <div class="mb-1">
                                @if(Str::endsWith(strtolower($msg->anexo), ['.jpg','.jpeg','.png','.gif','.bmp','.webp']))
                                    <a href="{{ asset('storage/anexos/' . $msg->anexo) }}" target="_blank"><img src="{{ asset('storage/anexos/' . $msg->anexo) }}" alt="anexo" class="max-h-32 max-w-xs rounded shadow"></a>
                                @elseif(Str::endsWith(strtolower($msg->anexo), ['.mp3','.wav','.ogg','.m4a','.aac']))
                                    <audio controls class="w-full max-w-xs">
                                        <source src="{{ asset('storage/anexos/' . $msg->anexo) }}">
                                        Seu navegador não suporta áudio.
                                    </audio>
                                @else
                                    <a href="{{ asset('storage/anexos/' . $msg->anexo) }}" target="_blank" class="underline text-cyan-200">📎 Baixar anexo</a>
                                @endif
                            </div>
                        @endif
                        <span>{{ $msg->conteudo }}</span>
                        <span class="block text-xs text-gray-400 mt-1">{{ $msg->created_at->format('H:i') }}</span>
                    </div>
                    @if($msg->user_id === auth()->id())
                        <div class="w-8 h-8 rounded-full overflow-hidden ml-2 flex-shrink-0">
                            <img src="{{ $msg->user ? $msg->user->avatarUrl() : asset('img/default-avatar.svg') }}" alt="{{ $msg->user->name ?? 'You' }}" class="w-full h-full object-cover">
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-gray-400 text-center">Nenhuma mensagem ainda.</div>
            @endforelse
        </div>
        @if($chat_bloqueado)
            <div class="absolute inset-0 bg-white bg-opacity-80 flex flex-col items-center justify-center z-10">
                @include('components.icon', ['name' => 'wallet', 'class' => 'w-10 h-10 text-cyan-500 mb-2'])
                <span class="text-cyan-700 font-semibold">Chat disponível após aceitação do serviço</span>
            </div>
        @endif
        <script>
        // Preview de imagem opcional (pode ser melhorado com Alpine.js)
        </script>
        <style>
        input[type='file']::-webkit-file-upload-button { background: #0e4c92; color: #fff; border: none; border-radius: 4px; padding: 4px 8px; }
        </style>
    </div>
</div>
<script>
        let lastMessageCount = {{ count($messages) }};
        window.addEventListener('livewire:update', function() {
            const currentCount = document.querySelectorAll('#chat-messages > div').length;
            if (currentCount > lastMessageCount) {
                // Notificação sonora
                const audio = new Audio('https://cdn.pixabay.com/audio/2022/07/26/audio_124bfae6c2.mp3');
                audio.play();
                // Notificação visual (browser)
                if (Notification && Notification.permission === 'granted') {
                    new Notification('Nova mensagem no chat!');
                }
            }
            lastMessageCount = currentCount;
        });
        if (Notification && Notification.permission !== 'granted') {
            Notification.requestPermission();
        }
        </script>
