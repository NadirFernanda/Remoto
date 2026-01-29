<div>


    <div class="flex min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col py-8 px-4">
        <div class="mb-8">
            <div class="w-20 h-20 rounded-full bg-[#F5F7FA] mx-auto flex items-center justify-center text-3xl font-bold text-[#00B6E6]">{{ strtoupper(optional(auth()->user())->name[0] ?? 'F') }}</div>
            <div class="text-center mt-2 font-semibold text-[#222]">{{ optional(auth()->user())->name ?? 'Freelancer' }}</div>
            <div class="text-center text-xs text-[#888]">{{ optional(auth()->user())->email ?? '' }}</div>
        </div>
        <nav class="flex flex-col gap-2">
            <a href="{{ route('freelancer.dashboard') }}" class="py-2 px-4 rounded hover:bg-[#F5F7FA] text-[#222] font-medium">Dashboard</a>
            <a href="{{ route('freelancer.available-projects') }}" class="py-2 px-4 rounded hover:bg-cyan-100 text-cyan-700 font-bold">Projetos Disponíveis</a>
            <a href="#" class="py-2 px-4 rounded hover:bg-[#F5F7FA] text-[#222] font-medium">Histórico</a>
            <a href="{{ route('freelancer.settings') }}" class="py-2 px-4 rounded hover:bg-[#F5F7FA] text-[#222] font-medium">Configurações</a>
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="w-full py-2 px-4 rounded bg-red-100 text-red-600 font-bold hover:bg-red-200">Sair</button>
            </form>
        </nav>
    </aside>
    <!-- Main Content -->
    <main class="flex-1 p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-2">
            <h1 class="font-semibold text-2xl text-[#222]">Dashboard do Freelancer</h1>
        </div>
        <!-- KPIs -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-[#F5F7FA] p-5 rounded shadow text-center">
                <div class="text-[#00B6E6] text-lg font-bold">Kz {{ number_format($kpi_total_recebido ?? 0, 2, ',', '.') }}</div>
                <div class="text-[#888] text-sm mt-1">Total Recebido</div>
            </div>
            <div class="bg-[#F5F7FA] p-5 rounded shadow text-center">
                <div class="text-[#222] text-lg font-bold">{{ $kpi_projetos_concluidos ?? 0 }}</div>
                <div class="text-[#888] text-sm mt-1">Projetos Concluídos</div>
            </div>
            <div class="bg-[#F5F7FA] p-5 rounded shadow text-center">
                <div class="text-[#009E4F] text-lg font-bold">{{ $kpi_projetos_andamento ?? 0 }}</div>
                <div class="text-[#888] text-sm mt-1">Em Andamento</div>
            </div>
            <div class="bg-[#F5F7FA] p-5 rounded shadow text-center">
                <div class="text-[#FFB800] text-lg font-bold">Kz {{ number_format($saldo_pendente ?? 0, 2, ',', '.') }}</div>
                <div class="text-[#888] text-sm mt-1">Saldo Pendente</div>
            </div>
        </div>
        <!-- Últimos Projetos -->
        <div class="mb-8">
            <h2 class="font-semibold text-xl mb-2 text-[#222]">Últimos Projetos</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded shadow">
                    <thead>
                        <tr class="bg-[#F5F7FA] text-[#222]">
                            <th class="py-2 px-4">Título</th>
                            <th class="py-2 px-4">Status</th>
                            <th class="py-2 px-4">Valor</th>
                            <th class="py-2 px-4">Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                            <tr class="border-b">
                                <td class="py-2 px-4">{{ $project->titulo ?? '-' }}</td>
                                <td class="py-2 px-4">
                                    @php
                                        $statusLabels = [
                                            'published' => 'Publicado',
                                            'accepted' => 'Aceite',
                                            'in_progress' => 'Em andamento',
                                            'delivered' => 'Entregue',
                                            'completed' => 'Concluído',
                                            'cancelled' => 'Cancelado',
                                        ];
                                    @endphp
                                    <span class="font-bold" style="color: #222;">
                                        {{ $statusLabels[$project->status] ?? ucfirst(str_replace('_', ' ', $project->status)) }}
                                    </span>
                                </td>
                                <td class="py-2 px-4">Kz {{ number_format($project->valor, 2, ',', '.') }}</td>
                                <td class="py-2 px-4">{{ $project->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-4 text-[#888]">Nenhum projeto encontrado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>