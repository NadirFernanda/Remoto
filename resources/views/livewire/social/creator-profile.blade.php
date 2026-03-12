<div class="max-w-4xl mx-auto py-8 px-4">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm font-medium">{{ session('success') }}</div>
    @endif

    {{-- Creator header card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
        <div class="flex flex-col sm:flex-row gap-6 items-start sm:items-center">

            {{-- Avatar --}}
            <img src="{{ $creator->avatarUrl() }}"
                 alt="{{ $creator->name }}"
                 class="w-20 h-20 rounded-full object-cover ring-4 ring-[#00baff]/20 flex-shrink-0"
                 onerror="this.src='{{ asset('img/default-avatar.svg') }}'">

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <h1 class="text-xl font-bold text-gray-900">{{ $creator->name }}</h1>
                @if($creator->freelancerProfile?->headline)
                    <p class="text-sm text-gray-500 mt-0.5">{{ $creator->freelancerProfile->headline }}</p>
                @endif
                @if($creator->location)
                    <p class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                        </svg>
                        {{ $creator->location }}
                    </p>
                @endif

                {{-- Stats row --}}
                <div class="flex items-center gap-6 mt-3 text-sm">
                    <div class="text-center">
                        <p class="font-bold text-gray-900">{{ $followersCount }}</p>
                        <p class="text-xs text-gray-400">seguidores</p>
                    </div>
                    <div class="text-center">
                        <p class="font-bold text-gray-900">{{ $posts->total() }}</p>
                        <p class="text-xs text-gray-400">publicações</p>
                    </div>
                    @if($creator->averageRating() > 0)
                        <div class="flex items-center gap-1 text-amber-500">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            <span class="font-bold text-gray-900">{{ $creator->averageRating() }}</span>
                            <span class="text-xs text-gray-400">avaliação</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col gap-2 flex-shrink-0">
                @auth
                    @if(auth()->id() !== $creator->id)
                        <button wire:click="toggleFollow"
                            class="px-5 py-2.5 text-sm font-semibold rounded-xl border-2 transition
                                {{ $isFollowing
                                    ? 'border-gray-200 text-gray-600 hover:border-red-300 hover:text-red-600'
                                    : 'border-[#00baff] bg-[#00baff] text-white hover:bg-[#009ad6] hover:border-[#009ad6]' }}">
                            {{ $isFollowing ? 'A seguir ✓' : 'Seguir' }}
                        </button>
                        @if($creator->role === 'freelancer')
                            <a href="{{ route('freelancer.show', $creator) }}"
                               class="text-center px-5 py-2.5 text-sm font-semibold rounded-xl border-2 border-gray-200 text-gray-600 hover:bg-gray-50 transition">
                                Contratar
                            </a>
                        @endif
                    @else
                        <a href="{{ route('social.create') }}"
                           class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-[#00baff] text-white hover:bg-[#009ad6] transition text-center">
                            + Nova publicação
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}"
                       class="px-5 py-2.5 text-sm font-semibold rounded-xl border-2 border-[#00baff] text-[#00baff] hover:bg-[#00baff] hover:text-white transition text-center">
                        Seguir
                    </a>
                    @if($creator->role === 'freelancer')
                        <a href="{{ route('freelancer.show', $creator) }}"
                           class="text-center px-5 py-2.5 text-sm font-semibold rounded-xl border-2 border-gray-200 text-gray-600 hover:bg-gray-50 transition">
                            Contratar
                        </a>
                    @endif
                @endauth
            </div>
        </div>

        {{-- Bio --}}
        @if($creator->bio)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-sm text-gray-600 leading-relaxed">{{ $creator->bio }}</p>
            </div>
        @endif

        {{-- Skills --}}
        @if($creator->freelancerProfile?->skills)
            <div class="mt-3 flex flex-wrap gap-1.5">
                @foreach(array_slice($creator->freelancerProfile->skills, 0, 8) as $skill)
                    <span class="text-xs bg-blue-50 text-[#00baff] px-2.5 py-1 rounded-full font-medium">{{ $skill }}</span>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Posts grid --}}
    @if($posts->isEmpty())
        <div class="text-center py-16 text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
            </svg>
            <p class="text-sm font-medium">Ainda não há publicações.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($posts as $post)
                @include('livewire.social.partials.post-card', ['post' => $post])
            @endforeach
        </div>

        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    @endif

    {{-- Report modal --}}
    @if($reportingPostId)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                <h3 class="text-lg font-bold mb-1">Denunciar publicação</h3>
                <p class="text-sm text-gray-500 mb-4">Descreva o motivo da sua denúncia.</p>
                <textarea wire:model="reportReason" rows="4"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-[#00baff]/40"
                    placeholder="Ex: Conteúdo inapropriado, spam..."></textarea>
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
