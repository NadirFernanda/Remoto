<div x-data="{ confirmDelete: null }" class="space-y-6">

    {{-- Flash --}}
    @if(session('success'))
        <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-2xl text-sm text-green-800">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-2xl text-sm text-red-800">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- ── HEADER ─────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-6 bg-gradient-to-r from-slate-900 to-slate-700 rounded-2xl text-white">
        <div>
            <h1 class="text-xl font-bold tracking-tight">Gestão de Administradores</h1>
            <p class="text-slate-300 text-sm mt-0.5">Cadastre, edite e defina permissões de cada administrador da plataforma.</p>
        </div>
        <button wire:click="openCreate"
            class="inline-flex items-center gap-2 bg-[#00baff] hover:bg-[#009edf] text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Novo Administrador
        </button>
    </div>

    {{-- ── FILTERS ─────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row gap-3 bg-white border border-gray-100 rounded-2xl p-4">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.35-4.35"/></svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Pesquisar por nome, e-mail ou cargo…"
                class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] outline-none">
        </div>
        <select wire:model.live="roleFilter" class="rounded-xl border border-gray-200 text-sm px-3 py-2.5 focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] outline-none">
            <option value="">Todos os perfis</option>
            <option value="master">Admin Master</option>
            <option value="financeiro">Diretor Financeiro</option>
            <option value="gestor">Gestor de Operações</option>
            <option value="suporte">Gestor de Suporte</option>
            <option value="analista">Analista de Dados</option>
        </select>
    </div>

    {{-- ── ADMIN TABLE ──────────────────────────────────────────────────────── --}}
    <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="py-3 px-5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Administrador</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Perfil / Cargo</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">E-mail Corporativo</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Contacto</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Módulos com Acesso</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Cadastrado em</th>
                        <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Acções</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($admins as $admin)
                        @php
                            $isMaster = ($admin->admin_role === null || $admin->admin_role === 'master');
                            $roleColors = [
                                'master'     => 'bg-purple-100 text-purple-800',
                                'financeiro' => 'bg-emerald-100 text-emerald-800',
                                'gestor'     => 'bg-blue-100 text-blue-800',
                                'suporte'    => 'bg-orange-100 text-orange-800',
                                'analista'   => 'bg-cyan-100 text-cyan-800',
                            ];
                            $roleKey   = $admin->admin_role ?? 'master';
                            $roleClass = $roleColors[$roleKey] ?? 'bg-gray-100 text-gray-700';
                            $moduleCount = $isMaster ? count($modules) : $admin->adminPermissions->where('access', '!=', 'none')->count();
                        @endphp
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            {{-- Name + avatar --}}
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-slate-700 to-slate-500 flex items-center justify-center text-white text-sm font-bold flex-shrink-0 overflow-hidden">
                                        @if($admin->profile_photo)
                                            <img src="{{ $admin->avatarUrl() }}" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr($admin->name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-800 leading-tight">{{ $admin->name }}</div>
                                        <div class="text-xs text-gray-400">{{ $admin->email }}</div>
                                    </div>
                                </div>
                            </td>
                            {{-- Role badge + cargo --}}
                            <td class="py-3.5 px-4">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $roleClass }}">
                                    {{ $admin->adminRoleLabel() }}
                                </span>
                                @if($admin->admin_cargo)
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $admin->admin_cargo }}</div>
                                @endif
                            </td>
                            {{-- Corporate email --}}
                            <td class="py-3.5 px-4 text-gray-600">
                                {{ $admin->admin_corporate_email ?? '—' }}
                            </td>
                            {{-- Phone --}}
                            <td class="py-3.5 px-4 text-gray-600">
                                {{ $admin->admin_phone ?? '—' }}
                            </td>
                            {{-- Module count badge --}}
                            <td class="py-3.5 px-4">
                                @if($isMaster)
                                    <span class="inline-flex items-center gap-1 text-xs bg-purple-50 text-purple-700 border border-purple-200 rounded-full px-2.5 py-0.5 font-medium">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.403 12.652a3 3 0 000-5.304 3 3 0 00-3.75-3.751 3 3 0 00-5.305 0 3 3 0 00-3.751 3.75 3 3 0 000 5.305 3 3 0 003.75 3.751 3 3 0 005.305 0 3 3 0 003.751-3.75zm-2.546-4.46a.75.75 0 00-1.214-.883l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                                        Acesso Total
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs bg-slate-50 text-slate-600 border border-slate-200 rounded-full px-2.5 py-0.5 font-medium">
                                        {{ $moduleCount }}/{{ count($modules) }} módulos
                                    </span>
                                @endif
                            </td>
                            {{-- Created at --}}
                            <td class="py-3.5 px-4 text-gray-500 text-xs">
                                {{ $admin->created_at->format('d/m/Y') }}
                            </td>
                            {{-- Actions --}}
                            <td class="py-3.5 px-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="openEdit({{ $admin->id }})"
                                        class="inline-flex items-center gap-1.5 text-xs text-slate-600 hover:text-[#00baff] hover:bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 012.828 2.828L11.414 16H9v-2.586z"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18"/></svg>
                                        Editar
                                    </button>
                                    @if(!$isMaster)
                                        <button
                                            x-on:click="confirmDelete = {{ $admin->id }}"
                                            class="inline-flex items-center gap-1.5 text-xs text-red-500 hover:text-red-700 hover:bg-red-50 border border-red-200 rounded-lg px-3 py-1.5 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Remover
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                <p class="font-medium">Nenhum administrador encontrado</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($admins->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">{{ $admins->links() }}</div>
        @endif
    </div>

    {{-- ── CONFIRM DELETE DIALOG ────────────────────────────────────────────── --}}
    <div x-show="confirmDelete !== null" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div @click.outside="confirmDelete = null"
            class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Remover Administrador</h3>
                    <p class="text-sm text-gray-500 mt-1">O administrador será removido do painel. Esta acção é registada na auditoria.</p>
                </div>
            </div>
            <div class="flex gap-3 mt-5">
                <button @click="confirmDelete = null" class="flex-1 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition">Cancelar</button>
                <button @click="$wire.deleteAdmin(confirmDelete); confirmDelete = null"
                    class="flex-1 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-white text-sm font-semibold transition">
                    Remover
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
         MODAL: CREATE / EDIT ADMIN
         ═══════════════════════════════════════════════════════════════════ --}}
    @if($modalMode === 'create' || $modalMode === 'edit')
        <div class="fixed inset-0 z-50 flex items-start justify-center bg-black/50 backdrop-blur-sm overflow-y-auto py-6 px-4">
            <div wire:click.stop class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl flex flex-col max-h-[92vh]">

                {{-- Modal header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-slate-900 flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $modalMode === 'create' ? 'M12 4v16m8-8H4' : 'M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 012.828 2.828L11.414 16H9v-2.586z' }}"/>
                            </svg>
                        </div>
                        <h2 class="font-bold text-gray-900">
                            {{ $modalMode === 'create' ? 'Novo Administrador' : 'Editar Administrador' }}
                        </h2>
                    </div>
                    <button wire:click="closeModal" class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-gray-100 transition text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Tab nav --}}
                <div class="flex border-b border-gray-100 overflow-x-auto">
                    @foreach([
                        'perfil'        => ['icon' => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0', 'label' => 'Perfil'],
                        'permissoes'    => ['icon' => 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z', 'label' => 'Permissões'],
                        'seguranca'     => ['icon' => 'M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z', 'label' => 'Segurança'],
                        'notificacoes'  => ['icon' => 'M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0', 'label' => 'Notificações'],
                    ] as $tab => $info)
                        <button wire:click="$set('permTab', '{{ $tab }}')"
                            class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap transition border-b-2
                                {{ $permTab === $tab ? 'border-[#00baff] text-[#00baff]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $info['icon'] }}"/>
                            </svg>
                            {{ $info['label'] }}
                        </button>
                    @endforeach
                </div>

                {{-- Tab content --}}
                <div class="p-6 space-y-5 overflow-y-auto flex-1">

                    {{-- ── TAB: PERFIL ─────────────────────────────────────── --}}
                    @if($permTab === 'perfil')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Nome Completo *</label>
                                <input wire:model="name" type="text" placeholder="Ex: Mariana Costa"
                                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] outline-none">
                                @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">E-mail de Login *</label>
                                <input wire:model="email" type="email" placeholder="login@plataforma.com"
                                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] outline-none">
                                @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">E-mail Corporativo</label>
                                <input wire:model="corporateEmail" type="email" placeholder="nome@24horas.ao"
                                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] outline-none">
                                @error('corporateEmail') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Contacto / Telefone</label>
                                <input wire:model="phone" type="text" placeholder="+244 9XX XXX XXX"
                                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] outline-none">
                                @error('phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Cargo / Função</label>
                                <input wire:model="cargo" type="text" placeholder="Ex: Diretor Financeiro"
                                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] outline-none">
                                @error('cargo') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wide">Perfil de Acesso *</label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    @foreach([
                                        'master'     => ['label' => 'Admin Master', 'desc' => 'Acesso total ao sistema', 'color' => 'purple'],
                                        'financeiro' => ['label' => 'Diretor Financeiro', 'desc' => 'Módulos financeiros', 'color' => 'emerald'],
                                        'gestor'     => ['label' => 'Gestor de Operações', 'desc' => 'Disputas, utilizadores', 'color' => 'blue'],
                                        'suporte'    => ['label' => 'Gestor de Suporte', 'desc' => 'Atendimento e disputas', 'color' => 'orange'],
                                        'analista'   => ['label' => 'Analista de Dados', 'desc' => 'Relatórios e auditoria', 'color' => 'cyan'],
                                    ] as $roleVal => $info)
                                        @php
                                            $colorMap = [
                                                'purple'  => 'border-purple-300 bg-purple-50 ring-purple-400',
                                                'emerald' => 'border-emerald-300 bg-emerald-50 ring-emerald-400',
                                                'blue'    => 'border-blue-300 bg-blue-50 ring-blue-400',
                                                'orange'  => 'border-orange-300 bg-orange-50 ring-orange-400',
                                                'cyan'    => 'border-cyan-300 bg-cyan-50 ring-cyan-400',
                                            ];
                                            $activeClass  = $adminRole === $roleVal ? 'ring-2 ' . $colorMap[$info['color']] : 'border-gray-200 bg-white hover:bg-gray-50';
                                        @endphp
                                        <label class="cursor-pointer border rounded-xl p-3 transition {{ $activeClass }}">
                                            <input type="radio" wire:model.live="adminRole" value="{{ $roleVal }}" class="sr-only">
                                            <div class="font-semibold text-xs text-gray-800 leading-tight">{{ $info['label'] }}</div>
                                            <div class="text-[11px] text-gray-500 mt-0.5">{{ $info['desc'] }}</div>
                                        </label>
                                    @endforeach
                                </div>
                                @error('adminRole') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            {{-- Password --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                                    Senha {{ $modalMode === 'edit' ? '(deixe em branco para manter)' : '*' }}
                                </label>
                                <input wire:model="password" type="password" placeholder="Mínimo 10 caracteres"
                                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] outline-none">
                                @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Confirmar Senha {{ $modalMode === 'create' ? '*' : '' }}</label>
                                <input wire:model="passwordConfirm" type="password" placeholder="Repita a senha"
                                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] outline-none">
                                @error('passwordConfirm') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>

                    {{-- ── TAB: PERMISSÕES ─────────────────────────────────── --}}
                    @elseif($permTab === 'permissoes')
                        @if($adminRole === 'master')
                            <div class="flex flex-col items-center justify-center py-10 text-center">
                                <div class="w-14 h-14 rounded-2xl bg-purple-100 flex items-center justify-center mb-4">
                                    <svg class="w-7 h-7 text-purple-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.403 12.652a3 3 0 000-5.304 3 3 0 00-3.75-3.751 3 3 0 00-5.305 0 3 3 0 00-3.751 3.75 3 3 0 000 5.305 3 3 0 003.75 3.751 3 3 0 005.305 0 3 3 0 003.751-3.75zm-2.546-4.46a.75.75 0 00-1.214-.883l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                                </div>
                                <h3 class="font-bold text-gray-800 text-lg">Acesso Total</h3>
                                <p class="text-sm text-gray-500 mt-1 max-w-xs">O Admin Master tem acesso irrestrito a todos os módulos da plataforma. As permissões não são aplicáveis.</p>
                            </div>
                        @else
                            <div class="flex items-center justify-between mb-4">
                                <p class="text-sm text-gray-600">Configure o nível de acesso por módulo. <span class="font-medium text-gray-800">Perfil: {{ $adminRole }}</span></p>
                                <button wire:click="applyRoleDefaults" class="text-xs text-[#00baff] hover:underline font-medium">↺ Aplicar padrões do perfil</button>
                            </div>
                            <div class="space-y-2">
                                <div class="grid grid-cols-12 gap-2 px-3 py-1.5 text-[11px] font-semibold text-gray-400 uppercase tracking-wide bg-gray-50 rounded-lg">
                                    <div class="col-span-5">Módulo</div>
                                    <div class="col-span-7 grid grid-cols-4 text-center">
                                        <span>Sem acesso</span>
                                        <span>Leitura</span>
                                        <span>Escrita</span>
                                        <span>Total</span>
                                    </div>
                                </div>
                                @foreach($modules as $modKey => $modLabel)
                                    @php $currentAccess = $permissions[$modKey] ?? 'none'; @endphp
                                    <div class="grid grid-cols-12 gap-2 items-center px-3 py-2.5 rounded-xl border border-gray-100 hover:bg-gray-50/50 transition">
                                        <div class="col-span-5 text-sm font-medium text-gray-700">{{ $modLabel }}</div>
                                        <div class="col-span-7 grid grid-cols-4 gap-1">
                                            @foreach(['none' => 'bg-gray-200', 'read' => 'bg-blue-400', 'write' => 'bg-amber-400', 'full' => 'bg-emerald-500'] as $level => $color)
                                                <label class="flex flex-col items-center cursor-pointer group">
                                                    <input type="radio" wire:model="permissions.{{ $modKey }}" value="{{ $level }}" class="sr-only">
                                                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition
                                                        {{ $currentAccess === $level ? $color . ' border-transparent' : 'border-gray-300 group-hover:border-gray-400' }}">
                                                        @if($currentAccess === $level)
                                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                        @endif
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="flex gap-3 mt-3 flex-wrap">
                                @foreach(['none' => ['bg-gray-200', 'Sem acesso'], 'read' => ['bg-blue-400', 'Leitura'], 'write' => ['bg-amber-400', 'Escrita'], 'full' => ['bg-emerald-500', 'Total']] as $lv => [$clr, $lbl])
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-3 h-3 rounded-full {{ $clr }}"></div>
                                        <span class="text-xs text-gray-500">{{ $lbl }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                    {{-- ── TAB: SEGURANÇA ──────────────────────────────────── --}}
                    @elseif($permTab === 'seguranca')
                        <div class="space-y-4">
                            {{-- 2FA --}}
                            <div class="flex items-start justify-between p-4 rounded-xl border border-gray-100 bg-gray-50/50">
                                <div>
                                    <div class="font-semibold text-sm text-gray-800">Autenticação 2 Factores (2FA)</div>
                                    <div class="text-xs text-gray-500 mt-0.5">Obrigar este administrador a activar 2FA no próximo login.</div>
                                </div>
                                <button type="button" wire:click="$toggle('twoFactorRequired')"
                                    class="relative ml-4 flex-shrink-0 w-11 h-6 rounded-full transition-colors focus:outline-none"
                                    style="{{ $twoFactorRequired ? 'background:#00baff' : 'background:#d1d5db' }}">
                                    <span class="absolute top-0.5 w-5 h-5 bg-white rounded-full shadow transition-all"
                                        style="{{ $twoFactorRequired ? 'left:22px' : 'left:2px' }}"></span>
                                </button>
                            </div>

                            {{-- Force password change --}}
                            <div class="flex items-start justify-between p-4 rounded-xl border border-gray-100 bg-gray-50/50">
                                <div>
                                    <div class="font-semibold text-sm text-gray-800">Forçar Mudança de Senha</div>
                                    <div class="text-xs text-gray-500 mt-0.5">Obrigar este admin a alterar a senha no próximo acesso.</div>
                                </div>
                                <button type="button" wire:click="$toggle('forcePasswordChange')"
                                    class="relative ml-4 flex-shrink-0 w-11 h-6 rounded-full transition-colors focus:outline-none"
                                    style="{{ $forcePasswordChange ? 'background:#00baff' : 'background:#d1d5db' }}">
                                    <span class="absolute top-0.5 w-5 h-5 bg-white rounded-full shadow transition-all"
                                        style="{{ $forcePasswordChange ? 'left:22px' : 'left:2px' }}"></span>
                                </button>
                            </div>

                            {{-- Session timeout --}}
                            <div class="p-4 rounded-xl border border-gray-100 bg-gray-50/50">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="font-semibold text-sm text-gray-800">Tempo de Sessão</div>
                                        <div class="text-xs text-gray-500 mt-0.5">Encerrar sessão após inactividade.</div>
                                    </div>
                                    <button type="button" wire:click="$toggle('sessionTimeoutEnabled')"
                                        class="relative ml-4 flex-shrink-0 w-11 h-6 rounded-full transition-colors focus:outline-none"
                                        style="{{ $sessionTimeoutEnabled ? 'background:#00baff' : 'background:#d1d5db' }}">
                                        <span class="absolute top-0.5 w-5 h-5 bg-white rounded-full shadow transition-all"
                                            style="{{ $sessionTimeoutEnabled ? 'left:22px' : 'left:2px' }}"></span>
                                    </button>
                                </div>
                                @if($sessionTimeoutEnabled)
                                    <div class="mt-3">
                                        <label class="text-xs text-gray-600 font-medium">Minutos de inactividade</label>
                                        <input wire:model="sessionTimeoutMinutes" type="number" min="5" max="480"
                                            class="mt-1.5 w-32 rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] outline-none">
                                    </div>
                                @endif
                            </div>

                            {{-- IP restriction --}}
                            <div class="p-4 rounded-xl border border-gray-100 bg-gray-50/50">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="font-semibold text-sm text-gray-800">Restrição por IP</div>
                                        <div class="text-xs text-gray-500 mt-0.5">Permitir acesso apenas a partir dos IPs definidos.</div>
                                    </div>
                                    <button type="button" wire:click="$toggle('ipRestriction')"
                                        class="relative ml-4 flex-shrink-0 w-11 h-6 rounded-full transition-colors focus:outline-none"
                                        style="{{ $ipRestriction ? 'background:#00baff' : 'background:#d1d5db' }}">
                                        <span class="absolute top-0.5 w-5 h-5 bg-white rounded-full shadow transition-all"
                                            style="{{ $ipRestriction ? 'left:22px' : 'left:2px' }}"></span>
                                    </button>
                                </div>
                                @if($ipRestriction)
                                    <div class="mt-3">
                                        <label class="text-xs text-gray-600 font-medium">IPs Permitidos (um por linha ou separados por vírgula)</label>
                                        <textarea wire:model="allowedIps" rows="3" placeholder="192.168.1.1&#10;10.0.0.1"
                                            class="mt-1.5 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] outline-none resize-none"></textarea>
                                    </div>
                                @endif
                            </div>
                        </div>

                    {{-- ── TAB: NOTIFICAÇÕES ───────────────────────────────── --}}
                    @elseif($permTab === 'notificacoes')
                        <div class="space-y-3">
                            <p class="text-sm text-gray-600">Selecione os eventos sobre os quais este administrador deve ser notificado.</p>

                            @foreach([
                                'notifyNewUser'              => 'Novo utilizador registado',
                                'notifyNewDispute'           => 'Nova disputa aberta',
                                'notifyKycPending'           => 'KYC pendente de revisão',
                                'notifyPayoutRequest'        => 'Novo pedido de saque',
                                'notifyHighValueTransaction' => 'Transacção de alto valor',
                                'notifySystemError'          => 'Erro crítico no sistema',
                                'notifyDailyReport'          => 'Relatório diário automático',
                            ] as $prop => $label)
                                <label class="flex items-center justify-between p-3.5 rounded-xl border border-gray-100 cursor-pointer hover:bg-gray-50/50 transition">
                                    <span class="text-sm text-gray-700">{{ $label }}</span>
                                    <input type="checkbox" wire:model="{{ $prop }}"
                                        class="w-4 h-4 rounded border-gray-300 text-[#00baff] accent-[#00baff]">
                                </label>
                            @endforeach

                            <div class="p-4 rounded-xl border border-gray-100 bg-gray-50/50 mt-2">
                                <label class="block text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wide">Canal de Notificação</label>
                                <div class="flex gap-3 flex-wrap">
                                    @foreach(['email' => 'E-mail', 'system' => 'Painel', 'both' => 'Ambos'] as $val => $lbl)
                                        <label class="flex items-center gap-2 cursor-pointer p-2.5 rounded-xl border transition
                                            {{ $notifyChannel === $val ? 'border-[#00baff] bg-sky-50' : 'border-gray-200 hover:bg-gray-50' }}">
                                            <input type="radio" wire:model="notifyChannel" value="{{ $val }}" class="accent-[#00baff]">
                                            <span class="text-sm text-gray-700">{{ $lbl }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                </div>

                {{-- Modal footer --}}
                <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl">
                    <button wire:click="closeModal"
                        class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-100 rounded-xl px-4 py-2.5 transition">
                        Cancelar
                    </button>
                    <div class="flex gap-3">
                        @php
                            $tabs  = ['perfil','permissoes','seguranca','notificacoes'];
                            $idx   = (int) array_search($permTab, $tabs);
                            $isLast  = $permTab === 'notificacoes';
                            $isFirst = $permTab === 'perfil';
                        @endphp
                        @if(!$isFirst)
                            <button wire:click="$set('permTab', '{{ $tabs[$idx - 1] }}')"
                                class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-100 rounded-xl px-4 py-2.5 transition">
                                ← Anterior
                            </button>
                        @endif
                        @if(!$isLast)
                            <button wire:click="$set('permTab', '{{ $tabs[$idx + 1] }}')"
                                class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-100 rounded-xl px-4 py-2.5 transition">
                                Próximo →
                            </button>
                        @endif
                        @if($permTab === 'notificacoes')
                            <button wire:click="saveAdmin" wire:loading.attr="disabled" wire:loading.class="opacity-60 cursor-not-allowed"
                                class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-100 rounded-xl px-4 py-2.5 transition">
                                <span wire:loading.remove wire:target="saveAdmin">
                                    {{ $modalMode === 'create' ? 'Criar Administrador' : 'Guardar Alterações' }}
                                </span>
                                <span wire:loading wire:target="saveAdmin">A guardar…</span>
                            </button>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    @endif

</div>
