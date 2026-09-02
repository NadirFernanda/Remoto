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
                class="w-full pl-9 pr-3 py-2 rounded-xl border border-gray-200 bg-white text-gray-900 placeholder:text-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-[#0055ff]/30 focus:border-[#0055ff]">
        </div>
        {{-- Status --}}
        <select wire:model.live="statusFilter" class="rounded-xl border border-gray-200 bg-white text-gray-900 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0055ff]/30 focus:border-[#0055ff]">
            <option value="">Todos os estados</option>
            <option value="aberto">Abertos</option>
            <option value="em_andamento">Em Andamento</option>
            <option value="fechado">Fechados</option>
        </select>
        {{-- Category --}}
        <select wire:model.live="categoryFilter" class="rounded-xl border border-gray-200 bg-white text-gray-900 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0055ff]/30 focus:border-[#0055ff]">
            <option value="">Todas as categorias</option>
            <option value="pagamento">Pagamento</option>
            <option value="projecto">Projecto</option>
            <option value="conta">Conta</option>
            <option value="tecnico">Técnico</option>
            <option value="outro">Outro</option>
        </select>
        {{-- Priority --}}
        <select wire:model.live="priorityFilter" class="rounded-xl border border-gray-200 bg-white text-gray-900 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0055ff]/30 focus:border-[#0055ff]">
            <option value="">Todas as prioridades</option>
            <option value="urgente">Urgente</option>
            <option value="alta">Alta</option>
            <option value="normal">Normal</option>
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
            <button wire:key="support-ticket-{{ $ticket->id }}"
                wire:click="selectTicket({{ $ticket->id }})"
                class="w-full text-left bg-white rounded-2xl border p-4 transition {{ $isSelected ? 'border-[#0055ff] ring-1 ring-[#0055ff]/30 shadow-sm' : 'border-gray-200 hover:border-[#0055ff]/40 hover:shadow-sm' }}">
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
                    <p class="text-xs text-gray-600 truncate">{{ $ticket->user->name }}</p>
                    <span class="text-xs text-gray-500">· {{ \App\Models\SupportTicket::categoryLabel($ticket->category) }}</span>
                </div>
                <p class="text-xs text-gray-500 mt-1">{{ $ticket->updated_at->diffForHumans() }}</p>
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
        <div class="flex-1 min-w-0 space-y-4" x-data="{ bulkIds: [] }">

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
                    <div class="flex-1 min-w-0">
                        <h2 class="text-base font-bold text-gray-900">#{{ $selected->id }} · {{ $selected->subject }}</h2>
                        <div class="flex items-center gap-2 mt-1 flex-wrap">
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $sc2 }}">{{ \App\Models\SupportTicket::statusLabel($selected->status) }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $pc2 }}">{{ \App\Models\SupportTicket::priorityLabel($selected->priority) }}</span>
                            <span class="text-xs text-gray-500">{{ \App\Models\SupportTicket::categoryLabel($selected->category) }}</span>
                        </div>
                        <div class="flex items-center gap-3 mt-3">
                            <img src="{{ $selected->user->avatarUrl() }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                            <div>
                                <p class="text-sm text-gray-800 font-semibold">{{ $selected->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $selected->user->email }}</p>
                            </div>
                            <a href="{{ route('admin.impersonate.start', $selected->user) }}"
                                onclick="return confirm('Vai aceder à plataforma como {{ addslashes($selected->user->name) }}. Um banner vermelho ficará visível enquanto estiver neste modo. Continuar?')"
                                class="ml-auto inline-flex items-center gap-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 text-xs font-semibold transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Aceder como utilizador
                            </a>
                        </div>
                        @if($selected->user_provided_id || $selected->contact_email || $selected->contact_phone)
                        <div class="mt-2 text-xs text-gray-500 flex flex-wrap gap-x-4 gap-y-1">
                            @if($selected->user_provided_id)<span>ID: <span class="font-medium text-gray-700">{{ $selected->user_provided_id }}</span></span>@endif
                            @if($selected->contact_email)<span>Email: <span class="font-medium text-gray-700">{{ $selected->contact_email }}</span></span>@endif
                            @if($selected->contact_phone)<span>Tel: <span class="font-medium text-gray-700">{{ $selected->contact_phone }}</span></span>@endif
                        </div>
                        @endif
                    </div>
                    {{-- Status changer --}}
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <select wire:model="newStatus" class="rounded-xl border border-gray-200 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0055ff]/30 focus:border-[#0055ff]">
                            <option value="aberto">Aberto</option>
                            <option value="em_andamento">Em Andamento</option>
                            <option value="fechado">Fechado</option>
                        </select>
                        <button wire:click="updateStatus" class="btn-outline text-xs">Guardar</button>
                    </div>
                </div>
            </div>

            {{-- ── TABS: Conversa / Conta Completa ──────────────────────── --}}
            <div class="flex border-b border-gray-200 bg-white rounded-t-2xl overflow-hidden">
                <button wire:click="$set('detailTab','conversa')"
                    class="flex items-center gap-2 px-5 py-3 text-sm font-medium border-b-2 transition {{ $detailTab === 'conversa' ? 'border-[#0055ff] text-[#0055ff]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    Conversa
                </button>
                <button wire:click="$set('detailTab','conta')"
                    class="flex items-center gap-2 px-5 py-3 text-sm font-medium border-b-2 transition {{ $detailTab === 'conta' ? 'border-[#0055ff] text-[#0055ff]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Conta Completa
                    @if($userServices->count())
                        <span class="ml-1 text-xs bg-[#0055ff] text-white rounded-full px-1.5 py-0.5 font-semibold">{{ $userServices->count() }}</span>
                    @endif
                </button>
            </div>

            {{-- ── TAB: CONVERSA ──────────────────────────────────────────── --}}
            @if($detailTab === 'conversa')
            <div class="space-y-3">
                {{-- Original message --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <img src="{{ $selected->user->avatarUrl() }}" class="w-7 h-7 rounded-full object-cover">
                        <span class="text-sm font-semibold text-gray-800">{{ $selected->user->name }}</span>
                        <span class="text-xs text-gray-500">· {{ $selected->created_at->diffForHumans() }}</span>
                        <span class="text-xs text-gray-500 ml-auto">{{ $selected->created_at->format('d/m/Y H:i') }}</span>
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
                            {{ $isAdmin ? 'Suporte · ' . $reply->user->name : $reply->user->name }}
                        </span>
                        <span class="text-xs text-gray-500">· {{ $reply->created_at->diffForHumans() }}</span>
                        <span class="text-xs text-gray-500 ml-auto">{{ $reply->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <p class="text-sm {{ $isAdmin ? 'text-blue-900' : 'text-gray-700' }} leading-relaxed whitespace-pre-line">{{ $reply->message }}</p>
                </div>
                @endforeach
            </div>

            {{-- Admin reply form --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-4 space-y-3">
                <p class="text-sm font-semibold text-gray-700">Responder como Suporte</p>
                <textarea wire:model="replyMessage" rows="4" placeholder="Escreva a sua resposta ao utilizador..."
                    class="w-full rounded-xl border border-gray-200 bg-white text-gray-900 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0055ff]/30 focus:border-[#0055ff] resize-none"></textarea>
                @error('replyMessage') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                <div class="flex items-center gap-3">
                    <button wire:click="sendReply" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        Enviar Resposta
                    </button>
                    <p class="text-xs text-gray-400">O utilizador será notificado por notificação na plataforma.</p>
                </div>
            </div>
            @endif

            {{-- ── TAB: CONTA COMPLETA ────────────────────────────────────── --}}
            @if($detailTab === 'conta')
            <div class="space-y-4">

                {{-- Carteira --}}
                @if($selected->user->wallet)
                <div class="bg-white rounded-2xl border border-gray-200 p-5">
                    <h3 class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#0055ff]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        Carteira
                    </h3>
                    <div class="grid gap-3 sm:grid-cols-3 mb-4">
                        <div class="rounded-xl border border-gray-100 bg-gray-50 p-3 text-xs">
                            <div class="text-gray-500">Saldo disponível</div>
                            <div class="mt-1.5 text-base font-bold text-gray-800">{{ number_format($selected->user->wallet->saldo, 0, ',', '.') }} Kz</div>
                        </div>
                        <div class="rounded-xl border border-gray-100 bg-gray-50 p-3 text-xs">
                            <div class="text-gray-500">Saldo pendente</div>
                            <div class="mt-1.5 text-base font-bold text-gray-800">{{ number_format($selected->user->wallet->saldo_pendente, 0, ',', '.') }} Kz</div>
                        </div>
                        <div class="rounded-xl border border-gray-100 bg-gray-50 p-3 text-xs">
                            <div class="text-gray-500">Mínimo saque</div>
                            <div class="mt-1.5 text-base font-bold text-gray-800">{{ number_format($selected->user->wallet->saque_minimo, 0, ',', '.') }} Kz</div>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('admin.user.wallet.history', $selected->user) }}" target="_blank"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-[#0055ff] bg-[#0055ff]/10 px-3 py-2 text-xs font-semibold text-[#0055ff] hover:bg-[#0055ff]/20 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Movimentos da carteira
                        </a>
                        <a href="{{ route('admin.wallet.adjustment') }}" target="_blank"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                            Ajuste de saldo
                        </a>
                    </div>
                </div>
                @endif

                {{-- Projectos / Recibos --}}
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                        <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#0055ff]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Projectos com comprovativo ({{ $userServices->count() }})
                        </h3>
                        {{-- Bulk action --}}
                        <div x-show="bulkIds.length > 0" x-cloak class="flex items-center gap-2">
                            <span class="text-xs text-gray-500" x-text="bulkIds.length + ' seleccionado(s)'"></span>
                            <a :href="'{{ route('admin.services.receipts.bulk') }}?ids=' + bulkIds.join(',')"
                                target="_blank"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-[#0055ff] text-white text-xs font-semibold px-3 py-2 hover:bg-[#0033cc] transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                Extrair selecionados
                            </a>
                        </div>
                        @if($userServices->count() > 1)
                        <button x-show="bulkIds.length === 0" x-cloak
                            @click="bulkIds = {{ $userServices->pluck('id') }}"
                            class="text-xs text-[#0055ff] hover:underline font-medium">
                            Seleccionar todos
                        </button>
                        @endif
                    </div>

                    @forelse($userServices as $svc)
                    @php
                        $svcStatus = match($svc->status) {
                            'published'                                  => ['t' => 'Retido (Escrow)',    'c' => 'bg-blue-100 text-blue-700'],
                            'accepted','negotiating'                     => ['t' => 'Em Negociação',      'c' => 'bg-blue-100 text-blue-700'],
                            'in_progress','em_andamento','em andamento'  => ['t' => 'Em Execução',        'c' => 'bg-amber-100 text-amber-700'],
                            'revision_requested'                         => ['t' => 'Revisão Pedida',     'c' => 'bg-orange-100 text-orange-700'],
                            'delivered'                                  => ['t' => 'Aguarda Revisão',    'c' => 'bg-sky-100 text-sky-700'],
                            'completed','concluido'                      => ['t' => 'Concluído',          'c' => 'bg-green-100 text-green-700'],
                            'cancelled','cancelado'                      => ['t' => 'Cancelado',          'c' => 'bg-red-100 text-red-700'],
                            default                                      => ['t' => ucfirst($svc->status),'c' => 'bg-gray-100 text-gray-600'],
                        };
                    @endphp
                    <div class="flex items-center gap-3 px-5 py-3.5 border-b border-gray-50 last:border-0 hover:bg-gray-50 transition">
                        {{-- Checkbox --}}
                        <input type="checkbox" :value="{{ $svc->id }}"
                            x-model="bulkIds"
                            class="w-4 h-4 rounded border-gray-300 accent-[#0055ff] flex-shrink-0">
                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $svc->titulo }}</p>
                            <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                <span class="text-xs font-semibold {{ $svcStatus['c'] }} px-2 py-0.5 rounded-full">{{ $svcStatus['t'] }}</span>
                                @if($svc->freelancer)
                                <span class="text-xs text-gray-500">Freelancer: {{ $svc->freelancer->name }}</span>
                                @endif
                                <span class="text-xs text-gray-400">{{ $svc->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                        {{-- Valor --}}
                        <div class="text-sm font-bold text-gray-800 flex-shrink-0 text-right">
                            {{ number_format((float)($svc->valor ?? 0), 0, ',', '.') }} Kz
                        </div>
                        {{-- Botão individual --}}
                        <a href="{{ route('admin.service.receipt', $svc) }}" target="_blank"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-[#0055ff] bg-[#0055ff]/10 px-3 py-1.5 text-xs font-semibold text-[#0055ff] hover:bg-[#0055ff]/20 transition flex-shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Extrair
                        </a>
                    </div>
                    @empty
                    <div class="py-10 text-center text-gray-400 text-sm">
                        <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Sem projectos com comprovativo disponível.
                    </div>
                    @endforelse
                </div>

            </div>
            @endif

        </div>
        @endif
    </div>
</div>
