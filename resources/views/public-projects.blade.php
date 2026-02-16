
@extends('layouts.main')

@section('content')
    <div class="min-h-screen pt-24 pb-12 section section--alt">
        <div class="container mx-auto px-4 py-8">
            <div class="projects-hero">
                <h2>Projetos Disponíveis</h2>
                <p>Encontre trabalhos publicados por clientes com briefing completo — filtre por valor, data e tipo.</p>
            </div>
            @php $advancedOpen = (request('business_type') || request('target_audience')) ? 'open' : ''; @endphp
            <form method="GET" class="mb-8 container--card filters-row" role="search" aria-label="Filtrar projetos">
                <div class="form-group">
                    <label class="filter-label" for="valor_min">Valor mínimo</label>
                    <input type="number" step="0.01" name="valor_min" id="valor_min" class="filter-input" placeholder="Kz 0,00" value="{{ request('valor_min') }}" aria-label="Valor mínimo">
                </div>
                <div class="form-group">
                    <label class="filter-label" for="valor_max">Valor máximo</label>
                    <input type="number" step="0.01" name="valor_max" id="valor_max" class="filter-input" placeholder="Kz 0,00" value="{{ request('valor_max') }}" aria-label="Valor máximo">
                </div>
                <div class="form-group">
                    <label class="filter-label" for="data_inicio">Data inicial</label>
                    <input type="date" name="data_inicio" id="data_inicio" class="filter-input filter-date" value="{{ request('data_inicio') }}" aria-label="Data inicial">
                </div>
                <div class="form-group">
                    <label class="filter-label" for="data_fim">Data final</label>
                    <input type="date" name="data_fim" id="data_fim" class="filter-input filter-date" value="{{ request('data_fim') }}" aria-label="Data final">
                </div>
                <div class="form-group">
                    <label class="filter-label" for="status">Status</label>
                    <select name="status" id="status" class="filter-select" aria-label="Status do projeto">
                        <option value="">Todos</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Publicado</option>
                        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Aceito</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Em andamento</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Entregue</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Concluído</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
                <div class="form-actions" style="display:flex; gap:0.75rem; align-items:center;">
                    <button type="submit" class="hero-btn">Filtrar</button>
                    <a href="{{ route('public.projects') }}" class="hero-btn-outline">Limpar filtros</a>
                    <button type="button" id="toggle-advanced-filters" class="hero-btn-outline" aria-expanded="{{ $advancedOpen ? 'true' : 'false' }}" aria-controls="advanced-filters">Mais filtros ▾</button>
                </div>

                <div id="advanced-filters" class="advanced-filters {{ $advancedOpen }}" aria-hidden="{{ $advancedOpen ? 'false' : 'true' }}">
                    <div class="form-group">
                        <label class="filter-label" for="business_type">Tipo de negócio</label>
                        <input type="text" name="business_type" id="business_type" class="filter-input" placeholder="Ex: e-commerce, SaaS" value="{{ request('business_type') }}" aria-label="Tipo de negócio">
                    </div>
                    <div class="form-group">
                        <label class="filter-label" for="target_audience">Público-alvo</label>
                        <input type="text" name="target_audience" id="target_audience" class="filter-input" placeholder="Ex: jovens, empresas" value="{{ request('target_audience') }}" aria-label="Público-alvo">
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
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($projects as $project)
                    @php $briefing = json_decode($project->briefing, true); $thumb = is_array($briefing) ? ($briefing['thumbnail'] ?? null) : null; $statusClass = 'status-' . ($project->status ?? 'published'); @endphp
                    <div class="glass-card project-card-hover" role="article" aria-labelledby="project-title-{{ $project->id }}">
                        <a href="{{ route('public.project.show', $project->id) }}" class="card-link" aria-label="Abrir projeto {{ $project->titulo }}" tabindex="0">Abrir projeto</a>

                        <div class="card-body">
                            @if($thumb && file_exists(public_path('img/' . $thumb)))
                                <a href="{{ route('public.project.show', $project->id) }}" class="block hover:opacity-95">
                                    <img src="{{ asset('img/' . $thumb) }}" alt="{{ $project->titulo }}" class="project-thumbnail">
                                </a>
                            @else
                                <div class="project-avatar">{{ strtoupper(substr($project->titulo, 0, 1)) }}</div>
                            @endif

                            <a href="{{ route('public.project.show', $project->id) }}" class="block" id="project-title-{{ $project->id }}">
                                <h2 class="project-title">{{ $project->titulo }}</h2>
                            </a>

                            <div class="mb-2">
                                <span class="font-semibold text-cyan-400">Status:</span>
                                <span class="status-badge {{ $statusClass }}">{{ $statusMap[$project->status] ?? ucfirst($project->status) }}</span>
                            </div>

                            <div class="project-excerpt">
                                @php $briefing = json_decode($project->briefing, true); @endphp
                                @if(is_array($briefing))
                                    <div>
                                        @if(isset($briefing['title']))<div><b>Título:</b> {{ $briefing['title'] }}</div>@endif
                                        @if(isset($briefing['business_type']))<div><b>Tipo de negócio:</b> {{ $briefing['business_type'] }}</div>@endif
                                        @if(isset($briefing['necessity']))<div><b>Necessidade:</b> {{ $briefing['necessity'] }}</div>@endif
                                        @if(isset($briefing['target_audience']))<div><b>Público-alvo:</b> {{ $briefing['target_audience'] }}</div>@endif
                                        @if(isset($briefing['style']))<div><b>Estilo:</b> {{ $briefing['style'] }}</div>@endif
                                        @if(isset($briefing['colors']))<div><b>Cores:</b> {{ $briefing['colors'] }}</div>@endif
                                        @if(isset($briefing['usage']))<div><b>Uso:</b> {{ $briefing['usage'] }}</div>@endif
                                    </div>
                                @elseif(is_string($project->briefing) && trim($project->briefing) !== '')
                                    <div>{{ $project->briefing }}</div>
                                @else
                                    <span class="text-gray-500">Sem descrição disponível.</span>
                                @endif
                            </div>

                            <div class="project-meta">
                                <div class="project-meta-left">
                                    <div class=""><span class="font-semibold text-cyan-400">Valor:</span> <span class="project-price-badge">Kz {{ number_format($project->valor, 2, ',', '.') }}</span></div>
                                </div>
                                <div class="text-sm"><span class="font-semibold text-cyan-400">Publicado em:</span> <span class="text-white">{{ $project->created_at->format('d/m/Y') }}</span></div>
                            </div>
                        </div>

                        <div class="card-actions mt-4 flex flex-col gap-2">
                            <a href="#" onclick="showLoginMsg{{ $project->id }}('detalhes'); return false;" class="hero-btn-outline mb-2" role="button" aria-controls="login-msg-{{ $project->id }}" aria-label="Ver detalhes do projeto">Ver detalhes</a>
                            <div style="display:flex; gap:.5rem; align-items:center;">
                                <div class="action-item" style="display:flex; align-items:center; gap:.5rem;">
                                    <a href="#" onclick="showLoginMsg{{ $project->id }}('detalhes'); return false;" class="action-btn" role="button" aria-controls="login-msg-{{ $project->id }}" aria-label="Ver detalhes do projeto {{ $project->id }}" title="Ver detalhes do projeto">
                                        @include('components.icon', ['name' => 'eye', 'class' => 'w-5 h-5'])
                                    </a>
                                    <span class="action-label">Detalhes</span>
                                </div>

                                <div style="flex:1"></div>

                                <button onclick="showLoginMsg{{ $project->id }}('aceitar')" class="btn-eq btn-primary small flex items-center gap-2" aria-label="Aceitar projeto">
                                    @include('components.icon', ['name' => 'dots', 'class' => 'w-4 h-4 text-cyan-700'])
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>Aceitar</span>
                                </button>

                                <div class="action-row">
                                    <button onclick="showLoginMsg{{ $project->id }}('enviar_proposta')" class="btn-eq btn-outline small" aria-label="Enviar proposta" title="Enviar proposta">
                                        @include('components.icon', ['name' => 'dots', 'class' => 'w-4 h-4 text-cyan-700'])
                                        <span>Enviar proposta</span>
                                    </button>
                                </div>
                            </div>
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
            <div class="mt-10">
                <div class="pagination-custom">{{ $projects->links() }}</div>
            </div>
        </div>
    </div>
    {{-- Rodapé já incluso pelo layout --}}
@endsection
