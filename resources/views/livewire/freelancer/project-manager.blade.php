<div class="space-y-6">

    @php
        $statusLabels = [
            'negotiating'  => 'Em Negociação',
            'published'    => 'Publicado',
            'accepted'     => 'Aceite',
            'in_progress'  => 'Em Andamento',
            'delivered'    => 'Entregue',
            'completed'    => 'Concluído',
            'cancelled'    => 'Cancelado',
            'em_moderacao' => 'Em Moderação',
        ];
        $statusColors = [
            'negotiating'  => 'bg-amber-100 text-amber-700',
            'published'    => 'bg-blue-100 text-blue-700',
            'accepted'     => 'bg-indigo-100 text-indigo-700',
            'in_progress'  => 'bg-yellow-100 text-yellow-700',
            'delivered'    => 'bg-orange-100 text-orange-700',
            'completed'    => 'bg-green-100 text-green-700',
            'cancelled'    => 'bg-red-100 text-red-600',
            'em_moderacao' => 'bg-purple-100 text-purple-700',
        ];
    @endphp

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Os Meus Projectos</h1>
            <p class="text-sm text-gray-500">Acompanhe o estado e actue sobre os seus projectos activos.</p>
        </div>
    </div>

    {{-- Contadores de status --}}
    <div class="flex flex-wrap gap-2">
        @foreach(['accepted' => 'Aceite', 'negotiating' => 'Em Negociação', 'in_progress' => 'Em Andamento', 'delivered' => 'Entregue', 'completed' => 'Concluído'] as $key => $label)
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-white border border-gray-200 text-gray-600">
                <span class="w-2 h-2 rounded-full
                    {{ $key === 'accepted' ? 'bg-indigo-400' : ($key === 'negotiating' ? 'bg-amber-400' : ($key === 'in_progress' ? 'bg-yellow-400' : ($key === 'delivered' ? 'bg-orange-400' : 'bg-green-400'))) }}">
                </span>
                {{ $label }}: <span class="font-bold text-gray-800">{{ $statusCounts[$key] ?? 0 }}</span>
            </span>
        @endforeach
    </div>

    {{-- Filtros --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1 max-w-xs">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
            <input type="text" wire:model.debounce.400ms="search" placeholder="Pesquisar por título..."
                class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#00baff]/40 bg-white">
        </div>
        <select wire:model="status"
            class="px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#00baff]/40 bg-white text-gray-700">
            <option value="">Todos os estados</option>
            <option value="negotiating">Em Negociação</option>
            <option value="accepted">Aceite</option>
            <option value="in_progress">Em Andamento</option>
            <option value="delivered">Entregue</option>
            <option value="completed">Concluído</option>
            <option value="cancelled">Cancelado</option>
        </select>
    </div>

    {{-- Tabela moderna --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <table class="min-w-full">
            <thead>
                <tr class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-gray-200">
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Título</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Valor a Receber</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Data</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Acções</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($projects as $project)
                    <tr class="hover:bg-slate-50/60 transition-colors group">
                        <td class="px-5 py-4">
                            <span class="font-medium text-gray-900 text-sm group-hover:text-[#00baff] transition-colors">
                                {{ $project->titulo ?? '—' }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusColors[$project->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $statusLabels[$project->status] ?? $project->status }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-sm font-semibold text-gray-800">
                            Kz {{ number_format($project->valor_liquido ?? ($project->valor * 0.8), 2, ',', '.') }}
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-500">
                            {{ $project->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-wrap gap-1.5">
                                <a href="{{ route('service.chat', ['service' => $project->id]) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-[#e0f7fa] text-[#00baff] hover:bg-[#00baff] hover:text-white text-xs font-semibold transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 0 1-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    Chat
                                </a>
                                @if(in_array($project->status, ['accepted', 'in_progress', 'delivered']))
                                    <a href="{{ route('freelancer.service.delivery', ['service' => $project->id]) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg {{ $project->status === 'delivered' ? 'bg-orange-50 text-orange-700 hover:bg-orange-600' : 'bg-green-50 text-green-700 hover:bg-green-600' }} hover:text-white text-xs font-semibold transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                                        {{ $project->status === 'delivered' ? 'Re-entregar' : 'Entregar' }}
                                    </a>
                                @endif
                                @if(in_array($project->status, ['accepted', 'in_progress', 'delivered']))
                                    <a href="{{ route('service.dispute', ['service' => $project->id]) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white text-xs font-semibold transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                                        Disputa
                                    </a>
                                @endif
                                @if($project->status === 'em_moderacao')
                                    <a href="{{ route('service.dispute', ['service' => $project->id]) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-600 hover:text-white text-xs font-semibold transition-colors">
                                        Moderação
                                    </a>
                                @endif
                                @if($project->status === 'completed')
                                    @if(in_array($project->id, $reviewedIds))
                                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-green-50 text-green-600 text-xs font-semibold">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                            Avaliado
                                        </span>
                                    @else
                                        <a href="{{ route('service.review.leave', ['service' => $project->id]) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-yellow-50 text-yellow-700 hover:bg-yellow-500 hover:text-white text-xs font-semibold transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/></svg>
                                            Avaliar
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-14 text-center">
                            <div class="flex flex-col items-center gap-2 text-gray-400">
                                <svg class="w-10 h-10 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                <p class="font-medium text-sm">Nenhum projecto encontrado.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($projects->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $projects->links() }}
            </div>
        @endif
    </div>

</div>
