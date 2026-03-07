<div>
    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 text-green-800 rounded-[10px] border border-green-200 text-sm">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="mb-4 p-3 bg-red-50 text-red-800 rounded-[10px] border border-red-200 text-sm">{{ session('error') }}</div>
    @endif

    {{-- KYC alert --}}
    @if($pendingKyc > 0)
        <div class="mb-4 flex items-center gap-3 p-3 bg-yellow-50 border border-yellow-200 rounded-[10px] text-sm">
            <svg class="w-4 h-4 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
            </svg>
            <span class="text-yellow-800 font-medium">{{ $pendingKyc }} utilizador(es) com KYC pendente</span>
            <button wire:click="bulkVerifyKyc"
                wire:confirm="Verificar todos os {{ $pendingKyc }} utilizadores com KYC pendente?"
                class="ml-auto btn-primary text-xs">
                Verificar todos em lote
            </button>
            <button wire:click="$set('kycFilter', 'pending')" class="btn-outline text-xs">Ver pendentes</button>
        </div>
    @endif

    {{-- Filters --}}
    <div class="flex flex-wrap gap-3 mb-5">
        <input wire:model.live.debounce.300ms="search" type="text"
            placeholder="Buscar nome ou email..."
            class="border border-gray-200 rounded-[10px] px-3 py-2 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
        <select wire:model.live="roleFilter" class="border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
            <option value="">Todos os tipos</option>
            <option value="admin">Admin</option>
            <option value="freelancer">Freelancer</option>
            <option value="cliente">Cliente</option>
        </select>
        <select wire:model.live="kycFilter" class="border border-gray-200 rounded-[10px] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
            <option value="">Todos KYC</option>
            <option value="pending">KYC Pendente</option>
            <option value="verified">KYC Verificado</option>
            <option value="rejected">KYC Rejeitado</option>
        </select>
        @if($kycFilter || $roleFilter || $search)
            <button wire:click="$set('kycFilter', ''); $set('roleFilter', ''); $set('search', '')" class="btn-outline text-xs">Limpar filtros</button>
        @endif
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto rounded-2xl border border-gray-200">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Utilizador</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Tipo</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">KYC</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Estado</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Cadastro</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Acções</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 {{ $user->is_suspended ? 'opacity-60' : '' }}">
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-2">
                            <img src="{{ $user->avatarUrl() }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                            <div>
                                <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                            {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' :
                               ($user->role === 'freelancer' ? 'bg-[#00baff]/10 text-[#00baff]' : 'bg-green-100 text-green-700') }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        @php $kyc = $user->kyc_status ?? 'pending'; @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                            {{ $kyc === 'verified' ? 'bg-green-100 text-green-700' :
                               ($kyc === 'rejected' ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ match($kyc) { 'verified' => '✓ Verificado', 'rejected' => '✗ Rejeitado', default => '⏳ Pendente' } }}
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        @if($user->is_suspended)
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-600">Suspenso</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Activo</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-xs text-gray-500">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-1 flex-wrap">
                            {{-- KYC --}}
                            @if(($user->kyc_status ?? 'pending') !== 'verified')
                                <button wire:click="verifyKyc({{ $user->id }})"
                                    class="px-2 py-1 text-xs bg-green-50 text-green-700 border border-green-200 rounded-lg hover:bg-green-100 transition">
                                    KYC ✓
                                </button>
                            @endif
                            @if(($user->kyc_status ?? 'pending') !== 'rejected')
                                <button wire:click="rejectKyc({{ $user->id }})"
                                    class="px-2 py-1 text-xs bg-red-50 text-red-600 border border-red-200 rounded-lg hover:bg-red-100 transition">
                                    KYC ✗
                                </button>
                            @endif
                            {{-- Suspend / Reactivate --}}
                            @if($user->role !== 'admin')
                                @if($user->is_suspended)
                                    <button wire:click="approveUser({{ $user->id }})"
                                        class="px-2 py-1 text-xs bg-blue-50 text-blue-700 border border-blue-200 rounded-lg hover:bg-blue-100 transition">
                                        Reactivar
                                    </button>
                                @else
                                    <button wire:click="suspendUser({{ $user->id }})"
                                        wire:confirm="Suspender {{ $user->name }}?"
                                        class="px-2 py-1 text-xs bg-orange-50 text-orange-700 border border-orange-200 rounded-lg hover:bg-orange-100 transition">
                                        Suspender
                                    </button>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-10 text-center text-gray-400 text-sm">Nenhum utilizador encontrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
</div>
