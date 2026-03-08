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

                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1.25rem;">
                    @forelse($freelancers as $freelancer)
                        @php
                            $fp  = $freelancer->freelancerProfile;
                            $avg = $freelancer->averageRating();
                            $cnt = $freelancer->reviewsReceived->count();
                            $avail = $fp->availability_status ?? 'disponivel';
                            $availColor = match($avail) {
                                'available','disponivel' => '#22c55e',
                                'busy','ocupado'         => '#eab308',
                                default                  => '#94a3b8',
                            };
                            $availLabel = match($avail) {
                                'available','disponivel' => 'Disponível',
                                'busy','ocupado'         => 'Ocupado',
                                default                  => 'Indisponível',
                            };
                        @endphp
                        <div style="background:#fff;border-radius:16px;border:1px solid #e8edf3;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.06);display:flex;flex-direction:column;transition:box-shadow .2s,transform .2s;" onmouseover="this.style.boxShadow='0 8px 28px rgba(0,186,255,.13)';this.style.transform='translateY(-3px)'" onmouseout="this.style.boxShadow='0 2px 12px rgba(0,0,0,.06)';this.style.transform='translateY(0)'">

                            {{-- Gradient header com avatar --}}
                            <div style="height:80px;background:linear-gradient(135deg,#0f172a 0%,#1a3a5c 55%,#0099d6 100%);position:relative;flex-shrink:0;">
                                <div style="position:absolute;bottom:-28px;left:16px;">
                                    <div style="position:relative;display:inline-block;">
                                        <img src="{{ $freelancer->avatarUrl() }}" alt="{{ $freelancer->name }}"
                                             width="60" height="60" loading="lazy"
                                             style="width:60px;height:60px;border-radius:50%;object-fit:cover;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.18);">
                                        <span style="position:absolute;bottom:2px;right:2px;width:14px;height:14px;border-radius:50%;border:2px solid #fff;background:{{ $availColor }};display:block;"></span>
                                    </div>
                                </div>
                                @if($freelancer->kyc_status === 'verified')
                                <div style="position:absolute;top:10px;right:12px;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.4);border-radius:30px;padding:3px 10px;font-size:.7rem;color:#fff;font-weight:700;display:flex;align-items:center;gap:4px;">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 12l2 2 4-4M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    Verificado
                                </div>
                                @endif
                            </div>

                            {{-- Corpo --}}
                            <div style="padding:40px 16px 16px;flex:1;display:flex;flex-direction:column;gap:10px;">

                                <div>
                                    <a href="{{ route('freelancer.show', $freelancer) }}" style="font-weight:800;font-size:1rem;color:#0f172a;text-decoration:none;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $freelancer->name }}</a>
                                    <p style="font-size:.78rem;color:#64748b;margin:.15rem 0 0;line-height:1.35;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $fp->headline ?? 'Freelancer' }}</p>
                                </div>

                                {{-- Rating --}}
                                <div style="display:flex;align-items:center;gap:.4rem;">
                                    <div style="display:flex;gap:2px;">
                                        @for($i=1;$i<=5;$i++)
                                            <svg width="14" height="14" viewBox="0 0 20 20" fill="{{ $i <= round($avg) ? '#facc15' : '#e2e8f0' }}"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                    </div>
                                    <span style="font-size:.78rem;color:#0f172a;font-weight:700;">{{ $avg > 0 ? number_format($avg,1) : 'Novo' }}</span>
                                    @if($cnt > 0)
                                        <span style="font-size:.72rem;color:#94a3b8;">({{ $cnt }})</span>
                                    @endif
                                </div>

                                {{-- Skills --}}
                                @if($fp && !empty($fp->skills))
                                    <div style="display:flex;flex-wrap:wrap;gap:.3rem;">
                                        @foreach(array_slice((array)$fp->skills, 0, 4) as $tag)
                                            <span style="background:#f0f9ff;color:#0284c7;font-size:.7rem;padding:.2rem .6rem;border-radius:20px;font-weight:600;">{{ $tag }}</span>
                                        @endforeach
                                        @if(count((array)$fp->skills) > 4)
                                            <span style="font-size:.7rem;color:#94a3b8;">+{{ count((array)$fp->skills) - 4 }}</span>
                                        @endif
                                    </div>
                                @endif

                                {{-- Rodapé --}}
                                <div style="margin-top:auto;padding-top:.75rem;border-top:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;gap:.5rem;">
                                    <div>
                                        @if($fp && $fp->hourly_rate)
                                            <span style="font-size:1rem;font-weight:900;color:#0f172a;">{{ number_format($fp->hourly_rate, 0, ',', '.') }}</span>
                                            <span style="font-size:.7rem;color:#94a3b8;"> {{ $fp->currency ?? 'Kz' }}/h</span>
                                        @else
                                            <span style="font-size:.78rem;color:#94a3b8;">Negociável</span>
                                        @endif
                                        <div style="font-size:.68rem;font-weight:700;color:{{ $availColor }};margin-top:1px;">● {{ $availLabel }}</div>
                                    </div>
                                    <a href="{{ route('freelancer.show', $freelancer) }}"
                                       style="background:#00baff;color:#fff;font-weight:700;font-size:.75rem;padding:.42rem .9rem;border-radius:8px;text-decoration:none;white-space:nowrap;">Ver perfil</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="grid-column:1/-1;padding:3.5rem 1rem;display:flex;flex-direction:column;align-items:center;gap:.75rem;">
                            <svg width="48" height="48" fill="none" stroke="#d1d5db" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35"/></svg>
                            <p style="font-weight:700;color:#64748b;font-size:.9rem;margin:0;">Nenhum freelancer encontrado.</p>
                            <p style="font-size:.8rem;color:#94a3b8;margin:0;">Experimente outros filtros.</p>
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
