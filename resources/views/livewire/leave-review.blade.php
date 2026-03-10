<div class="min-h-screen bg-gray-50">
    <div class="max-w-xl mx-auto px-4 py-10">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Avaliar projecto</h1>
        <p class="text-gray-500 mb-6">{{ $service->titulo }}</p>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded-xl">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded-xl">{{ session('error') }}</div>
        @endif

        @if($alreadyReviewed)
            <div class="bg-white border border-gray-100 rounded-2xl p-8 text-center shadow-sm">
                <svg class="w-12 h-12 text-green-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-gray-700 font-semibold text-lg">Avaliação já enviada!</p>
                <p class="text-gray-400 text-sm mt-1">Obrigado pelo seu feedback.</p>
            </div>
        @elseif($service->status !== 'completed')
            <div class="bg-white border border-yellow-100 rounded-2xl p-8 text-center shadow-sm">
                <p class="text-yellow-700 font-semibold">Só é possível avaliar projectos concluídos.</p>
            </div>
        @else
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                {{-- Target user --}}
                <div class="flex items-center gap-3 mb-6">
                    <img src="{{ $targetUser->avatarUrl() }}" class="w-12 h-12 rounded-full object-cover border border-gray-200" alt="{{ $targetUser->name }}">
                    <div>
                        <div class="font-semibold text-gray-800">{{ $targetUser->name }}</div>
                        <div class="text-xs text-gray-400">Avaliando este utilizador</div>
                    </div>
                </div>

                {{-- Star rating --}}
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nota</label>
                    <div class="flex gap-2" x-data="{ hover: 0, selected: @entangle('rating') }">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button"
                                @mouseenter="hover = {{ $i }}"
                                @mouseleave="hover = 0"
                                @click="selected = {{ $i }}"
                                :class="(hover >= {{ $i }} || selected >= {{ $i }}) ? 'text-yellow-400' : 'text-gray-300'"
                                class="text-3xl transition-colors focus:outline-none">★</button>
                        @endfor
                    </div>
                    @error('rating') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                {{-- Comment --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Comentário (opcional)</label>
                    <textarea wire:model.defer="comment" rows="4"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-cyan-300 focus:outline-none resize-none"
                        placeholder="Descreva a sua experiência..."></textarea>
                    @error('comment') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <button wire:click="submitReview" wire:loading.attr="disabled"
                    class="btn-eq btn-primary w-full justify-center">
                    <span wire:loading.remove wire:target="submitReview">Enviar avaliação</span>
                    <span wire:loading wire:target="submitReview">A enviar...</span>
                </button>
            </div>
        @endif
    </div>
</div>
