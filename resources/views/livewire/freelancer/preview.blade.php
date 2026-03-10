<div>
@php use Illuminate\Support\Str; @endphp

@if($show)
    <div x-data="{
        open: @entangle('show'),
        focusables: [],
        firstFocusable: null,
        lastFocusable: null,
        init() {
            this.$nextTick(() => {
                this.focusables = Array.from($el.querySelectorAll('a,button,input,textarea,select,[tabindex]:not([tabindex=\"-1\"])')).filter(el => !el.hasAttribute('disabled'));
                this.firstFocusable = this.focusables[0] || null;
                this.lastFocusable = this.focusables[this.focusables.length - 1] || null;
                if (this.firstFocusable) this.firstFocusable.focus();
            });
        },
        handleTab(e) {
            if (this.focusables.length === 0) return;
            if (e.shiftKey) {
                if (document.activeElement === this.firstFocusable) {
                    e.preventDefault();
                    this.lastFocusable.focus();
                }
            } else {
                if (document.activeElement === this.lastFocusable) {
                    e.preventDefault();
                    this.firstFocusable.focus();
                }
            }
        }
    }" x-init="init()" x-cloak @keydown.window.escape="$wire.close()" @keydown.window="if ($event.key === 'Tab') handleTab($event)" x-effect="document.body.classList.toggle('overflow-hidden', open)">
    <div class="fixed inset-0 z-40">
        <div class="absolute inset-0 bg-black/50" @click="$wire.close()" x-transition.opacity></div>
        <div class="absolute right-0 top-0 h-full w-full md:w-2/5 bg-white shadow-lg p-6 overflow-y-auto"
             x-transition:enter="transform transition ease-in-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full">
                @if($userModel)
                    <div class="flex items-start justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100">
                                <img src="{{ $userModel->avatarUrl() }}" alt="{{ $userModel->name }}" class="w-full h-full object-cover">
                                </div>
                            <div>
                                <div class="text-xl font-semibold">{{ $userModel->name }}</div>
                                @if($userModel->freelancerProfile && $userModel->freelancerProfile->headline)
                                    <div class="text-sm text-gray-600">{{ $userModel->freelancerProfile->headline }}</div>
                                @endif
                            </div>
                        </div>
                        <div>
                            <button @click="$wire.close()" class="text-gray-500">Fechar ✕</button>
                        </div>
                    </div>

                    @if($userModel->freelancerProfile && $userModel->freelancerProfile->summary)
                        <div class="mt-4 text-gray-700">{{ Str::limit($userModel->freelancerProfile->summary, 400) }}</div>
                    @endif

                    @if($userModel->freelancerProfile && $userModel->freelancerProfile->skills)
                        <div class="mt-4">
                            <h4 class="font-medium">Skills</h4>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($userModel->freelancerProfile->skills as $skill)
                                    <span class="text-xs bg-blue-50 text-blue-700 px-3 py-1 rounded-full">{{ $skill }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($userModel->portfolios && $userModel->portfolios->count())
                        <div class="mt-4">
                            <h4 class="font-medium">Portfólio</h4>
                            <div class="mt-2 grid grid-cols-2 gap-3">
                                @foreach($userModel->portfolios->take(4) as $item)
                                    <div class="rounded overflow-hidden border">
                                        @if(Str::startsWith($item->media_path, 'http') || Str::startsWith($item->media_path, '/'))
                                            <img src="{{ $item->media_path }}" alt="portfolio" class="w-full h-24 object-cover">
                                        @else
                                            <img src="{{ asset('storage/' . $item->media_path) }}" alt="portfolio" class="w-full h-24 object-cover">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mt-6 flex items-center gap-3">
                        <a href="{{ route('freelancer.show', $userModel->id) }}" class="px-4 py-2 rounded-full bg-white border">Ver perfil completo</a>
                        <button wire:click="openProposal({{ $userModel->id }})" class="px-4 py-2 rounded-full bg-blue-600 text-white">Enviar proposta</button>
                    </div>
                @else
                    <div>A carregar...</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
