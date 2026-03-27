<div>
    @if($sent)
        <div class="mb-5 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl text-sm">
            Notificação enviada com sucesso!
        </div>
    @endif

    @if($errorMsg)
        <div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm">
            {{ $errorMsg }}
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-200 p-6 max-w-lg">
        <h2 class="text-base font-semibold text-gray-700 mb-5">Nova Notificação em Massa</h2>

        {{-- Target --}}
        <div class="mb-4">
            <label class="block text-xs text-gray-500 mb-1">Destinatários</label>
            <select wire:model.live="target"
                class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
                <option value="all">Todos os utilizadores activos</option>
                <option value="freelancers">Apenas Freelancers</option>
                <option value="clients">Apenas Clientes</option>
                <option value="user">Utilizador específico</option>
            </select>
        </div>

        @if($target === 'user')
            <div class="mb-4">
                <label class="block text-xs text-gray-500 mb-1">Pesquisar utilizador</label>
                <input wire:model.live.debounce.300ms="userQuery" type="text"
                    class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]"
                    placeholder="Nome, email ou ID">

                @if(!empty($userMatches))
                    <div class="mt-2 border border-gray-200 rounded-lg overflow-hidden">
                        @foreach($userMatches as $match)
                            <button type="button" wire:click="selectUser({{ $match['id'] }})"
                                class="w-full text-left px-3 py-2 text-sm hover:bg-[#f4fbfd] flex justify-between">
                                <span>{{ $match['name'] }} — {{ $match['email'] }}</span>
                                <span class="text-xs text-gray-400">#{{ $match['id'] }} · {{ $match['role'] }}</span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        {{-- Title --}}
        <div class="mb-4">
            <label class="block text-xs text-gray-500 mb-1">Título <span class="text-red-400">*</span></label>
            <input wire:model="titulo" type="text" maxlength="120"
                class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] @error('titulo') border-red-400 @enderror"
                placeholder="Ex: Nova funcionalidade disponível">
            @error('titulo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Message --}}
        <div class="mb-5">
            <label class="block text-xs text-gray-500 mb-1">Mensagem <span class="text-red-400">*</span></label>
            <textarea wire:model="mensagem" rows="4" maxlength="1000"
                class="w-full border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] resize-none @error('mensagem') border-red-400 @enderror"
                placeholder="Corpo da notificação..."></textarea>
            @error('mensagem') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <button wire:click="send" wire:loading.attr="disabled"
            class="btn-primary w-full">
            <span wire:loading.remove wire:target="send">Enviar Notificação</span>
            <span wire:loading wire:target="send">A enviar...</span>
        </button>
    </div>
</div>
