<div class="max-w-6xl mx-auto space-y-6">
    <div class="bg-gradient-to-r from-[#00baff] to-[#0095cc] rounded-2xl p-6 text-white">
        <h2 class="text-2xl font-extrabold">Projetos Disponiveis</h2>
        <p class="text-sm text-white/90 mt-1">Escolha os projectos que combinam com o seu perfil e agenda.</p>
    </div>

    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">
            {{ session('info') }}
        </div>
    @endif

    <div class="flex items-center justify-between">
        <div class="hidden md:flex items-center gap-2 text-xs text-slate-500 bg-white border border-gray-200 rounded-full px-4 py-2">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            <span>Pedidos em tempo real — actualize para ver novos projectos</span>
        </div>
    </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($projects as $project)
                    @php
                        // Tenta decodificar o briefing como JSON; se não for JSON válido, mantém como string
                        $decoded = json_decode($project->briefing, true);
                        $briefing = is_array($decoded) ? $decoded : null;

                        // Extrai campos conhecidos do briefing (novo formato)
                        $title       = $briefing['title']           ?? null;
                        $tipoNegocio = $briefing['business_type']   ?? null;
                        $necessidade = $briefing['necessity']       ?? null;
                        $publicoAlvo = $briefing['target_audience'] ?? null;
                        $usage       = $briefing['usage']           ?? null;

                        // Monta um resumo rápido humano a partir dos campos
                        $partesResumo = [];
                        if ($necessidade) {
                            $partesResumo[] = $necessidade;
                        }
                        if ($usage) {
                            $partesResumo[] = $usage;
                        }
                        if (!$partesResumo && $title) {
                            $partesResumo[] = $title;
                        }
                        $briefingTexto = $partesResumo ? implode(' · ', $partesResumo) : ($project->briefing ?? '');

                        $createdAt = $project->created_at?->format('d/m/Y');

                        $statusLabels = [
                            'published'    => 'Publicado',
                            'negotiating'  => 'Em Negociação',
                            'accepted'     => 'Aceite',
                            'in_progress'  => 'Em Andamento',
                            'delivered'    => 'Entregue',
                            'completed'    => 'Concluído',
                            'cancelled'    => 'Cancelado',
                            'em_moderacao' => 'Em Moderação',
                            'draft'        => 'Rascunho',
                        ];
                        $statusLabel = $statusLabels[$project->status] ?? $project->status;
                    @endphp

                    <div class="rounded-2xl shadow-lg p-6 flex flex-col justify-between border border-cyan-100 hover:border-cyan-300 hover:shadow-2xl transition bg-white text-gray-900">
                        <div>
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <div>
                                    <h3 class="text-lg font-extrabold text-cyan-700 leading-snug line-clamp-2">{{ $project->titulo }}</h3>
                                    @if($tipoNegocio || $necessidade)
                                        <div class="mt-1 flex flex-wrap gap-1 text-[11px] text-gray-500">
                                            @if($tipoNegocio)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-cyan-50 text-cyan-700 font-medium">{{ $tipoNegocio }}</span>
                                            @endif
                                            @if($necessidade)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-medium">{{ $necessidade }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <div class="text-[11px] uppercase tracking-wide text-gray-400">Valor líquido</div>
                                    <div class="text-lg font-bold text-emerald-600">Kz {{ number_format($project->valor_liquido, 0, ',', '.') }}</div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-[11px] text-gray-500 mb-3">
                                <div class="flex items-center gap-2">
                                    @if($createdAt)
                                        <span class="inline-flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                                            <span>Criado em {{ $createdAt }}</span>
                                        </span>
                                    @endif
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-cyan-50 text-cyan-700 font-semibold">
                                    {{ $statusLabel }}
                                </span>
                            </div>

                            <div class="text-sm text-gray-700 mb-2">
                                <span class="font-semibold text-gray-900 block mb-1">Resumo rápido</span>
                                <p class="line-clamp-3 leading-relaxed">{{ $briefingTexto }}</p>
                            </div>

                            {{-- Público-alvo removido --}}
                        </div>

                        <div class="mt-5 flex flex-col gap-2">
                            @if(in_array($project->id, $myCandidacies))
                                {{-- Já candidatado: mostrar badge + botão de chat --}}
                                <div class="flex items-center justify-center gap-1 py-2 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                    Proposta enviada
                                </div>
                                <a href="{{ route('service.chat', $project->id) }}"
                                    class="flex items-center justify-center gap-2 bg-white border border-cyan-400 text-cyan-600 hover:bg-cyan-50 font-semibold py-2 px-4 rounded-lg w-full text-center transition-all text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 0 1-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    Chat com o cliente
                                </a>
                            @else
                                <button type="button" wire:click="acceptService({{ $project->id }})" class="bg-cyan-500 hover:bg-cyan-600 text-white font-semibold py-2.5 px-4 rounded-lg w-full block text-center transition-all text-sm">Aceitar projecto</button>
                                <button type="button" wire:click="showProposalModal({{ $project->id }})" class="bg-white border border-cyan-400 text-cyan-600 hover:bg-cyan-50 font-semibold py-2 px-4 rounded-lg w-full text-center transition-all text-sm">Enviar proposta</button>
                            @endif
                        </div>

                        <div class="flex items-center gap-3 mt-3 pt-3 border-t border-gray-100">
                            <a href="{{ route('public.project.show', $project->id) }}"
                               class="flex-1 flex items-center justify-center gap-1.5 text-xs font-semibold text-cyan-600 bg-cyan-50 border border-cyan-200 hover:bg-cyan-100 py-2 px-3 rounded-lg transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Ver detalhes
                            </a>
                            @if($project->cliente)
                                <a href="{{ route('freelancer.show', $project->cliente) }}"
                                   class="flex-1 flex items-center justify-center gap-1.5 text-xs font-semibold text-gray-600 bg-gray-50 border border-gray-200 hover:bg-gray-100 py-2 px-3 rounded-lg transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                    Perfil do cliente
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-gray-500 text-center text-sm bg-white border border-dashed border-gray-300 rounded-xl py-10">
                        Nenhum projecto disponível de momento. Volte mais tarde ou mantenha as suas notificações activas.
                    </div>
                @endforelse
            </div>

            @if($projects->hasPages())
                <div class="mt-8">
                    {{ $projects->links() }}
                </div>
            @endif

    {{-- Proposal Modal --}}
    @if($proposalModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-2xl w-full max-w-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold mb-3">Enviar proposta</h3>
                <form wire:submit.prevent="sendProposal" class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Mensagem</label>
                        <textarea wire:model.defer="proposalMessage" class="mt-1 block w-full border border-gray-200 rounded-xl p-2" rows="5"></textarea>
                        @error('proposalMessage') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Valor (opcional)</label>
                        <input type="number" step="0.01" wire:model.defer="proposalValue" class="mt-1 block w-48 border border-gray-200 rounded-xl p-2" />
                        @error('proposalValue') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" wire:click="$set('proposalModal', false)" class="px-4 py-2 rounded-xl bg-gray-100">Cancelar</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-500 text-white">Enviar proposta</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
