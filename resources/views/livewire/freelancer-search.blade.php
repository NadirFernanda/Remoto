{{-- ════════════════════════════════════════════════════════
     Freelancer Search — redesign visual (inspirado no Freelancer.com)
     ════════════════════════════════════════════════════════ --}}
<div>
<style>
/* ── Hero ─────────────────────────────────────────────── */
.fsp-hero {
    background: linear-gradient(135deg, #071428 0%, #0a2040 50%, #003d66 100%);
    padding: 3.5rem 1.25rem 2.75rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.fsp-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 80% 60% at 50% 0%, rgba(0,186,255,.18) 0%, transparent 70%);
    pointer-events: none;
}
.fsp-hero-eyebrow {
    display: inline-flex;align-items: center;gap: .4rem;
    background: rgba(0,186,255,.12);border: 1px solid rgba(0,186,255,.3);
    border-radius: 30px;padding: .3rem .9rem;
    font-size: .7rem;font-weight: 700;color: #00baff;
    letter-spacing: .06em;text-transform: uppercase;margin-bottom: .9rem;
}
.fsp-hero h1 {
    font-size: clamp(1.7rem, 4vw, 2.5rem);font-weight: 900;
    color: #fff;margin: 0 0 .55rem;line-height: 1.15;
}
.fsp-hero-sub {
    font-size: .95rem;color: rgba(255,255,255,.6);
    margin: 0 auto 1.6rem;max-width: 500px;
}
/* Search bar in hero */
.fsp-searchbar {
    max-width: 560px;margin: 0 auto;position: relative;display: flex;gap: .5rem;
}
.fsp-searchbar-icon {
    position: absolute;left: 1rem;top: 50%;transform: translateY(-50%);
    color: rgba(255,255,255,.4);pointer-events: none;
}
.fsp-searchbar input {
    flex: 1;padding: .875rem 1rem .875rem 2.9rem;
    border-radius: 12px;border: 1.5px solid rgba(255,255,255,.12);
    background: rgba(255,255,255,.08);backdrop-filter: blur(8px);
    color: #fff;font-size: .9rem;outline: none;
    transition: border .2s,background .2s;
}
.fsp-searchbar input::placeholder { color: rgba(255,255,255,.38); }
.fsp-searchbar input:focus { border-color: #00baff;background: rgba(255,255,255,.13); }
/* Hero stats */
.fsp-hero-stats {
    display: flex;justify-content: center;gap: 2.25rem;
    margin-top: 1.6rem;flex-wrap: wrap;
}
.fsp-hero-stat strong { display: block;font-size: 1.25rem;font-weight: 900;color: #fff; }
.fsp-hero-stat span   { font-size: .72rem;color: rgba(255,255,255,.48); }

/* ── Layout ──────────────────────────────────────────── */
.fsp-body {
    max-width: 1220px;margin: 0 auto;
    padding: 2rem 1.25rem 3rem;
    display: flex;gap: 1.75rem;align-items: flex-start;
}
@media(max-width:820px) {
    .fsp-body   { flex-direction: column; }
    .fsp-aside  { width: 100% !important;position: static !important; }
}

/* ── Sidebar ─────────────────────────────────────────── */
.fsp-aside { width: 244px;flex-shrink: 0;position: sticky;top: 76px; }
.fsp-filter-card {
    background: #fff;border-radius: 18px;
    border: 1px solid #e8edf3;
    box-shadow: 0 2px 14px rgba(0,0,0,.05);overflow: hidden;
}
.fsp-filter-head {
    padding: .8rem 1.1rem;border-bottom: 1px solid #f1f5f9;
    display: flex;align-items: center;justify-content: space-between;
}
.fsp-filter-head h3 { font-size: .82rem;font-weight: 700;color: #0f172a;margin: 0; }
.fsp-filter-group { padding: .85rem 1.1rem;border-bottom: 1px solid #f8fafc; }
.fsp-filter-group:last-child { border-bottom: none; }
.fsp-filter-label {
    font-size: .67rem;font-weight: 700;color: #94a3b8;
    letter-spacing: .07em;text-transform: uppercase;display: block;margin-bottom: .42rem;
}
.fsp-input, .fsp-select {
    width: 100%;padding: .5rem .75rem;
    border: 1.5px solid #e2e8f0;border-radius: 10px;
    font-size: .8rem;color: #334155;background: #f8fafc;outline: none;
    transition: border .2s,background .2s;box-sizing: border-box;
}
.fsp-input:focus, .fsp-select:focus { border-color: #00baff;background: #fff; }

/* ── Sort bar ────────────────────────────────────────── */
.fsp-sortbar {
    display: flex;align-items: center;
    justify-content: space-between;gap: 1rem;
    margin-bottom: 1.2rem;flex-wrap: wrap;
}
.fsp-count { font-size: .82rem;color: #64748b; }
.fsp-count strong { color: #0f172a;font-weight: 700; }
.fsp-sort-pills { display: flex;gap: .38rem;flex-wrap: wrap; }
.fsp-sort-pill {
    padding: .34rem .82rem;border-radius: 20px;font-size: .73rem;font-weight: 600;
    border: 1.5px solid #e2e8f0;color: #64748b;background: #fff;
    cursor: pointer;transition: all .17s;white-space: nowrap;
}
.fsp-sort-pill:hover { border-color: #00baff;color: #00baff; }
.fsp-sort-pill.active { background: #00baff;color: #fff;border-color: #00baff; }

/* ── Cards grid ──────────────────────────────────────── */
.fsp-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(255px, 1fr));
    gap: 1.1rem;
}
.fsp-card {
    background: #fff;border-radius: 18px;
    border: 1.5px solid #eef2f7;overflow: hidden;
    display: flex;flex-direction: column;
    transition: box-shadow .22s,border-color .22s,transform .22s;cursor: pointer;
}
.fsp-card:hover {
    box-shadow: 0 10px 32px rgba(0,186,255,.14);
    border-color: #ade8ff;transform: translateY(-3px);
}
.fsp-card-cover {
    height: 70px;flex-shrink: 0;position: relative;
    background: linear-gradient(135deg, #071428 0%, #0a2040 50%, #006bb3 100%);
}
.fsp-card-cover::after {
    content: '';position: absolute;inset: 0;
    background: radial-gradient(ellipse at 25% 60%, rgba(0,186,255,.22) 0%, transparent 55%),
                radial-gradient(ellipse at 80% 20%, rgba(0,100,210,.2) 0%, transparent 45%);
}
.fsp-card-avatar {
    position: absolute;bottom: -22px;left: 15px;z-index: 1;
}
.fsp-card-avatar img {
    width: 50px;height: 50px;border-radius: 50%;object-fit: cover;
    border: 3px solid #fff;box-shadow: 0 2px 10px rgba(0,0,0,.18);display: block;
}
.fsp-avail-dot {
    position: absolute;bottom: 2px;right: 2px;
    width: 12px;height: 12px;border-radius: 50%;border: 2.5px solid #fff;
}
.fsp-badge-verified {
    position: absolute;top: 8px;right: 10px;z-index: 1;
    background: rgba(255,255,255,.14);border: 1px solid rgba(255,255,255,.32);
    border-radius: 20px;padding: .16rem .52rem;
    font-size: .63rem;font-weight: 700;color: #fff;
    display: flex;align-items: center;gap: 3px;backdrop-filter: blur(4px);
}
.fsp-card-body {
    padding: 30px 15px 15px;flex: 1;
    display: flex;flex-direction: column;gap: 8px;
}
.fsp-card-name {
    font-size: .92rem;font-weight: 800;color: #0f172a;text-decoration: none;
    display: block;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;
    line-height: 1.2;
}
.fsp-card-name:hover { color: #00baff; }
.fsp-card-headline {
    font-size: .73rem;color: #64748b;margin: 0;
    white-space: nowrap;overflow: hidden;text-overflow: ellipsis;line-height: 1.3;
}
.fsp-stars { display: flex;align-items: center;gap: .28rem; }
.fsp-star-row { display: flex;gap: 1px; }
.fsp-skills { display: flex;flex-wrap: wrap;gap: .26rem; }
.fsp-skill-tag {
    background: #f0f9ff;color: #0369a1;font-size: .66rem;
    font-weight: 600;padding: .17rem .52rem;border-radius: 20px;border: 1px solid #bae6fd;
}
.fsp-card-footer {
    margin-top: auto;padding-top: .65rem;border-top: 1px solid #f1f5f9;
    display: flex;align-items: center;justify-content: space-between;gap: .5rem;
}
.fsp-rate { font-size: .98rem;font-weight: 900;color: #0f172a; }
.fsp-rate-unit { font-size: .66rem;color: #94a3b8;font-weight: 400; }
.fsp-btn-view {
    background: #00baff;color: #fff;font-size: .73rem;font-weight: 700;
    padding: .4rem .95rem;border-radius: 9px;text-decoration: none;
    white-space: nowrap;transition: background .17s;
}
.fsp-btn-view:hover { background: #0099d4;color: #fff; }
.fsp-empty {
    grid-column: 1/-1;padding: 4rem 1rem;text-align: center;
}
</style>


{{-- ── Hero ── --}}
<div class="fsp-hero">
    <div class="fsp-hero-eyebrow">
        <svg width="8" height="8" fill="currentColor" viewBox="0 0 10 10"><circle cx="5" cy="5" r="5"/></svg>
        Profissionais verificados
    </div>
    <h1>Encontre o Freelancer ideal</h1>
    <p class="fsp-hero-sub">Habilidades, avaliações e disponibilidade em tempo real</p>
    <div class="fsp-searchbar">
        <svg class="fsp-searchbar-icon" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35"/>
        </svg>
        <input type="search" wire:model.live.debounce.380ms="query"
               placeholder="Ex: designer, programador, redactor…" autocomplete="off">
    </div>
    <div class="fsp-hero-stats">
        <div class="fsp-hero-stat">
            <strong>{{ \App\Models\User::whereHas('freelancerProfile')->count() }}</strong>
            <span>Freelancers</span>
        </div>
        <div class="fsp-hero-stat">
            <strong>{{ \App\Models\Service::where('status','completed')->count() }}</strong>
            <span>Projectos concluídos</span>
        </div>
        <div class="fsp-hero-stat">
            <strong>24h</strong>
            <span>Resposta média</span>
        </div>
    </div>
</div>

{{-- ── Body ── --}}
<div class="fsp-body">

    {{-- Sidebar filtros --}}
    <aside class="fsp-aside">
        <div class="fsp-filter-card">
            <div class="fsp-filter-head">
                <h3>Filtros</h3>
                @if($skill || $language || $availability || $minRating > 0)
                    <button wire:click="$set('skill',''); $set('language',''); $set('availability',''); $set('minRating',0); $set('minRate',0); $set('maxRate',999999)"
                            style="font-size:.7rem;color:#00baff;font-weight:700;background:none;border:none;cursor:pointer;padding:0;">
                        Limpar
                    </button>
                @endif
            </div>

            <div class="fsp-filter-group">
                <label class="fsp-filter-label">Habilidade</label>
                <select wire:model.live="skill" class="fsp-select">
                    <option value="">Todas as habilidades</option>
                    @foreach($allSkills as $s)
                        <option value="{{ $s }}">{{ $s }}</option>
                    @endforeach
                </select>
            </div>

            <div class="fsp-filter-group">
                <label class="fsp-filter-label">Idioma</label>
                <select wire:model.live="language" class="fsp-select">
                    <option value="">Todos os idiomas</option>
                    @foreach($allLanguages as $lang)
                        <option value="{{ $lang }}">{{ $lang }}</option>
                    @endforeach
                </select>
            </div>

            <div class="fsp-filter-group">
                <label class="fsp-filter-label">Disponibilidade</label>
                <div style="display:flex;flex-direction:column;gap:.38rem;margin-top:.05rem;">
                    @foreach(['' => ['Qualquer',''], 'disponivel' => ['Disponível','#22c55e'], 'ocupado' => ['Ocupado','#eab308'], 'ferias' => ['Férias','#94a3b8']] as $val => [$label, $color])
                        <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.8rem;color:#475569;">
                            <input type="radio" wire:model.live="availability" value="{{ $val }}"
                                   style="accent-color:#00baff;width:14px;height:14px;flex-shrink:0;">
                            @if($color)
                                <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:{{ $color }};flex-shrink:0;"></span>
                            @endif
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="fsp-filter-group">
                <label class="fsp-filter-label">Avaliação mínima</label>
                <div style="display:flex;flex-direction:column;gap:.38rem;margin-top:.05rem;">
                    @foreach(['0'=>'Qualquer','3'=>'3★ ou mais','4'=>'4★ ou mais','4.5'=>'4.5★ ou mais','5'=>'Apenas 5★'] as $val => $label)
                        <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.8rem;color:#475569;">
                            <input type="radio" wire:model.live="minRating" value="{{ $val }}"
                                   style="accent-color:#00baff;width:14px;height:14px;">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="fsp-filter-group">
                <label class="fsp-filter-label">Taxa/hora (Kz)</label>
                <div style="display:flex;gap:.45rem;align-items:center;">
                    <input type="number" wire:model.live.debounce.500ms="minRate" min="0"
                           placeholder="Min" class="fsp-input" style="padding:.45rem .6rem;">
                    <span style="color:#cbd5e1;font-size:.8rem;flex-shrink:0;">–</span>
                    <input type="number" wire:model.live.debounce.500ms="maxRate" min="0"
                           placeholder="Máx" class="fsp-input" style="padding:.45rem .6rem;">
                </div>
            </div>
        </div>
    </aside>

    {{-- Resultados --}}
    <div style="flex:1;min-width:0;">

        {{-- Sort bar --}}
        <div class="fsp-sortbar">
            <p class="fsp-count">
                <strong>{{ $freelancers->total() }}</strong> freelancer(s) encontrado(s)
            </p>
            <div class="fsp-sort-pills">
                @foreach(['relevancia'=>'Relevância','popularidade'=>'Melhor avaliados','preco_asc'=>'Menor preço','preco_desc'=>'Maior preço'] as $val => $label)
                    <button wire:click="$set('sort','{{ $val }}')"
                            class="fsp-sort-pill {{ $sort === $val ? 'active' : '' }}">{{ $label }}</button>
                @endforeach
            </div>
        </div>

        {{-- Loading indicator --}}
        <div wire:loading.flex style="justify-content:center;align-items:center;gap:.6rem;padding:1.5rem 0;">
            <svg class="animate-spin" width="20" height="20" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="#00baff" stroke-width="4"/>
                <path class="opacity-75" fill="#00baff" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
            </svg>
            <span style="font-size:.8rem;color:#94a3b8;">A filtrar…</span>
        </div>

        {{-- Card grid --}}
        <div class="fsp-grid" wire:loading.remove>
            @forelse($freelancers as $freelancer)
                @php
                    $fp    = $freelancer->freelancerProfile;
                    $avg   = $freelancer->averageRating();
                    $cnt   = $freelancer->reviewsReceived->count();
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

                <div class="fsp-card" onclick="window.location='{{ route('freelancer.show', $freelancer) }}'">

                    {{-- Cover + avatar --}}
                    <div class="fsp-card-cover">
                        @if($freelancer->kyc_status === 'verified')
                            <div class="fsp-badge-verified">
                                <svg width="9" height="9" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Verificado
                            </div>
                        @endif
                        <div class="fsp-card-avatar">
                            <div style="position:relative;display:inline-block;">
                                <img src="{{ $freelancer->avatarUrl() }}" alt="{{ $freelancer->name }}" loading="lazy"
                                     onerror="this.src='{{ asset('img/default-avatar.svg') }}'">
                                <span class="fsp-avail-dot" style="background:{{ $availColor }};"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="fsp-card-body">
                        <div>
                            <a href="{{ route('freelancer.show', $freelancer) }}" class="fsp-card-name"
                               onclick="event.stopPropagation()">{{ $freelancer->name }}</a>
                            <p class="fsp-card-headline">{{ $fp->headline ?? 'Freelancer / Criador' }}</p>
                        </div>

                        {{-- Stars --}}
                        <div class="fsp-stars">
                            <div class="fsp-star-row">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg width="13" height="13" viewBox="0 0 20 20"
                                         fill="{{ $i <= round($avg) ? '#facc15' : '#e2e8f0' }}">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <span style="font-size:.77rem;font-weight:700;color:#0f172a;">
                                {{ $avg > 0 ? number_format($avg, 1) : '—' }}
                            </span>
                            @if($cnt > 0)
                                <span style="font-size:.7rem;color:#94a3b8;">({{ $cnt }} aval.)</span>
                            @else
                                <span style="font-size:.7rem;color:#94a3b8;">Novo</span>
                            @endif
                        </div>

                        {{-- Skills --}}
                        @if($fp && !empty($fp->skills))
                            <div class="fsp-skills">
                                @foreach(array_slice((array)$fp->skills, 0, 3) as $tag)
                                    <span class="fsp-skill-tag">{{ $tag }}</span>
                                @endforeach
                                @if(count((array)$fp->skills) > 3)
                                    <span style="font-size:.66rem;color:#94a3b8;padding:.17rem .3rem;">
                                        +{{ count((array)$fp->skills) - 3 }}
                                    </span>
                                @endif
                            </div>
                        @endif

                        {{-- Footer --}}
                        <div class="fsp-card-footer">
                            <div>
                                @if($fp && $fp->hourly_rate)
                                    <span class="fsp-rate">{{ number_format($fp->hourly_rate, 0, ',', '.') }}</span>
                                    <span class="fsp-rate-unit"> {{ $fp->currency ?? 'Kz' }}/h</span>
                                @else
                                    <span style="font-size:.78rem;color:#94a3b8;font-style:italic;">Negociável</span>
                                @endif
                                <div style="font-size:.66rem;font-weight:700;color:{{ $availColor }};margin-top:2px;">
                                    ● {{ $availLabel }}
                                </div>
                            </div>
                            <a href="{{ route('freelancer.show', $freelancer) }}" class="fsp-btn-view"
                               onclick="event.stopPropagation()">Ver perfil</a>
                        </div>
                    </div>
                </div>

            @empty
                <div class="fsp-empty">
                    <svg width="50" height="50" fill="none" stroke="#d1d5db" stroke-width="1.3" viewBox="0 0 24 24"
                         style="margin:0 auto .75rem;display:block;">
                        <circle cx="11" cy="11" r="8"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35"/>
                    </svg>
                    <p style="font-size:.95rem;font-weight:700;color:#475569;margin:.25rem 0 0;">
                        Nenhum freelancer encontrado
                    </p>
                    <p style="font-size:.82rem;color:#94a3b8;margin:.35rem 0 0;">
                        Experimente ajustar os filtros ou a pesquisa
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div style="margin-top:2rem;">
            {{ $freelancers->links() }}
        </div>
    </div>

</div>
</div>

