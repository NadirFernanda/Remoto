@php
    $avgReceived = $reviewsReceived->count() > 0 ? round($reviewsReceived->avg('rating'), 1) : null;
    $avgGiven    = $reviewsGiven->count()    > 0 ? round($reviewsGiven->avg('rating'), 1)    : null;
@endphp

<div class="max-w-4xl mx-auto space-y-6" x-data="{ tab: 'received' }">

    {{-- ─── Gradient Header ──────────────────────────────────── --}}
    <div class="bg-gradient-to-r from-[#00baff] to-[#0095cc] rounded-2xl p-6 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
            <div>
                <h2 class="text-2xl font-extrabold">Avaliações</h2>
                <p class="text-sm text-white/75 mt-1">O seu historial de avaliações recebidas e feitas</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                {{-- Recebidas --}}
                <div class="bg-white/10 border border-white/20 rounded-xl px-5 py-3 text-center min-w-[110px]">
                    <div class="text-xs text-white/60 font-medium mb-1">Recebidas</div>
                    <div class="text-2xl font-extrabold">{{ $reviewsReceived->count() }}</div>
                    @if($avgReceived)
                        <div class="flex items-center justify-center gap-0.5 mt-1">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-3 h-3 {{ $i <= round($avgReceived) ? 'text-yellow-300' : 'text-white/20' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                            <span class="text-xs font-bold text-yellow-300 ml-1">{{ $avgReceived }}</span>
                        </div>
                    @endif
                </div>
                {{-- Feitas --}}
                <div class="bg-white/10 border border-white/20 rounded-xl px-5 py-3 text-center min-w-[110px]">
                    <div class="text-xs text-white/60 font-medium mb-1">Feitas</div>
                    <div class="text-2xl font-extrabold">{{ $reviewsGiven->count() }}</div>
                    @if($avgGiven)
                        <div class="flex items-center justify-center gap-0.5 mt-1">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-3 h-3 {{ $i <= round($avgGiven) ? 'text-yellow-300' : 'text-white/20' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                            <span class="text-xs font-bold text-yellow-300 ml-1">{{ $avgGiven }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Tabs ─────────────────────────────────────────────── --}}
    <div class="flex gap-2">
        <button @click="tab = 'received'"
            :class="tab === 'received' ? 'bg-[#00baff] text-white border-[#00baff] shadow-sm' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold border transition">
            Recebidas
            <span :class="tab === 'received' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600'"
                  class="px-1.5 py-0.5 rounded-full text-[10px] font-bold">{{ $reviewsReceived->count() }}</span>
        </button>
        <button @click="tab = 'given'"
            :class="tab === 'given' ? 'bg-[#00baff] text-white border-[#00baff] shadow-sm' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold border transition">
            Feitas
            <span :class="tab === 'given' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600'"
                  class="px-1.5 py-0.5 rounded-full text-[10px] font-bold">{{ $reviewsGiven->count() }}</span>
        </button>
    </div>

    {{-- ─── Avaliações Recebidas ─────────────────────────────── --}}
    <div x-show="tab === 'received'" class="space-y-4">
        @forelse($reviewsReceived as $review)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all p-5">
                <div class="flex items-start gap-4">
                    <img src="{{ $review->author->avatarUrl() }}" alt="{{ $review->author->name ?? '' }}"
                        class="w-12 h-12 rounded-xl object-cover border border-gray-100 flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2 flex-wrap mb-2">
                            <div>
                                <p class="font-bold text-gray-900 text-sm">{{ $review->author->name ?? 'Utilizador' }}</p>
                                @if($review->service)
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $review->service->titulo }}</p>
                                @endif
                            </div>
                            <span class="text-xs text-gray-400 flex-shrink-0">{{ $review->created_at->format('d/m/Y') }}</span>
                        </div>
                        {{-- Estrelas --}}
                        <div class="flex items-center gap-1 mb-3">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                            <span class="text-sm font-extrabold text-yellow-500 ml-1">{{ $review->rating }}<span class="text-gray-300 font-normal">/5</span></span>
                        </div>
                        @if($review->comment)
                            <p class="text-sm text-gray-700 bg-gray-50 rounded-xl px-4 py-3 border border-gray-100 leading-relaxed">
                                "{{ $review->comment }}"
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center py-20 text-gray-400 bg-white rounded-2xl border border-dashed border-gray-200">
                <svg class="w-14 h-14 opacity-20 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/></svg>
                <p class="text-base font-semibold">Ainda não recebeu nenhuma avaliação</p>
                <p class="text-sm mt-1">Complete projectos para começar a receber avaliações</p>
            </div>
        @endforelse
    </div>

    {{-- ─── Avaliações Feitas ────────────────────────────────── --}}
    <div x-show="tab === 'given'" class="space-y-4">
        @forelse($reviewsGiven as $review)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all p-5">
                <div class="flex items-start gap-4">
                    <img src="{{ $review->target->avatarUrl() }}" alt="{{ $review->target->name ?? '' }}"
                        class="w-12 h-12 rounded-xl object-cover border border-gray-100 flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2 flex-wrap mb-2">
                            <div>
                                <p class="font-bold text-gray-900 text-sm">{{ $review->target->name ?? 'Utilizador' }}</p>
                                @if($review->service)
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $review->service->titulo }}</p>
                                @endif
                            </div>
                            <span class="text-xs text-gray-400 flex-shrink-0">{{ $review->created_at->format('d/m/Y') }}</span>
                        </div>
                        {{-- Estrelas --}}
                        <div class="flex items-center gap-1 mb-3">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                            <span class="text-sm font-extrabold text-yellow-500 ml-1">{{ $review->rating }}<span class="text-gray-300 font-normal">/5</span></span>
                        </div>
                        @if($review->comment)
                            <p class="text-sm text-gray-700 bg-gray-50 rounded-xl px-4 py-3 border border-gray-100 leading-relaxed">
                                "{{ $review->comment }}"
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center py-20 text-gray-400 bg-white rounded-2xl border border-dashed border-gray-200">
                <svg class="w-14 h-14 opacity-20 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/></svg>
                <p class="text-base font-semibold">Ainda não fez nenhuma avaliação</p>
                <p class="text-sm mt-1">Avalie os freelancers após concluir projectos</p>
            </div>
        @endforelse
    </div>

</div>
