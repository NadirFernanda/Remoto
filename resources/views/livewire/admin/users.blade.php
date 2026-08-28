<div>
    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 text-green-800 rounded-xl border border-green-200 text-sm">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="mb-4 p-3 bg-red-50 text-red-800 rounded-xl border border-red-200 text-sm">{{ session('error') }}</div>
    @endif

    {{-- Esta página só lista clientes e freelancers. Administradores têm uma secção própria. --}}
    @if(auth()->user()->admin_role === null)
        <div class="mb-4 flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-xl text-sm">
            <svg class="w-4 h-4 text-[#0055ff] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0"/>
            </svg>
            <span class="text-blue-900">Esta lista mostra apenas clientes e freelancers.</span>
            <a href="{{ route('admin.managers') }}" class="ml-auto btn-outline text-xs">Gerir administradores →</a>
        </div>
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
                            <tr wire:key="kyc-sub-{{ $sub->id }}" class="hover:bg-gray-50">
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
                            <a href="{{ route('admin.kyc.document', [$reviewingSubmission->id, 'front']) }}" target="_blank"
                                class="block border border-gray-200 rounded-xl overflow-hidden hover:opacity-80 transition">
                                @if(str_ends_with(strtolower($reviewingSubmission->document_front_path), '.pdf'))
                                    <div class="p-4 bg-gray-50 text-center text-sm text-gray-500">Ficheiro PDF — Clique para abrir</div>
                                @else
                                    <img src="{{ route('admin.kyc.document', [$reviewingSubmission->id, 'front']) }}" class="w-full object-cover max-h-48" alt="Frente">
                                @endif
                            </a>
                        </div>
                        @if($reviewingSubmission->document_back_path)
                            <div>
                                <p class="text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Verso do documento</p>
                                <a href="{{ route('admin.kyc.document', [$reviewingSubmission->id, 'back']) }}" target="_blank"
                                    class="block border border-gray-200 rounded-xl overflow-hidden hover:opacity-80 transition">
                                    @if(str_ends_with(strtolower($reviewingSubmission->document_back_path), '.pdf'))
                                        <div class="p-4 bg-gray-50 text-center text-sm text-gray-500">Ficheiro PDF — Clique para abrir</div>
                                    @else
                                        <img src="{{ route('admin.kyc.document', [$reviewingSubmission->id, 'back']) }}" class="w-full object-cover max-h-48" alt="Verso">
                                    @endif
                                </a>
                            </div>
                        @endif
                        @if($reviewingSubmission->selfie_path)
                            <div class="md:col-span-2">
                                <p class="text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Selfie com documento</p>
                                <a href="{{ route('admin.kyc.document', [$reviewingSubmission->id, 'selfie']) }}" target="_blank"
                                    class="block border border-gray-200 rounded-xl overflow-hidden hover:opacity-80 transition">
                                    <img src="{{ route('admin.kyc.document', [$reviewingSubmission->id, 'selfie']) }}" class="w-full object-cover max-h-48" alt="Selfie">
                                </a>
                            </div>
                        @endif
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-sm font-semibold text-gray-700">Nome completo (conforme consta no documento)</label>
                            @if($verifiedNameFromOcr)
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-full px-2 py-0.5">
                                    Sugestão automática (Google Vision)
                                </span>
                            @endif
                        </div>
                        <input type="text" wire:model="verifiedName"
                            class="block w-full rounded-xl border border-gray-200 py-2.5 px-3 text-sm focus:ring-2 focus:ring-[#0055ff]/30 focus:border-[#0055ff] outline-none">
                        @error('verifiedName') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
                        <p class="text-xs text-gray-400 mt-1">
                            @if($verifiedNameFromOcr)
                                Sugestão lida automaticamente do documento — confirme sempre com a foto acima antes de aprovar.
                            @else
                                Não foi possível ler o nome automaticamente no documento — confirme ou corrija manualmente. Este é o nome usado para validar a conta bancária que o utilizador registar mais tarde.
                            @endif
                        </p>
                        @if($ocrRawText)
                            <details class="mt-2">
                                <summary class="text-xs text-gray-400 cursor-pointer hover:text-gray-600">Ver texto extraído do documento</summary>
                                <pre class="mt-1 text-xs text-gray-500 bg-gray-50 border border-gray-100 rounded-xl p-2 whitespace-pre-wrap">{{ $ocrRawText }}</pre>
                            </details>
                        @endif
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-sm font-semibold text-gray-700">Número do BI / documento</label>
                            @if($documentNumberFromOcr)
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-full px-2 py-0.5">
                                    Sugestão automática (Google Vision)
                                </span>
                            @endif
                        </div>
                        <input type="text" wire:model="documentNumber"
                            class="block w-full rounded-xl border border-gray-200 py-2.5 px-3 text-sm focus:ring-2 focus:ring-[#0055ff]/30 focus:border-[#0055ff] outline-none">
                        @error('documentNumber') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
                        <p class="text-xs text-gray-400 mt-1">
                            @if($documentNumberFromOcr)
                                Sugestão lida automaticamente do documento — confirme com a foto acima.
                            @else
                                Não foi possível ler o número automaticamente — pode confirmar/preencher manualmente, ou deixar em branco.
                            @endif
                        </p>
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

    {{-- Barra de acções em lote --}}
    @if(count($selected) > 0)
        <div class="flex items-center gap-3 mb-4 px-4 py-3 bg-[#0055ff]/5 border border-[#0055ff]/20 rounded-xl">
            <span class="text-sm font-semibold text-[#0055ff]">{{ count($selected) }} seleccionado(s)</span>
            <div class="ml-auto flex items-center gap-2">
                <button wire:click="bulkSuspendSelected" wire:confirm="Suspender {{ count($selected) }} utilizador(es)?"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-orange-700 border border-orange-200 bg-orange-50 rounded-lg hover:bg-orange-100 transition">
                    Suspender seleccionados
                </button>
                @if(auth()->user()->admin_role === null)
                    <button wire:click="bulkDeleteSelected"
                        wire:confirm="Eliminar PERMANENTEMENTE {{ count($selected) }} utilizador(es)? Só quem não tiver saldo, projectos ou assinaturas associadas é eliminado — o resto é ignorado. Esta acção não pode ser desfeita."
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-red-700 border border-red-200 bg-red-50 rounded-lg hover:bg-red-100 transition">
                        Eliminar seleccionados
                    </button>
                    <button wire:click="bulkDeleteSelected(true)"
                        wire:confirm="⚠ FORÇAR ELIMINAÇÃO de {{ count($selected) }} utilizador(es)?&#10;&#10;Isto IGNORA a protecção de saldo/projectos/assinaturas e apaga tudo PERMANENTEMENTE — incluindo saldo em carteira real, se existir. Não há forma de recuperar depois.&#10;&#10;Só confirme se tiver a certeza absoluta de que nenhuma destas contas tem dinheiro ou actividade real."
                        title="Ignora a protecção de saldo/projectos/assinaturas — usar com muito cuidado"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-red-700 rounded-lg hover:bg-red-800 transition">
                        ⚠ Forçar eliminação
                    </button>
                @endif
                <button wire:click="$set('selected', []); $set('selectPage', false)" class="text-xs text-gray-400 hover:text-gray-600 transition px-2">
                    Cancelar
                </button>
            </div>
        </div>
    @endif

    {{-- Tabela de utilizadores --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="py-3 pl-5 pr-2 text-left w-8">
                            <input type="checkbox" wire:model.live="selectPage"
                                class="w-4 h-4 rounded border-gray-300 text-[#0055ff] focus:ring-[#0055ff]/30 cursor-pointer">
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Utilizador</th>
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
                            'admin'      => 'bg-blue-100 text-[#0055ff]',
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
                    <tr wire:key="admin-user-{{ $user->id }}" class="hover:bg-gray-50 transition-colors {{ $user->is_suspended ? 'opacity-60' : '' }}">

                        {{-- Seleccionar --}}
                        <td class="py-3 pl-5 pr-2">
                            @if($user->id !== auth()->id())
                                <input type="checkbox" wire:model.live="selected" value="{{ $user->id }}"
                                    class="w-4 h-4 rounded border-gray-300 text-[#0055ff] focus:ring-[#0055ff]/30 cursor-pointer">
                            @endif
                        </td>

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
                        <td class="py-3 px-4 text-right">
                            @if($user->role !== 'admin')
                                <div x-data="{
                                        open: false,
                                        top: 0,
                                        left: 0,
                                        toggle() {
                                            if (this.open) { this.open = false; return; }
                                            const r = this.$refs.trigger.getBoundingClientRect();
                                            this.top  = r.bottom + window.scrollY + 4;
                                            this.left = r.right + window.scrollX - 208;
                                            this.open = true;
                                        }
                                     }"
                                     @keydown.escape.window="open = false"
                                     class="inline-block text-left">
                                    <button x-ref="trigger" @click="toggle()"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6h.008v.008H12V6zm0 6h.008v.008H12V12zm0 6h.008v.008H12V18z"/></svg>
                                    </button>

                                    <template x-teleport="body">
                                    <div x-show="open" x-cloak x-transition @click.outside="open = false"
                                        :style="`position:absolute; top:${top}px; left:${left}px;`"
                                        class="w-52 py-1.5 rounded-xl shadow-2xl z-[100]"
                                        style="background:#131f35; border:1px solid rgba(255,255,255,.1);">

                                        <button wire:click="reviewUserKyc({{ $user->id }})" @click="open = false"
                                            class="w-full flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-200 hover:bg-white/5 transition text-left">
                                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                            Ver documentos
                                        </button>

                                        @if($kyc !== 'verified')
                                            <button wire:click="verifyKyc({{ $user->id }})" @click="open = false"
                                                class="w-full flex items-center gap-2.5 px-3.5 py-2 text-sm text-emerald-400 hover:bg-white/5 transition text-left">
                                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                Aprovar identidade
                                            </button>
                                        @endif
                                        @if($kyc !== 'rejected')
                                            <button wire:click="rejectKyc({{ $user->id }})" @click="open = false"
                                                wire:confirm="Rejeitar a verificação de identidade de {{ $user->name }}?"
                                                class="w-full flex items-center gap-2.5 px-3.5 py-2 text-sm text-red-400 hover:bg-white/5 transition text-left">
                                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                Rejeitar identidade
                                            </button>
                                        @endif

                                        <div class="my-1 border-t border-white/10"></div>

                                        @if($user->is_suspended)
                                            <button wire:click="approveUser({{ $user->id }})" @click="open = false"
                                                class="w-full flex items-center gap-2.5 px-3.5 py-2 text-sm text-[#5b9dff] hover:bg-white/5 transition text-left">
                                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1012.728 0M12 3v9"/></svg>
                                                Reactivar conta
                                            </button>
                                        @else
                                            <button wire:click="suspendUser({{ $user->id }})" @click="open = false"
                                                wire:confirm="Suspender o utilizador {{ $user->name }}?"
                                                class="w-full flex items-center gap-2.5 px-3.5 py-2 text-sm text-amber-400 hover:bg-white/5 transition text-left">
                                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                Suspender conta
                                            </button>
                                        @endif

                                        {{-- Eliminar — só Admin Master, só contas sem actividade real (útil para limpar contas de teste) --}}
                                        @if(auth()->user()->admin_role === null)
                                            <div class="my-1 border-t border-white/10"></div>
                                            <button wire:click="deleteUser({{ $user->id }})" @click="open = false"
                                                wire:confirm="Eliminar PERMANENTEMENTE o utilizador &quot;{{ $user->name }}&quot; ({{ $user->email }})? Só é possível se não tiver saldo, projectos ou assinaturas associadas. Esta acção não pode ser desfeita."
                                                title="Só contas sem actividade — use Suspender para contas reais"
                                                class="w-full flex items-center gap-2.5 px-3.5 py-2 text-sm font-semibold text-red-400 hover:bg-red-500/10 transition text-left">
                                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Eliminar conta
                                            </button>
                                            <button wire:click="deleteUser({{ $user->id }}, true)" @click="open = false"
                                                wire:confirm="⚠ FORÇAR ELIMINAÇÃO de &quot;{{ $user->name }}&quot; ({{ $user->email }})?&#10;&#10;Isto IGNORA a protecção de saldo/projectos/assinaturas e apaga tudo PERMANENTEMENTE — incluindo saldo em carteira real, se existir. Não há forma de recuperar depois."
                                                title="Ignora a protecção de saldo/projectos/assinaturas — usar com muito cuidado"
                                                class="w-full flex items-center gap-2.5 px-3.5 py-2 text-xs font-bold text-white bg-red-700/90 hover:bg-red-700 transition text-left">
                                                ⚠ Forçar eliminação
                                            </button>
                                        @endif
                                    </div>
                                    </template>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-14 text-center">
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
