<div x-data="{}">

    {{-- ─── Period filter ──────────────────────────────────────── --}}
    <div class="flex items-center gap-3 mb-6">
        <span class="text-sm font-medium text-gray-600">Período:</span>
        @foreach([7 => '7 dias', 30 => '30 dias', 90 => '90 dias'] as $days => $label)
            <button
                wire:click="$set('period', {{ $days }})"
                class="px-3 py-1.5 rounded-[10px] text-xs font-medium border transition
                    {{ $period === $days
                        ? 'bg-[#00baff] text-white border-[#00baff]'
                        : 'bg-white text-gray-600 border-gray-200 hover:border-[#00baff] hover:text-[#00baff]' }}"
            >{{ $label }}</button>
        @endforeach
    </div>

    {{-- ─── GMV + Revenue row ──────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 mb-1">GMV Total</p>
            <p class="text-2xl font-bold text-gray-900">{{ money_aoa($stats['gmv_total']) }}</p>
            <p class="text-xs text-gray-400 mt-1">Volume transacionado</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 mb-1">GMV ({{ $period }}d)</p>
            <p class="text-2xl font-bold text-[#00baff]">{{ money_aoa($stats['gmv_period']) }}</p>
            <p class="text-xs text-gray-400 mt-1">Últimos {{ $period }} dias</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 mb-1">Receita em Taxas</p>
            <p class="text-2xl font-bold text-green-600">{{ money_aoa($stats['revenue_total']) }}</p>
            <p class="text-xs text-gray-400 mt-1">Comissões acumuladas</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 mb-1">Taxas ({{ $period }}d)</p>
            <p class="text-2xl font-bold text-green-500">{{ money_aoa($stats['revenue_period']) }}</p>
            <p class="text-xs text-gray-400 mt-1">Últimos {{ $period }} dias</p>
        </div>
    </div>

    {{-- ─── Projects pipeline ──────────────────────────────────── --}}
    <div class="grid grid-cols-3 sm:grid-cols-6 gap-3 mb-6">
        @php
            $pipeCards = [
                ['label' => 'Publicados',   'val' => $stats['projects_published'], 'cls' => 'bg-blue-50 text-blue-700 border-blue-100'],
                ['label' => 'Em Andamento', 'val' => $stats['projects_active'],    'cls' => 'bg-yellow-50 text-yellow-700 border-yellow-100'],
                ['label' => 'Entregues',    'val' => $stats['projects_delivered'], 'cls' => 'bg-orange-50 text-orange-700 border-orange-100'],
                ['label' => 'Concluídos',   'val' => $stats['projects_completed'], 'cls' => 'bg-green-50 text-green-700 border-green-100'],
                ['label' => 'Cancelados',   'val' => $stats['projects_cancelled'], 'cls' => 'bg-red-50 text-red-600 border-red-100'],
                ['label' => 'Conversão',    'val' => $stats['conversion_rate'].'%','cls' => 'bg-[#00baff]/10 text-[#00baff] border-[#00baff]/20'],
            ];
        @endphp
        @foreach($pipeCards as $card)
            <div class="rounded-2xl border p-3 text-center {{ $card['cls'] }}">
                <p class="text-xl font-bold">{{ $card['val'] }}</p>
                <p class="text-[11px] font-medium mt-1">{{ $card['label'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- ─── Acquisition Funnel ─────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-6">
        <h2 class="text-sm font-bold text-gray-700 mb-4">Funil de Aquisição (Clientes)</h2>
        @php
            $funnelSteps = [
                ['label' => 'Registados',       'val' => $funnel['registered'], 'color' => 'bg-blue-400'],
                ['label' => 'Publicaram proj.', 'val' => $funnel['posted'],     'color' => 'bg-indigo-400'],
                ['label' => 'Contrataram',      'val' => $funnel['hired'],      'color' => 'bg-[#00baff]'],
                ['label' => 'Concluíram',       'val' => $funnel['completed'],  'color' => 'bg-green-500'],
            ];
            $maxFunnel = max(array_column($funnelSteps, 'val')) ?: 1;
        @endphp
        <div class="space-y-3">
            @foreach($funnelSteps as $i => $step)
                @php $pct = round($step['val'] / $maxFunnel * 100); @endphp
                <div class="flex items-center gap-4">
                    <span class="w-36 text-xs text-gray-600 flex-shrink-0 text-right">{{ $step['label'] }}</span>
                    <div class="flex-1 bg-gray-100 rounded-full h-6 overflow-hidden">
                        <div class="{{ $step['color'] }} h-full rounded-full flex items-center pl-3 transition-all"
                             style="width: {{ max($pct, 5) }}%">
                            <span class="text-white text-xs font-bold">{{ number_format($step['val']) }}</span>
                        </div>
                    </div>
                    @if($i > 0 && $funnelSteps[$i-1]['val'] > 0)
                        <span class="text-xs text-gray-400 w-10 flex-shrink-0">
                            {{ round($step['val'] / ($funnelSteps[$i-1]['val'] ?: 1) * 100) }}%
                        </span>
                    @else
                        <span class="w-10 flex-shrink-0"></span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ─── Users + KYC + Disputes ────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <h2 class="text-sm font-bold text-gray-700 mb-3">Utilizadores</h2>
            <div class="space-y-2">
                @foreach([
                    ['Total',       $stats['users_total'],       'font-bold text-gray-900'],
                    ['Clientes',    $stats['users_clients'],      'text-green-600'],
                    ['Freelancers', $stats['users_freelancers'],  'text-[#00baff]'],
                    ['Novos ('.$period.'d)', '+'.$stats['users_new_period'], 'text-indigo-600'],
                    ['Suspensos',   $stats['users_suspended'],    'text-red-500'],
                ] as [$lbl, $val, $cls])
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">{{ $lbl }}</span>
                        <span class="font-semibold {{ $cls }}">{{ $val }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <h2 class="text-sm font-bold text-gray-700 mb-3">Verificação KYC</h2>
            @if($stats['kyc_pending'] > 0)
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                        <span class="text-lg font-bold text-yellow-700">{{ $stats['kyc_pending'] }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Aguardam verificação</p>
                        <p class="text-xs text-gray-400">verificação de identidade</p>
                    </div>
                </div>
                <a href="{{ route('admin.users') }}" class="btn-primary text-xs">Verificar agora</a>
            @else
                <div class="flex items-center gap-2 text-green-600 mt-2">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium">Todos verificados</span>
                </div>
            @endif
            <div class="mt-4 pt-3 border-t border-gray-100">
                <a href="{{ route('admin.users') }}" class="text-xs text-[#00baff] hover:underline">Gerir utilizadores →</a>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <h2 class="text-sm font-bold text-gray-700 mb-3">Disputas</h2>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 bg-red-500 rounded-full"></span><span class="text-gray-500">Abertas</span></span>
                    <span class="font-bold text-red-600">{{ $stats['disputes_open'] }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 bg-yellow-500 rounded-full"></span><span class="text-gray-500">Em mediação</span></span>
                    <span class="font-bold text-yellow-600">{{ $stats['disputes_mediation'] }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 bg-orange-500 rounded-full"></span><span class="text-gray-500">Em moderação</span></span>
                    <span class="font-bold text-orange-600">{{ $stats['moderacao_pendente'] }}</span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100">
                <a href="{{ route('admin.disputes') }}" class="text-xs text-[#00baff] hover:underline">Ver todas as disputas →</a>
            </div>
        </div>
    </div>

    {{-- ─── Recent Audit Logs ──────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-bold text-gray-700">Actividade Recente</h2>
            <a href="{{ route('admin.audit') }}" class="text-xs text-[#00baff] hover:underline">Ver todos →</a>
        </div>
        <div class="space-y-1">
            @forelse($recentLogs as $log)
                <div class="flex items-start gap-3 py-2 border-b border-gray-50 last:border-0">
                    <div class="w-6 h-6 rounded-full bg-[#00baff]/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-3 h-3 text-[#00baff]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-800">{{ $log->description }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $log->user->name ?? 'Sistema' }} · {{ $log->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <span class="text-xs text-gray-300 flex-shrink-0">{{ $log->ip }}</span>
                </div>
            @empty
                <p class="text-sm text-gray-400 text-center py-4">Nenhuma actividade registada ainda.</p>
            @endforelse
        </div>
    </div>

    {{-- ─── Quick nav ──────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @foreach([
            ['route' => 'admin.users',    'icon' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z', 'label' => 'Utilizadores'],
            ['route' => 'admin.services', 'icon' => 'M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776', 'label' => 'Serviços'],
            ['route' => 'admin.disputes', 'icon' => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z', 'label' => 'Disputas'],
            ['route' => 'admin.audit',    'icon' => 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z', 'label' => 'Logs & Audit'],
        ] as $qa)
            <a href="{{ route($qa['route']) }}" class="bg-white rounded-2xl border border-gray-200 p-4 text-center hover:border-[#00baff]/50 transition group">
                <svg class="w-6 h-6 mx-auto mb-2 text-gray-400 group-hover:text-[#00baff] transition" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $qa['icon'] }}"/>
                </svg>
                <span class="text-xs font-medium text-gray-700">{{ $qa['label'] }}</span>
            </a>
        @endforeach
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-sm font-semibold text-gray-500">Usuários Totais</h2>
            <p class="text-2xl font-bold">{{ $stats['users_total'] ?? 0 }}</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-sm font-semibold text-gray-500">Clientes</h2>
            <p class="text-2xl font-bold">{{ $stats['users_clients'] ?? 0 }}</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-sm font-semibold text-gray-500">Freelancers</h2>
            <p class="text-2xl font-bold">{{ $stats['users_freelancers'] ?? 0 }}</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-sm font-semibold text-gray-500">Admins</h2>
            <p class="text-2xl font-bold">{{ $stats['users_admins'] ?? 0 }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-sm font-semibold text-gray-500">Projetos Totais</h2>
            <p class="text-2xl font-bold">{{ $stats['services_total'] ?? 0 }}</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-sm font-semibold text-gray-500">Publicados</h2>
            <p class="text-2xl font-bold">{{ $stats['services_published'] ?? 0 }}</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-sm font-semibold text-gray-500">Em andamento</h2>
            <p class="text-2xl font-bold">{{ $stats['services_in_progress'] ?? 0 }}</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-sm font-semibold text-gray-500">Cancelados</h2>
            <p class="text-2xl font-bold">{{ $stats['services_cancelled'] ?? 0 }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-sm font-semibold text-gray-500">Projetos Entregues</h2>
            <p class="text-2xl font-bold">{{ $stats['services_delivered'] ?? 0 }}</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-sm font-semibold text-gray-500">Receita em Taxas (entregues)</h2>
            <p class="text-2xl font-bold">{{ money_aoa($stats['revenue_fees'] ?? 0) }}</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-sm font-semibold text-gray-500">Próximos passos</h2>
            <ul class="list-disc pl-5 text-sm mt-1 space-y-1">
                <li>Adicionar lista de últimos projetos criados.</li>
                <li>Exibir últimos usuários cadastrados.</li>
                <li>Implementar filtros por período (7/30 dias).</li>
            </ul>
        </div>
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-sm font-semibold text-gray-500">Taxa BRL → AOA</h2>
            <div class="flex items-center gap-3">
                <div id="aoa-rate-display" class="text-lg font-bold">{{ app(\App\Services\ExchangeRateService::class)->getRate() }}</div>
                <button id="refresh-aoa-btn" class="ml-auto bg-cyan-400 text-[#021018] rounded px-3 py-1 font-bold">Atualizar taxa</button>
            </div>
            <div id="aoa-rate-msg" class="text-sm text-gray-500 mt-2"></div>
        </div>
    </div>
    <div class="bg-white shadow rounded p-6 mt-8">
        <h2 class="text-lg font-bold mb-4">Últimos Usuários Cadastrados</h2>
        <table class="min-w-full text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="py-2 px-4 text-left">Nome</th>
                    <th class="py-2 px-4 text-left">E-mail</th>
                    <th class="py-2 px-4 text-left">Tipo</th>
                    <th class="py-2 px-4 text-left">Cadastro</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentUsers as $user)
                <tr class="border-b">
                    <td class="py-2 px-4">{{ $user->name }}</td>
                    <td class="py-2 px-4">{{ $user->email }}</td>
                    <td class="py-2 px-4">{{ ucfirst($user->role) }}</td>
                    <td class="py-2 px-4">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($recentUsers->isEmpty())
            <div class="text-center text-gray-500 py-4">Nenhum usuário cadastrado.</div>
        @endif
    </div>
</div>
