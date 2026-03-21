<div>

    {{-- ── Header ─────────────────────────────────────────────────────────── --}}
    <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem;">
        <div>
            <h2 style="font-size:1.25rem;font-weight:800;color:#0f172a;margin:0;">Gestão de Assinaturas</h2>
            <p style="font-size:.82rem;color:#64748b;margin:.2rem 0 0;">Desempenho e histórico por ciclo mensal</p>
        </div>
        @if($creatorProfile)
            <div style="background:#f0f9ff;border:1.5px solid #bae6fd;border-radius:12px;padding:.55rem 1rem;display:flex;align-items:center;gap:.6rem;">
                <svg width="15" height="15" fill="none" stroke="#0284c7" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span style="font-size:.78rem;font-weight:700;color:#0284c7;">
                    Preço: Kz 3.000 / mês
                </span>
            </div>
        @endif
    </div>

    {{-- ── KPI Cards ────────────────────────────────────────────────────────── --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:.75rem;margin-bottom:1.5rem;">

        <div style="background:#fff;border:1.5px solid #eef2f7;border-radius:14px;padding:1.1rem 1.2rem;">
            <p style="font-size:.68rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin:0 0 .45rem;">Assinantes Activos</p>
            <p style="font-size:1.8rem;font-weight:900;color:#0f172a;margin:0;line-height:1;">{{ $activeSubscribers }}</p>
            <p style="font-size:.72rem;color:#64748b;margin:.3rem 0 0;">em tempo real</p>
        </div>

        <div style="background:linear-gradient(135deg,#0575e6 0%,#00baff 100%);border-radius:14px;padding:1.1rem 1.2rem;">
            <p style="font-size:.68rem;font-weight:700;color:rgba(255,255,255,.75);text-transform:uppercase;letter-spacing:.06em;margin:0 0 .45rem;">Receita Mensal (MRR)</p>
            <p style="font-size:1.45rem;font-weight:900;color:#fff;margin:0;line-height:1.1;">{{ money_aoa($mrr, false) }}</p>
            <p style="font-size:.72rem;color:rgba(255,255,255,.7);margin:.3rem 0 0;">assinaturas activas</p>
        </div>

        <div style="background:#fff;border:1.5px solid #eef2f7;border-radius:14px;padding:1.1rem 1.2rem;">
            <p style="font-size:.68rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin:0 0 .45rem;">Total Ganho</p>
            <p style="font-size:1.45rem;font-weight:900;color:#0f172a;margin:0;line-height:1.1;">{{ money_aoa($allTimeEarnings, false) }}</p>
            <p style="font-size:.72rem;color:#64748b;margin:.3rem 0 0;">histórico completo</p>
        </div>

        <div style="background:#fff;border:1.5px solid #eef2f7;border-radius:14px;padding:1.1rem 1.2rem;">
            <p style="font-size:.68rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin:0 0 .45rem;">Total Assinaturas</p>
            <p style="font-size:1.8rem;font-weight:900;color:#0f172a;margin:0;line-height:1;">{{ $totalSubscriptions }}</p>
            <p style="font-size:.72rem;color:#64748b;margin:.3rem 0 0;">desde início</p>
        </div>

    </div>

    {{-- ── Bar Chart + Year Selector ────────────────────────────────────────── --}}
    <div style="background:#fff;border:1.5px solid #eef2f7;border-radius:16px;padding:1.25rem 1.4rem;margin-bottom:1.25rem;">

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.2rem;flex-wrap:wrap;gap:.75rem;">
            <div>
                <p style="font-size:.92rem;font-weight:800;color:#0f172a;margin:0;">Novas Assinaturas por Mês</p>
                <p style="font-size:.73rem;color:#94a3b8;margin:.15rem 0 0;">Ciclo anual — {{ $selectedYear }}</p>
            </div>
            <div style="display:flex;gap:.4rem;">
                @foreach($years as $yr)
                    <button wire:click="$set('selectedYear', {{ $yr }})"
                            style="padding:.32rem .7rem;border-radius:8px;font-size:.75rem;font-weight:700;border:1.5px solid {{ $selectedYear == $yr ? '#00baff' : '#e2e8f0' }};background:{{ $selectedYear == $yr ? '#f0f9ff' : '#fff' }};color:{{ $selectedYear == $yr ? '#0284c7' : '#64748b' }};cursor:pointer;">
                        {{ $yr }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Bars --}}
        <div style="display:flex;align-items:flex-end;gap:.4rem;height:130px;padding-bottom:.5rem;border-bottom:1.5px solid #f1f5f9;">
            @foreach($months as $m => $data)
                @php $pct = $maxNew > 0 ? round(($data['new'] / $maxNew) * 100) : 0; @endphp
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:.2rem;height:100%;justify-content:flex-end;"
                     title="{{ $data['label'] }}: {{ $data['new'] }} novas assinaturas">
                    @if($data['new'] > 0)
                        <span style="font-size:.6rem;font-weight:700;color:#0284c7;">{{ $data['new'] }}</span>
                    @endif
                    <div style="width:100%;background:{{ $data['new'] > 0 ? 'linear-gradient(180deg,#00baff,#0575e6)' : '#f1f5f9' }};border-radius:5px 5px 0 0;height:{{ max(3, $pct) }}%;transition:height .3s;"></div>
                </div>
            @endforeach
        </div>
        {{-- X-axis labels --}}
        <div style="display:flex;gap:.4rem;padding-top:.35rem;">
            @foreach($months as $m => $data)
                <div style="flex:1;text-align:center;font-size:.6rem;color:#94a3b8;font-weight:600;">{{ $data['label'] }}</div>
            @endforeach
        </div>

    </div>

    {{-- ── Monthly Table ────────────────────────────────────────────────────── --}}
    <div style="background:#fff;border:1.5px solid #eef2f7;border-radius:16px;overflow:hidden;margin-bottom:1.25rem;">
        <div style="padding:1rem 1.4rem;border-bottom:1px solid #f1f5f9;">
            <p style="font-size:.92rem;font-weight:800;color:#0f172a;margin:0;">Desempenho por Ciclo — {{ $selectedYear }}</p>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:.78rem;">
                <thead>
                    <tr style="background:#f8fafc;">
                        <th style="text-align:left;padding:.65rem 1.1rem;font-size:.65rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;">Mês</th>
                        <th style="text-align:center;padding:.65rem .75rem;font-size:.65rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;">Novas</th>
                        <th style="text-align:center;padding:.65rem .75rem;font-size:.65rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;">Canceladas</th>
                        <th style="text-align:center;padding:.65rem .75rem;font-size:.65rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;">Crescimento</th>
                        <th style="text-align:right;padding:.65rem 1.1rem;font-size:.65rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;">Receita</th>
                    </tr>
                </thead>
                <tbody>
                    @php $hasAnyData = collect($months)->sum('new') > 0; @endphp
                    @foreach(array_reverse($months, true) as $m => $data)
                        @if($data['new'] > 0 || $data['cancelled'] > 0)
                            <tr style="border-top:1px solid #f1f5f9;">
                                <td style="padding:.65rem 1.1rem;font-weight:700;color:#334155;">
                                    {{ $data['label'] }} {{ $selectedYear }}
                                </td>
                                <td style="text-align:center;padding:.65rem .75rem;color:#16a34a;font-weight:700;">
                                    @if($data['new'] > 0) +{{ $data['new'] }} @else — @endif
                                </td>
                                <td style="text-align:center;padding:.65rem .75rem;color:#dc2626;font-weight:700;">
                                    @if($data['cancelled'] > 0) -{{ $data['cancelled'] }} @else — @endif
                                </td>
                                <td style="text-align:center;padding:.65rem .75rem;">
                                    @php $net = $data['net']; @endphp
                                    <span style="font-weight:700;color:{{ $net > 0 ? '#16a34a' : ($net < 0 ? '#dc2626' : '#94a3b8') }};">
                                        {{ $net > 0 ? '+' : '' }}{{ $net }}
                                    </span>
                                </td>
                                <td style="text-align:right;padding:.65rem 1.1rem;font-weight:700;color:#0f172a;">
                                    {{ money_aoa($data['revenue'], false) }}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    @if(!$hasAnyData)
                        <tr>
                            <td colspan="5" style="text-align:center;padding:2rem;color:#94a3b8;font-size:.82rem;">
                                Sem assinaturas em {{ $selectedYear }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Active Subscribers List ──────────────────────────────────────────── --}}
    <div style="background:#fff;border:1.5px solid #eef2f7;border-radius:16px;overflow:hidden;">
        <div style="padding:1rem 1.4rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
            <p style="font-size:.92rem;font-weight:800;color:#0f172a;margin:0;">Assinantes Activos</p>
            <span style="background:#dcfce7;color:#16a34a;font-size:.7rem;font-weight:700;padding:.2rem .55rem;border-radius:8px;">{{ $activeSubscribers }} activos</span>
        </div>

        @forelse($recentSubscribers as $sub)
            @php $subscriber = $sub->subscriber; @endphp
            <div style="display:flex;align-items:center;gap:.8rem;padding:.7rem 1.1rem;border-top:1px solid #f8fafc;">
                <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#0575e6,#00baff);display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;">
                    @if($subscriber?->profile_photo)
                        <img src="{{ $subscriber->avatarUrl() }}" style="width:36px;height:36px;object-fit:cover;" loading="lazy">
                    @else
                        <span style="font-weight:800;font-size:.85rem;color:#fff;">{{ strtoupper(substr($subscriber?->name ?? '?', 0, 1)) }}</span>
                    @endif
                </div>
                <div style="flex:1;min-width:0;">
                    <p style="font-size:.82rem;font-weight:700;color:#0f172a;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ e($subscriber?->name ?? 'Utilizador') }}
                    </p>
                    <p style="font-size:.7rem;color:#94a3b8;margin:.1rem 0 0;">
                        Desde {{ $sub->starts_at->format('d/m/Y') }}
                    </p>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                    <p style="font-size:.78rem;font-weight:700;color:#0284c7;margin:0;">{{ money_aoa($sub->net_amount, false) }}</p>
                    <p style="font-size:.65rem;color:#94a3b8;margin:.1rem 0 0;">
                        Expira {{ $sub->expires_at->format('d/m/Y') }}
                    </p>
                </div>
            </div>
        @empty
            <div style="text-align:center;padding:2.5rem 1rem;">
                <svg width="40" height="40" fill="none" stroke="#d1d5db" stroke-width="1.3" viewBox="0 0 24 24" style="margin:0 auto .75rem;display:block;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p style="font-size:.88rem;font-weight:700;color:#475569;margin:0;">Ainda não tens assinantes</p>
                <p style="font-size:.78rem;color:#94a3b8;margin:.3rem 0 0;">Partilha o teu perfil para ganhar os primeiros subscritos</p>
            </div>
        @endforelse
    </div>

</div>
