
@extends('layouts.main')

@section('content')
    <div class="light-page min-h-screen pt-8 pb-12">
        <div class="container mx-auto px-4 py-8 max-w-6xl">
            <!-- Hero -->
            <div class="text-center mb-8">
                <h2 class="text-3xl font-extrabold text-gray-800 mb-2">Projetos Disponíveis</h2>
                <p class="text-gray-500">Encontre trabalhos publicados por clientes com briefing completo — filtre por valor, data e tipo.</p>
            </div>

            <!-- Filtros -->
            @php $advancedOpen = (request('business_type') || request('target_audience')) ? 'open' : ''; @endphp
            <form method="GET" class="mb-8 bg-white rounded-xl shadow-sm border border-gray-100 p-6" role="search" aria-label="Filtrar projetos">
                <div class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[160px]">
                        <label class="block text-xs font-semibold text-gray-500 mb-1" for="valor_min">Valor mínimo</label>
                        <input type="number" step="0.01" name="valor_min" id="valor_min" class="light-input" placeholder="Kz 0,00" value="{{ request('valor_min') }}">
                    </div>
                    <div class="flex-1 min-w-[160px]">
                        <label class="block text-xs font-semibold text-gray-500 mb-1" for="valor_max">Valor máximo</label>
                        <input type="number" step="0.01" name="valor_max" id="valor_max" class="light-input" placeholder="Kz 0,00" value="{{ request('valor_max') }}">
                    </div>
                    <div class="flex-1 min-w-[160px]">
                        <label class="block text-xs font-semibold text-gray-500 mb-1" for="data_inicio">Data inicial</label>
                        <input type="date" name="data_inicio" id="data_inicio" class="light-input" value="{{ request('data_inicio') }}">
                    </div>
                    <div class="flex-1 min-w-[160px]">
                        <label class="block text-xs font-semibold text-gray-500 mb-1" for="data_fim">Data final</label>
                        <input type="date" name="data_fim" id="data_fim" class="light-input" value="{{ request('data_fim') }}">
                    </div>
                    <div class="flex-1 min-w-[160px]">
                        <label class="block text-xs font-semibold text-gray-500 mb-1" for="status">Status</label>
                        <select name="status" id="status" class="light-input">
                            <option value="">Todos</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Publicado</option>
                            <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Aceito</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Em andamento</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Entregue</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Concluído</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-3 mt-4">
                    <button type="submit" class="bg-[#00baff] text-white font-bold px-5 py-2 rounded-lg hover:bg-[#009ad6] transition">Filtrar</button>
                    <a href="{{ route('public.projects') }}" class="border border-gray-300 text-gray-600 font-semibold px-5 py-2 rounded-lg hover:bg-gray-50 transition">Limpar</a>
                    <button type="button" id="toggle-advanced-filters" class="border border-gray-300 text-gray-600 font-semibold px-5 py-2 rounded-lg hover:bg-gray-50 transition" aria-expanded="{{ $advancedOpen ? 'true' : 'false' }}" aria-controls="advanced-filters">Mais filtros ▾</button>
                </div>
                <div id="advanced-filters" class="advanced-filters {{ $advancedOpen }}">
                    <div class="flex flex-wrap gap-4 mt-3">
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-xs font-semibold text-gray-500 mb-1" for="business_type">Tipo de negócio</label>
                            <input type="text" name="business_type" id="business_type" class="light-input" placeholder="Ex: e-commerce, SaaS" value="{{ request('business_type') }}">
                        </div>
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-xs font-semibold text-gray-500 mb-1" for="target_audience">Público-alvo</label>
                            <input type="text" name="target_audience" id="target_audience" class="light-input" placeholder="Ex: jovens, empresas" value="{{ request('target_audience') }}">
                        </div>
                    </div>
                </div>
                <script>
                    (function(){
                        var btn = document.getElementById('toggle-advanced-filters');
                        var adv = document.getElementById('advanced-filters');
                        if(!btn || !adv) return;
                        btn.addEventListener('click', function(){
                            var open = adv.classList.toggle('open');
                            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
                            adv.setAttribute('aria-hidden', open ? 'false' : 'true');
                            btn.innerText = open ? 'Mais filtros ▴' : 'Mais filtros ▾';
                        });
                    })();
                </script>
            </form>

            @php
                $statusMap = [
                    'published' => 'Publicado',
                    'accepted' => 'Aceito',
                    'in_progress' => 'Em andamento',
                    'delivered' => 'Entregue',
                    'completed' => 'Concluído',
                    'cancelled' => 'Cancelado',
                ];
                $statusColors = [
                    'published' => 'bg-blue-100 text-blue-700',
                    'accepted' => 'bg-green-100 text-green-700',
                    'in_progress' => 'bg-yellow-100 text-yellow-700',
                    'delivered' => 'bg-cyan-100 text-cyan-700',
                    'completed' => 'bg-gray-100 text-gray-600',
                    'cancelled' => 'bg-red-100 text-red-600',
                ];
            @endphp

            <!-- Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($projects as $project)
                    @php
                        $briefing = json_decode($project->briefing, true);
                        $thumb = is_array($briefing) ? ($briefing['thumbnail'] ?? null) : null;
                        $statusClass = $statusColors[$project->status] ?? 'bg-gray-100 text-gray-600';
                    @endphp
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition group relative">
                        @if($thumb && file_exists(public_path('img/' . $thumb)))
                            <a href="{{ route('public.project.show', $project->id) }}" class="block">
                                <img src="{{ asset('img/' . $thumb) }}" alt="{{ $project->titulo }}" class="w-full h-40 object-cover">
                            </a>
                        @else
                            <div class="w-full h-24 bg-gradient-to-r from-[#00baff]/10 to-cyan-100 flex items-center justify-center">
                                <span class="text-3xl font-extrabold text-[#00baff]/40">{{ strtoupper(substr($project->titulo, 0, 1)) }}</span>
                            </div>
                        @endif

                        <div class="p-5">
                            <a href="{{ route('public.project.show', $project->id) }}" class="block">
                                <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-[#00baff] transition">{{ $project->titulo }}</h3>
                            </a>

                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold {{ $statusClass }} mb-3">
                                {{ $statusMap[$project->status] ?? ucfirst($project->status) }}
                            </span>

                            <div class="text-sm text-gray-500 mb-3 line-clamp-3">
                                @if(is_array($briefing))
                                    @if(isset($briefing['business_type']))<span class="font-medium text-gray-600">{{ $briefing['business_type'] }}</span> · @endif
                                    @if(isset($briefing['necessity'])){{ $briefing['necessity'] }}@endif
                                    @if(isset($briefing['target_audience'])) · {{ $briefing['target_audience'] }}@endif
                                @elseif(is_string($project->briefing) && trim($project->briefing) !== '')
                                    {{ Str::limit($project->briefing, 120) }}
                                @else
                                    <span class="text-gray-400">Sem descrição disponível.</span>
                                @endif
                            </div>

                            <div class="flex items-center justify-between pt-3 border-t border-gray-50">
                                <span class="text-lg font-extrabold text-[#00baff]">Kz {{ number_format($project->valor, 2, ',', '.') }}</span>
                                <span class="text-xs text-gray-400">{{ $project->created_at->format('d/m/Y') }}</span>
                            </div>

                            <div class="mt-4 flex gap-2">
                                <a href="{{ route('public.project.show', $project->id) }}" class="flex-1 text-center bg-[#00baff] text-white font-bold py-2 rounded-lg hover:bg-[#009ad6] transition text-sm">Ver detalhes</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-16">
                        <div class="text-gray-400 text-lg font-semibold">Nenhum projeto disponível no momento.</div>
                        <p class="text-gray-300 mt-1">Volte em breve para ver novos projetos.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-10">
                {{ $projects->links() }}
            </div>
        </div>
    </div>
@endsection
