<div class="container mx-auto p-4">

	<div class="dashboard-header">
		<div>
				<div class="text-sm text-gray-500">Visão geral rápida dos seus pedidos e KPIs</div>
			</div>
		<div class="filters-row">
			<input type="search" placeholder="Buscar pedidos, título ou cliente..." class="search-input" aria-label="Buscar pedidos">
			<select class="filter-input" aria-label="Filtrar por status">
				<option value="">Todos</option>
				<option value="published">Publicado</option>
				<option value="accepted">Aceito</option>
				<option value="in_progress">Em andamento</option>
				<option value="delivered">Entregue</option>
				<option value="completed">Concluído</option>
				<option value="cancelled">Cancelado</option>
			</select>
		</div>
	</div>

	<!-- KPIs do Cliente -->
	<div class="kpi-grid fade-up">
		<div class="kpi-card">
			<div class="value">{{ money_aoa($kpi_total_gasto ?? 0) }}</div>
			<div class="label">Total Gasto</div>
		</div>
		<div class="kpi-card">
			<div class="value">{{ $kpi_projetos_publicados ?? 0 }}</div>
			<div class="label">Projetos Publicados</div>
		</div>
		<div class="kpi-card">
			<div class="value">{{ $kpi_freelancers_contratados ?? 0 }}</div>
			<div class="label">Freelancers Contratados</div>
		</div>
		<div class="kpi-card">
			<div class="value">{{ $kpi_projetos_andamento ?? 0 }}</div>
			<div class="label">Em Andamento</div>
		</div>
	</div>

	<!-- Últimos Pedidos -->
	<div class="mb-8">
		<h2 class="font-semibold text-xl mb-2 text-[#222]">Últimos Pedidos</h2>
		<div class="overflow-x-auto">
			<table class="orders-table">
				<thead>
					<tr>
						<th class="py-2 px-4">Título</th>
						<th class="py-2 px-4">Status</th>
						<th class="py-2 px-4">Valor</th>
						<th class="py-2 px-4">Data</th>
						<th class="py-2 px-4">Ações</th>
					</tr>
				</thead>
				<tbody>
					@forelse($orders as $order)
						<tr class="border-b">
							<td class="py-2 px-4">{{ $order->titulo }}</td>
							<td class="py-2 px-4">{{ ucfirst(str_replace('_', ' ', $order->status ?? 'published')) }}</td>
							<td class="py-2 px-4">{{ money_aoa($order->valor) }}</td>
							<td class="py-2 px-4">{{ $order->created_at->format('d/m/Y') }}</td>
							<td>
								<div class="table-actions" role="group" aria-label="Ações do pedido" style="display: flex; gap: 0.5rem;">
									<a href="{{ route('client.service.cancel', $order->id) }}" class="action-btn action-icon" title="Ver detalhes" aria-label="Ver detalhes do pedido {{ $order->id }}">
										@include('components.icon', ['name' => 'eye', 'class' => 'w-5 h-5'])
									</a>
									<a href="{{ route('service.chat', ['service' => $order->id]) }}" class="action-btn action-icon relative" title="Abrir chat" aria-label="Abrir chat do pedido {{ $order->id }}">
										@include('components.icon', ['name' => 'chat', 'class' => 'w-5 h-5'])
										@livewire('chat.chat-badge', ['serviceId' => $order->id], key('chat-badge-'.$order->id))
									</a>
									<button wire:click="colocarEmModeracao({{ $order->id }})" class="action-btn action-icon" title="Colocar em moderação" aria-label="Colocar pedido {{ $order->id }} em moderação">
										@include('components.icon', ['name' => 'close', 'class' => 'w-5 h-5'])
									</button>
								</div>
							</td>
						</tr>
					@empty
						<tr><td colspan="5" class="text-center py-4 text-[#888]">Nenhum pedido encontrado.</td></tr>
					@endforelse
				</tbody>
			</table>
		</div>
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
					@forelse($candidates as $candidate)
						<tr class="border-b">
							<td class="py-2 px-4">{{ $candidate->service->titulo ?? '-' }}</td>
							<td class="py-2 px-4">{{ optional($candidate->freelancer)->name ?? '—' }}</td>
							<td class="py-2 px-4">{{ ucfirst(str_replace('_', ' ', $candidate->status)) }}</td>
							<td class="py-2 px-4">
								@if($candidate->status === 'pending' && optional($candidate->service)->status === 'published')
									<button wire:click="escolherFreelancer({{ $candidate->service_id }}, {{ $candidate->freelancer_id }})" class="action-btn action-icon" title="Escolher freelancer" aria-label="Escolher freelancer">
										@include('components.icon', ['name' => 'check', 'class' => 'w-5 h-5'])
									</button>
								@elseif($candidate->status === 'chosen')
									<span class="text-green-600 font-semibold">Escolhido</span>
								@elseif($candidate->status === 'rejected')
									<span class="text-red-600">Rejeitado</span>
								@else
									<span class="text-gray-600">{{ ucfirst($candidate->status) }}</span>
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

	<!-- Mensagens Recentes -->


</div>
