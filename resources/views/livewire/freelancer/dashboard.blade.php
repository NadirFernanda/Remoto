<div class="container mx-auto p-4">
    @livewire('freelancer.notifications-panel')

    {{-- Onboarding checklist --}}
    @livewire('freelancer.onboarding')

    {{-- Quick links --}}
    <div class="flex flex-wrap gap-3 mb-6">
        <a href="{{ route('freelancer.portfolio') }}" class="inline-flex items-center gap-1.5 text-sm font-medium px-4 py-2 rounded-full border border-gray-200 bg-white hover:border-[#00baff] hover:text-[#00baff] transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Portfólio
        </a>
        <a href="{{ route('freelancer.financial') }}" class="inline-flex items-center gap-1.5 text-sm font-medium px-4 py-2 rounded-full border border-gray-200 bg-white hover:border-[#00baff] hover:text-[#00baff] transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Financeiro
        </a>
        <a href="{{ route('freelancer.profile.edit') }}" class="inline-flex items-center gap-1.5 text-sm font-medium px-4 py-2 rounded-full border border-gray-200 bg-white hover:border-[#00baff] hover:text-[#00baff] transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Editar perfil
        </a>
    </div>

    <div class="kpi-grid mb-6">
        <div class="kpi-card">
            <div class="value text-[#00B6E6]">Kz {{ number_format($kpi_total_recebido ?? 0, 2, ',', '.') }}</div>
            <div class="label">Total Recebido</div>
        </div>
        <div class="kpi-card">
            <div class="value text-[#222]">{{ $kpi_projetos_concluidos ?? 0 }}</div>
            <div class="label">Projetos Concluídos</div>
        </div>
        <div class="kpi-card">
            <div class="value text-[#009E4F]">{{ $kpi_projetos_andamento ?? 0 }}</div>
            <div class="label">Em Andamento</div>
        </div>
        <div class="kpi-card">
            <div class="value text-[#FFB800]">Kz {{ number_format($saldo_pendente ?? 0, 2, ',', '.') }}</div>
            <div class="label">Saldo Pendente</div>
        </div>
    </div>

    @livewire('freelancer.wallet-history')

    <div class="mb-8">
        <h2 class="font-semibold text-xl mb-2 text-[#222]">Últimos Projetos</h2>
        <div class="overflow-x-auto">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th class="py-2 px-4">Título</th>
                        <th class="py-2 px-4">Status</th>
                        <th class="py-2 px-4">Valor</th>
                        <th class="py-2 px-4">Data</th>
                        <th class="py-2 px-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                        <tr class="border-b">
                            <td class="py-2 px-4">{{ $project->titulo ?? '-' }}</td>
                            <td class="py-2 px-4">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</td>
                            <td class="py-2 px-4">Kz {{ number_format($project->valor, 2, ',', '.') }}</td>
                            <td class="py-2 px-4">{{ $project->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="table-actions" role="group" aria-label="Ações do projeto">
                                    <div class="action-item" style="display:flex; align-items:center; gap:.5rem;">
                                        <a href="{{ route('service.chat', ['service' => $project->id]) }}" class="action-btn relative" title="Abrir chat" aria-label="Abrir chat do projeto {{ $project->id }}">
                                            @include('components.icon', ['name' => 'dots', 'class' => 'w-5 h-5'])
                                            @livewire('chat.chat-badge', ['serviceId' => $project->id], key('chat-badge-'.$project->id))
                                        </a>
                                        <span class="action-label">Chat</span>
                                    </div>
                                    <div class="action-item" style="display:flex; align-items:center; gap:.5rem;">
                                        <a href="{{ route('freelancer.service.delivery', ['service' => $project->id]) }}" class="action-btn" title="Entregar" aria-label="Entregar projeto {{ $project->id }}">
                                            @include('components.icon', ['name' => 'check', 'class' => 'w-5 h-5'])
                                        </a>
                                        <span class="action-label">Entregar</span>
                                    </div>
                                    <div class="action-item" style="display:flex; align-items:center; gap:.5rem;">
                                        <button wire:click="sendToModeration({{ $project->id }})" class="action-btn" title="Enviar para Moderação" aria-label="Enviar para Moderação projeto {{ $project->id }}">
                                            @include('components.icon', ['name' => 'close', 'class' => 'w-5 h-5'])
                                        </button>
                                        <span class="action-label">Enviar para Moderação</span>
                                    </div>
                                    @if($project->status === 'completed')
                                    <div class="action-item" style="display:flex; align-items:center; gap:.5rem;">
                                        <a href="{{ route('service.review.leave', ['service' => $project->id]) }}" class="action-btn" title="Avaliar" aria-label="Avaliar projeto {{ $project->id }}">
                                            @include('components.icon', ['name' => 'star', 'class' => 'w-5 h-5'])
                                        </a>
                                        <span class="action-label">Avaliar</span>
                                    </div>
                                    @endif
                                    @if(in_array($project->status, ['in_progress','delivered','completed']))
                                    <div class="action-item" style="display:flex; align-items:center; gap:.5rem;">
                                        <a href="{{ route('service.dispute', ['service' => $project->id]) }}" class="action-btn" title="Disputar" aria-label="Abrir disputa do projeto {{ $project->id }}">
                                            @include('components.icon', ['name' => 'flag', 'class' => 'w-5 h-5'])
                                        </a>
                                        <span class="action-label">Disputar</span>
                                    </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-4 text-[#888]">Nenhum projeto encontrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>