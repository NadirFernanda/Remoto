<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-sky-50/40 pb-16">

    {{-- ── Hero Header ── --}}
    <div class="bg-white border-b border-slate-100 shadow-sm">
        <div class="max-w-6xl mx-auto px-6 py-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#00baff] to-blue-600 flex items-center justify-center shadow-lg shadow-sky-200">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800 leading-tight">Assinaturas</h1>
                <p class="text-sm text-slate-500">Acompanhe receitas, ciclo anual e assinantes activos</p>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-6 pt-8">

    {{-- ── KPI Cards ────────────────────────────────────────────────────────── --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:.85rem;margin-bottom:1.75rem;">

        {{-- Saldo Disponível + Saque --}}
        <div style="background:linear-gradient(135deg,#0575e6 0%,#00baff 100%);border-radius:16px;padding:1.25rem 1.35rem;position:relative;overflow:hidden;">
            <div style="position:absolute;top:-18px;right:-18px;width:80px;height:80px;background:rgba(255,255,255,.08);border-radius:50%;"></div>
            <p style="font-size:.65rem;font-weight:700;color:rgba(255,255,255,.8);text-transform:uppercase;letter-spacing:.07em;margin:0 0 .5rem;">Saldo Disponível</p>
            <p style="font-size:1.55rem;font-weight:900;color:#fff;margin:0;line-height:1.1;">{{ money_aoa($saldoAssinDisponivel, false) }}</p>
            <p style="font-size:.7rem;color:rgba(255,255,255,.7);margin:.35rem 0 0;">disponível para saque</p>

            {{-- Botão de saque --}}
            <div style="margin-top:.85rem;">
                @if($pendenteSaqueAssin)
                    <span style="display:inline-flex;align-items:center;gap:6px;padding:.3rem .75rem;border-radius:8px;background:rgba(255,255,255,.15);color:#fff;font-size:.72rem;font-weight:700;">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Saque pendente
                    </span>
                @elseif(!$podeRealizarSaque)
                    <span style="display:inline-flex;align-items:center;gap:6px;padding:.3rem .75rem;border-radius:8px;background:rgba(255,255,255,.15);color:#fff;font-size:.72rem;font-weight:700;">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Disponível em {{ $diasParaProximoSaque }} dia(s)
                    </span>
                @else
                    <button wire:click="abrirSaqueAssin"
                        style="display:inline-flex;align-items:center;gap:6px;padding:.3rem .75rem;border-radius:8px;background:rgba(255,255,255,.2);color:#fff;font-size:.72rem;font-weight:700;border:none;cursor:pointer;transition:background .2s;"
                        onmouseover="this.style.background='rgba(255,255,255,.3)'" onmouseout="this.style.background='rgba(255,255,255,.2)'">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Solicitar Saque
                    </button>
                @endif
            </div>
            <p style="font-size:.62rem;color:rgba(255,255,255,.55);margin:.3rem 0 0;">Saques permitidos a cada 22 dias</p>

            <div style="position:absolute;bottom:1rem;right:1rem;">
                <svg width="22" height="22" fill="none" stroke="rgba(255,255,255,.5)" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        {{-- Total de Assinaturas --}}
        <div style="background:#fff;border:1.5px solid #eef2f7;border-radius:16px;padding:1.25rem 1.35rem;position:relative;overflow:hidden;">
            <div style="position:absolute;top:-18px;right:-18px;width:80px;height:80px;background:#f0f9ff;border-radius:50%;"></div>
            <p style="font-size:.65rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.07em;margin:0 0 .5rem;">Total de Assinaturas</p>
            <p style="font-size:1.9rem;font-weight:900;color:#0f172a;margin:0;line-height:1;">{{ $totalSubscriptions }}</p>
            <p style="font-size:.7rem;color:#64748b;margin:.35rem 0 0;">{{ $activeSubscribers }} activas agora</p>
            <div style="position:absolute;bottom:1rem;right:1rem;">
                <svg width="22" height="22" fill="none" stroke="#cbd5e1" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>

        {{-- Comissão da Plataforma 25% --}}
        <div style="background:#fff;border:1.5px solid #fef3c7;border-radius:16px;padding:1.25rem 1.35rem;position:relative;overflow:hidden;">
            <div style="position:absolute;top:-18px;right:-18px;width:80px;height:80px;background:#fffbeb;border-radius:50%;"></div>
            <p style="font-size:.65rem;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.07em;margin:0 0 .5rem;opacity:.7;">Comissão Plataforma <span style="background:#fef08a;color:#78350f;padding:.05rem .3rem;border-radius:4px;font-size:.6rem;">25%</span></p>
            <p style="font-size:1.55rem;font-weight:900;color:#b45309;margin:0;line-height:1.1;">{{ money_aoa($comissaoTotal, false) }}</p>
            <p style="font-size:.7rem;color:#d97706;margin:.35rem 0 0;">total retido pela plataforma</p>
            <div style="position:absolute;bottom:1rem;right:1rem;">
                <svg width="22" height="22" fill="none" stroke="#fcd34d" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                </svg>
            </div>
        </div>

        {{-- Valor da Assinatura --}}
        <div style="background:#fff;border:1.5px solid #d1fae5;border-radius:16px;padding:1.25rem 1.35rem;position:relative;overflow:hidden;">
            <div style="position:absolute;top:-18px;right:-18px;width:80px;height:80px;background:#ecfdf5;border-radius:50%;"></div>
            <p style="font-size:.65rem;font-weight:700;color:#065f46;text-transform:uppercase;letter-spacing:.07em;margin:0 0 .5rem;opacity:.75;">Valor da Assinatura</p>
            <p style="font-size:1.55rem;font-weight:900;color:#059669;margin:0;line-height:1.1;">{{ money_aoa($valorAssinatura, false) }}</p>
            <p style="font-size:.7rem;color:#10b981;margin:.35rem 0 0;">por mês · recebe <strong>{{ money_aoa($valorAssinatura * 0.75, false) }}</strong></p>
            <div style="position:absolute;bottom:1rem;right:1rem;">
                <svg width="22" height="22" fill="none" stroke="#6ee7b7" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
                </svg>
            </div>
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

    {{-- Modal: Saque das Assinaturas --}}
    @if($showSaqueModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" wire:click.self="fecharSaqueAssin">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Saque de Assinaturas</h3>
                    <p class="text-xs text-gray-500">Disponível: <strong class="text-blue-600">Kz {{ number_format($saldoAssinDisponivel, 2, ',', '.') }}</strong></p>
                </div>
            </div>

            @if($saqueMsg)
            <div class="mb-4 px-4 py-3 rounded-xl text-sm font-medium border
                {{ $saqueMsgType === 'success' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                {{ $saqueMsg }}
            </div>
            @endif

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Valor a sacar (Kz)</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-sm text-gray-400 font-medium">Kz</span>
                    <input type="number" wire:model="valorSaqueAssin"
                        min="1000" step="100" max="{{ $saldoAssinDisponivel }}"
                        class="w-full border border-slate-200 rounded-xl pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                        placeholder="0">
                </div>
                @error('valorSaqueAssin') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 mb-5">
                <p class="text-xs text-amber-700 font-medium">
                    <svg class="inline w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Saques de assinaturas são permitidos <strong>a cada 22 dias</strong>.
                    O processamento ocorre em até 2 dias úteis após aprovação.
                </p>
            </div>

            <div class="flex gap-3">
                <button wire:click="solicitarSaqueAssin" wire:loading.attr="disabled"
                    @if(!$podeRealizarSaque) disabled @endif
                    class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-[#00baff] text-white rounded-xl text-sm font-semibold hover:opacity-90 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="solicitarSaqueAssin">
                        @if(!$podeRealizarSaque)
                            Disponível em {{ $diasParaProximoSaque }} dia{{ $diasParaProximoSaque == 1 ? '' : 's' }}
                        @else
                            Confirmar Saque
                        @endif
                    </span>
                    <span wire:loading wire:target="solicitarSaqueAssin">A processar...</span>
                </button>
                <button wire:click="fecharSaqueAssin" class="px-4 py-2.5 text-sm text-gray-600 hover:text-gray-900 border border-gray-200 rounded-xl transition">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
