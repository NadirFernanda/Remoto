<a href="{{ route('admin.dashboard') }}" class="btn btn-secondary mb-6">← Voltar ao Dashboard</a>

<div class="p-6 bg-white rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Painel de Reembolsos</h2>
    <div class="flex gap-4 mb-4">
        <input type="text" wire:model.debounce.500ms="search" placeholder="Pesquisar motivo..." class="input input-bordered w-1/3" />
        <select wire:model="status" class="input input-bordered">
            <option value="">Todos status</option>
            <option value="pendente">Pendente</option>
            <option value="aprovado">Aprovado</option>
            <option value="rejeitado">Rejeitado</option>
        </select>
    </div>
    <table class="min-w-full text-sm">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2">ID</th>
                <th class="p-2">Cliente</th>
                <th class="p-2">Motivo</th>
                <th class="p-2">Status</th>
                <th class="p-2">Data</th>
                <th class="p-2">Provas</th>
                <th class="p-2">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($refunds as $refund)
                <tr class="border-b">
                    <td class="p-2">{{ $refund->id }}</td>
                    <td class="p-2">{{ $refund->user->name ?? '-' }}</td>
                    <td class="p-2">{{ $refund->reason }}</td>
                    <td class="p-2">
                        <span class="px-2 py-1 rounded {{
                            $refund->status === 'aprovado' ? 'bg-green-100 text-green-700' :
                            ($refund->status === 'rejeitado' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')
                        }}">
                            {{ ucfirst($refund->status) }}
                        </span>
                    </td>
                    <td class="p-2">{{ $refund->created_at->format('d/m/Y H:i') }}</td>
                    <td class="p-2">
                        @if($refund->proof)
                            <a href="{{ asset('storage/'.$refund->proof) }}" target="_blank" class="text-blue-600 underline">Ver ficheiro</a>
                        @else
                            -
                        @endif
                    </td>
                    <td class="p-2">
                        @if($refund->status === 'pendente')
                            <button wire:click="approve({{ $refund->id }})" class="btn btn-xs btn-success mr-1">Aprovar</button>
                            <button wire:click="reject({{ $refund->id }})" class="btn btn-xs btn-error">Rejeitar</button>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="p-2 text-center">Nenhum reembolso encontrado.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4">{{ $refunds->links() }}</div>
</div>
