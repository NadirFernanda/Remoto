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
            <button wire:click="$set('kycFilter', 'pending')" class="ml-auto btn-outline text-xs">Ver pendentes</button>
        </div>
    @endif

    {{-- Pending KYC Submissions --}}
    @if($pendingSubmissions->count() > 0)
        <div class="mb-6">
            <h2 class="text-base font-semibold text-gray-700 mb-3">📋 Documentos KYC aguardando revisão ({{ $pendingSubmissions->count() }})</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-2 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Utilizador</th>
                            <th class="py-2 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                            <th class="py-2 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Submetido</th>
                            <th class="py-2 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Acção</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingSubmissions as $sub)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2 px-4 font-medium">{{ $sub->user->name }}<br><span class="text-xs text-gray-400">{{ $sub->user->email }}</span></td>
                                <td class="py-2 px-4">{{ match($sub->document_type) { 'bi' => 'Bilhete de Identidade', 'passport' => 'Passaporte', 'driving_license' => 'Carta de condução', default => $sub->document_type } }}</td>
                                <td class="py-2 px-4 text-gray-500">{{ $sub->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-2 px-4">
                                    <button wire:click="openKycReview({{ $sub->id }})"
                                        class="px-3 py-1 text-xs bg-[#00baff] text-white rounded-lg hover:bg-[#009ad6] transition font-semibold">
                                        Rever documentos
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- No-docs modal --}}
    @if($noDocsUserName)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
                <div class="text-4xl mb-3">📂</div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Sem documentação</h3>
                <p class="text-gray-600 text-sm mb-5">
                    O utilizador <strong>{{ $noDocsUserName }}</strong> ainda não submeteu nenhuma documentação de identidade.
                </p>
                <button wire:click="closeKycReview"
                    class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition text-sm">
                    Fechar
                </button>
            </div>
        </div>
    @endif

    {{-- KYC Review Modal --}}
    @if($reviewingSubmission)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between p-5 border-b">
                    <h3 class="text-lg font-bold">Revisão KYC — {{ $reviewingSubmission->user->name }}</h3>
                    <button wire:click="closeKycReview" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>
                <div class="p-5 space-y-4">
                    <p class="text-sm text-gray-500">Tipo: <strong>{{ match($reviewingSubmission->document_type) { 'bi' => 'Bilhete de Identidade', 'passport' => 'Passaporte', 'driving_license' => 'Carta de condução', default => $reviewingSubmission->document_type } }}</strong></p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 mb-1 uppercase">Frente do documento</p>
                            <a href="{{ $this->kycDocumentUrl($reviewingSubmission->document_front_path) }}" target="_blank"
                                class="block border rounded-lg overflow-hidden hover:opacity-80 transition">
                                @if(str_ends_with(strtolower($reviewingSubmission->document_front_path), '.pdf'))
                                    <div class="p-4 bg-gray-50 text-center text-sm text-gray-600">📄 PDF — Clique para abrir</div>
                                @else
                                    <img src="{{ $this->kycDocumentUrl($reviewingSubmission->document_front_path) }}" class="w-full object-cover max-h-48" alt="Frente">
                                @endif
                            </a>
                        </div>
                        @if($reviewingSubmission->document_back_path)
                            <div>
                                <p class="text-xs font-semibold text-gray-500 mb-1 uppercase">Verso do documento</p>
                                <a href="{{ $this->kycDocumentUrl($reviewingSubmission->document_back_path) }}" target="_blank"
                                    class="block border rounded-lg overflow-hidden hover:opacity-80 transition">
                                    @if(str_ends_with(strtolower($reviewingSubmission->document_back_path), '.pdf'))
                                        <div class="p-4 bg-gray-50 text-center text-sm text-gray-600">📄 PDF — Clique para abrir</div>
                                    @else
                                        <img src="{{ $this->kycDocumentUrl($reviewingSubmission->document_back_path) }}" class="w-full object-cover max-h-48" alt="Verso">
                                    @endif
                                </a>
                            </div>
                        @endif
                        @if($reviewingSubmission->selfie_path)
                            <div class="md:col-span-2">
                                <p class="text-xs font-semibold text-gray-500 mb-1 uppercase">Selfie com documento</p>
                                <a href="{{ $this->kycDocumentUrl($reviewingSubmission->selfie_path) }}" target="_blank"
                                    class="block border rounded-lg overflow-hidden hover:opacity-80 transition">
                                    <img src="{{ $this->kycDocumentUrl($reviewingSubmission->selfie_path) }}" class="w-full object-cover max-h-48" alt="Selfie">
                                </a>
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Notas / motivo (opcional)</label>
                        <textarea wire:model="adminNotes" rows="2" placeholder="Ex: documento ilegível, expirado, etc."
                            class="block w-full rounded-lg border border-gray-200 py-2 px-3 text-sm"></textarea>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button wire:click="approveKycSubmission"
                            wire:confirm="Aprovar o KYC de {{ $reviewingSubmission->user->name }}?"
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-lg transition text-sm">
                            ✓ Aprovar
                        </button>
                        <button wire:click="rejectKycSubmission"
                            wire:confirm="Rejeitar o KYC de {{ $reviewingSubmission->user->name }}?"
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg transition text-sm">
                            ✗ Rejeitar
                        </button>
                        <button wire:click="closeKycReview" class="px-4 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Filters --}}
    <div class="flex flex-wrap gap-3 mb-5">
        <input wire:model.live.debounce.300ms="search" type="text"
            placeholder="Pesquisar nome ou email..."
            class="border border-gray-200 rounded-[10px] px-3 py-2 text-sm w-full sm:w-64 focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
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
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Nível Admin</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">KYC</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Estado</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Registo</th>
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
                        @if($user->role === 'admin')
                            <select wire:change="setAdminRole({{ $user->id }}, $event.target.value)"
                                class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-[#00baff]/30">
                                <option value="" {{ is_null($user->admin_role) ? 'selected' : '' }}>Master (padrão)</option>
                                <option value="master"     {{ $user->admin_role === 'master'     ? 'selected' : '' }}>Master</option>
                                <option value="gestor"     {{ $user->admin_role === 'gestor'     ? 'selected' : '' }}>Gestor</option>
                                <option value="financeiro" {{ $user->admin_role === 'financeiro' ? 'selected' : '' }}>Financeiro</option>
                                <option value="suporte"    {{ $user->admin_role === 'suporte'    ? 'selected' : '' }}>Suporte</option>
                                <option value="analista"   {{ $user->admin_role === 'analista'   ? 'selected' : '' }}>Analista</option>
                            </select>
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
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
                            {{-- Ver documentos KYC --}}
                            @if($user->role !== 'admin')
                                <button wire:click="reviewUserKyc({{ $user->id }})"
                                    class="px-2 py-1 text-xs bg-[#00baff]/10 text-[#00baff] border border-[#00baff]/30 rounded-lg hover:bg-[#00baff]/20 transition font-semibold">
                                    📄 Ver docs
                                </button>
                            @endif
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
                    <td colspan="7" class="py-10 text-center text-gray-400 text-sm">Nenhum utilizador encontrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
</div>
