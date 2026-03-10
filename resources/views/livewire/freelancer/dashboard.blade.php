<div x-data="{}">
    {{-- Filtros de período --}}
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

    {{-- KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 mb-1">Total Recebido</p>
            <p class="text-2xl font-bold text-[#00baff]">Kz {{ number_format($kpi_total_recebido ?? 0, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Recebido em pagamentos</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 mb-1">Projectos Concluídos</p>
            <p class="text-2xl font-bold text-green-600">{{ $kpi_projetos_concluidos ?? 0 }}</p>
            <p class="text-xs text-gray-400 mt-1">Finalizados com sucesso</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 mb-1">Em Andamento</p>
            <p class="text-2xl font-bold text-yellow-500">{{ $kpi_projetos_andamento ?? 0 }}</p>
            <p class="text-xs text-gray-400 mt-1">Projectos activos</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 mb-1">Saldo Pendente</p>
            <p class="text-2xl font-bold text-orange-500">Kz {{ number_format($saldo_pendente ?? 0, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">A receber</p>
        </div>
    </div>

    {{-- Atalhos rápidos --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <a href="{{ route('freelancer.portfolio') }}" class="bg-white rounded-2xl border border-gray-200 p-4 text-center hover:border-[#00baff]/50 transition group">
            <svg class="w-6 h-6 mx-auto mb-2 text-gray-400 group-hover:text-[#00baff] transition" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span class="text-xs font-medium text-gray-700">Portfólio</span>
            <div class="mt-2">
                <span class="inline-block px-3 py-1 text-xs rounded-full bg-[#e0f7fa] text-[#00baff] font-semibold">Ver todos</span>
            </div>
        </a>
        <a href="{{ route('freelancer.financial') }}" class="bg-white rounded-2xl border border-gray-200 p-4 text-center hover:border-[#00baff]/50 transition group">
            <svg class="w-6 h-6 mx-auto mb-2 text-gray-400 group-hover:text-[#00baff] transition" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span class="text-xs font-medium text-gray-700">Financeiro</span>
            <div class="mt-2">
                <span class="inline-block px-3 py-1 text-xs rounded-full bg-[#e0f7fa] text-[#00baff] font-semibold">Ver todos</span>
            </div>
        </a>
        <a href="{{ route('freelancer.profile.edit') }}" class="bg-white rounded-2xl border border-gray-200 p-4 text-center hover:border-[#00baff]/50 transition group">
            <svg class="w-6 h-6 mx-auto mb-2 text-gray-400 group-hover:text-[#00baff] transition" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            <span class="text-xs font-medium text-gray-700">Editar perfil</span>
            <div class="mt-2">
                <span class="inline-block px-3 py-1 text-xs rounded-full bg-[#e0f7fa] text-[#00baff] font-semibold">Ver todos</span>
            </div>
        </a>
        <a href="{{ route('freelancer.projects') }}" class="bg-white rounded-2xl border border-gray-200 p-4 text-center hover:border-[#00baff]/50 transition group">
            <svg class="w-6 h-6 mx-auto mb-2 text-gray-400 group-hover:text-[#00baff] transition" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18"/></svg>
            <span class="text-xs font-medium text-gray-700">Histórico de Projectos</span>
            <div class="mt-2">
                <span class="inline-block px-3 py-1 text-xs rounded-full bg-[#e0f7fa] text-[#00baff] font-semibold">Ver todos</span>
            </div>
        </a>
        {{-- <a href="{{ route('freelancer.wallet') }}" class="bg-white rounded-2xl border border-gray-200 p-4 text-center hover:border-[#00baff]/50 transition group">
            <svg class="w-6 h-6 mx-auto mb-2 text-gray-400 group-hover:text-[#00baff] transition" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
            <span class="text-xs font-medium text-gray-700">Extrato</span>
        </a> --}}
    </div>


</div>