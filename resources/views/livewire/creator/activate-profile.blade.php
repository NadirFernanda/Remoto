<div class="max-w-2xl mx-auto py-12 px-4">

    @if(session('success'))
        <div class="mb-6 p-3 bg-green-100 text-green-700 rounded-xl text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="text-center mb-10">
        <div class="w-16 h-16 bg-[#00baff]/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-[#00baff]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Ativar Perfil</h1>
        <p class="text-sm text-gray-500 mt-1">
            Na 24Horas Remoto pode ter múltiplos perfis com a mesma conta.
            Os ganhos e dados de cada perfil são visíveis no painel unificado.
        </p>
    </div>

    @if($targetProfile === 'creator')
        {{-- Creator activation form --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-[#00baff] to-purple-500 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Criador / Seguidor</h2>
                    <p class="text-xs text-gray-500">Publique conteúdo exclusivo e monetize através de assinaturas</p>
                </div>
            </div>

            <div class="bg-blue-50 rounded-xl p-4 mb-6 text-sm text-gray-600 space-y-1">
                <p>✦ <strong>3.000 KZS/mês</strong> por assinante — 70% para si, 30% para a plataforma</p>
                <p>✦ Venda infoprodutos com <strong>80% dos ganhos</strong> para si</p>
                <p>✦ Patrocine conteúdos por <strong>600 KZS/dia</strong></p>
                <p>✦ Ganhos unificados com os seus outros perfis</p>
            </div>

            <form wire:submit.prevent="activate" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Categoria do conteúdo *</label>
                    <select wire:model="category"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/40">
                        @foreach($categories as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Bio do Criador</label>
                    <textarea wire:model="bio" rows="3"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-[#00baff]/40"
                        placeholder="Apresente-se ao seu público. O que vai partilhar? Quem é?"></textarea>
                    @error('bio') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-1">{{ strlen($bio) }}/600 caracteres</p>
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-[#00baff] to-cyan-400 text-white font-bold py-3 rounded-xl hover:opacity-90 transition"
                    wire:loading.attr="disabled" wire:loading.class="opacity-70">
                    <span wire:loading.remove>Ativar Perfil de Criador</span>
                    <span wire:loading>A ativar...</span>
                </button>
            </form>
        </div>

    @elseif($targetProfile === 'freelancer')
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
            <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
                <svg class="w-7 h-7 text-[#00baff]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Ativar Perfil Freelancer</h2>
            <p class="text-sm text-gray-500 mb-6">
                Com o perfil Freelancer poderá candidatar-se a projetos, receber propostas e gerir contratos.
                Após ativar, complete o seu perfil para aparecer nas pesquisas.
            </p>
            <form wire:submit.prevent="activate">
                <button type="submit"
                    class="bg-[#00baff] text-white font-bold px-8 py-3 rounded-xl hover:bg-[#009ad6] transition"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>Ativar Perfil Freelancer</span>
                    <span wire:loading>A ativar...</span>
                </button>
            </form>
        </div>

    @elseif($targetProfile === 'cliente')
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
            <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
                <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Ativar Perfil Cliente</h2>
            <p class="text-sm text-gray-500 mb-6">
                Com o perfil Cliente poderá publicar projetos, contratar freelancers e gerir pagamentos.
            </p>
            <form wire:submit.prevent="activate">
                <button type="submit"
                    class="bg-purple-600 text-white font-bold px-8 py-3 rounded-xl hover:bg-purple-700 transition"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>Ativar Perfil Cliente</span>
                    <span wire:loading>A ativar...</span>
                </button>
            </form>
        </div>
    @endif

</div>
