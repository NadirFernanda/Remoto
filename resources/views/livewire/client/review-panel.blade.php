<div x-data="{ tab: 'received' }">
    {{-- Summary bar --}}
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white border border-gray-200 rounded-2xl p-5">
            <p class="text-xs text-gray-500 mb-1">Avaliações recebidas</p>
            <p class="text-3xl font-bold text-gray-900">{{ $reviewsReceived->count() }}</p>
            @if($reviewsReceived->count() > 0)
                @php $avgReceived = round($reviewsReceived->avg('rating'), 1); @endphp
                <div class="flex items-center gap-1 mt-2">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 {{ $i <= round($avgReceived) ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                    <span class="text-sm font-semibold text-gray-700 ml-1">{{ $avgReceived }}</span>
                    <span class="text-xs text-gray-400">média</span>
                </div>
            @endif
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl p-5">
            <p class="text-xs text-gray-500 mb-1">Avaliações feitas</p>
            <p class="text-3xl font-bold text-gray-900">{{ $reviewsGiven->count() }}</p>
            @if($reviewsGiven->count() > 0)
                @php $avgGiven = round($reviewsGiven->avg('rating'), 1); @endphp
                <div class="flex items-center gap-1 mt-2">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 {{ $i <= round($avgGiven) ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                    <span class="text-sm font-semibold text-gray-700 ml-1">{{ $avgGiven }}</span>
                    <span class="text-xs text-gray-400">média atribuída</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 bg-gray-100 rounded-xl p-1 mb-6 w-fit">
        <button @click="tab = 'received'"
            :class="tab === 'received' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700'"
            class="px-4 py-2 rounded-[10px] text-sm font-medium transition">
            Recebidas <span class="ml-1 text-xs font-bold text-[#00baff]">{{ $reviewsReceived->count() }}</span>
        </button>
        <button @click="tab = 'given'"
            :class="tab === 'given' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700'"
            class="px-4 py-2 rounded-[10px] text-sm font-medium transition">
            Feitas <span class="ml-1 text-xs font-bold text-[#00baff]">{{ $reviewsGiven->count() }}</span>
        </button>
    </div>

    {{-- Received reviews --}}
    <div x-show="tab === 'received'" class="space-y-4">
        @forelse($reviewsReceived as $review)
            <div class="bg-white border border-gray-200 rounded-2xl p-5">
                <div class="flex items-start gap-4">
                    <img src="{{ $review->author->avatarUrl() }}" alt="{{ $review->author->name ?? '' }}"
                        class="w-11 h-11 rounded-full object-cover border border-gray-100 flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 flex-wrap mb-1">
                            <span class="font-semibold text-gray-800 text-sm">{{ $review->author->name ?? 'Utilizador' }}</span>
                            <span class="text-xs text-gray-400">{{ $review->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex items-center gap-0.5 mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                            <span class="text-xs text-gray-500 ml-1">{{ $review->rating }}/5</span>
                        </div>
                        @if($review->comment)
                            <p class="text-sm text-gray-700">{{ $review->comment }}</p>
                        @endif
                        @if($review->service)
                            <p class="text-xs text-gray-400 mt-2">Projeto: {{ $review->service->titulo }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white border border-dashed border-gray-200 rounded-2xl p-10 text-center text-gray-400 text-sm">
                Ainda não recebeu nenhuma avaliação.
            </div>
        @endforelse
    </div>

    {{-- Given reviews --}}
    <div x-show="tab === 'given'" class="space-y-4">
        @forelse($reviewsGiven as $review)
            <div class="bg-white border border-gray-200 rounded-2xl p-5">
                <div class="flex items-start gap-4">
                    <img src="{{ $review->target->avatarUrl() }}" alt="{{ $review->target->name ?? '' }}"
                        class="w-11 h-11 rounded-full object-cover border border-gray-100 flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 flex-wrap mb-1">
                            <span class="font-semibold text-gray-800 text-sm">Para: {{ $review->target->name ?? 'Utilizador' }}</span>
                            <span class="text-xs text-gray-400">{{ $review->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex items-center gap-0.5 mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                            <span class="text-xs text-gray-500 ml-1">{{ $review->rating }}/5</span>
                        </div>
                        @if($review->comment)
                            <p class="text-sm text-gray-700">{{ $review->comment }}</p>
                        @endif
                        @if($review->service)
                            <p class="text-xs text-gray-400 mt-2">Projeto: {{ $review->service->titulo }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white border border-dashed border-gray-200 rounded-2xl p-10 text-center text-gray-400 text-sm">
                Ainda não fez nenhuma avaliação.
            </div>
        @endforelse
    </div>
</div>
