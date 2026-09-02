<div class="space-y-6">

    {{-- ── OVERVIEW CARDS ──────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        {{-- Online --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-col items-center text-center">
            <span class="w-3 h-3 rounded-full bg-emerald-500 mb-2 animate-pulse"></span>
            <div class="text-2xl font-extrabold text-emerald-600">{{ $overview['online'] }}</div>
            <div class="text-xs text-slate-400 mt-0.5 font-medium">Online</div>
        </div>
        {{-- Idle --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-col items-center text-center">
            <span class="w-3 h-3 rounded-full bg-amber-400 mb-2"></span>
            <div class="text-2xl font-extrabold text-amber-500">{{ $overview['idle'] }}</div>
            <div class="text-xs text-slate-400 mt-0.5 font-medium">Ausente</div>
        </div>
        {{-- Offline --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-col items-center text-center">
            <span class="w-3 h-3 rounded-full bg-slate-300 mb-2"></span>
            <div class="text-2xl font-extrabold text-slate-500">{{ $overview['offline'] }}</div>
            <div class="text-xs text-slate-400 mt-0.5 font-medium">Offline</div>
        </div>
        {{-- Acções --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-col items-center text-center">
            <div class="w-8 h-8 rounded-xl bg-[#0055ff]/10 flex items-center justify-center mb-1.5">
                <svg class="w-4 h-4 text-[#0055ff]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="text-2xl font-extrabold text-[#0055ff]">{{ $overview['total_accoes'] }}</div>
            <div class="text-xs text-slate-400 mt-0.5 font-medium">Acções</div>
        </div>
        {{-- Tickets respondidos --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-col items-center text-center">
            <div class="w-8 h-8 rounded-xl bg-sky-50 flex items-center justify-center mb-1.5">
                <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
            <div class="text-2xl font-extrabold text-sky-600">{{ $overview['total_tickets'] }}</div>
            <div class="text-xs text-slate-400 mt-0.5 font-medium">Suporte</div>
        </div>
        {{-- Tickets em aberto --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-col items-center text-center">
            <div class="w-8 h-8 rounded-xl bg-red-50 flex items-center justify-center mb-1.5">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/></svg>
            </div>
            <div class="text-2xl font-extrabold text-red-500">{{ $overview['tickets_abertos'] }}</div>
            <div class="text-xs text-slate-400 mt-0.5 font-medium">Abertos</div>
        </div>
    </div>

    {{-- ── PERÍODO + PESQUISA + TÍTULO ────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-base font-bold text-gray-100">Actividade da Equipa</h2>
            <p class="text-xs text-slate-400 mt-0.5">Clique num membro para ver o histórico detalhado</p>
        </div>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 self-start">
            {{-- Pesquisa --}}
            <div class="relative">
                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input wire:model.live.debounce.300ms="search"
                       type="text"
                       placeholder="Pesquisar admin..."
                       class="pl-8 pr-3 py-1.5 text-xs rounded-xl border border-slate-700 bg-transparent text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-[#0055ff]/40 focus:border-[#0055ff] transition w-44">
            </div>
            {{-- Período --}}
            <div class="flex rounded-xl border border-slate-700 overflow-hidden">
                @foreach(['hoje' => 'Hoje', 'semana' => 'Semana', 'mes' => 'Mês'] as $val => $label)
                    <button wire:click="$set('periodo', '{{ $val }}')"
                        class="px-3 py-1.5 text-xs font-semibold transition {{ $periodo === $val ? 'bg-[#0055ff] text-white' : 'bg-transparent text-slate-400 hover:text-white' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── LISTA DE ADMINS ──────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @forelse($admins as $admin)
            @php
                $status   = $admin->presenceStatus();
                $accoes   = $accoesCount[$admin->id] ?? 0;
                $tickets  = $ticketsRespondidos[$admin->id] ?? 0;
                $isSelected = $selectedAdminId === $admin->id;

                $statusDot   = match($status) { 'online' => 'bg-emerald-500 animate-pulse', 'idle' => 'bg-amber-400', default => 'bg-slate-500' };
                $statusLabel = match($status) { 'online' => 'Online', 'idle' => 'Ausente', default => 'Offline' };
                $statusColor = match($status) { 'online' => 'text-emerald-400', 'idle' => 'text-amber-400', default => 'text-slate-500' };

                $lastSeen = $admin->last_seen_at
                    ? $admin->last_seen_at->diffForHumans()
                    : 'Nunca acedeu';

                $initial = strtoupper(mb_substr($admin->name, 0, 1));
            @endphp

            <div wire:key="admin-team-{{ $admin->id }}"
                 wire:click="selectAdmin({{ $admin->id }})"
                 class="bg-white rounded-2xl border shadow-sm p-5 cursor-pointer transition-all duration-150 {{ $isSelected ? 'border-[#0055ff] ring-2 ring-[#0055ff]/20' : 'border-gray-100 hover:border-slate-300' }}">

                {{-- Header do card --}}
                <div class="flex items-start gap-4">
                    {{-- Avatar --}}
                    <div class="relative flex-shrink-0">
                        @if($admin->profile_photo)
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($admin->profile_photo) }}"
                                 alt="{{ $admin->name }}"
                                 class="w-12 h-12 rounded-xl object-cover">
                        @else
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg">
                                {{ $initial }}
                            </div>
                        @endif
                        <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full border-2 border-white {{ $statusDot }}"></span>
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm font-bold text-gray-900 truncate">{{ $admin->name }}</span>
                            @if($admin->admin_role === null)
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-[#0055ff]/10 text-[#0055ff]">Master</span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500 mt-0.5 truncate">{{ $admin->adminRoleLabel() }}
                            @if($admin->admin_cargo) · {{ $admin->admin_cargo }} @endif
                        </div>
                        <div class="flex items-center gap-1.5 mt-1">
                            <span class="text-[11px] {{ $statusColor }} font-semibold">{{ $statusLabel }}</span>
                            <span class="text-[10px] text-slate-400">· {{ $lastSeen }}</span>
                        </div>
                    </div>

                    {{-- Chevron --}}
                    <svg class="w-4 h-4 text-slate-400 flex-shrink-0 mt-1 transition-transform {{ $isSelected ? 'rotate-180' : '' }}"
                         fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>

                {{-- Métricas do período --}}
                <div class="mt-4 grid grid-cols-3 gap-3">
                    <div class="text-center bg-gray-50 rounded-xl py-2.5 px-2">
                        <div class="text-lg font-extrabold text-gray-900">{{ $accoes }}</div>
                        <div class="text-[10px] text-gray-400 font-medium mt-0.5">Acções</div>
                    </div>
                    <div class="text-center bg-sky-50 rounded-xl py-2.5 px-2">
                        <div class="text-lg font-extrabold text-sky-700">{{ $tickets }}</div>
                        <div class="text-[10px] text-sky-400 font-medium mt-0.5">Tickets</div>
                    </div>
                    <div class="text-center bg-gray-50 rounded-xl py-2.5 px-2">
                        @php
                            $lastLogin = $admin->last_login_at ? $admin->last_login_at->format('d/m') : '—';
                        @endphp
                        <div class="text-lg font-extrabold text-gray-900">{{ $lastLogin }}</div>
                        <div class="text-[10px] text-gray-400 font-medium mt-0.5">Login</div>
                    </div>
                </div>

                {{-- ── HISTÓRICO DETALHADO (expandível) ──────────────────────── --}}
                @if($isSelected && count($ultimasAccoes))
                    <div class="mt-4 border-t border-gray-100 pt-4">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Últimas acções</p>
                        <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                            @foreach($ultimasAccoes as $log)
                                @php
                                    $catColor = match($log->category ?? '') {
                                        'financeiro'  => 'bg-emerald-50 text-emerald-700',
                                        'suporte'     => 'bg-sky-50 text-sky-700',
                                        'utilizadores'=> 'bg-violet-50 text-violet-700',
                                        'operacoes'   => 'bg-[#0055ff]/8 text-[#0055ff]',
                                        'seguranca'   => 'bg-red-50 text-red-700',
                                        default       => 'bg-slate-100 text-slate-600',
                                    };
                                @endphp
                                <div class="flex items-start gap-2.5 py-2 border-b border-gray-50 last:border-0">
                                    <div class="flex-shrink-0 mt-0.5">
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $catColor }}">
                                            {{ ucfirst($log->category ?? 'geral') }}
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-gray-700 leading-snug">{{ $log->description }}</p>
                                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $log->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @elseif($isSelected && !count($ultimasAccoes))
                    <div class="mt-4 border-t border-gray-100 pt-4 text-center text-xs text-slate-400 py-4">
                        Sem acções registadas para este administrador.
                    </div>
                @endif
            </div>
        @empty
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm py-20 text-center">
                <p class="text-sm text-slate-400">Nenhum administrador activo encontrado.</p>
            </div>
        @endforelse
    </div>

    {{-- ── LEGENDA ──────────────────────────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-center gap-4 text-xs text-slate-400 pt-2">
        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Online — activo nos últimos 5 min</span>
        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span> Ausente — entre 5 e 30 min</span>
        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span> Offline — mais de 30 min ou nunca acedeu</span>
    </div>

</div>
