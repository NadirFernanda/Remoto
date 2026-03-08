<div class="pub-page">
    <div class="pub-container" style="padding-top:2rem;padding-bottom:3rem;">

        <div class="pub-hero" style="margin-bottom:1.75rem;">
            <div class="pub-hero-label">Profissionais</div>
            <h1 class="pub-hero-title">Encontrar Freelancers</h1>
            <p class="pub-hero-sub">Pesquise por habilidade, preço, avaliação e disponibilidade</p>
        </div>

        <div style="display:flex;flex-direction:column;gap:1.5rem;">
            @media (min-width:1024px) {
                .fs-layout { flex-direction: row !important; }
            }
        </div>

        <div class="fs-layout" style="display:flex;flex-wrap:wrap;gap:1.5rem;align-items:flex-start;">

            {{-- ── Sidebar de Filtros ── --}}
            <aside style="width:240px;flex-shrink:0;display:flex;flex-direction:column;gap:.75rem;">

                <div class="pub-sidebar-block">
                    <label class="pub-filter-label">Pesquisa</label>
                    <input type="text" wire:model.debounce.400ms="query"
                        placeholder="Nome, headline…"
                        class="pub-input">
                </div>

                <div class="pub-sidebar-block">
                    <label class="pub-filter-label">Habilidade</label>
                    <select wire:model="skill" class="pub-select">
                        <option value="">Todas</option>
                        @foreach($allSkills as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="pub-sidebar-block">
                    <label class="pub-filter-label">Idioma</label>
                    <select wire:model="language" class="pub-select">
                        <option value="">Todos</option>
                        @foreach($allLanguages as $lang)
                            <option value="{{ $lang }}">{{ $lang }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="pub-sidebar-block">
                    <label class="pub-filter-label">Disponibilidade</label>
                    <select wire:model="availability" class="pub-select">
                        <option value="">Qualquer</option>
                        <option value="disponivel">Disponível</option>
                        <option value="ocupado">Ocupado</option>
                        <option value="ferias">Férias</option>
                    </select>
                </div>

                <div class="pub-sidebar-block">
                    <label class="pub-filter-label">Avaliação mínima</label>
                    <select wire:model="minRating" class="pub-select">
                        <option value="0">Qualquer</option>
                        <option value="3">3★ ou mais</option>
                        <option value="4">4★ ou mais</option>
                        <option value="4.5">4.5★ ou mais</option>
                        <option value="5">Apenas 5★</option>
                    </select>
                </div>

                <div class="pub-sidebar-block">
                    <label class="pub-filter-label">Taxa/hora (Kz)</label>
                    <div style="display:flex;gap:.5rem;align-items:center;">
                        <input type="number" wire:model.debounce.500ms="minRate" min="0"
                            placeholder="Min" class="pub-input" style="padding:.5rem .65rem;font-size:.8rem;">
                        <span style="color:#94a3b8;font-size:.75rem;">–</span>
                        <input type="number" wire:model.debounce.500ms="maxRate" min="0"
                            placeholder="Máx" class="pub-input" style="padding:.5rem .65rem;font-size:.8rem;">
                    </div>
                </div>

                <div class="pub-sidebar-block">
                    <label class="pub-filter-label">Ordenar por</label>
                    <select wire:model="sort" class="pub-select">
                        <option value="relevancia">Relevância</option>
                        <option value="popularidade">Melhor avaliados</option>
                        <option value="preco_asc">Menor preço</option>
                        <option value="preco_desc">Maior preço</option>
                    </select>
                </div>
            </aside>

            {{-- ── Resultados ── --}}
            <div style="flex:1;min-width:0;">
                <div wire:loading style="font-size:.8rem;color:#94a3b8;margin-bottom:.75rem;">A filtrar...</div>

                <div class="pub-results-grid-3">
                    @forelse($freelancers as $freelancer)
                        @php
                            $fp  = $freelancer->freelancerProfile;
                            $avg = $freelancer->averageRating();
                            $cnt = $freelancer->reviewsReceived->count();
                        @endphp
                        <a href="{{ route('freelancer.show', $freelancer) }}" class="pub-freelancer-card" style="text-decoration:none;display:block;">
                            <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem;">
                                <img src="{{ $freelancer->avatarUrl() }}"
                                    style="width:48px;height:48px;border-radius:50%;object-fit:cover;border:2px solid #e8edf3;"
                                    alt="{{ $freelancer->name }}">
                                <div style="min-width:0;">
                                    <div style="font-weight:800;color:#0f172a;font-size:.9rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $freelancer->name }}</div>
                                    <div style="font-size:.75rem;color:#94a3b8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $fp->headline ?? 'Freelancer' }}</div>
                                </div>
                            </div>

                            {{-- Rating --}}
                            <div style="display:flex;align-items:center;gap:.35rem;margin-bottom:.65rem;">
                                <span style="color:#facc15;font-size:.85rem;letter-spacing:-.05em;">
                                    @for($i=1;$i<=5;$i++){{ $i <= round($avg) ? '★' : '☆' }}@endfor
                                </span>
                                <span style="font-size:.75rem;color:#94a3b8;">{{ $avg > 0 ? number_format($avg,1) : '—' }} ({{ $cnt }})</span>
                            </div>

                            {{-- Skills --}}
                            @if(!empty($fp->skills))
                                <div style="display:flex;flex-wrap:wrap;gap:.3rem;margin-bottom:.65rem;">
                                    @foreach(array_slice($fp->skills, 0, 4) as $tag)
                                        <span class="pub-skill">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            @endif

                            <div style="display:flex;align-items:center;justify-content:space-between;font-size:.78rem;margin-top:.5rem;border-top:1px solid #f1f5f9;padding-top:.5rem;">
                                @if($fp && $fp->hourly_rate)
                                    <span style="font-weight:800;color:#00baff;">{{ number_format($fp->hourly_rate, 0, ',', '.') }} Kz/h</span>
                                @else
                                    <span style="color:#94a3b8;">—</span>
                                @endif
                                @if($fp)
                                    @php
                                        $avail = $fp->availability_status ?? 'disponivel';
                                        $availColor = ['disponivel' => '#16a34a', 'ocupado' => '#ca8a04', 'ferias' => '#94a3b8'][$avail] ?? '#94a3b8';
                                        $availLabel = ['disponivel' => '● Disponível', 'ocupado' => '● Ocupado', 'ferias' => '● Férias'][$avail] ?? $avail;
                                    @endphp
                                    <span style="color:{{ $availColor }};font-weight:700;">{{ $availLabel }}</span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="pub-empty" style="grid-column:1/-1;">
                            <svg width="38" height="38" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto .5rem;"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35"/></svg>
                            <p style="font-weight:700;color:#64748b;font-size:.95rem;margin:.25rem 0 0;">Nenhum freelancer encontrado.</p>
                            <p style="font-size:.8rem;color:#94a3b8;margin:.2rem 0 0;">Experimente outros filtros.</p>
                        </div>
                    @endforelse
                </div>

                <div style="margin-top:1.75rem;">
                    {{ $freelancers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
