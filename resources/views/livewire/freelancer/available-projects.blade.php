<div class="max-w-6xl mx-auto space-y-6 available-projects-page">
    <div class="available-projects-hero rounded-2xl p-6 text-white">
        <h2 class="text-2xl font-extrabold">Projetos Disponíveis</h2>
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
        <div class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-700">
            {{ session('info') }}
        </div>
    @endif

    {{-- Pesquisa --}}
    <div class="relative">
        <svg class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
        </svg>
        <input type="search" wire:model.live.debounce.400ms="search"
               placeholder="Pesquisar por título, descrição ou categoria..."
               class="w-full bg-white border border-gray-200 shadow-sm rounded-2xl pl-11 pr-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#0055ff]/30 focus:border-[#0055ff]">
    </div>

    <div class="flex items-center justify-between">
        <div class="hidden md:flex items-center gap-2 text-xs text-slate-500 bg-white border border-gray-200 rounded-full px-4 py-2">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            <span>Actualização em tempo real — actualize para ver novos projectos</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($projects as $project)
            @php
                $decoded = json_decode($project->briefing, true);
                $briefing = is_array($decoded) ? $decoded : null;

                $title       = $briefing['title']           ?? null;
                $tipoNegocio = $briefing['business_type']   ?? null;
                $necessidade = $briefing['necessity']       ?? null;
                $publicoAlvo = $briefing['target_audience'] ?? null;
                $usage       = $briefing['usage']           ?? null;

                $partesResumo = [];
                if ($necessidade) $partesResumo[] = $necessidade;
                if ($usage)       $partesResumo[] = $usage;
                if (!$partesResumo && $title) $partesResumo[] = $title;
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

            <div wire:key="available-project-{{ $project->id }}" class="rounded-2xl shadow-lg p-6 flex flex-col justify-between border border-slate-100 hover:border-sky-300 hover:shadow-2xl transition bg-white text-gray-900">
                <div>
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-800 leading-snug line-clamp-2">{{ $project->titulo }}</h3>
                            @if($tipoNegocio || $necessidade)
                                <div class="mt-1 flex flex-wrap gap-1 text-[11px] text-gray-500">
                                    @if($tipoNegocio)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-sky-50 text-sky-700 font-medium">{{ $tipoNegocio }}</span>
                                    @endif
                                    @if($necessidade)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-medium">{{ $necessidade }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="text-right flex-shrink-0">
                            <div class="text-[11px] uppercase tracking-wide text-gray-400">Valor líquido</div>
                            <div class="text-lg font-bold text-emerald-600">Kz {{ number_format($project->valor_liquido, 0, ',', '.') }}</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-[11px] text-gray-500 mb-3">
                        <div class="flex items-center gap-2">
                            @if($createdAt)
                                <span class="inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                                    <span>Criado em {{ $createdAt }}</span>
                                </span>
                            @endif
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-sky-50 text-sky-700 font-semibold">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div class="text-sm text-gray-700 mb-2">
                        <span class="font-semibold text-gray-900 block mb-1">Resumo rápido</span>
                        <p class="line-clamp-3 leading-relaxed">{{ $briefingTexto }}</p>
                    </div>
                </div>

                <div class="mt-5 flex flex-col gap-2">
                    @php $propCount = $proposalCounts[$project->id] ?? 0; $vagas = 6 - $propCount; @endphp
                    <div class="flex items-center gap-2 mb-1">
                        <div class="flex gap-0.5">
                            @for($i = 0; $i < 6; $i++)
                                <div class="w-5 h-1.5 rounded-full {{ $i < $propCount ? 'bg-orange-400' : 'bg-gray-200' }}"></div>
                            @endfor
                        </div>
                        <span class="text-xs {{ $vagas <= 1 ? 'text-orange-500 font-bold' : 'text-gray-400' }}">
                            {{ $vagas }} vaga{{ $vagas !== 1 ? 's' : '' }} restante{{ $vagas !== 1 ? 's' : '' }}
                        </span>
                    </div>

                    @if(in_array($project->id, $myCandidacies))
                        <div class="flex items-center justify-center gap-1 py-2 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            Proposta enviada
                        </div>
                        <a href="{{ route('service.chat', $project->id) }}"
                            class="flex items-center justify-center gap-2 bg-white border border-sky-400 text-sky-600 hover:bg-sky-50 font-semibold py-2 px-4 rounded-lg w-full text-center transition-all text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 0 1-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            Chat com o cliente
                        </a>
                    @else
                        <button type="button" wire:click="acceptService({{ $project->id }})"
                            class="hover: hover: text-white font-semibold py-2.5 px-4 rounded-lg w-full block text-center transition-all text-sm shadow-sm">
                            Aceitar projecto
                        </button>
                        <button type="button" wire:click="showProposalModal({{ $project->id }})"
                            class="bg-white border border-sky-400 text-sky-600 hover:bg-sky-50 font-semibold py-2 px-4 rounded-lg w-full text-center transition-all text-sm">
                            Enviar proposta
                        </button>
                    @endif
                </div>

                <div class="flex items-center gap-3 mt-3 pt-3 border-t border-gray-100">
                    <a href="{{ route('public.project.show', $project->id) }}"
                       class="flex-1 flex items-center justify-center gap-1.5 text-xs font-semibold text-sky-600 bg-sky-50 border border-sky-200 hover:bg-sky-100 py-2 px-3 rounded-lg transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Ver detalhes
                    </a>
                    @if($project->cliente)
                        <a href="{{ route('client.public', $project->cliente) }}"
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

    {{-- Modal: Enviar Proposta --}}
    @if($proposalModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div class="bg-white rounded-2xl w-full max-w-2xl p-6 shadow-2xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-800">Enviar proposta</h3>
                    <button wire:click="$set('proposalModal', false)" class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-gray-100 text-gray-400 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                @php $pb = $this->proposalBreakdown; @endphp
                <form wire:submit.prevent="sendProposal" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Mensagem</label>
                        <textarea wire:model.defer="proposalMessage"
                            class="w-full border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition resize-none"
                            rows="5" placeholder="Descreva a sua abordagem para este projecto..."></textarea>
                        @error('proposalMessage') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div style="display:flex;align-items:center;justify-content:space-between;border-radius:.75rem;background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.3);padding:.75rem 1rem;">
                        <span style="font-size:.85rem;font-weight:500;color:#34d399;">Valor actual do projecto <span style="font-weight:400;color:#6ee7b7;">(já pago pelo cliente)</span></span>
                        <span style="font-size:1rem;font-weight:700;color:#34d399;">Kz {{ number_format($pb['atual'], 2, ',', '.') }}</span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Valor adicional a propor (opcional)</label>
                        <input type="number" step="0.01" wire:model.live.debounce.400ms="proposalValue"
                            class="w-48 border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition"
                            placeholder="0.00" />
                        <p class="text-xs text-gray-400 mt-1">Deixe em branco se aceita o valor actual sem alterações.</p>
                        @error('proposalValue') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>

                    @if($pb['extra'] > 0)
                    <div style="border-radius:.75rem;background:rgba(14,165,233,.1);border:1px solid rgba(14,165,233,.25);padding:.85rem 1rem;font-size:.85rem;">
                        <div style="display:flex;justify-content:space-between;color:#94a3b8;padding:.15rem 0;">
                            <span>Já pago pelo cliente</span>
                            <span style="font-weight:600;color:#e2e8f0;">Kz {{ number_format($pb['atual'], 2, ',', '.') }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;color:#94a3b8;padding:.15rem 0;">
                            <span>+ Acréscimo proposto</span>
                            <span style="font-weight:600;color:#e2e8f0;">Kz {{ number_format($pb['extra'], 2, ',', '.') }}</span>
                        </div>
                        <div style="border-top:1px solid rgba(14,165,233,.25);margin:.5rem 0;"></div>
                        <div style="display:flex;justify-content:space-between;color:#94a3b8;padding:.15rem 0;">
                            <span>= Novo valor total do projecto</span>
                            <span style="font-weight:600;color:#e2e8f0;">Kz {{ number_format($pb['novo'], 2, ',', '.') }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;color:#94a3b8;padding:.15rem 0;">
                            <span>&minus; Comissão da plataforma ({{ $pb['clientRatePercent'] == (int) $pb['clientRatePercent'] ? (int) $pb['clientRatePercent'] : number_format($pb['clientRatePercent'], 1, ',', '.') }}%)</span>
                            <span style="font-weight:600;color:#e2e8f0;">Kz {{ number_format($pb['taxa'], 2, ',', '.') }}</span>
                        </div>
                        <div style="border-top:1px solid rgba(14,165,233,.25);margin:.5rem 0;"></div>
                        <div style="display:flex;justify-content:space-between;color:#38bdf8;font-weight:700;font-size:.92rem;">
                            <span>Vais receber</span>
                            <span>Kz {{ number_format($pb['valor_liquido'], 2, ',', '.') }}</span>
                        </div>
                    </div>
                    @endif

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('proposalModal', false)"
                            class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium transition">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 rounded-xl hover: hover: text-white text-sm font-semibold transition shadow-sm">
                            Enviar proposta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
