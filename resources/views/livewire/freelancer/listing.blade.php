<div class="space-y-6">

    {{-- Search & Filters --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex flex-col sm:flex-row gap-3">
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
