<div class="container mx-auto p-4">
    @livewire('freelancer.notifications-panel')

    {{-- Subtitle and actions are provided by the layout via layout data --}}

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