<div>
    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 text-green-800 rounded-xl border border-green-200 text-sm">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="mb-4 p-3 bg-red-50 text-red-800 rounded-xl border border-red-200 text-sm">{{ session('error') }}</div>
    @endif

    {{-- Alerta KYC pendente --}}
    @if($pendingKyc > 0)
        <div class="mb-4 flex items-center gap-3 p-3 bg-amber-50 border border-amber-200 rounded-xl text-sm">
            <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
            </svg>
            <span class="text-amber-800 font-medium">{{ $pendingKyc }} utilizador(es) com verificação de identidade pendente</span>
            <button wire:click="$set('kycFilter', 'pending')" class="ml-auto btn-outline text-xs">Ver pendentes</button>
        </div>
    @endif

    {{-- Documentos pendentes de revisão --}}
    @if($pendingSubmissions->count() > 0)
        <div class="mb-6 bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                <span class="text-sm font-semibold text-gray-700">Documentos aguardando revisão</span>
                <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700">{{ $pendingSubmissions->count() }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-2.5 px-5 text-left text-xs font-semibold text-gray-500 uppercase">Utilizador</th>
                            <th class="py-2.5 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Tipo de documento</th>
                            <th class="py-2.5 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Submetido em</th>
                            <th class="py-2.5 px-4 text-left text-xs font-semibold text-gray-500 uppercase">Acção</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($pendingSubmissions as $sub)
                            <tr class="hover:bg-gray-50/60">
                                <td class="py-2.5 px-5 font-medium text-gray-800">
                                    {{ $sub->user->name }}
                                    <span class="block text-xs text-gray-400 font-normal">{{ $sub->user->email }}</span>
                                </td>
                                <td class="py-2.5 px-4 text-gray-600">
                                    {{ match($sub->document_type) {
                                        'bi'              => 'Bilhete de Identidade',
                                        'passport'        => 'Passaporte',
                                        'driving_license' => 'Carta de condução',
                                        default           => ucfirst($sub->document_type)
                                    } }}
                                </td>
                                <td class="py-2.5 px-4 text-gray-500 text-xs">{{ $sub->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-2.5 px-4">
                                    <button wire:click="openKycReview({{ $sub->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs bg-[#0055ff] text-white rounded-lg hover:bg-[#0044dd] transition font-semibold">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
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

    {{-- Modal: sem documentação --}}
    @if($noDocsUserName)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
                <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Sem documentação</h3>
                <p class="text-gray-500 text-sm mb-5">
                    <strong>{{ $noDocsUserName }}</strong> ainda não submeteu nenhum documento de identificação.
                </p>
                <button wire:click="closeKycReview"
                    class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition text-sm">
                    Fechar
                </button>
            </div>
        </div>
    @endif

    {{-- Modal: revisão de documentos --}}
    @if($reviewingSubmission)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between p-5 border-b border-gray-100">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Revisão de identidade</h3>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $reviewingSubmission->user->name }}</p>
                    </div>
                    <button wire:click="closeKycReview" class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-gray-100 transition text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-5 space-y-4">
                    <p class="text-sm text-gray-600">Tipo de documento: <strong class="text-gray-800">{{ match($reviewingSubmission->document_type) {
                        'bi'              => 'Bilhete de Identidade',
                        'passport'        => 'Passaporte',
                        'driving_license' => 'Carta de condução',
                        default           => ucfirst($reviewingSubmission->document_type)
                    } }}</strong></p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Frente do documento</p>
                            <a href="{{ $this->kycDocumentUrl($reviewingSubmission->document_front_path) }}" target="_blank"
                                class="block border border-gray-200 rounded-xl overflow-hidden hover:opacity-80 transition">
                                @if(str_ends_with(strtolower($reviewingSubmission->document_front_path), '.pdf'))
                                    <div class="p-4 bg-gray-50 text-center text-sm text-gray-500">Ficheiro PDF — Clique para abrir</div>
                                @else
                                    <img src="{{ $this->kycDocumentUrl($reviewingSubmission->document_front_path) }}" class="w-full object-cover max-h-48" alt="Frente">
                                @endif
                            </a>
                        </div>
                        @if($reviewingSubmission->document_back_path)
                            <div>
                                <p class="text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Verso do documento</p>
                                <a href="{{ $this->kycDocumentUrl($reviewingSubmission->document_back_path) }}" target="_blank"
                                    class="block border border-gray-200 rounded-xl overflow-hidden hover:opacity-80 transition">
                                    @if(str_ends_with(strtolower($reviewingSubmission->document_back_path), '.pdf'))
                                        <div class="p-4 bg-gray-50 text-center text-sm text-gray-500">Ficheiro PDF — Clique para abrir</div>
                                    @else
                                        <img src="{{ $this->kycDocumentUrl($reviewingSubmission->document_back_path) }}" class="w-full object-cover max-h-48" alt="Verso">
                                    @endif
                                </a>
                            </div>
                        @endif
                        @if($reviewingSubmission->selfie_path)
                            <div class="md:col-span-2">
                                <p class="text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Selfie com documento</p>
                                <a href="{{ $this->kycDocumentUrl($reviewingSubmission->selfie_path) }}" target="_blank"
                                    class="block border border-gray-200 rounded-xl overflow-hidden hover:opacity-80 transition">
                                    <img src="{{ $this->kycDocumentUrl($reviewingSubmission->selfie_path) }}" class="w-full object-cover max-h-48" alt="Selfie">
                                </a>
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Observações / motivo (opcional)</label>
                        <textarea wire:model="adminNotes" rows="2" placeholder="Ex.: documento ilegível, expirado, etc."
                            class="block w-full rounded-xl border border-gray-200 py-2.5 px-3 text-sm focus:ring-2 focus:ring-[#0055ff]/30 focus:border-[#0055ff] outline-none"></textarea>
                    </div>

                    <div class="flex gap-3 pt-1">
                        <button wire:click="approveKycSubmission"
                            wire:confirm="Aprovar a verificação de identidade de {{ $reviewingSubmission->user->name }}?"
                            class="flex-1 inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 rounded-xl transition text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            Aprovar
                        </button>
                        <button wire:click="rejectKycSubmission"
                            wire:confirm="Rejeitar a verificação de identidade de {{ $reviewingSubmission->user->name }}?"
                            class="flex-1 inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-xl transition text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Rejeitar
                        </button>
                        <button wire:click="closeKycReview" class="px-4 border border-gray-200 rounded-xl text-sm text-gray-500 hover:bg-gray-50 transition">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Filtros --}}
    <div class="flex flex-wrap gap-3 mb-5 items-center">
        <div class="relative flex-1 min-w-[200px]">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.300ms="search" type="text"
                placeholder="Pesquisar nome ou e-mail..."
                class="w-full pl-9 pr-3 border border-gray-200 rounded-xl py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0055ff]/30 focus:border-[#0055ff]">
        </div>
        <select wire:model.live="roleFilter" class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0055ff]/30 focus:border-[#0055ff]">
            <option value="">Todas as funções</option>
            <option value="admin">Administrador</option>
            <option value="freelancer">Freelancer</option>
            <option value="cliente">Cliente</option>
        </select>
        <select wire:model.live="kycFilter" class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0055ff]/30 focus:border-[#0055ff]">
            <option value="">Todas as verificações</option>
            <option value="pending">Verificação pendente</option>
            <option value="verified">Identidade verificada</option>
            <option value="rejected">Verificação rejeitada</option>
        </select>
        @if($kycFilter || $roleFilter || $search)
            <button wire:click="$set('kycFilter', ''); $set('roleFilter', ''); $set('search', '')" class="btn-outline text-xs">
                Limpar filtros
            </button>
        @endif
    </div>

    {{-- Tabela de utilizadores --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="py-3 px-5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Utilizador</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Função</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Verificação</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Estado</th>
                        <th class="hidden sm:table-cell py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Desde</th>
                        <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Acções</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                    @php
                        $kyc = $user->kyc_status ?? 'pending';
                        $funcaoLabel = match($user->role) {
                            'admin'      => 'Administrador',
                            'freelancer' => 'Freelancer',
                            'cliente'    => 'Cliente',
                            default      => ucfirst($user->role),
                        };
                        $funcaoClasse = match($user->role) {
                            'admin'      => 'bg-purple-100 text-purple-700',
                            'freelancer' => 'bg-[#0055ff]/10 text-[#0055ff]',
                            default      => 'bg-emerald-100 text-emerald-700',
                        };
                        $kycLabel = match($kyc) {
                            'verified' => 'Verificada',
                            'rejected' => 'Rejeitada',
                            default    => 'Pendente',
                        };
                        $kycClasse = match($kyc) {
                            'verified' => 'bg-green-100 text-green-700',
                            'rejected' => 'bg-red-100 text-red-600',
                            default    => 'bg-amber-100 text-amber-700',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50/60 transition-colors {{ $user->is_suspended ? 'opacity-60' : '' }}">

                        {{-- Utilizador --}}
                        <td class="py-3 px-5">
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->avatarUrl() }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-800 truncate leading-tight">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Função --}}
                        <td class="py-3 px-4">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $funcaoClasse }}">
                                {{ $funcaoLabel }}
                            </span>
                        </td>

                        {{-- Verificação de identidade --}}
                        <td class="py-3 px-4">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $kycClasse }}">
                                {{ $kycLabel }}
                            </span>
                        </td>

                        {{-- Estado --}}
                        <td class="py-3 px-4">
                            @if($user->is_suspended)
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-600">Suspenso</span>
                            @else
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Activo</span>
                            @endif
                        </td>

                        {{-- Data de registo --}}
                        <td class="hidden sm:table-cell py-3 px-4 text-xs text-gray-500">
                            {{ $user->created_at->format('d/m/Y') }}
                        </td>

                        {{-- Acções --}}
                        <td class="py-3 px-4">
                            <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                {{-- Documentos de identificação --}}
                                @if($user->role !== 'admin')
                                    <button wire:click="reviewUserKyc({{ $user->id }})"
                                        title="Ver documentos de identificação"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs text-slate-600 border border-slate-200 rounded-lg hover:border-[#0055ff] hover:text-[#0055ff] transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                        Documentos
                                    </button>
                                @endif

                                {{-- Verificar / Rejeitar identidade --}}
                                @if($user->role !== 'admin')
                                    @if($kyc !== 'verified')
                                        <button wire:click="verifyKyc({{ $user->id }})"
                                            title="Aprovar verificação de identidade"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs text-green-700 border border-green-200 bg-green-50 rounded-lg hover:bg-green-100 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                            Aprovar
                                        </button>
                                    @endif
                                    @if($kyc !== 'rejected')
                                        <button wire:click="rejectKyc({{ $user->id }})"
                                            title="Rejeitar verificação de identidade"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs text-red-600 border border-red-200 bg-red-50 rounded-lg hover:bg-red-100 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Rejeitar
                                        </button>
                                    @endif
                                @endif

                                {{-- Suspender / Reactivar --}}
                                @if($user->role !== 'admin')
                                    @if($user->is_suspended)
                                        <button wire:click="approveUser({{ $user->id }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs text-[#0055ff] border border-[#0055ff]/30 bg-[#0055ff]/5 rounded-lg hover:bg-[#0055ff]/10 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1012.728 0M12 3v9"/></svg>
                                            Reactivar
                                        </button>
                                    @else
                                        <button wire:click="suspendUser({{ $user->id }})"
                                            wire:confirm="Suspender o utilizador {{ $user->name }}?"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs text-orange-700 border border-orange-200 bg-orange-50 rounded-lg hover:bg-orange-100 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            Suspender
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-14 text-center">
                            <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                            <p class="text-sm text-gray-400">Nenhum utilizador encontrado.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
</div>
