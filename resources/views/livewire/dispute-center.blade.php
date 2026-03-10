<div class="min-h-screen bg-gray-50">
    <div class="max-w-2xl mx-auto px-4 py-10">
        <h1 class="text-2xl font-bold text-gray-900 mb-1">Central de Disputas</h1>
        <p class="text-gray-500 mb-6 text-sm">Projecto: <span class="font-semibold text-gray-700">{{ $service->titulo }}</span></p>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded-xl">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded-xl">{{ session('error') }}</div>
        @endif

        @if(!$dispute)
            {{-- Open dispute form --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm mb-6">
                <h2 class="font-semibold text-gray-800 mb-4 text-lg">Abrir disputa</h2>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Motivo</label>
                    <select wire:model.defer="reason" class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-cyan-300 focus:outline-none">
                        <option value="">Selecione um motivo</option>
                        <option value="atraso">Atraso na entrega</option>
                        <option value="qualidade">Qualidade insuficiente</option>
                        <option value="nao_pagamento">Não pagamento</option>
                        <option value="outro">Outro motivo</option>
                    </select>
                    @error('reason') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Descrição do problema</label>
                    <textarea wire:model.defer="description" rows="5"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-cyan-300 focus:outline-none resize-none"
                        placeholder="Descreva detalhadamente o problema..."></textarea>
                    @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <button wire:click="openDispute" wire:loading.attr="disabled"
                    class="btn-eq btn-primary w-full justify-center">
                    <span wire:loading.remove wire:target="openDispute">Abrir disputa</span>
                    <span wire:loading wire:target="openDispute">A processar...</span>
                </button>
            </div>
        @else
            {{-- Dispute status --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm mb-4 flex items-center justify-between">
                <div>
                    <div class="text-xs text-gray-400 mb-1">Motivo</div>
                    <div class="font-semibold text-gray-800">{{ \App\Models\Dispute::$reasons[$dispute->reason] ?? $dispute->reason }}</div>
                </div>
                <div>
                    @php
                        $statusColors = [
                            'aberta'       => 'bg-red-50 text-red-700 border-red-200',
                            'em_mediacao'  => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                            'resolvida'    => 'bg-green-50 text-green-700 border-green-200',
                            'encerrada'    => 'bg-gray-100 text-gray-500 border-gray-200',
                        ];
                        $statusLabels = [
                            'aberta'      => 'Aberta',
                            'em_mediacao' => 'Em mediação',
                            'resolvida'   => 'Resolvida',
                            'encerrada'   => 'Encerrada',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $statusColors[$dispute->status] ?? 'bg-gray-100 text-gray-500' }}">
                        {{ $statusLabels[$dispute->status] ?? ucfirst($dispute->status) }}
                    </span>
                </div>
            </div>

            @if($dispute->admin_note)
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 mb-4 text-sm text-blue-800">
                    <span class="font-semibold">Nota da equipa de suporte:</span> {{ $dispute->admin_note }}
                </div>
            @endif

            {{-- Messages --}}
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm mb-4 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-800">Comunicação</h2>
                </div>
                <div class="divide-y divide-gray-50 max-h-80 overflow-y-auto">
                    @forelse($messages as $msg)
                        <div class="px-5 py-4 {{ $msg->user_id === auth()->id() ? 'bg-cyan-50/50' : '' }}">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-semibold text-sm text-gray-800">{{ $msg->user->name }}</span>
                                @if($msg->user->role === 'admin')
                                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Suporte</span>
                                @endif
                                <span class="text-xs text-gray-400">{{ $msg->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <p class="text-gray-700 text-sm whitespace-pre-line">{{ $msg->message }}</p>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-center text-gray-400 text-sm">Sem mensagens ainda.</div>
                    @endforelse
                </div>
            </div>

            {{-- Reply --}}
            @if(!in_array($dispute->status, ['resolvida', 'encerrada']))
                <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
                    <textarea wire:model.defer="newMessage" rows="3"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-cyan-300 focus:outline-none resize-none mb-3"
                        placeholder="Escreva uma mensagem..."></textarea>
                    @error('newMessage') <span class="text-red-500 text-sm block mb-2">{{ $message }}</span> @enderror
                    <button wire:click="sendMessage" wire:loading.attr="disabled"
                        class="btn-eq btn-primary">
                        <span wire:loading.remove wire:target="sendMessage">Enviar mensagem</span>
                        <span wire:loading wire:target="sendMessage">A enviar...</span>
                    </button>
                </div>
            @endif
        @endif
    </div>
</div>
