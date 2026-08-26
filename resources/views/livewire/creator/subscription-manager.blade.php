<div class="max-w-6xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-[#00c8ff] to-[#0033cc] rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-2xl font-extrabold">Assinaturas</h2>
                <p class="text-sm text-white/75 mt-1">Acompanhe receitas, ciclo anual e assinantes activos</p>
            </div>
            {{-- Ganhos + link para o Painel Financeiro --}}
            <div class="flex items-center gap-3 bg-white/15 border border-white/30 rounded-xl px-4 py-3">
                <div>
                    <p class="text-xs font-bold text-white/70 uppercase tracking-wide">Total Ganho em Assinaturas</p>
                    <p class="text-xl font-black text-white">{{ money_aoa($allTimeEarnings, false) }}</p>
                </div>
                <div>
                    <a href="{{ route('freelancer.financial') }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/20 hover:bg-white/30 text-white text-xs font-bold transition whitespace-nowrap border border-white/20">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Sacar no Painel Financeiro
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($gatedPorAssinaturas)
    <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-2xl text-sm text-amber-800">
        <svg class="w-5 h-5 mt-0.5 shrink-0 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <div>
            <p class="font-semibold">Tem Kz {{ number_format($saldoAssinAtribuivel, 0, ',', '.') }} de assinaturas por resgatar.</p>
            <p class="mt-0.5">
                @if($cooldownDiasAssinaturas > 0)
                    Enquanto isso, o saque no Painel Financeiro exige um mínimo de <strong>Kz {{ number_format($saqueMinimoAssinaturas, 0, ',', '.') }}</strong> e um intervalo de <strong>{{ $cooldownDiasAssinaturas }} dia(s)</strong> entre pedidos.
                    @if($diasParaProximoSaqueAssin > 0)
                        Próximo saque disponível daqui a <strong>{{ $diasParaProximoSaqueAssin }} dia(s)</strong>.
                    @else
                        Já pode solicitar o saque.
                    @endif
                @else
                    Enquanto isso, o saque no Painel Financeiro exige um mínimo de <strong>Kz {{ number_format($saqueMinimoAssinaturas, 0, ',', '.') }}</strong> — assim que atingir esse valor, pode sacar a qualquer momento, sem tempo de espera.
                @endif
            </p>
        </div>
    </div>
    @endif

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        {{-- Total Assinaturas --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Total de Assinaturas</p>
            <p class="text-4xl font-black text-gray-900 leading-none">{{ $totalSubscriptions }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ $activeSubscribers }} activas agora</p>
        </div>

        {{-- Comissão Plataforma --}}
        <div class="bg-white rounded-2xl border border-amber-100 shadow-sm p-5">
            <p class="text-xs font-bold text-amber-700/70 uppercase tracking-wide mb-2">
                Comissão Plataforma
                <span class="bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded text-xs font-bold ml-1">25%</span>
            </p>
            <p class="text-3xl font-black text-amber-600 leading-none">{{ money_aoa($comissaoTotal, false) }}</p>
            <p class="text-sm text-amber-500 mt-1">por assinante, ao preço actual</p>
        </div>

        {{-- Valor da Assinatura --}}
        <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-5">
            <p class="text-xs font-bold text-emerald-700/70 uppercase tracking-wide mb-2">Valor da Assinatura</p>
            <p class="text-3xl font-black text-emerald-600 leading-none">{{ money_aoa($valorAssinatura, false) }}</p>
            <p class="text-sm text-emerald-500 mt-1">por mês · recebe <strong>{{ money_aoa($valorAssinatura * 0.75, false) }}</strong></p>
        </div>

    </div>

    {{-- Definir preço da assinatura --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <p class="text-sm font-bold text-gray-800">Preço da sua assinatura</p>
        <p class="text-xs text-gray-400 mt-0.5 mb-3">Define quanto cobra por mês. Mínimo permitido pela plataforma: Kz {{ number_format(\App\Models\CreatorProfile::MIN_SUBSCRIPTION_PRICE, 0, ',', '.') }}.</p>

        @if($precoMsg)
            <div class="mb-3 p-3 rounded-lg text-sm font-medium {{ $precoMsgType === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                {{ $precoMsg }}
            </div>
        @endif

        <form wire:submit="atualizarPreco" class="flex items-end gap-3 flex-wrap">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Valor mensal (Kz)</label>
                <input type="number" step="1" min="{{ \App\Models\CreatorProfile::MIN_SUBSCRIPTION_PRICE }}" wire:model="precoAssinatura"
                       class="w-40 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400 outline-none">
                @error('precoAssinatura') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                Guardar
            </button>
        </form>
    </div>

    {{-- Bar Chart --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5 flex-wrap gap-3">
            <div>
                <p class="text-base font-bold text-gray-900">Novas Assinaturas por Mês</p>
                <p class="text-xs text-gray-400 mt-0.5">Ciclo anual — {{ $selectedYear }}</p>
            </div>
            <div class="flex gap-1.5">
                @foreach($years as $yr)
                    <button wire:click="$set('selectedYear', {{ $yr }})"
                        class="px-3 py-1 rounded-lg text-xs font-bold border transition
                            {{ $selectedYear == $yr ? 'bg-[#0055ff]/10 border-[#0055ff] text-[#0055ff]' : 'border-gray-200 text-gray-500 hover:border-gray-300' }}">
                        {{ $yr }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Bars --}}
        <div class="flex items-end gap-1.5 h-32 pb-2 border-b border-gray-100">
            @foreach($months as $m => $data)
                @php $pct = $maxNew > 0 ? round(($data['new'] / $maxNew) * 100) : 0; @endphp
                <div class="flex-1 flex flex-col items-center gap-1 h-full justify-end"
                     title="{{ $data['label'] }}: {{ $data['new'] }} novas assinaturas">
                    @if($data['new'] > 0)
                        <span class="text-xs font-bold text-[#0055ff]">{{ $data['new'] }}</span>
                    @endif
                    <div class="w-full rounded-t-md transition-all"
                         style="height:{{ max(3, $pct) }}%;background:{{ $data['new'] > 0 ? 'linear-gradient(180deg,#00c8ff,#0033cc)' : '#f1f5f9' }}"></div>
                </div>
            @endforeach
        </div>
        {{-- X-axis labels --}}
        <div class="flex gap-1.5 pt-2">
            @foreach($months as $m => $data)
                <div class="flex-1 text-center text-xs text-gray-400 font-semibold">{{ $data['label'] }}</div>
            @endforeach
        </div>
    </div>

    {{-- Monthly Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <p class="text-base font-bold text-gray-900">Desempenho por Ciclo — {{ $selectedYear }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-bold text-gray-400 uppercase tracking-wide">Mês</th>
                        <th class="text-center px-4 py-3 text-xs font-bold text-gray-400 uppercase tracking-wide">Novas</th>
                        <th class="text-center px-4 py-3 text-xs font-bold text-gray-400 uppercase tracking-wide">Canceladas</th>
                        <th class="text-center px-4 py-3 text-xs font-bold text-gray-400 uppercase tracking-wide">Crescimento</th>
                        <th class="text-right px-5 py-3 text-xs font-bold text-gray-400 uppercase tracking-wide">Receita</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @php $hasAnyData = collect($months)->sum('new') > 0; @endphp
                    @foreach(array_reverse($months, true) as $m => $data)
                        @if($data['new'] > 0 || $data['cancelled'] > 0)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3 font-semibold text-gray-700">{{ $data['label'] }} {{ $selectedYear }}</td>
                                <td class="text-center px-4 py-3 font-bold text-green-600">
                                    @if($data['new'] > 0) +{{ $data['new'] }} @else — @endif
                                </td>
                                <td class="text-center px-4 py-3 font-bold text-red-500">
                                    @if($data['cancelled'] > 0) -{{ $data['cancelled'] }} @else — @endif
                                </td>
                                <td class="text-center px-4 py-3">
                                    @php $net = $data['net']; @endphp
                                    <span class="font-bold {{ $net > 0 ? 'text-green-600' : ($net < 0 ? 'text-red-500' : 'text-gray-400') }}">
                                        {{ $net > 0 ? '+' : '' }}{{ $net }}
                                    </span>
                                </td>
                                <td class="text-right px-5 py-3 font-bold text-gray-900">{{ money_aoa($data['revenue'], false) }}</td>
                            </tr>
                        @endif
                    @endforeach
                    @if(!$hasAnyData)
                        <tr>
                            <td colspan="5" class="text-center py-8 text-gray-400 text-sm">Sem assinaturas em {{ $selectedYear }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- Active Subscribers --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <p class="text-base font-bold text-gray-900">Assinantes Activos</p>
            <span class="bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded-lg">{{ $activeSubscribers }} activos</span>
        </div>

        <div class="divide-y divide-gray-50">
        @forelse($recentSubscribers as $sub)
            @php $subscriber = $sub->subscriber; @endphp
            <div class="flex items-center gap-3 px-6 py-3.5 hover:bg-gray-50 transition-colors">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-[#00c8ff] to-[#0033cc] flex items-center justify-center flex-shrink-0 overflow-hidden">
                    @if($subscriber?->profile_photo)
                        <img src="{{ $subscriber->avatarUrl() }}" class="w-9 h-9 object-cover" loading="lazy">
                    @else
                        <span class="font-bold text-sm text-white">{{ strtoupper(substr($subscriber?->name ?? '?', 0, 1)) }}</span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ e($subscriber?->name ?? 'Utilizador') }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Desde {{ $sub->starts_at->format('d/m/Y') }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-sm font-bold text-[#0055ff]">{{ money_aoa($sub->net_amount, false) }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Expira {{ $sub->expires_at->format('d/m/Y') }}</p>
                </div>
            </div>
        @empty
            <div class="text-center py-14 px-4">
                <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.3" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p class="text-base font-bold text-gray-600">Ainda não tens assinantes</p>
                <p class="text-sm text-gray-400 mt-1">Partilha o teu perfil para ganhar os primeiros subscritos</p>
            </div>
        @endforelse
        </div>
    </div>

</div>
