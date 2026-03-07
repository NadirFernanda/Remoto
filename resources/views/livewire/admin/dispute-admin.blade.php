<div class="light-page min-h-screen pt-8 pb-12">
<div class="max-w-7xl mx-auto px-4">
    <h1 class="text-2xl font-bold mb-6">Central de Disputas</h1>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    <div class="flex gap-6">
        {{-- LEFT: list --}}
        <div class="w-full md:w-1/2 lg:w-2/5">
            <div class="mb-3 flex items-center gap-2">
                <label class="text-sm font-medium text-gray-600">Filtrar por status:</label>
                <select wire:model.live="statusFilter" class="border rounded px-2 py-1 text-sm">
                    <option value="">Todos</option>
                    <option value="aberta">Aberta</option>
                    <option value="em_mediacao">Em mediação</option>
                    <option value="resolvida">Resolvida</option>
                    <option value="encerrada">Encerrada</option>
                </select>
            </div>

            <div class="space-y-3">
                @forelse($disputes as $d)
                <button
                    wire:click="select({{ $d->id }})"
                    class="w-full text-left bg-white border rounded-xl p-4 shadow-sm hover:border-cyan-400 transition {{ $selectedId === $d->id ? 'border-cyan-500 ring-1 ring-cyan-400' : '' }}"
                >
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-sm truncate">{{ $d->service->titulo ?? 'Serviço #'.$d->service_id }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full font-semibold
                            {{ $d->status === 'aberta' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $d->status === 'em_mediacao' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $d->status === 'resolvida' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $d->status === 'encerrada' ? 'bg-gray-100 text-gray-600' : '' }}
                        ">{{ ucfirst(str_replace('_',' ',$d->status)) }}</span>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">Aberta por {{ $d->opener->name }} · {{ $d->created_at->diffForHumans() }}</div>
                    <div class="text-xs text-gray-600 mt-1 truncate">{{ \App\Models\Dispute::$reasons[$d->reason] ?? $d->reason }}</div>
                </button>
                @empty
                    <p class="text-gray-500 text-sm">Nenhuma disputa encontrada.</p>
                @endforelse
            </div>

            <div class="mt-4">{{ $disputes->links() }}</div>
        </div>

        {{-- RIGHT: detail --}}
        <div class="flex-1">
            @if($selected)
            <div class="bg-white border rounded-2xl shadow p-6">
                <h2 class="text-lg font-semibold mb-1">{{ $selected->service->titulo ?? 'Serviço #'.$selected->service_id }}</h2>
                <div class="text-sm text-gray-500 mb-4">
                    Cliente: <span class="font-medium">{{ $selected->service->cliente->name ?? '—' }}</span>
                    &nbsp;·&nbsp;
                    Freelancer: <span class="font-medium">{{ $selected->service->freelancer->name ?? '—' }}</span>
                </div>

                <div class="mb-4 p-3 bg-gray-50 rounded-lg text-sm">
                    <span class="font-medium">Motivo:</span> {{ \App\Models\Dispute::$reasons[$selected->reason] ?? $selected->reason }}<br>
                    <span class="font-medium text-gray-700 mt-1 block">{{ $selected->description }}</span>
                </div>

                {{-- Message thread --}}
                <div class="space-y-3 mb-5 max-h-64 overflow-y-auto pr-1">
                    @foreach($selected->messages as $msg)
                    <div class="flex items-start gap-2">
                        <img src="{{ $msg->user->avatarUrl() }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                        <div>
                            <div class="text-xs font-semibold text-gray-700">
                                {{ $msg->user->name }}
                                @if($msg->user->role === 'admin')<span class="ml-1 text-[10px] bg-purple-100 text-purple-700 px-1 rounded">Admin</span>@endif
                            </div>
                            <div class="text-sm text-gray-800 bg-gray-100 rounded-xl px-3 py-2 mt-0.5 whitespace-pre-line">{{ $msg->message }}</div>
                            <div class="text-[10px] text-gray-400 mt-0.5">{{ $msg->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Admin reply --}}
                <div class="mb-5">
                    <textarea wire:model.defer="replyMessage" rows="3" placeholder="Escreva uma mensagem para as partes..." class="block w-full border rounded-lg p-2 text-sm focus:ring-1 focus:ring-cyan-400"></textarea>
                    <button wire:click="sendReply" class="mt-2 btn-eq btn-primary text-sm">Enviar mensagem</button>
                </div>

                <hr class="my-4">

                {{-- Change status + note --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alterar status</label>
                        <select wire:model.defer="newStatus" class="block w-full border rounded-lg p-2 text-sm">
                            <option value="aberta">Aberta</option>
                            <option value="em_mediacao">Em mediação</option>
                            <option value="resolvida">Resolvida</option>
                            <option value="encerrada">Encerrada</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nota administrativa (visível às partes)</label>
                        <textarea wire:model.defer="adminNote" rows="2" class="block w-full border rounded-lg p-2 text-sm focus:ring-1 focus:ring-cyan-400" placeholder="Resolução, instruções..."></textarea>
                    </div>
                </div>
                <button wire:click="saveChanges" class="mt-4 btn-eq btn-primary">Salvar alterações</button>
            </div>
            @else
                <div class="flex items-center justify-center h-64 text-gray-400 text-sm border-2 border-dashed rounded-2xl">
                    Selecione uma disputa para ver os detalhes
                </div>
            @endif
        </div>
    </div>
</div>
</div>
