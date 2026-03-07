<div>
    <div class="flex flex-col md:flex-row gap-3 mb-6">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por nome ou e-mail..." class="border rounded px-3 py-2 text-sm w-full md:w-72">
        <select wire:model.live="roleFilter" class="border rounded px-3 py-2 text-sm">
            <option value="">Todos os tipos</option>
            <option value="admin">Admin</option>
            <option value="freelancer">Freelancer</option>
            <option value="cliente">Cliente</option>
        </select>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="py-2 px-4 text-left">ID</th>
                    <th class="py-2 px-4 text-left">Nome</th>
                    <th class="py-2 px-4 text-left">E-mail</th>
                    <th class="py-2 px-4 text-left">Tipo</th>
                    <th class="py-2 px-4 text-left">Cadastro</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-2 px-4 text-gray-400">{{ $user->id }}</td>
                    <td class="py-2 px-4 font-medium">{{ $user->name }}</td>
                    <td class="py-2 px-4">{{ $user->email }}</td>
                    <td class="py-2 px-4">
                        <span class="px-2 py-0.5 rounded text-xs font-semibold
                            {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' :
                               ($user->role === 'freelancer' ? 'bg-cyan-100 text-cyan-700' : 'bg-green-100 text-green-700') }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="py-2 px-4 text-gray-500">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-6 text-center text-gray-400">Nenhum usuário encontrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
