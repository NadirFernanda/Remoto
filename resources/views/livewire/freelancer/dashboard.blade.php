<div x-data="{}">
    {{-- Filtros de período --}}
    <div class="flex items-center gap-3 mb-6">
        <span class="text-sm font-medium text-gray-600">Período:</span>
        @foreach([7 => '7 dias', 30 => '30 dias', 90 => '90 dias'] as $days => $label)
            <button
                wire:click="$set('period', {{ $days }})"
                class="px-3 py-1.5 rounded-[10px] text-xs font-medium border transition
                    {{ $period === $days
                        ? 'bg-[#00baff] text-white border-[#00baff]'
                        : 'bg-white text-gray-600 border-gray-200 hover:border-[#00baff] hover:text-[#00baff]' }}"
            >{{ $label }}</button>
        @endforeach
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 mb-1">Total Recebido</p>
            <p class="text-2xl font-bold text-[#00baff]">Kz {{ number_format($kpi_total_recebido ?? 0, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Recebido em pagamentos</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 mb-1">Projetos Concluídos</p>
            <p class="text-2xl font-bold text-green-600">{{ $kpi_projetos_concluidos ?? 0 }}</p>
            <p class="text-xs text-gray-400 mt-1">Finalizados com sucesso</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 mb-1">Em Andamento</p>
            <p class="text-2xl font-bold text-yellow-500">{{ $kpi_projetos_andamento ?? 0 }}</p>
            <p class="text-xs text-gray-400 mt-1">Projetos ativos</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 mb-1">Saldo Pendente</p>
            <p class="text-2xl font-bold text-orange-500">Kz {{ number_format($saldo_pendente ?? 0, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">A receber</p>
        </div>
    </div>

    {{-- Atalhos rápidos --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <a href="{{ route('freelancer.portfolio') }}" class="bg-white rounded-2xl border border-gray-200 p-4 text-center hover:border-[#00baff]/50 transition group">
            <svg class="w-6 h-6 mx-auto mb-2 text-gray-400 group-hover:text-[#00baff] transition" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span class="text-xs font-medium text-gray-700">Portfólio</span>
        </a>
        <a href="{{ route('freelancer.financial') }}" class="bg-white rounded-2xl border border-gray-200 p-4 text-center hover:border-[#00baff]/50 transition group">
            <svg class="w-6 h-6 mx-auto mb-2 text-gray-400 group-hover:text-[#00baff] transition" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span class="text-xs font-medium text-gray-700">Financeiro</span>
        </a>
        <a href="{{ route('freelancer.profile.edit') }}" class="bg-white rounded-2xl border border-gray-200 p-4 text-center hover:border-[#00baff]/50 transition group">
            <svg class="w-6 h-6 mx-auto mb-2 text-gray-400 group-hover:text-[#00baff] transition" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            <span class="text-xs font-medium text-gray-700">Editar perfil</span>
        </a>
        <a href="{{ route('freelancer.wallet') }}" class="bg-white rounded-2xl border border-gray-200 p-4 text-center hover:border-[#00baff]/50 transition group">
            <svg class="w-6 h-6 mx-auto mb-2 text-gray-400 group-hover:text-[#00baff] transition" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
            <span class="text-xs font-medium text-gray-700">Extrato</span>
        </a>
    </div>

    {{-- Histórico de carteira --}}
    @livewire('freelancer.wallet-history')

    {{-- Últimos Projetos --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-8">
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