{{--
    Component: livewire.social.stories
    Stories bar + viewer modal (Alpine.js) + create story modal (Livewire)
    $storyGroups: Collection<{user_id, name, avatar, is_self, all_viewed, stories[]}>
--}}
<div
    x-data="{
        viewer: null,
        groupIdx: 0,
        storyIdx: 0,
        progress: 0,
        timer: null,
        storyDuration: 5000,

        groups: {{ json_encode($storyGroups->values()->toArray()) }},

        openGroup(gIdx) {
            this.groupIdx = gIdx;
            this.storyIdx = 0;
            this.viewer = this.groups[gIdx];
            this.startTimer();
            if (this.currentStory()) {
                $wire.markViewed(this.currentStory().id);
            }
        },
        closeViewer() {
            clearInterval(this.timer);
            this.viewer = null;
            this.progress = 0;
        },
        currentStory() {
            return this.viewer ? this.viewer.stories[this.storyIdx] : null;
        },
        nextStory() {
            clearInterval(this.timer);
            this.progress = 0;
            if (this.storyIdx < this.viewer.stories.length - 1) {
                this.storyIdx++;
                this.startTimer();
                $wire.markViewed(this.currentStory().id);
            } else if (this.groupIdx < this.groups.length - 1) {
                this.openGroup(this.groupIdx + 1);
            } else {
                this.closeViewer();
            }
        },
        prevStory() {
            clearInterval(this.timer);
            this.progress = 0;
            if (this.storyIdx > 0) {
                this.storyIdx--;
                this.startTimer();
            } else if (this.groupIdx > 0) {
                this.openGroup(this.groupIdx - 1);
            }
        },
        startTimer() {
            this.progress = 0;
            const interval = 50;
            const steps = this.storyDuration / interval;
            this.timer = setInterval(() => {
                this.progress += (100 / steps);
                if (this.progress >= 100) {
                    this.nextStory();
                }
            }, interval);
        }
    }"
    @keydown.escape.window="closeViewer()"
