<div class="container mx-auto p-4">

	<div class="dashboard-header">
		<div>
				<div class="text-sm text-gray-500">Visão geral rápida dos seus pedidos e KPIs</div>
			</div>
		<div class="mt-3 flex flex-wrap gap-2">
			<a href="{{ route('client.projects') }}" class="btn-primary text-xs">
				<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776"/></svg>
				Gerir Projetos
			</a>
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
							<td class="py-2 px-4">
							@php
								$orderStatusLabels = [
									'published'   => 'Publicado',
									'accepted'    => 'Aceite',
									'in_progress' => 'Em andamento',
									'delivered'   => 'Entregue',
									'completed'   => 'Concluído',
									'cancelled'   => 'Cancelado',
									'em_moderacao'=> 'Em moderação',
								];
							@endphp
							{{ $orderStatusLabels[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status ?? 'published')) }}
						</td>
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
									@if($order->status === 'delivered' && !$order->is_payment_released)
										<form method="POST" action="{{ route('client.service.release_payment', $order->id) }}" style="display:inline;">
											@csrf
											<button type="submit" class="action-btn action-icon" title="Confirmar entrega e liberar pagamento" aria-label="Confirmar entrega e liberar pagamento">
												@include('components.icon', ['name' => 'check', 'class' => 'w-5 h-5'])
											</button>
										</form>
									@endif
									@if(!$order->is_payment_released && !in_array($order->status, ['cancelled', 'completed']))
										<form method="POST" action="{{ route('client.service.refund', $order->id) }}" style="display:inline;">
											@csrf
											<button type="submit" class="action-btn action-icon" title="Solicitar reembolso" aria-label="Solicitar reembolso">
												@include('components.icon', ['name' => 'arrow-uturn-left', 'class' => 'w-5 h-5'])
											</button>
										</form>
									@endif
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
					@forelse($candidates->whereNotIn('status', ['rejected']) as $candidate)
						<tr class="border-b">
							<td class="py-2 px-4">{{ $candidate->service->titulo ?? '-' }}</td>
							<td class="py-2 px-4">{{ optional($candidate->freelancer)->name ?? '—' }}</td>
							<td class="py-2 px-4">
								@php
									$statusLabels = [
										'pending'  => 'Pendente',
										'chosen'   => 'Escolhido',
										'rejected' => 'Rejeitado',
									];
								@endphp
								{{ $statusLabels[$candidate->status] ?? ucfirst(str_replace('_', ' ', $candidate->status)) }}
							</td>
							<td class="py-2 px-4">
								@if($candidate->status === 'pending' && optional($candidate->service)->status === 'published')
									<button wire:click="escolherFreelancer({{ $candidate->service_id }}, {{ $candidate->freelancer_id }})" class="action-btn action-icon" title="Escolher freelancer" aria-label="Escolher freelancer">
										@include('components.icon', ['name' => 'check', 'class' => 'w-5 h-5'])
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
