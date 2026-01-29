<div>
	<div class="flex min-h-screen bg-gray-50">
	<!-- Sidebar -->
	<aside class="w-64 bg-white border-r border-gray-200 flex flex-col py-8 px-4">
		<div class="mb-8">
			<div class="w-20 h-20 rounded-full bg-[#F5F7FA] mx-auto flex items-center justify-center text-3xl font-bold text-[#00B6E6]">{{ auth()->user()->name[0] ?? 'C' }}</div>
			<div class="text-center mt-2 font-semibold text-[#222]">{{ auth()->user()->name ?? 'Cliente' }}</div>
			<div class="text-center text-xs text-[#888]">{{ auth()->user()->email ?? '' }}</div>
		</div>
		<nav class="flex flex-col gap-2">
			<a href="{{ route('client.dashboard') }}" class="py-2 px-4 rounded hover:bg-[#F5F7FA] text-[#222] font-medium">Dashboard</a>
			<a href="{{ route('client.publish') }}" class="py-2 px-4 rounded hover:bg-[#F5F7FA] text-[#00B6E6] font-bold">+ Novo Pedido</a>
			<a href="{{ route('client.profile') }}" class="py-2 px-4 rounded hover:bg-[#F5F7FA] text-[#222] font-medium">Perfil</a>
			<a href="{{ route('client.settings') }}" class="py-2 px-4 rounded hover:bg-[#F5F7FA] text-[#222] font-medium">Configurações</a>
			<a href="#" class="py-2 px-4 rounded hover:bg-[#F5F7FA] text-[#222] font-medium">Histórico</a>
			<form method="POST" action="{{ route('logout') }}" class="mt-4">
				@csrf
				<button type="submit" class="w-full py-2 px-4 rounded bg-red-100 text-red-600 font-bold hover:bg-red-200">Sair</button>
			</form>
		</nav>
	</aside>
	<!-- Main Content -->
	<main class="flex-1 p-8">
		<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-2">
			<h1 class="font-semibold text-2xl text-[#222]">Dashboard do Cliente</h1>
			<a href="{{ route('client.publish') }}" class="inline-block bg-[#00B6E6] hover:bg-[#009E4F] text-white font-bold py-2 px-6 rounded transition">+ Novo Pedido</a>
		</div>
		<!-- KPIs -->
		<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
			<div class="bg-[#F5F7FA] p-5 rounded shadow text-center">
				<div class="text-[#00B6E6] text-lg font-bold">Kz {{ number_format($kpi_total_gasto, 2, ',', '.') }}</div>
				<div class="text-[#888] text-sm mt-1">Total Gasto</div>
			</div>
			<div class="bg-[#F5F7FA] p-5 rounded shadow text-center">
				<div class="text-[#222] text-lg font-bold">{{ $kpi_projetos_publicados }}</div>
				<div class="text-[#888] text-sm mt-1">Projetos Publicados</div>
			</div>
			<div class="bg-[#F5F7FA] p-5 rounded shadow text-center">
				<div class="text-[#009E4F] text-lg font-bold">{{ $kpi_freelancers_contratados }}</div>
				<div class="text-[#888] text-sm mt-1">Freelancers Contratados</div>
			</div>
			<div class="bg-[#F5F7FA] p-5 rounded shadow text-center">
				<div class="text-[#FFB800] text-lg font-bold">{{ $kpi_projetos_andamento }}</div>
				<div class="text-[#888] text-sm mt-1">Em Andamento</div>
			</div>
			<div class="bg-[#F5F7FA] p-5 rounded shadow text-center">
				<div class="text-[#222] text-lg font-bold">{{ $kpi_projetos_concluidos }}</div>
				<div class="text-[#888] text-sm mt-1">Concluídos</div>
			</div>
		</div>
		<!-- Últimos Pedidos -->
		<div class="mb-8">
			<h2 class="font-semibold text-xl mb-2 text-[#222]">Últimos Pedidos</h2>
			<div class="overflow-x-auto">
				<table class="min-w-full bg-white rounded shadow">
					<thead>
						<tr class="bg-[#F5F7FA] text-[#222]">
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
								<td class="py-2 px-4">{{ $order->titulo ?? '-' }}</td>
								<td class="py-2 px-4">
									@php
										$statusColors = [
											'published' => '#00B6E6',
											'accepted' => '#FFB800',
											'in_progress' => '#009E4F',
											'delivered' => '#00B6E6',
											'completed' => '#009E4F',
											'cancelled' => '#888',
										];
									@endphp
									<span class="font-bold" style="color: {{ $statusColors[$order->status] ?? '#222' }}">
										{{ ucfirst(str_replace('_', ' ', $order->status)) }}
									</span>
								</td>
								<td class="py-2 px-4">Kz {{ number_format($order->valor, 2, ',', '.') }}</td>
								<td class="py-2 px-4">{{ $order->created_at->format('d/m/Y') }}</td>
								<td class="py-2 px-4">
									<a href="{{ route('client.service.cancel', $order->id) }}" class="text-[#00B6E6] hover:underline">Detalhes</a>
								</td>
							</tr>
						@empty
							<tr><td colspan="5" class="text-center py-4 text-[#888]">Nenhum pedido encontrado.</td></tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
		<!-- Mensagens Recentes -->
		<div>
			<h2 class="font-semibold text-xl mb-2 text-[#222]">Mensagens Recentes</h2>
			<ul class="divide-y divide-[#F5F7FA] bg-white rounded shadow">
				@forelse($recent_messages as $msg)
					<li class="p-4 flex flex-col md:flex-row md:items-center justify-between">
						<div>
							<span class="font-bold text-[#00B6E6]">{{ $msg->user->name ?? 'Usuário' }}</span>
							<span class="text-[#888] ml-2">{{ $msg->service->titulo ?? '-' }}</span>
							<div class="text-[#222] mt-1">{{ $msg->conteudo }}</div>
						</div>
						<div class="text-[#888] text-sm mt-2 md:mt-0">{{ $msg->created_at->diffForHumans() }}</div>
					</li>
				@empty
					<li class="p-4 text-center text-[#888]">Nenhuma mensagem recente.</li>
				@endforelse
			</ul>
		</div>
	</main>
</div>
