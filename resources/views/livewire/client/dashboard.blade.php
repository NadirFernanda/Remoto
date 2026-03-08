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
			<p class="text-xs text-gray-500 mb-1">Total Gasto</p>
			<p class="text-2xl font-bold text-[#00baff]">{{ money_aoa($kpi_total_gasto ?? 0) }}</p>
			<p class="text-xs text-gray-400 mt-1">Pagamentos realizados</p>
		</div>
		<div class="bg-white rounded-2xl border border-gray-200 p-5">
			<p class="text-xs text-gray-500 mb-1">Projetos Publicados</p>
			<p class="text-2xl font-bold text-green-600">{{ $kpi_projetos_publicados ?? 0 }}</p>
			<p class="text-xs text-gray-400 mt-1">Pedidos criados</p>
		</div>
		<div class="bg-white rounded-2xl border border-gray-200 p-5">
			   <p class="text-xs text-gray-500 mb-1">Relatórios</p>
			<p class="text-2xl font-bold text-indigo-600">{{ $kpi_freelancers_contratados ?? 0 }}</p>
			<p class="text-xs text-gray-400 mt-1">Contratações</p>
		</div>
		<div class="bg-white rounded-2xl border border-gray-200 p-5">
			<p class="text-xs text-gray-500 mb-1">Em Andamento</p>
			<p class="text-2xl font-bold text-yellow-500">{{ $kpi_projetos_andamento ?? 0 }}</p>
			<p class="text-xs text-gray-400 mt-1">Pedidos ativos</p>
		</div>
	</div>

	{{-- Atalhos rápidos --}}
	   <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
		   <a href="{{ route('client.projects') }}" class="bg-white rounded-2xl border border-gray-200 p-5 flex flex-col items-center justify-center text-center hover:border-[#00baff]/50 transition group h-full min-h-[150px]">
			   <svg class="w-6 h-6 mb-2 text-gray-700 group-hover:text-[#00baff] transition" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776"/></svg>
			   <span class="text-xs text-gray-700">Gerir Projetos</span>
			   <span class="inline-block px-4 py-1.5 text-xs rounded-full bg-[#e6fafd] text-gray-700 font-medium mt-2">Ver todos</span>
		   </a>
		   <a href="{{ route('client.payments') }}" class="bg-white rounded-2xl border border-gray-200 p-5 flex flex-col items-center justify-center text-center hover:border-[#00baff]/50 transition group h-full min-h-[150px]">
			   <svg class="w-6 h-6 mb-2 text-gray-700 group-hover:text-[#00baff] transition" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776"/></svg>
			   <span class="text-xs text-gray-700">Solicitar reembolso</span>
			   <span class="inline-block px-4 py-1.5 text-xs rounded-full bg-[#e6fafd] text-gray-700 font-medium mt-2">Solicitar</span>
		   </a>
		   <a href="{{ route('client.profile.edit') }}" class="bg-white rounded-2xl border border-gray-200 p-5 flex flex-col items-center justify-center text-center hover:border-[#00baff]/50 transition group h-full min-h-[150px]">
			   <svg class="w-6 h-6 mb-2 text-gray-700 group-hover:text-[#00baff] transition" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
			   <span class="text-xs text-gray-700">Editar perfil</span>
			   <span class="inline-block px-4 py-1.5 text-xs rounded-full bg-[#e6fafd] text-gray-700 font-medium mt-2">Ver todos</span>
		   </a>
		   <a href="{{ route('client.reports') }}" class="bg-white rounded-2xl border border-gray-200 p-5 flex flex-col items-center justify-center text-center hover:border-[#00baff]/50 transition group h-full min-h-[150px]">
			   <svg class="w-6 h-6 mb-2 text-gray-700 group-hover:text-[#00baff] transition" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
			   <span class="text-xs text-gray-700">Relatórios</span>
			   <span class="inline-block px-4 py-1.5 text-xs rounded-full bg-[#e6fafd] text-gray-700 font-medium mt-2">Ver todos</span>
		   </a>
	</div>


	<!-- Candidatos dos Pedidos -->
	<div class="mb-8">
		<h2 class="font-semibold text-xl mb-2 text-[#222]">Candidatos dos Pedidos</h2>
		<div class="overflow-x-auto">
			<table class="orders-table">
				<thead>
					<tr>
						<th class="py-2 px-4">Pedido</th>
						<th class="py-2 px-4">Freelancer</th>
						<th class="py-2 px-4">Status</th>
						<th class="py-2 px-4">Ação</th>
					</tr>
				</thead>
				<tbody>
					@forelse($candidates->whereNotIn('status', ['rejected']) as $candidate)
						<tr class="border-b">
							<td class="py-2 px-4">{{ $candidate->service->titulo ?? '-' }}</td>
							<td class="py-2 px-4">{{ optional($candidate->freelancer)->name ?? '—' }}</td>
							<td class="py-2 px-4">
								@php
									$statusLabels = [
										'pending'       => 'Pendente',
										'proposal_sent' => 'Proposta enviada',
										'invited'       => 'Convidado',
										'chosen'        => 'Escolhido',
										'rejected'      => 'Rejeitado',
									];
								@endphp
								{{ $statusLabels[$candidate->status] ?? ucfirst(str_replace('_', ' ', $candidate->status)) }}
							</td>
							<td class="py-2 px-4">
								@if(in_array($candidate->status, ['pending', 'proposal_sent', 'invited']) && optional($candidate->service)->status === 'published')
								<button wire:click="escolherFreelancer({{ $candidate->service_id }}, {{ $candidate->freelancer_id }})" class="inline-flex items-center gap-1 bg-[#00baff] text-white text-xs font-semibold px-3 py-1.5 rounded-lg hover:bg-[#009ad6] transition" title="Escolher freelancer" aria-label="Escolher freelancer">
									@include('components.icon', ['name' => 'check', 'class' => 'w-4 h-4'])
									Escolher
									</button>
								@elseif($candidate->status === 'chosen')
									<a href="{{ route('service.chat', $candidate->service_id) }}" class="inline-flex items-center gap-1 bg-[#00baff] text-white text-xs font-semibold px-3 py-1.5 rounded-lg hover:bg-[#009ad6] transition">
										@include('components.icon', ['name' => 'chat', 'class' => 'w-4 h-4'])
										Ir para o chat
									</a>
								@endif
							</td>
						</tr>
					@empty
						<tr><td colspan="4" class="text-center py-4 text-[#888]">Nenhum candidato encontrado.</td></tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>

</div>
