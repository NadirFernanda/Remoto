<div class="max-w-5xl mx-auto space-y-6">

    {{-- Gradient Header --}}
    <div class="bg-gradient-to-r from-[#00baff] to-[#0095cc] rounded-2xl p-6 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold">Suporte</h2>
            <p class="text-sm text-white/75 mt-1">Abra um ticket para a nossa equipa e acompanhe as respostas.</p>
        </div>
        <button wire:click="openForm" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-white/15 border border-white/30 hover:bg-white/25 text-white transition self-start">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Novo Ticket
        </button>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-2xl px-4 py-3 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-2xl px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    {{-- New ticket form --}}
    @if($showForm)
    <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
        <h2 class="text-base font-bold text-gray-900">Abrir Novo Ticket</h2>

        {{-- Category --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Categoria *</label>
            <select wire:model="category" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
                <option value="">Selecione uma categoria</option>
                <option value="pagamento">💳 Pagamento</option>
                <option value="projecto">📁 Projecto</option>
                <option value="conta">👤 Conta</option>
                <option value="tecnico">🔧 Técnico</option>
                <option value="outro">❓ Outro</option>
            </select>
            @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Priority --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Prioridade *</label>
            <div class="flex gap-3 flex-wrap">
                @foreach(['normal' => ['Normal', 'bg-gray-100 text-gray-700', 'bg-gray-500'], 'alta' => ['Alta', 'bg-amber-100 text-amber-700', 'bg-amber-500'], 'urgente' => ['Urgente', 'bg-red-100 text-red-700', 'bg-red-500']] as $val => [$label, $cls, $dot])
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" wire:model="priority" value="{{ $val }}" class="sr-only">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border-2 transition cursor-pointer
                        {{ $priority === $val ? $cls . ' border-current' : 'bg-white text-gray-500 border-gray-200' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>
                        {{ $label }}
                    </span>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Subject --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Assunto *</label>
            <input wire:model="subject" type="text" placeholder="Descreva brevemente o problema"
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
            @error('subject') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Message --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Descrição detalhada *</label>
            <textarea wire:model="message" rows="5" placeholder="Descreva o problema com o máximo de detalhe possível..."
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] resize-none"></textarea>
            @error('message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-3 pt-1">
            <button wire:click="submitTicket" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                Enviar Ticket
            </button>
            <button wire:click="$set('showForm', false)" class="btn-outline">Cancelar</button>
        </div>
    </div>
    @endif

    {{-- Layout: list + detail --}}
    <div class="flex gap-5 items-start">

        {{-- Ticket list --}}
        <div class="w-full {{ $selected ? 'hidden lg:block lg:w-72 flex-shrink-0' : '' }} space-y-3">

            {{-- Status filter --}}
            <div class="flex gap-2 flex-wrap">
                @foreach(['' => 'Todos', 'aberto' => 'Abertos', 'em_andamento' => 'Em Andamento', 'fechado' => 'Fechados'] as $val => $label)
                <button wire:click="$set('statusFilter', '{{ $val }}')"
                    class="px-3 py-1 rounded-full text-xs font-medium border transition
                        {{ $statusFilter === $val ? 'bg-[#00baff] text-white border-[#00baff]' : 'bg-white text-gray-500 border-gray-200 hover:border-[#00baff]' }}">
                    {{ $label }}
                </button>
                @endforeach
            </div>

            @forelse($tickets as $ticket)
            @php
                $statusColor = match($ticket->status) {
                    'aberto'       => 'bg-blue-100 text-blue-700',
                    'em_andamento' => 'bg-amber-100 text-amber-700',
                    'fechado'      => 'bg-green-100 text-green-700',
                    default        => 'bg-gray-100 text-gray-600',
                };
                $priorityDot = match($ticket->priority) {
                    'urgente' => 'bg-red-500',
                    'alta'    => 'bg-amber-500',
                    default   => 'bg-gray-400',
                };
                $isSelected = $selected && $selected->id === $ticket->id;
            @endphp
            <button wire:click="selectTicket({{ $ticket->id }})"
                class="w-full text-left bg-white rounded-2xl border p-4 transition
                    {{ $isSelected ? 'border-[#00baff] ring-1 ring-[#00baff]/30 shadow-sm' : 'border-gray-200 hover:border-[#00baff]/40 hover:shadow-sm' }}">
                <div class="flex items-start justify-between gap-2 mb-1">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="w-2 h-2 rounded-full flex-shrink-0 {{ $priorityDot }}"></span>
                        <p class="text-sm font-semibold text-gray-800 truncate">#{{ $ticket->id }} · {{ $ticket->subject }}</p>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $statusColor }} flex-shrink-0">
                        {{ \App\Models\SupportTicket::statusLabel($ticket->status) }}
                    </span>
                </div>
                <p class="text-xs text-gray-400">{{ \App\Models\SupportTicket::categoryLabel($ticket->category) }} · {{ $ticket->updated_at->diffForHumans() }}</p>
            </button>
            @empty
                <div class="bg-white rounded-2xl border border-gray-200 p-8 text-center">
                    <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a3 3 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z"/>
                    </svg>
                    <p class="text-sm text-gray-500 font-medium">Sem tickets</p>
                    <button wire:click="openForm" class="btn-primary mt-3 text-xs">Abrir primeiro ticket</button>
                </div>
            @endforelse

            {{ $tickets->links() }}
        </div>

        {{-- Ticket detail --}}
        @if($selected)
        <div class="flex-1 min-w-0">

            {{-- Back on mobile --}}
            <button wire:click="$set('selectedTicketId', null)" class="lg:hidden btn-outline text-xs flex items-center gap-1 mb-4">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Voltar
            </button>

            {{-- Ticket header --}}
            @php
                $sc = match($selected->status) {
                    'aberto'       => 'bg-blue-100 text-blue-700',
                    'em_andamento' => 'bg-amber-100 text-amber-700',
                    'fechado'      => 'bg-green-100 text-green-700',
                    default        => 'bg-gray-100 text-gray-600',
                };
                $pc = match($selected->priority) {
                    'urgente' => 'bg-red-100 text-red-700',
                    'alta'    => 'bg-amber-100 text-amber-700',
                    default   => 'bg-gray-100 text-gray-600',
                };
            @endphp
            <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-4">
                <div class="flex items-start justify-between gap-3 flex-wrap">
                    <div>
                        <h2 class="text-base font-bold text-gray-900">#{{ $selected->id }} · {{ $selected->subject }}</h2>
                        <div class="flex items-center gap-2 mt-1 flex-wrap">
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $sc }}">{{ \App\Models\SupportTicket::statusLabel($selected->status) }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $pc }}">{{ \App\Models\SupportTicket::priorityLabel($selected->priority) }}</span>
                            <span class="text-xs text-gray-400">{{ \App\Models\SupportTicket::categoryLabel($selected->category) }}</span>
                            <span class="text-xs text-gray-400">· {{ $selected->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Thread --}}
            <div class="space-y-3 mb-4">
                {{-- Original message --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <img src="{{ $selected->user->avatarUrl() }}" class="w-7 h-7 rounded-full object-cover">
                        <span class="text-sm font-semibold text-gray-800">{{ $selected->user->name }}</span>
                        <span class="text-xs text-gray-400">· {{ $selected->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $selected->message }}</p>
                </div>

                {{-- Replies --}}
                @foreach($selected->replies as $reply)
                @php $isAdmin = $reply->is_admin_reply; @endphp
                <div class="rounded-2xl border p-4 {{ $isAdmin ? 'bg-blue-50 border-blue-100 ml-4' : 'bg-white border-gray-200' }}">
                    <div class="flex items-center gap-2 mb-2">
                        <img src="{{ $reply->user->avatarUrl() }}" class="w-7 h-7 rounded-full object-cover">
                        <span class="text-sm font-semibold {{ $isAdmin ? 'text-blue-800' : 'text-gray-800' }}">
                            {{ $isAdmin ? '🛡 Suporte · ' . $reply->user->name : $reply->user->name }}
                        </span>
                        <span class="text-xs text-gray-400">· {{ $reply->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm {{ $isAdmin ? 'text-blue-900' : 'text-gray-700' }} leading-relaxed whitespace-pre-line">{{ $reply->message }}</p>
                </div>
                @endforeach
            </div>

            {{-- Reply form (only if not closed) --}}
            @if($selected->status !== 'fechado')
            <div class="bg-white rounded-2xl border border-gray-200 p-4 space-y-3">
                <textarea wire:model="replyMessage" rows="4" placeholder="Escreva a sua resposta ou informação adicional..."
                    class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] resize-none"></textarea>
                @error('replyMessage') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                <button wire:click="sendReply" class="btn-primary text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Enviar Resposta
                </button>
            </div>
            @else
            <div class="bg-gray-50 border border-gray-200 rounded-2xl p-4 text-center text-sm text-gray-500">
                Este ticket está fechado. Envie uma mensagem para reabri-lo automaticamente.
                <div class="mt-3">
                    <textarea wire:model="replyMessage" rows="3" placeholder="Escreva uma mensagem para reabrir..."
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] resize-none"></textarea>
                    @error('replyMessage') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <button wire:click="sendReply" class="btn-outline text-xs mt-2">Reabrir e Enviar</button>
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>
