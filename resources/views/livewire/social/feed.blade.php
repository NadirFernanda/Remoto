<div class="max-w-2xl mx-auto py-8 px-4">

    {{-- Flash message --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    {{-- Stories bar --}}
    @auth
        <livewire:social.stories />
    @endauth

    {{-- Header row --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                @if($hashtag) #{{ $hashtag }}
                @elseif($bookmarkedOnly) Publicações Guardadas
                @else Feed Social
                @endif
            </h1>
            @if($hashtag)
                <a href="{{ route('social.feed') }}" class="text-xs text-gray-400 hover:text-[#00baff] mt-0.5 inline-block">← Todos os posts</a>
            @endif
        </div>
        <div class="flex items-center gap-2">
            @auth
                {{-- Bookmarks toggle --}}
                <a href="{{ route('social.bookmarks') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl border transition
                       {{ $bookmarkedOnly ? 'bg-[#00baff] text-white border-[#00baff]' : 'border-gray-200 text-gray-600 hover:border-[#00baff] hover:text-[#00baff]' }}">
                    <svg class="w-4 h-4" fill="{{ $bookmarkedOnly ? 'currentColor' : 'none'}}" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
                    </svg>
                    Guardados
                </a>
                @if(auth()->user()->activeRole() === 'freelancer')
                    <a href="{{ route('social.create') }}"
                       class="inline-flex items-center gap-2 bg-[#00baff] hover:bg-[#009ad6] text-white text-sm font-semibold px-4 py-2 rounded-xl transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Publicar
                    </a>
                @endif
            @endauth
        </div>
    </div>

    {{-- Empty state for new users --}}
    @if($isEmpty)
        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6 mb-6 text-center">
            <svg class="w-10 h-10 text-[#00baff] mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p class="text-sm text-gray-600 font-medium">Ainda não segue nenhum criador.</p>
            <p class="text-xs text-gray-400 mt-1 mb-4">Explore os freelancers e comece a seguir os que mais gosta.</p>
            <a href="{{ route('freelancers.search') }}"
               class="inline-flex items-center gap-1.5 text-sm text-[#00baff] font-semibold hover:underline">
                Descobrir freelancers
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
            </a>
        </div>
    @endif

    {{-- Posts --}}
    <div class="space-y-6">
        @forelse($posts as $post)
            @include('livewire.social.partials.post-card', ['post' => $post])
        @empty
            <div class="text-center py-16 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
                <p class="text-sm font-medium">
                    @if($bookmarkedOnly) Nenhuma publicação guardada.
                    @elseif($hashtag) Nenhuma publicação com #{{ $hashtag }}.
                    @else Nenhuma publicação encontrada.
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-8">{{ $posts->links() }}</div>

    {{-- Report modal --}}
    @if($reportingPostId || $reportingUserId)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                <h3 class="text-lg font-bold mb-1">Denunciar {{ $reportType === 'post' ? 'publicação' : 'utilizador' }}</h3>
                <p class="text-sm text-gray-500 mb-4">Descreva brevemente o motivo da sua denúncia.</p>
                <textarea wire:model="reportReason" rows="4"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-[#00baff]/40"
                    placeholder="Ex: Conteúdo inapropriado, spam, ofensivo..."></textarea>
                @error('reportReason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <div class="flex gap-3 mt-4">
                    <button wire:click="submitReport"
                        class="flex-1 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold py-2 rounded-xl transition">
                        Enviar denúncia
                    </button>
                    <button wire:click="cancelReport"
                        class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2 rounded-xl hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>

    {{-- Flash message --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Feed Social</h1>
        @auth
            @if(auth()->user()->activeRole() === 'freelancer')
                <a href="{{ route('social.create') }}"
                   class="inline-flex items-center gap-2 bg-[#00baff] hover:bg-[#009ad6] text-white text-sm font-semibold px-4 py-2 rounded-xl transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nova publicação
                </a>
            @endif
        @endauth
    </div>

    {{-- Empty state for new users --}}
    @if($isEmpty)
        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6 mb-6 text-center">
            <svg class="w-10 h-10 text-[#00baff] mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p class="text-sm text-gray-600 font-medium">Ainda não segue nenhum criador.</p>
            <p class="text-xs text-gray-400 mt-1 mb-4">Explore os freelancers abaixo e comece a seguir os que mais gosta.</p>
            <a href="{{ route('freelancers.search') }}"
               class="inline-flex items-center gap-1.5 text-sm text-[#00baff] font-semibold hover:underline">
                Descobrir freelancers
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
            </a>
        </div>
    @endif

    {{-- Posts list --}}
    <div class="space-y-6">
        @forelse($posts as $post)
            @include('livewire.social.partials.post-card', ['post' => $post])
        @empty
            <div class="text-center py-16 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
                <p class="text-sm font-medium">Nenhuma publicação encontrada.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $posts->links() }}
    </div>

    {{-- Report modal --}}
    @if($reportingPostId || $reportingUserId)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                <h3 class="text-lg font-bold mb-1">Denunciar {{ $reportType === 'post' ? 'publicação' : 'utilizador' }}</h3>
                <p class="text-sm text-gray-500 mb-4">Descreva brevemente o motivo da sua denúncia.</p>
                <textarea wire:model="reportReason" rows="4"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-[#00baff]/40"
                    placeholder="Ex: Conteúdo inapropriado, spam, ofensivo..."></textarea>
                @error('reportReason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <div class="flex gap-3 mt-4">
                    <button wire:click="submitReport"
                        class="flex-1 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold py-2 rounded-xl transition">
                        Enviar denúncia
                    </button>
                    <button wire:click="cancelReport"
                        class="flex-1 border border-gray-200 text-gray-600 text-sm font-medium py-2 rounded-xl hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
