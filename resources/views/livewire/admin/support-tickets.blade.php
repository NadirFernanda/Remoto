<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <p class="text-sm text-gray-500">Gerencie os pedidos de suporte dos utilizadores.</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-blue-100 text-blue-700">
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                Abertos: {{ $counts['aberto'] }}
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-amber-100 text-amber-700">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                Em Andamento: {{ $counts['em_andamento'] }}
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-green-100 text-green-700">
                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                Fechados: {{ $counts['fechado'] }}
            </span>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-2xl px-4 py-3 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="flex gap-3 flex-wrap items-center">
        {{-- Search --}}
        <div class="relative flex-1 min-w-[200px]">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.400ms="search" type="text" placeholder="Pesquisar por assunto ou utilizador..."
                class="w-full pl-9 pr-3 py-2 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
        </div>
        {{-- Status --}}
        <select wire:model.live="statusFilter" class="rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
            <option value="">Todos os estados</option>
            <option value="aberto">Abertos</option>
            <option value="em_andamento">Em Andamento</option>
            <option value="fechado">Fechados</option>
        </select>
        {{-- Category --}}
        <select wire:model.live="categoryFilter" class="rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
            <option value="">Todas as categorias</option>
            <option value="pagamento">Pagamento</option>
            <option value="projecto">Projecto</option>
            <option value="conta">Conta</option>
            <option value="tecnico">Técnico</option>
            <option value="outro">Outro</option>
        </select>
        {{-- Priority --}}
        <select wire:model.live="priorityFilter" class="rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
            <option value="">Todas as prioridades</option>
            <option value="urgente">🔴 Urgente</option>
            <option value="alta">🟡 Alta</option>
            <option value="normal">⚪ Normal</option>
        </select>
    </div>

    {{-- Layout --}}
    <div class="flex gap-5 items-start">

        {{-- List --}}
        <div class="w-full {{ $selected ? 'hidden lg:block lg:w-80 flex-shrink-0' : '' }} space-y-2">
            @forelse($tickets as $ticket)
            @php
                $sc = match($ticket->status) {
                    'aberto'       => 'bg-blue-100 text-blue-700',
                    'em_andamento' => 'bg-amber-100 text-amber-700',
                    'fechado'      => 'bg-green-100 text-green-700',
                    default        => 'bg-gray-100 text-gray-600',
                };
                $dot = match($ticket->priority) {
                    'urgente' => 'bg-red-500',
                    'alta'    => 'bg-amber-500',
                    default   => 'bg-gray-400',
                };
                $isSelected = $selected && $selected->id === $ticket->id;
            @endphp
            <button wire:click="selectTicket({{ $ticket->id }})"
                class="w-full text-left bg-white rounded-2xl border p-4 transition
                    {{ $isSelected ? 'border-[#00baff] ring-1 ring-[#00baff]/30 shadow-sm' : 'border-gray-200 hover:border-[#00baff]/40 hover:shadow-sm' }}">
                <div class="flex items-center justify-between gap-2 mb-1">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="w-2 h-2 rounded-full flex-shrink-0 {{ $dot }}"></span>
                        <p class="text-sm font-semibold text-gray-800 truncate">#{{ $ticket->id }} {{ $ticket->subject }}</p>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $sc }} flex-shrink-0">
                        {{ \App\Models\SupportTicket::statusLabel($ticket->status) }}
                    </span>
                </div>
                <div class="flex items-center gap-2 mt-1">
                    <img src="{{ $ticket->user->avatarUrl() }}" class="w-4 h-4 rounded-full object-cover">
                    <p class="text-xs text-gray-500 truncate">{{ $ticket->user->name }}</p>
                    <span class="text-xs text-gray-400">· {{ \App\Models\SupportTicket::categoryLabel($ticket->category) }}</span>
                </div>
                <p class="text-xs text-gray-400 mt-1">{{ $ticket->updated_at->diffForHumans() }}</p>
            </button>
            @empty
                <div class="bg-white rounded-2xl border border-gray-200 p-8 text-center">
                    <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a3 3 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z"/>
                    </svg>
                    <p class="text-sm text-gray-500">Sem tickets correspondentes.</p>
                </div>
            @endforelse

            {{ $tickets->links() }}
        </div>

        {{-- Detail --}}
        @if($selected)
        <div class="flex-1 min-w-0 space-y-4">

            {{-- Back on mobile --}}
            <button wire:click="$set('selectedTicketId', null)" class="lg:hidden btn-outline text-xs flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Voltar
            </button>

            {{-- Header + status control --}}
            @php
                $sc2 = match($selected->status) {
                    'aberto'       => 'bg-blue-100 text-blue-700',
                    'em_andamento' => 'bg-amber-100 text-amber-700',
                    'fechado'      => 'bg-green-100 text-green-700',
                    default        => 'bg-gray-100 text-gray-600',
                };
                $pc2 = match($selected->priority) {
                    'urgente' => 'bg-red-100 text-red-700',
                    'alta'    => 'bg-amber-100 text-amber-700',
                    default   => 'bg-gray-100 text-gray-600',
                };
            @endphp
            <div class="bg-white rounded-2xl border border-gray-200 p-5">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                    <div>
                        <h2 class="text-base font-bold text-gray-900">#{{ $selected->id }} · {{ $selected->subject }}</h2>
                        <div class="flex items-center gap-2 mt-1 flex-wrap">
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $sc2 }}">{{ \App\Models\SupportTicket::statusLabel($selected->status) }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $pc2 }}">{{ \App\Models\SupportTicket::priorityLabel($selected->priority) }}</span>
                            <span class="text-xs text-gray-400">{{ \App\Models\SupportTicket::categoryLabel($selected->category) }}</span>
                        </div>
                        <div class="flex items-center gap-2 mt-2">
                            <img src="{{ $selected->user->avatarUrl() }}" class="w-5 h-5 rounded-full object-cover">
                            <span class="text-sm text-gray-700 font-medium">{{ $selected->user->name }}</span>
                            <span class="text-xs text-gray-400">{{ $selected->user->email }}</span>
                        </div>
                    </div>
                    {{-- Status changer --}}
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <select wire:model="newStatus" class="rounded-xl border border-gray-200 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
                            <option value="aberto">Aberto</option>
                            <option value="em_andamento">Em Andamento</option>
                            <option value="fechado">Fechado</option>
                        </select>
                        <button wire:click="updateStatus" class="btn-outline text-xs">Guardar</button>
                    </div>
                </div>
            </div>

            {{-- Thread --}}
            <div class="space-y-3">
                {{-- Original message --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <img src="{{ $selected->user->avatarUrl() }}" class="w-7 h-7 rounded-full object-cover">
                        <span class="text-sm font-semibold text-gray-800">{{ $selected->user->name }}</span>
                        <span class="text-xs text-gray-400">· {{ $selected->created_at->diffForHumans() }}</span>
                        <span class="text-xs text-gray-400 ml-auto">{{ $selected->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $selected->message }}</p>
                </div>

                {{-- Replies --}}
                @foreach($selected->replies as $reply)
                @php $isAdmin = $reply->is_admin_reply; @endphp
                <div class="rounded-2xl border p-4 {{ $isAdmin ? 'bg-blue-50 border-blue-100 ml-6' : 'bg-white border-gray-200' }}">
                    <div class="flex items-center gap-2 mb-2">
                        <img src="{{ $reply->user->avatarUrl() }}" class="w-7 h-7 rounded-full object-cover">
                        <span class="text-sm font-semibold {{ $isAdmin ? 'text-blue-800' : 'text-gray-800' }}">
                            {{ $isAdmin ? '🛡 Suporte · ' . $reply->user->name : $reply->user->name }}
                        </span>
                        <span class="text-xs text-gray-400">· {{ $reply->created_at->diffForHumans() }}</span>
                        <span class="text-xs text-gray-400 ml-auto">{{ $reply->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <p class="text-sm {{ $isAdmin ? 'text-blue-900' : 'text-gray-700' }} leading-relaxed whitespace-pre-line">{{ $reply->message }}</p>
                </div>
                @endforeach
            </div>

            {{-- Admin reply form --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-4 space-y-3">
                <p class="text-sm font-semibold text-gray-700">Responder como Suporte</p>
                <textarea wire:model="replyMessage" rows="4" placeholder="Escreva a sua resposta ao utilizador..."
                    class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] resize-none"></textarea>
                @error('replyMessage') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                <div class="flex items-center gap-3">
                    <button wire:click="sendReply" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        Enviar Resposta
                    </button>
                    <p class="text-xs text-gray-400">O utilizador será notificado por notificação na plataforma.</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