>

    {{-- ── Stories bar ──────────────────────────────────────────────────────── --}}
    @if($storyGroups->isNotEmpty() || (auth()->check() && auth()->user()->activeRole() === 'freelancer'))
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
            <div class="flex gap-4 overflow-x-auto pb-1 scrollbar-hide">

                {{-- Add my story button (freelancers only) --}}
                @auth
                    @if(auth()->user()->activeRole() === 'freelancer')
                        <div class="flex-shrink-0 flex flex-col items-center gap-1.5 cursor-pointer"
                             wire:click="$set('createModal', true)">
                            <div class="w-14 h-14 rounded-full border-2 border-dashed border-gray-300 hover:border-[#00baff] flex items-center justify-center transition bg-gray-50 hover:bg-blue-50/30">
                                <svg class="w-6 h-6 text-gray-400 hover:text-[#00baff]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                </svg>
                            </div>
                            <span class="text-xs text-gray-500 font-medium">Meu story</span>
                        </div>
                    @endif
                @endauth

                {{-- Story circles --}}
                <template x-for="(group, gIdx) in groups" :key="group.user_id">
                    <div class="flex-shrink-0 flex flex-col items-center gap-1.5 cursor-pointer"
                         @click="openGroup(gIdx)">
                        <div :class="group.all_viewed ? 'p-0.5 bg-gray-200' : 'p-0.5 bg-gradient-to-br from-[#00baff] to-purple-500'"
                             class="w-14 h-14 rounded-full">
                            <img :src="group.avatar" :alt="group.name"
                                 class="w-full h-full rounded-full object-cover border-2 border-white"
                                 onerror="this.src='{{ asset('img/default-avatar.svg') }}'">
                        </div>
                        <span class="text-xs text-gray-600 w-14 text-center truncate" x-text="group.is_self ? 'Eu' : group.name"></span>
                    </div>
                </template>

            </div>
        </div>
    @endif

    {{-- ── Story viewer modal ───────────────────────────────────────────────── --}}
    <template x-if="viewer">
        <div class="fixed inset-0 bg-black/90 z-50 flex items-center justify-center"
             @click.self="closeViewer()">

            <div class="relative w-full max-w-sm h-[85vh] flex flex-col">

                {{-- Progress bars --}}
                <div class="flex gap-1 p-3 absolute top-0 left-0 right-0 z-10">
                    <template x-for="(story, sIdx) in viewer.stories" :key="story.id">
                        <div class="flex-1 h-1 bg-white/30 rounded-full overflow-hidden">
                            <div class="h-full bg-white rounded-full transition-all duration-50"
                                 :style="'width: ' + (sIdx < storyIdx ? 100 : (sIdx === storyIdx ? progress : 0)) + '%'">
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Header --}}
                <div class="absolute top-8 left-0 right-0 z-10 flex items-center justify-between px-3 pt-2">
                    <div class="flex items-center gap-2">
                        <img :src="viewer.avatar" class="w-9 h-9 rounded-full object-cover border-2 border-white/60">
                        <div>
                            <p class="text-white text-sm font-semibold" x-text="viewer.name"></p>
                            <p class="text-white/60 text-xs"
                               x-text="currentStory() ? new Date(currentStory().expires_at).toLocaleTimeString('pt', {hour:'2-digit',minute:'2-digit'}) + ' (expira)' : ''"></p>
                        </div>
                    </div>
                    <button @click="closeViewer()" class="text-white/80 hover:text-white p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Media --}}
                <div class="w-full h-full rounded-2xl overflow-hidden bg-gray-900 flex items-center justify-center">
                    <template x-if="currentStory() && currentStory().type === 'image'">
                        <img :src="currentStory().url" class="w-full h-full object-cover">
                    </template>
                    <template x-if="currentStory() && currentStory().type === 'video'">
                        <video :src="currentStory().url" autoplay muted playsinline
                               class="w-full h-full object-contain"
                               @ended="nextStory()"></video>
                    </template>
                </div>

                {{-- Caption --}}
                <template x-if="currentStory() && currentStory().caption">
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4 rounded-b-2xl">
                        <p class="text-white text-sm" x-text="currentStory().caption"></p>
                    </div>
                </template>

                {{-- Navigation tap zones --}}
                <button @click="prevStory()" class="absolute left-0 top-0 bottom-0 w-1/3 opacity-0"></button>
                <button @click="nextStory()" class="absolute right-0 top-0 bottom-0 w-1/3 opacity-0"></button>

            </div>
        </div>
    </template>

    {{-- ── Create story modal (Livewire) ───────────────────────────────────── --}}
    @if($createModal)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" wire:key="create-story-modal">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                <h3 class="text-lg font-bold mb-4">Criar Story</h3>
                <p class="text-sm text-gray-500 mb-4">Imagens e vídeos ficam visíveis durante 24 horas.</p>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Ficheiro <span class="font-normal text-gray-400">(imagem ou vídeo · máx. 50 MB)</span>
                        </label>
                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-[#00baff]/60 hover:bg-blue-50/20 transition">
                            <svg class="w-8 h-8 text-gray-300 mb-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775"/>
                            </svg>
                            @if($storyFile)
                                <p class="text-xs text-[#00baff] font-medium">{{ $storyFile->getClientOriginalName() }}</p>
                            @else
                                <p class="text-xs text-gray-400">Clique para selecionar</p>
                            @endif
                            <input type="file" wire:model="storyFile" accept="image/*,video/*" class="hidden">
                        </label>
                        @error('storyFile') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        <div wire:loading wire:target="storyFile" class="mt-1 text-xs text-[#00baff]">A processar...</div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Legenda (opcional)</label>
                        <input wire:model="storyCaption" type="text" placeholder="Escreva uma legenda..."
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/40"
                               maxlength="300">
                        @error('storyCaption') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex gap-3 mt-5">
                    <button wire:click="publishStory"
                        class="flex-1 bg-[#00baff] hover:bg-[#009ad6] text-white text-sm font-semibold py-2.5 rounded-xl transition"
                        wire:loading.attr="disabled" wire:loading.class="opacity-70">
                        <span wire:loading.remove wire:target="publishStory">Publicar Story</span>
                        <span wire:loading wire:target="publishStory">A publicar...</span>
                    </button>
                    <button wire:click="$set('createModal', false)"
                        class="px-5 py-2.5 border border-gray-200 text-gray-600 text-sm rounded-xl hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
