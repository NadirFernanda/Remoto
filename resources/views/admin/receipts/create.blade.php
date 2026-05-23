@extends('layouts.dashboard')

@section('dashboard-title', 'Gerar Recibo')

@section('dashboard-content')
<div class="max-w-2xl mx-auto"
    x-data="{
        users: {{ $users->toJson() }},
        search: '',
        selectedUser: null,
        open: false,
        get filtered() {
            if (!this.search) return this.users.slice(0, 8);
            const q = this.search.toLowerCase();
            return this.users.filter(u =>
                u.name.toLowerCase().includes(q) || u.email.toLowerCase().includes(q)
            ).slice(0, 8);
        },
        select(user) {
            this.selectedUser = user;
            this.search = user.name;
            this.open = false;
            this.$nextTick(() => {
                if (!document.getElementById('nome-field').value) {
                    document.getElementById('nome-field').value = user.name;
                }
                if (!document.getElementById('email-field').value) {
                    document.getElementById('email-field').value = user.email;
                }
            });
        },
        clear() {
            this.selectedUser = null;
            this.search = '';
        }
    }">

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 text-red-700 rounded-xl border border-red-200 text-sm">
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.recibos.store') }}" enctype="multipart/form-data"
          class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        @csrf

        {{-- Hidden user_id --}}
        <input type="hidden" name="user_id" :value="selectedUser ? selectedUser.id : ''">

        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-100" style="background:linear-gradient(135deg,#f0f9ff,#e0f2fe);">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white" style="background:linear-gradient(135deg,#0070ff,#00baff);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">Dados do Recibo</p>
                    <p class="text-xs text-gray-500">Selecione um utilizador registado ou preencha manualmente</p>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-5">

            {{-- ── Utilizador registado ── --}}
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Utilizador da plataforma</p>

            <div class="relative" @click.outside="open = false">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Pesquisar utilizador
                    <span class="text-gray-400 font-normal">(opcional)</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/>
                        </svg>
                    </div>
                    <input type="text"
                        x-model="search"
                        @focus="open = true"
                        @input="open = true; if (!search) selectedUser = null"
                        placeholder="Nome ou e-mail do utilizador..."
                        autocomplete="off"
                        class="w-full pl-9 pr-10 py-2.5 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] transition"
                        :class="selectedUser ? 'bg-sky-50 border-sky-300' : 'bg-white'">
                    <button type="button" x-show="selectedUser" @click="clear()"
                        class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Dropdown --}}
                <div x-show="open && filtered.length > 0"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="absolute z-30 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                    <template x-for="user in filtered" :key="user.id">
                        <button type="button" @click="select(user)"
                            class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-sky-50 text-left transition"
                            :class="selectedUser && selectedUser.id === user.id ? 'bg-sky-50' : ''">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                                style="background:linear-gradient(135deg,#0070ff,#00baff);"
                                x-text="user.name.charAt(0).toUpperCase()">
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate" x-text="user.name"></p>
                                <p class="text-xs text-gray-400 truncate" x-text="user.email"></p>
                            </div>
                            <svg x-show="selectedUser && selectedUser.id === user.id"
                                class="w-4 h-4 text-sky-500 ml-auto flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8 15.414l-4.707-4.707a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </template>
                </div>

                {{-- No results --}}
                <div x-show="open && search && filtered.length === 0"
                    class="absolute z-30 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg px-4 py-3 text-sm text-gray-400 text-center">
                    Nenhum utilizador encontrado
                </div>
            </div>

            {{-- Selected user badge --}}
            <div x-show="selectedUser" x-transition
                class="flex items-center gap-3 p-3 rounded-xl bg-sky-50 border border-sky-200">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                    style="background:linear-gradient(135deg,#0070ff,#00baff);"
                    x-text="selectedUser ? selectedUser.name.charAt(0).toUpperCase() : ''">
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate" x-text="selectedUser ? selectedUser.name : ''"></p>
                    <p class="text-xs text-sky-600 truncate" x-text="selectedUser ? selectedUser.email : ''"></p>
                </div>
                <span class="ml-auto text-xs font-semibold text-sky-600 bg-sky-100 px-2 py-0.5 rounded-full whitespace-nowrap">Vinculado</span>
            </div>

            <hr class="border-gray-100">

            {{-- ── Identificação do Cliente ── --}}
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Identificação do cliente</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                    <input type="text" name="nome" id="nome-field" value="{{ old('nome') }}"
                           placeholder="Nome completo ou empresa"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIF</label>
                    <input type="text" name="nif" value="{{ old('nif') }}"
                           placeholder="Número de identificação fiscal"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                    <input type="text" name="telefone" value="{{ old('telefone') }}"
                           placeholder="+244 9XX XXX XXX"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                    <input type="email" name="email" id="email-field" value="{{ old('email') }}"
                           placeholder="email@exemplo.com"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Endereço</label>
                    <input type="text" name="endereco" value="{{ old('endereco') }}"
                           placeholder="Rua, cidade, província"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- ── Valor e Período ── --}}
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Valor e período</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Valor (AOA)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-400">Kz</span>
                        <input type="number" name="valor" value="{{ old('valor') }}"
                               placeholder="0.00" step="0.01" min="0"
                               class="w-full pl-9 pr-3 py-2.5 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data de Início</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data de Fim</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Notas --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notas / Descrição</label>
                <textarea name="notes" rows="3"
                          placeholder="Descreva o serviço, produto ou qualquer observação relevante..."
                          class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] resize-none">{{ old('notes') }}</textarea>
            </div>

            {{-- Documento --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Documento anexo (PDF)</label>
                <div class="flex items-center gap-3">
                    <input type="file" name="documento" id="doc-input" accept="application/pdf" class="hidden"
                           onchange="document.getElementById('doc-name').textContent = this.files[0] ? this.files[0].name : 'Nenhum ficheiro'">
                    <button type="button" onclick="document.getElementById('doc-input').click()"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-600 bg-gray-50 hover:bg-gray-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        Escolher PDF
                    </button>
                    <span id="doc-name" class="text-xs text-gray-400">Nenhum ficheiro</span>
                </div>
                <p class="text-xs text-gray-400 mt-1">PDF até 8 MB</p>
            </div>

        </div>

        {{-- Botões --}}
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-end gap-3">
            <a href="{{ route('admin.recibos.index') }}"
               class="px-5 py-2 rounded-xl text-sm font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 transition">
                Cancelar
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2 rounded-xl text-sm font-bold text-white shadow transition"
                    style="background:linear-gradient(135deg,#0070ff,#00baff);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Gerar Recibo
            </button>
        </div>

    </form>
</div>
@endsection
