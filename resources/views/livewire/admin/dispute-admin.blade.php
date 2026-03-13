<div>
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 text-green-800 rounded-[10px] border border-green-200 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Projectos em moderação sem disputa formal --}}
    @if($orphanModerations->count() > 0)
        <div class="mb-5 bg-orange-50 border border-orange-200 rounded-2xl p-4">
            <h3 class="text-sm font-bold text-orange-800 mb-3">⚠ Projectos em moderação sem disputa formal ({{ $orphanModerations->count() }})</h3>
            <div class="space-y-2">
                @foreach($orphanModerations as $svc)
                    <div class="flex items-center justify-between bg-white border border-orange-100 rounded-xl px-4 py-2 text-sm">
                        <div>
                            <span class="font-medium text-gray-800">{{ $svc->titulo }}</span>
                            <span class="text-xs text-gray-500 ml-2">Cliente: {{ $svc->cliente->name ?? '—' }} · Freelancer: {{ $svc->freelancer->name ?? '—' }}</span>
                        </div>
                        <a href="{{ route('service.dispute', $svc->id) }}" class="text-xs text-orange-700 hover:underline font-semibold">Ver</a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Status filter tabs --}}
    <div class="flex gap-2 flex-wrap mb-5">
        @foreach(['' => 'Todas', 'aberta' => 'Abertas', 'em_mediacao' => 'Em Mediação', 'resolvida' => 'Resolvidas', 'encerrada' => 'Encerradas'] as $val => $label)
            <button wire:click="$set('statusFilter', '{{ $val }}')"
                class="px-3 py-1.5 rounded-[10px] text-xs font-medium border transition
                    {{ $statusFilter === $val
                        ? 'bg-[#00baff] text-white border-[#00baff]'
                        : 'bg-white text-gray-600 border-gray-200 hover:border-[#00baff] hover:text-[#00baff]' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    <div class="flex gap-5 items-start">
        {{-- LEFT: list --}}
        <div class="{{ $selected ? 'hidden lg:block' : '' }} lg:w-80 w-full flex-shrink-0 space-y-2">
            @forelse($disputes as $d)
            <button wire:click="select({{ $d->id }})"
                class="w-full text-left bg-white border rounded-2xl p-4 shadow-sm hover:border-[#00baff]/40 transition
                    {{ $selectedId === $d->id ? 'border-[#00baff] ring-1 ring-[#00baff]/20' : 'border-gray-200' }}"
            >
                <div class="flex items-center justify-between gap-2">
                    <span class="font-medium text-sm text-gray-800 truncate flex-1">{{ $d->service->titulo ?? 'Serviço #'.$d->service_id }}</span>
                    <span class="text-xs px-2 py-0.5 rounded-full font-semibold flex-shrink-0
                        {{ $d->status === 'aberta'     ? 'bg-red-100 text-red-700' : '' }}
                        {{ $d->status === 'em_mediacao'? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $d->status === 'resolvida'  ? 'bg-green-100 text-green-700' : '' }}
                        {{ $d->status === 'encerrada'  ? 'bg-gray-100 text-gray-600' : '' }}
                    ">{{ ucfirst(str_replace('_',' ',$d->status)) }}</span>
                </div>
                <div class="text-xs text-gray-500 mt-1">{{ $d->opener->name ?? '—' }} · {{ $d->created_at->diffForHumans() }}</div>
                <div class="text-xs text-gray-400 truncate mt-0.5">{{ \App\Models\Dispute::$reasons[$d->reason] ?? $d->reason }}</div>
            </button>
            @empty
                <p class="text-sm text-gray-400 text-center py-8">Nenhuma disputa encontrada.</p>
            @endforelse
            <div class="mt-4">{{ $disputes->links() }}</div>
        </div>

        {{-- RIGHT: detail --}}
        <div class="flex-1 min-w-0 {{ !$selected ? 'hidden lg:block' : '' }}">
            @if($selected)
            {{-- Back on mobile --}}
            <button wire:click="$set('selectedId', null)" class="lg:hidden btn-outline text-xs flex items-center gap-1 mb-4">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Voltar à lista
            </button>

            <div class="bg-white rounded-2xl border border-gray-200 p-5 space-y-5">
                {{-- Header --}}
                <div>
                    <h2 class="text-base font-bold text-gray-900">{{ $selected->service->titulo ?? 'Serviço #'.$selected->service_id }}</h2>
                    <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-gray-500">
                        <span>Cliente: <span class="font-medium text-gray-700">{{ $selected->service->cliente->name ?? '—' }}</span></span>
                        <span>·</span>
                        <span>Freelancer: <span class="font-medium text-gray-700">{{ $selected->service->freelancer->name ?? '—' }}</span></span>
                        <span>·</span>
                        <span>Aberta por: <span class="font-medium text-gray-700">{{ $selected->opener->name ?? '—' }}</span></span>
                    </div>
                </div>

                {{-- Motivo --}}
                <div class="bg-gray-50 rounded-[10px] border border-gray-100 p-3 text-sm">
                    <span class="font-medium text-gray-600">Motivo:</span> {{ \App\Models\Dispute::$reasons[$selected->reason] ?? $selected->reason }}
                    <p class="text-gray-700 mt-1 leading-relaxed">{{ $selected->description }}</p>
                </div>

                {{-- Payment control --}}
                @if($selected->service)
                <div class="bg-orange-50 border border-orange-200 rounded-[10px] p-3">
                    <p class="text-xs font-semibold text-orange-700 mb-2">Controlo de Pagamento</p>
                    <div class="flex items-center gap-2 flex-wrap text-xs">
                        <span class="text-gray-600">Status do serviço:
                            <span class="font-semibold text-gray-800">{{ $selected->service->status }}</span>
                        </span>
                        <span class="text-gray-400">·</span>
                        <span class="text-gray-600">Pagamento libertado:
                            <span class="font-semibold {{ $selected->service->is_payment_released ? 'text-green-600' : 'text-red-500' }}">
                                {{ $selected->service->is_payment_released ? 'Sim' : 'Não' }}
                            </span>
                        </span>
                        @if(!$selected->service->is_payment_released)
                            <button wire:click="freezePayment({{ $selected->service->id }})"
                                wire:confirm="Congelar o pagamento deste projecto (colocar em moderação)?"
                                class="px-2 py-1 bg-orange-100 text-orange-700 border border-orange-300 rounded-lg hover:bg-orange-200 transition">
                                ❄ Congelar
                            </button>
                            <button wire:click="releasePayment({{ $selected->service->id }})"
                                wire:confirm="Libertar o pagamento ao freelancer? O valor líquido será creditado na sua carteira."
                                class="px-2 py-1 bg-green-100 text-green-700 border border-green-300 rounded-lg hover:bg-green-200 transition">
                                ✓ Libertar → Freelancer
                            </button>
                            <button wire:click="reembolsarCliente({{ $selected->service->id }})"
                                wire:confirm="Reembolsar o cliente? O escrow será devolvido à carteira do cliente e o projecto cancelado."
                                class="px-2 py-1 bg-blue-100 text-blue-700 border border-blue-300 rounded-lg hover:bg-blue-200 transition">
                                ↩ Reembolsar → Cliente
                            </button>
                        @else
                            <span class="px-2 py-1 bg-green-100 text-green-600 rounded-lg text-xs">Pagamento já libertado</span>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Message thread --}}
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @foreach($selected->messages as $msg)
                    <div class="flex items-start gap-2">
                        <img src="{{ $msg->user->avatarUrl() }}" class="w-7 h-7 rounded-full object-cover flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-700">
                                {{ $msg->user->name }}
                                @if($msg->user->role === 'admin')
                                    <span class="ml-1 text-[10px] bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded-full">Admin</span>
                                @endif
                            </p>
                            <p class="text-sm text-gray-800 bg-gray-100 rounded-xl px-3 py-2 mt-0.5 whitespace-pre-line">{{ $msg->message }}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">{{ $msg->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Reply --}}
                <div>
                    <textarea wire:model.defer="replyMessage" rows="3"
                        placeholder="Escreva uma mensagem para as partes..."
                        class="w-full rounded-[10px] border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] resize-none"></textarea>
                    <button wire:click="sendReply" class="mt-2 btn-primary text-xs">Enviar mensagem</button>
                </div>

                <hr class="border-gray-100">

                {{-- Status + Note --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Alterar status da disputa</label>
                        <select wire:model.defer="newStatus"
                            class="w-full rounded-[10px] border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
                            <option value="aberta">Aberta</option>
                            <option value="em_mediacao">Em mediação</option>
                            <option value="resolvida">Resolvida</option>
                            <option value="encerrada">Encerrada</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nota administrativa (visível às partes)</label>
                        <textarea wire:model.defer="adminNote" rows="2"
                            placeholder="Resolução, instruções..."
                            class="w-full rounded-[10px] border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] resize-none"></textarea>
                    </div>
                </div>
                <button wire:click="saveChanges" class="btn-primary text-sm">Guardar alterações</button>
            </div>

            @else
                <div class="flex items-center justify-center h-64 text-gray-400 text-sm border-2 border-dashed border-gray-200 rounded-2xl">
                    Selecione uma disputa para ver os detalhes
                </div>
            @endif
        </div>
    </div>
</div>
