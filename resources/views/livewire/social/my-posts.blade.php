<div class="max-w-5xl mx-auto space-y-6">

    {{-- Alerts --}}
    @if(session('success'))
        <div class="px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if($saqueMsg)
        <div class="px-4 py-3 rounded-xl text-sm font-medium border
            {{ $saqueMsgType === 'success' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200' }}">
            {{ $saqueMsg }}
        </div>
    @endif

    {{-- Header --}}
    <div class="bg-gradient-to-r from-[#00baff] to-[#0095cc] rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-2xl font-extrabold">Minhas Publicações</h2>
                <p class="text-sm text-white/75 mt-1">Gere todo o seu conteúdo publicado</p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                {{-- Earnings badge --}}
                <div class="flex items-center gap-3 bg-white/15 border border-white/30 rounded-xl px-4 py-2.5">
                    <div>
                        <p class="text-xs font-bold text-white/70 uppercase tracking-wide">Ganhos</p>
                        <p class="text-lg font-black text-white">Kz {{ number_format($saldoPublicacoesDisponivel, 2, ',', '.') }}</p>
                    </div>
                    @if($sakePendentePublicacoes)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-yellow-400/20 text-yellow-100 text-xs font-bold whitespace-nowrap">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Saque pendente
                        </span>
                    @else
                        <button wire:click="abrirSaque"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold transition whitespace-nowrap">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Sacar
                        </button>
                    @endif
                </div>
                {{-- New post button --}}
                <a href="{{ route('social.create') }}"
                   class="inline-flex items-center gap-1.5 bg-white/15 hover:bg-white/25 border border-white/30 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nova publicação
                </a>
            </div>
        </div>
    </div>

    {{-- Posts card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">

        {{-- Filter tabs --}}
        <div class="flex gap-1 p-4 border-b border-gray-100">
            @foreach(['all' => 'Todas', 'active' => 'Activas', 'archived' => 'Arquivadas'] as $val => $label)
                <button wire:click="$set('filter', '{{ $val }}')"
                    class="px-4 py-1.5 rounded-xl text-sm font-semibold transition
                        {{ $filter === $val ? 'bg-[#00baff] text-white' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Posts list --}}
        <div class="divide-y divide-gray-50">
        @forelse($posts as $post)
            @php
                $typeIcons = [
                    'text'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/>',
                    'image'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>',
                    'video'  => '<path stroke-linecap="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/>',
                    'audio'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a2.25 2.25 0 001.632-2.163zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 01-.99-3.467l2.31-.66A2.25 2.25 0 009 15.553z"/>',
                    'link'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/>',
                ];
                $icon = $typeIcons[$post->type] ?? $typeIcons['text'];
                $isActive = $post->status === 'active';
                $isArchived = $post->status === 'archived';
                $firstMedia = $post->media->first();
                $likesCount = $post->likes->count();
                $commentsCount = $post->comments->count();
            @endphp

            <div class="flex items-start gap-4 px-5 py-4 {{ $isArchived ? 'opacity-60' : '' }} hover:bg-gray-50/60 transition-colors">

                {{-- Thumbnail / icon --}}
                <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center overflow-hidden">
                    @if($firstMedia && $post->type === 'image')
                        <img src="{{ Storage::url($firstMedia->path) }}" class="w-11 h-11 object-cover" loading="lazy">
                    @else
                        <svg class="w-5 h-5 text-[#00baff]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            {!! $icon !!}
                        </svg>
                    @endif
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">{{ ucfirst($post->type) }}</span>
                        @if($post->visibility === 'followers')
                            <span class="text-xs bg-amber-100 text-amber-700 rounded-md px-1.5 py-0.5 font-semibold">Apenas assinantes</span>
                        @endif
                        <span class="text-xs rounded-md px-1.5 py-0.5 font-semibold
                            {{ $isActive ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $isActive ? 'Activa' : 'Arquivada' }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-700 line-clamp-2 mb-2">
                        {{ $post->content ?: ($post->link_title ?: '(sem texto)') }}
                    </p>
                    <div class="flex items-center gap-4 text-xs text-gray-400">
                        <span>{{ $post->created_at->diffForHumans() }}</span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                            {{ $likesCount }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/></svg>
                            {{ $commentsCount }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $post->views_count ?? 0 }}
                        </span>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex-shrink-0 flex items-center gap-2">
                    <button wire:click="toggleStatus({{ $post->id }})"
                            title="{{ $isActive ? 'Arquivar' : 'Reactivar' }}"
                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:border-[#00baff] hover:text-[#00baff] transition-all bg-white">
                        @if($isActive)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                        @endif
                    </button>

                    @if($confirmDeleteId === $post->id)
                        <button wire:click="deletePost({{ $post->id }})"
                                class="h-8 px-3 rounded-lg bg-red-500 text-white text-xs font-bold">
                            Confirmar
                        </button>
                        <button wire:click="$set('confirmDeleteId', null)"
                                class="h-8 px-3 rounded-lg border border-gray-200 text-gray-500 text-xs font-medium">
                            Cancelar
                        </button>
                    @else
                        <button wire:click="$set('confirmDeleteId', {{ $post->id }})"
                                title="Eliminar"
                                class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:border-red-400 hover:text-red-500 transition-all bg-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-16 px-4">
                <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.3" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                </svg>
                <p class="text-base font-bold text-gray-600">Ainda não tens publicações</p>
                <p class="text-sm text-gray-400 mt-1 mb-4">Começa a criar conteúdo para o teu público</p>
                <a href="{{ route('social.create') }}"
                   class="inline-flex items-center gap-1.5 bg-[#00baff] hover:bg-[#009ad6] text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition">
                    + Nova publicação
                </a>
            </div>
        @endforelse
        </div>

        @if($posts->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $posts->links() }}
            </div>
        @endif
    </div>

    {{-- Modal: Saque das Publicações --}}
    @if($showSaqueModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" wire:click.self="fecharSaque">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Saque das Publicações</h3>
                    <p class="text-xs text-gray-500">Disponivel: <strong class="text-emerald-600">Kz {{ number_format($saldoPublicacoesDisponivel, 2, ',', '.') }}</strong></p>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Valor a sacar (Kz)</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-sm text-gray-400 font-medium">Kz</span>
                    <input type="number" wire:model="valorSaquePublicacoes"
                        min="1000" step="100" max="{{ $saldoPublicacoesDisponivel }}"
                        class="w-full border border-slate-200 rounded-xl pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400"
                        placeholder="0">
                </div>
                @error('valorSaquePublicacoes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <p class="text-xs text-gray-400 mb-5">
                Os ganhos das publicações são creditados pela plataforma com base no desempenho do conteúdo. O processamento ocorre em até 2 dias úteis após aprovação.
            </p>

            <div class="flex gap-3">
                <button wire:click="solicitarSaque" wire:loading.attr="disabled"
                    class="flex-1 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-xl text-sm font-semibold hover:opacity-90 transition disabled:opacity-50">
                    <span wire:loading.remove wire:target="solicitarSaque">Confirmar Saque</span>
                    <span wire:loading wire:target="solicitarSaque">A processar...</span>
                </button>
                <button wire:click="fecharSaque" class="px-4 py-2.5 text-sm text-gray-600 hover:text-gray-900 border border-gray-200 rounded-xl transition">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
