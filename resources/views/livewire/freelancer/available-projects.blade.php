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
                <a href="{{ route('freelancer.available-projects') }}" class="py-2 px-4 rounded bg-cyan-100 text-cyan-700 font-bold">Projetos Disponíveis</a>
                <a href="#" class="py-2 px-4 rounded hover:bg-[#F5F7FA] text-[#222] font-medium">Histórico</a>
                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full py-2 px-4 rounded bg-red-100 text-red-600 font-bold hover:bg-red-200">Sair</button>
                </form>
            </nav>
        </aside>
        <!-- Main Content -->
        <main class="flex-1 p-8">
            <h2 class="text-2xl font-bold text-cyan-600 mb-6">Projetos Disponíveis</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($projects as $project)
                    <div class="rounded-xl shadow-lg p-6 flex flex-col justify-between border border-cyan-200 hover:shadow-2xl transition bg-white text-gray-900">
                        <div>
                            <h3 class="text-xl font-bold text-cyan-700 mb-2">{{ $project->titulo }}</h3>
                            <div class="mb-2">
                                <span class="font-semibold">Descrição:</span>
                                @php $briefing = json_decode($project->briefing, true); @endphp
                                <span>{{ is_array($briefing) && isset($briefing['texto']) ? $briefing['texto'] : $project->briefing }}</span>
                            </div>
                            <div class="mb-2"><span class="font-semibold">Valor:</span> Kz {{ number_format($project->valor, 2, ',', '.') }}</div>
                            <div class="mb-2"><span class="font-semibold">Status:</span>
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
                                <span class="font-bold text-xs text-cyan-700">{{ $statusLabels[$project->status] ?? ucfirst(str_replace('_', ' ', $project->status)) }}</span>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-col gap-2">
                            <a href="{{ route('freelancer.service.review', $project->id) }}" class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-2 px-4 rounded w-full block text-center transition-all mb-2">Ver detalhes</a>
                            <div class="flex gap-2">
                                <form wire:submit.prevent="acceptService({{ $project->id }})" class="flex-1">
                                    <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">Aceitar</button>
                                </form>
                                <form wire:submit.prevent="refuseService({{ $project->id }})" class="flex-1">
                                    <button type="submit" class="w-full bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Recusar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-gray-500 text-center">Nenhum projeto disponível no momento.</div>
                @endforelse
            </div>
        </main>
    </div>
</div>
