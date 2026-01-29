
@extends('layouts.main')

@section('content')
    <div class="min-h-screen pt-24 pb-12" style="background: #101c2c; color: #fff;">
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-3xl font-extrabold text-cyan-400 mb-8 text-center tracking-tight drop-shadow">Projetos Disponíveis</h1>
            <form method="GET" class="mb-8 rounded-lg p-4 flex flex-wrap gap-4 items-end justify-center shadow">
                <div>
                    <label class="block text-xs font-bold text-cyan-700 mb-1" for="valor_min">Valor mínimo</label>
                    <input type="number" step="0.01" name="valor_min" id="valor_min" class="px-2 py-1 rounded border focus:ring-cyan-400" value="{{ request('valor_min') }}">
                </div>
                <div>
                    <label class="block text-xs font-bold text-cyan-700 mb-1" for="valor_max">Valor máximo</label>
                    <input type="number" step="0.01" name="valor_max" id="valor_max" class="px-2 py-1 rounded border focus:ring-cyan-400" value="{{ request('valor_max') }}">
                </div>
                <div>
                    <label class="block text-xs font-bold text-cyan-700 mb-1" for="data_inicio">Data inicial</label>
                    <input type="date" name="data_inicio" id="data_inicio" class="px-2 py-1 rounded border focus:ring-cyan-400" value="{{ request('data_inicio') }}">
                </div>
                <div>
                    <label class="block text-xs font-bold text-cyan-700 mb-1" for="data_fim">Data final</label>
                    <input type="date" name="data_fim" id="data_fim" class="px-2 py-1 rounded border focus:ring-cyan-400" value="{{ request('data_fim') }}">
                </div>
                <div>
                    <label class="block text-xs font-bold text-cyan-700 mb-1" for="status">Status</label>
                    <select name="status" id="status" class="px-2 py-1 rounded border focus:ring-cyan-400">
                        <option value="">Todos</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Publicado</option>
                        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Aceito</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Em andamento</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Entregue</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Concluído</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-cyan-700 mb-1" for="business_type">Tipo de negócio</label>
                    <input type="text" name="business_type" id="business_type" class="px-2 py-1 rounded border focus:ring-cyan-400" value="{{ request('business_type') }}">
                </div>
                <div>
                    <label class="block text-xs font-bold text-cyan-700 mb-1" for="target_audience">Público-alvo</label>
                    <input type="text" name="target_audience" id="target_audience" class="px-2 py-1 rounded border focus:ring-cyan-400" value="{{ request('target_audience') }}">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-cyan-400 text-[#101c2c] font-bold px-6 py-2 rounded hover:bg-cyan-300 transition">Filtrar</button>
                    <a href="{{ route('public.projects') }}" class="bg-gray-300 text-gray-800 font-bold px-6 py-2 rounded hover:bg-gray-400 transition">Limpar filtros</a>
                </div>
            </form>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($projects as $project)
                    <div class="rounded-xl shadow-lg p-6 flex flex-col justify-between border border-cyan-200 hover:shadow-2xl transition" style="background: transparent;">
                        <div>
                            <h2 class="text-xl font-bold text-cyan-400 mb-2">{{ $project->titulo }}</h2>
                            <div class="mb-2">
                                <span class="font-semibold text-cyan-400">Status:</span>
                                <span class="inline-block px-3 py-1 rounded-full text-white font-bold text-xs" style="background:#0e4c92;">
                                    @php
                                        $statusMap = [
                                            'published' => 'Publicado',
                                            'accepted' => 'Aceito',
                                            'in_progress' => 'Em andamento',
                                            'delivered' => 'Entregue',
                                            'completed' => 'Concluído',
                                            'cancelled' => 'Cancelado',
                                        ];
                                    @endphp
                                    {{ $statusMap[$project->status] ?? ucfirst($project->status) }}
                                </span>
                            </div>
                            <div class="mb-2 text-sm" style="color: #fff;">
                                @php $briefing = json_decode($project->briefing, true); @endphp
                                @if($briefing)
                                    <ul class="list-none ml-0">
                                        @foreach($briefing as $key => $value)
                                            @php
                                                $labels = [
                                                    'business_type' => 'Tipo de negócio',
                                                    'target_audience' => 'Público-alvo',
                                                    'style' => 'Estilo desejado',
                                                    'colors' => 'Cores preferidas',
                                                    'usage' => 'Onde será utilizado',
                                                ];
                                            @endphp
                                            <li class="mb-1">
                                                <span class="font-semibold">{{ $labels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                <span style="color: #fff;">{{ $value }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-gray-500">Sem briefing disponível.</span>
                                @endif
                            </div>
                            <div class="mb-2"><span class="font-semibold text-cyan-400">Valor:</span> <span class="text-white">R$ {{ number_format($project->valor, 2, ',', '.') }}</span></div>
                            <div class="mb-2"><span class="font-semibold text-cyan-400">Publicado em:</span> <span class="text-white">{{ $project->created_at->format('d/m/Y') }}</span></div>
                        </div>
                        <div class="mt-4 flex flex-col gap-2">
                            <a href="{{ route('freelancer.service.review', $project->id) }}" class="bg-[#0e4c92] text-white font-bold py-2 px-4 rounded w-full block text-center transition-all hover:bg-[#09386a] mb-2">Ver detalhes</a>
                            <button onclick="showLoginMsg{{ $project->id }}('aceitar')" class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-2 px-4 rounded w-full block text-center">Aceitar</button>
                            <button onclick="showLoginMsg{{ $project->id }}('recusar')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded w-full block text-center">Recusar</button>
                            <div id="login-msg-{{ $project->id }}" style="display:none;" class="mt-2 p-3 rounded bg-yellow-100 text-yellow-900 text-center font-semibold text-sm border border-yellow-300">
                                <span>Você precisa entrar no sistema como freelancer. Caso ainda não tenha uma conta:</span><br>
                                <a href="/register?freelancer=1" class="inline-block mt-2 bg-cyan-400 text-[#101c2c] rounded px-3 py-1 font-bold hover:bg-cyan-300 transition animate-pulse">Torne-se freelancer</a>
                            </div>
                            <script>
                                function showLoginMsg{{ $project->id }}(acao) {
                                    var el = document.getElementById('login-msg-{{ $project->id }}');
                                    el.style.display = 'block';
                                    setTimeout(function() { el.style.display = 'none'; }, 6000);
                                }
                            </script>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-gray-300 text-center">Nenhum projeto disponível no momento.</div>
                @endforelse
            </div>
            <div class="mt-10 flex justify-center">{{ $projects->links() }}</div>
        </div>
    </div>
    {{-- Rodapé já incluso pelo layout --}}
@endsection
