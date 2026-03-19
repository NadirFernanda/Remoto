<div>
<style>
/* ── Hero ─────────────────────────────────────────────── */
.fl-hero {
    background: linear-gradient(135deg, #071428 0%, #0a2040 50%, #003d66 100%);
    padding: 3.5rem 1.25rem 2.75rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.fl-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 80% 60% at 50% 0%, rgba(0,186,255,.18) 0%, transparent 70%);
    pointer-events: none;
}
.fl-hero-eyebrow {
    display: inline-flex;align-items: center;gap: .4rem;
    background: rgba(0,186,255,.12);border: 1px solid rgba(0,186,255,.3);
    border-radius: 30px;padding: .3rem .9rem;
    font-size: .7rem;font-weight: 700;color: #00baff;
    letter-spacing: .06em;text-transform: uppercase;margin-bottom: .9rem;
}
.fl-hero h1 {
    font-size: clamp(1.7rem, 4vw, 2.5rem);font-weight: 900;
    color: #fff;margin: 0 0 .55rem;line-height: 1.15;
}
.fl-hero-sub {
    font-size: .95rem;color: rgba(255,255,255,.6);
    margin: 0 auto 1.6rem;max-width: 500px;
}
.fl-searchbar {
    max-width: 660px;margin: 0 auto;display: flex;gap: .5rem;position:relative;
}
.fl-searchbar-icon {
    position: absolute;left: 1rem;top: 50%;transform: translateY(-50%);
    color: rgba(255,255,255,.4);pointer-events: none;
}
.fl-searchbar input {
    flex: 1;padding: .875rem 1rem .875rem 2.9rem;
    border-radius: 12px;border: 1.5px solid rgba(255,255,255,.12);
    background: rgba(255,255,255,.08);backdrop-filter: blur(8px);
    color: #fff;font-size: .9rem;outline: none;
    transition: border .2s,background .2s;
}
.fl-searchbar input::placeholder { color: rgba(255,255,255,.38); }
.fl-searchbar input:focus { border-color: #00baff;background: rgba(255,255,255,.13); }
.fl-searchbar-skill {
    padding: .875rem .9rem;
    border-radius: 12px;border: 1.5px solid rgba(255,255,255,.12);
    background: rgba(255,255,255,.08);backdrop-filter: blur(8px);
    color: #fff;font-size: .85rem;outline: none;
    transition: border .2s;width: 180px;flex-shrink:0;
}
.fl-searchbar-skill:focus { border-color: #00baff; }
.fl-searchbar-skill option { color: #0f172a; background: #fff; }
.fl-hero-stats {
    display: flex;justify-content: center;gap: 2.25rem;
    margin-top: 1.6rem;flex-wrap: wrap;
}
.fl-hero-stat strong { display: block;font-size: 1.25rem;font-weight: 900;color: #fff; }
.fl-hero-stat span   { font-size: .72rem;color: rgba(255,255,255,.48); }
/* ── Body ─────────────────────────────────────────────── */
.fl-body {
    max-width: 1220px;margin: 0 auto;
    padding: 2rem 1.25rem 3rem;
}
.fl-sortbar {
    display: flex;align-items: center;
    justify-content: space-between;gap: 1rem;
    margin-bottom: 1.2rem;flex-wrap: wrap;
}
.fl-count { font-size: .82rem;color: #64748b; }
.fl-count strong { color: #0f172a;font-weight: 700; }
/* ── Cards ─────────────────────────────────────────────── */
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
    background: linear-gradient(120deg, #0575e6 0%, #00baff 100%);
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
    background: #f1f5f9;color: #0f172a;font-size: .73rem;font-weight: 700;
    padding: .4rem .95rem;border-radius: 9px;text-decoration: none;
    white-space: nowrap;transition: background .17s;
}
.fsp-btn-view:hover { background: #e2e8f0;color: #0f172a; }
.fsp-btn-hire {
    background: #00baff;color: #fff;font-size: .73rem;font-weight: 700;
    padding: .4rem .95rem;border-radius: 9px;text-decoration: none;
    white-space: nowrap;border:none;cursor:pointer;transition: background .17s;
}
.fsp-btn-hire:hover { background: #0099d4; }
.fsp-empty {
    grid-column: 1/-1;padding: 4rem 1rem;text-align: center;
}
</style>

{{-- ── Hero ── --}}
<div class="fl-hero">
    <div class="fl-hero-eyebrow">
        <svg width="8" height="8" fill="currentColor" viewBox="0 0 10 10"><circle cx="5" cy="5" r="5"/></svg>
        Profissionais verificados
    </div>
    <h1>Freelancers Qualificados</h1>
    <p class="fl-hero-sub">Encontre o profissional ideal para o seu projecto</p>

    {{-- Search bar --}}
    <div class="fl-searchbar">
        <svg class="fl-searchbar-icon" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35"/>
        </svg>
        <input type="search" wire:model.live.debounce.400ms="search"
               placeholder="Pesquisar por nome, especialidade…" autocomplete="off">
        <input type="text" wire:model.live.debounce.400ms="skill"
               placeholder="Filtrar por skill" class="fl-searchbar-skill">
    </div>

    {{-- Stats --}}
    <div class="fl-hero-stats">
        <div class="fl-hero-stat">
            <strong>{{ \App\Models\User::whereHas('freelancerProfile')->count() }}</strong>
            <span>Freelancers</span>
        </div>
        <div class="fl-hero-stat">
            <strong>{{ \App\Models\Service::where('status','completed')->count() }}</strong>
            <span>Projectos concluídos</span>
        </div>
        <div class="fl-hero-stat">
            <strong>24h</strong>
            <span>Resposta média</span>
        </div>
    </div>
</div>

{{-- ── Body ── --}}
<div class="fl-body">

    {{-- Count + loading --}}
    <div class="fl-sortbar">
        <p class="fl-count" wire:loading.remove>
            <strong>{{ $freelancers->total() }}</strong> freelancer(s) encontrado(s)
        </p>
        <div wire:loading.flex style="align-items:center;gap:.5rem;font-size:.82rem;color:#94a3b8;">
            <svg class="animate-spin" width="16" height="16" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="#00baff" stroke-width="4"/>
                <path class="opacity-75" fill="#00baff" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
            </svg>
            A filtrar…
        </div>
    </div>

    {{-- Cards --}}
    <div class="fsp-grid" wire:loading.class="opacity-50">
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

                {{-- Cover --}}
                <div class="fsp-card-cover">
                    @if($freelancer->kyc_status === 'verified')
                        <div class="fsp-badge-verified">
                            <svg width="9" height="9" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M9 12l2 2 4-4M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
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
                        <p class="fsp-card-headline">{{ $fp->headline ?? 'Freelancer' }}</p>
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
                        <div style="display:flex;gap:.4rem;" onclick="event.stopPropagation()">
                            <a href="{{ route('freelancer.show', $freelancer) }}" class="fsp-btn-view">Ver perfil</a>
                            @auth
                                <button type="button" wire:click="openProposal({{ $freelancer->id }})" class="fsp-btn-hire">
                                    Contratar
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="fsp-btn-hire">Contratar</a>
                            @endauth
                        </div>
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
                <p style="font-size:.95rem;font-weight:700;color:#475569;margin:.25rem 0 0;">Nenhum freelancer encontrado</p>
                <p style="font-size:.82rem;color:#94a3b8;margin:.35rem 0 0;">Tente outros termos de pesquisa</p>
            </div>
        @endforelse
    </div>

    {{-- Paginação --}}
    <div style="display:flex;justify-content:center;margin-top:2rem;">
        {{ $freelancers->links() }}
    </div>

</div>
</div>

        <div class="relative flex-1">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
            </svg>
            <input
                type="text"
                wire:model.debounce.400ms="search"
                placeholder="Pesquisar por nome, especialidade ou descrição…"
                class="w-full pl-10 pr-4 py-2.5 rounded-[10px] border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/40 focus:border-[#00baff]"
            >
        </div>
        <div class="relative sm:w-52">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10M7 11h6M7 15h4"/>
            </svg>
            <input
                type="text"
                wire:model.debounce.400ms="skill"
                placeholder="Filtrar por skill"
                class="w-full pl-10 pr-4 py-2.5 rounded-[10px] border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/40 focus:border-[#00baff]"
            >
        </div>
        <button
            wire:click="$refresh"
            class="btn-primary"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 01.707 1.707L13 12.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-8.586L3.293 4.707A1 1 0 013 4z"/>
            </svg>
            Filtrar
        </button>
    </div>

    {{-- Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($freelancers as $freelancer)
            @php
                $fp          = $freelancer->freelancerProfile;
                $metrics     = $fp ? (is_array($fp->metrics) ? $fp->metrics : json_decode($fp->metrics, true)) : [];
                $rating      = (float) ($metrics['rating']            ?? 0);
                $projects    = (int)   ($metrics['completed_projects'] ?? 0);
                $availability = $fp->availability_status ?? 'unavailable';
                $availColor  = match($availability) {
                    'available' => '#22c55e',
                    'busy'      => '#eab308',
                    default     => '#94a3b8',
                };
                $availLabel  = match($availability) {
                    'available' => 'Disponível',
                    'busy'      => 'Ocupado',
                    default     => 'Indisponível',
                };
            @endphp
            <div style="background:#fff;border-radius:16px;border:1px solid #e8edf3;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.06);transition:box-shadow .2s,transform .2s;display:flex;flex-direction:column;" onmouseover="this.style.boxShadow='0 8px 28px rgba(0,186,255,.13)';this.style.transform='translateY(-3px)'" onmouseout="this.style.boxShadow='0 2px 12px rgba(0,0,0,.06)';this.style.transform='translateY(0)'">

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

                {{-- Corpo do card --}}
                <div style="padding:40px 16px 16px;flex:1;display:flex;flex-direction:column;gap:10px;">

                    {{-- Nome + headline --}}
                    <div>
                        <a href="{{ route('freelancer.show', $freelancer->id) }}" style="font-weight:800;font-size:1rem;color:#0f172a;text-decoration:none;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $freelancer->name }}</a>
                        @if($fp && $fp->headline)
                            <p style="font-size:.78rem;color:#64748b;margin:.15rem 0 0;line-height:1.35;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $fp->headline }}</p>
                        @endif
                    </div>

                    {{-- Rating --}}
                    <div style="display:flex;align-items:center;gap:.4rem;">
                        <div style="display:flex;gap:2px;">
                            @for($s = 1; $s <= 5; $s++)
                                <svg width="14" height="14" viewBox="0 0 20 20" fill="{{ $s <= round($rating) ? '#facc15' : '#e2e8f0' }}"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <span style="font-size:.78rem;color:#0f172a;font-weight:700;">{{ $rating > 0 ? number_format($rating,1) : 'Novo' }}</span>
                        @if($projects > 0)
                            <span style="font-size:.72rem;color:#94a3b8;">· {{ $projects }} projectos</span>
                        @endif
                    </div>

                    {{-- Resumo --}}
                    @if($fp && $fp->summary)
                        <p style="font-size:.78rem;color:#64748b;line-height:1.55;margin:0;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $fp->summary }}</p>
                    @endif

                    {{-- Skills --}}
                    @if($fp && !empty($fp->skills))
                        <div style="display:flex;flex-wrap:wrap;gap:.3rem;">
                            @foreach(array_slice((array) $fp->skills, 0, 4) as $tag)
                                <span style="background:#f0f9ff;color:#0284c7;font-size:.7rem;padding:.2rem .6rem;border-radius:20px;font-weight:600;">{{ $tag }}</span>
                            @endforeach
                            @if(count((array) $fp->skills) > 4)
                                <span style="font-size:.7rem;color:#94a3b8;">+{{ count((array) $fp->skills) - 4 }}</span>
                            @endif
                        </div>
                    @endif

                    {{-- Rodapé: preço + disponibilidade + CTA --}}
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
                        <div style="display:flex;gap:.4rem;">
                            <a href="{{ route('freelancer.show', $freelancer->id) }}"
                               style="background:#f1f5f9;color:#0f172a;font-weight:700;font-size:.75rem;padding:.42rem .9rem;border-radius:8px;text-decoration:none;white-space:nowrap;">Ver perfil</a>
                            @auth
                                <button type="button" wire:click="openProposal({{ $freelancer->id }})"
                                        style="background:#00baff;color:#fff;font-weight:700;font-size:.75rem;padding:.42rem .9rem;border-radius:8px;border:none;cursor:pointer;white-space:nowrap;">Contratar</button>
                            @else
                                <a href="{{ route('login') }}"
                                   style="background:#00baff;color:#fff;font-weight:700;font-size:.75rem;padding:.42rem .9rem;border-radius:8px;text-decoration:none;white-space:nowrap;">Contratar</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column:1/-1;padding:3.5rem 1rem;display:flex;flex-direction:column;align-items:center;gap:.75rem;color:#94a3b8;">
                <svg width="48" height="48" fill="none" stroke="#d1d5db" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318A4.486 4.486 0 0012.016 15a4.486 4.486 0 00-3.198 1.318M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75z"/></svg>
                <p style="font-size:.9rem;font-weight:700;color:#64748b;margin:0;">Nenhum freelancer encontrado</p>
                <p style="font-size:.8rem;color:#94a3b8;margin:0;">Tente outros termos de pesquisa</p>
            </div>
        @endforelse
    </div>

    {{-- Paginação --}}
    <div style="display:flex;justify-content:center;margin-top:1.5rem;">
        {{ $freelancers->links() }}
    </div>

</div>
