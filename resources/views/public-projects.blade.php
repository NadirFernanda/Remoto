
@extends('layouts.main')

@section('content')
<div class="pub-page">
    <div class="pub-container" style="padding-top:2.5rem;padding-bottom:3rem;">

        {{-- Hero --}}
        <div class="pub-hero" style="margin-bottom:2rem;">
            <div class="pub-hero-label">Marketplace</div>
            <h1 class="pub-hero-title">Projectos Disponíveis</h1>
            <p class="pub-hero-sub">Encontre trabalhos publicados por clientes com briefing completo — filtre por valor, data e tipo.</p>
        </div>

        {{-- Filtros --}}
        @php $advancedOpen = (request('business_type') || request('target_audience')) ? 'open' : ''; @endphp
        <form method="GET" class="pub-filters" role="search" aria-label="Filtrar projectos" style="margin-bottom:2rem;">
            <div class="pub-filter-grid">
                <div class="pub-filter-group">
                    <label class="pub-filter-label" for="valor_min">Valor mínimo</label>
                    <input type="number" step="0.01" name="valor_min" id="valor_min" class="pub-input" placeholder="Kz 0,00" value="{{ request('valor_min') }}">
                </div>
                <div class="pub-filter-group">
                    <label class="pub-filter-label" for="valor_max">Valor máximo</label>
                    <input type="number" step="0.01" name="valor_max" id="valor_max" class="pub-input" placeholder="Kz 0,00" value="{{ request('valor_max') }}">
                </div>
                <div class="pub-filter-group">
                    <label class="pub-filter-label" for="data_inicio">Data inicial</label>
                    <input type="date" name="data_inicio" id="data_inicio" class="pub-input" value="{{ request('data_inicio') }}">
                </div>
                <div class="pub-filter-group">
                    <label class="pub-filter-label" for="data_fim">Data final</label>
                    <input type="date" name="data_fim" id="data_fim" class="pub-input" value="{{ request('data_fim') }}">
                </div>
                <div class="pub-filter-group">
                    <label class="pub-filter-label" for="status">Status</label>
                    <select name="status" id="status" class="pub-select">
                        <option value="">Todos</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Publicado</option>
                        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Aceite</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Em andamento</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Entregue</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Concluído</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
            </div>
            <div class="pub-filter-actions">
                <button type="submit" class="pub-btn-primary">Filtrar</button>
                <a href="{{ route('public.projects') }}" class="pub-btn-secondary">Limpar</a>
                <button type="button" id="toggle-advanced-filters" class="pub-btn-secondary" aria-expanded="{{ $advancedOpen ? 'true' : 'false' }}" aria-controls="advanced-filters">Mais filtros ▾</button>
            </div>
            <div id="advanced-filters" class="advanced-filters {{ $advancedOpen }}">
                <div class="pub-filter-grid" style="margin-top:.75rem;">
                    <div class="pub-filter-group">
                        <label class="pub-filter-label" for="business_type">Tipo de negócio</label>
                        <input type="text" name="business_type" id="business_type" class="pub-input" placeholder="Ex: e-commerce, SaaS" value="{{ request('business_type') }}">
                    </div>
                    <div class="pub-filter-group">
                        <label class="pub-filter-label" for="target_audience">Público-alvo</label>
                        <input type="text" name="target_audience" id="target_audience" class="pub-input" placeholder="Ex: jovens, empresas" value="{{ request('target_audience') }}">
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
                'published'   => 'Publicado',
                'accepted'    => 'Aceite',
                'in_progress' => 'Em andamento',
                'delivered'   => 'Entregue',
                'completed'   => 'Concluído',
                'cancelled'   => 'Cancelado',
            ];
        @endphp

        {{-- Cards --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1.5rem;">
            @forelse($projects as $project)
                @php
                    $briefing = json_decode($project->briefing, true);
                    $thumb    = is_array($briefing) ? ($briefing['thumbnail'] ?? null) : null;
                @endphp
                <div class="pub-card" style="display:flex;flex-direction:column;overflow:hidden;padding:0;">
                    @if($thumb && file_exists(public_path('img/' . $thumb)))
                        <a href="{{ route('public.project.show', $project->id) }}" style="display:block;">
                            <img src="{{ asset('img/' . $thumb) }}" alt="{{ $project->titulo }}" style="width:100%;height:160px;object-fit:cover;">
                        </a>
                    @else
                        <div style="width:100%;height:80px;background:linear-gradient(135deg,rgba(0,186,255,.12),rgba(0,186,255,.05));display:flex;align-items:center;justify-content:center;">
                            <span style="font-size:2rem;font-weight:900;color:rgba(0,186,255,.3);">{{ strtoupper(substr($project->titulo, 0, 1)) }}</span>
                        </div>
                    @endif

                    <div style="padding:1.25rem;display:flex;flex-direction:column;flex:1;">
                        <a href="{{ route('public.project.show', $project->id) }}" style="text-decoration:none;">
                            <h3 style="font-size:1rem;font-weight:800;color:#0f172a;margin:0 0 .6rem;line-height:1.3;transition:color .2s;" onmouseover="this.style.color='#00baff'" onmouseout="this.style.color='#0f172a'">{{ $project->titulo }}</h3>
                        </a>

                        <span class="pub-status-{{ $project->status }}" style="display:inline-block;width:fit-content;margin-bottom:.75rem;">
                            {{ $statusMap[$project->status] ?? ucfirst($project->status) }}
                        </span>

                        <p style="font-size:.875rem;color:#64748b;margin:0 0 .75rem;line-height:1.5;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">
                            @if(is_array($briefing))
                                @if(isset($briefing['business_type']))<strong style="color:#475569;">{{ $briefing['business_type'] }}</strong> · @endif
                                @if(isset($briefing['necessity'])){{ $briefing['necessity'] }}@endif
                                @if(isset($briefing['target_audience'])) · {{ $briefing['target_audience'] }}@endif
                            @elseif(is_string($project->briefing) && trim($project->briefing) !== '')
                                {{ Str::limit($project->briefing, 120) }}
                            @else
                                <em style="color:#94a3b8;">Sem descrição disponível.</em>
                            @endif
                        </p>

                        <div style="margin-top:auto;">
                            <div style="display:flex;align-items:center;justify-content:space-between;padding-top:.75rem;border-top:1px solid #f1f5f9;margin-bottom:.75rem;">
                                <span class="pub-price">Kz {{ number_format($project->valor, 2, ',', '.') }}</span>
                                <span style="font-size:.75rem;color:#94a3b8;">{{ $project->created_at->format('d/m/Y') }}</span>
                            </div>
                            <a href="{{ route('public.project.show', $project->id) }}" class="pub-btn-primary" style="width:100%;text-align:center;">Ver detalhes</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="pub-empty" style="grid-column:1/-1;">
                    <svg width="42" height="42" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto .75rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p style="font-size:1rem;font-weight:700;color:#64748b;margin:.25rem 0 0;">Nenhum projecto disponível no momento.</p>
                    <p style="font-size:.875rem;color:#94a3b8;margin:.25rem 0 0;">Volte em breve para ver novos projectos.</p>
                </div>
            @endforelse
        </div>

        <div style="margin-top:2.5rem;">
            {{ $projects->links() }}
        </div>
    </div>
</div>
@endsection
