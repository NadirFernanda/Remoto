<div class="dash-main">
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-6 space-y-5">

    {{-- ── PROFILE CARD ──────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- Cover --}}
        @if($user->coverPhotoUrl())
            <x-image-lightbox :src="$user->coverPhotoUrl()" :alt="$user->name . ' — capa'">
                <div class="h-32 sm:h-44">
                    <img src="{{ $user->coverPhotoUrl() }}" alt="{{ $user->name }} — capa" class="w-full h-full object-cover">
                </div>
            </x-image-lightbox>
        @else
            <div class="h-32 sm:h-44 bg-gradient-to-br from-[#00c8ff]/40 via-blue-200/30 to-indigo-200/20 relative">
                <div class="absolute -top-8 -right-8 w-40 h-40 bg-white/10 rounded-full"></div>
            </div>
        @endif

        <div class="px-5 sm:px-8 pb-6">
            <div class="flex flex-col sm:flex-row sm:items-end gap-4 -mt-10 sm:-mt-12 mb-4">
                {{-- Avatar --}}
                <x-image-lightbox :src="$user->avatarUrl()" :alt="$user->name">
                    <div class="p-1 rounded-2xl bg-white shadow-md">
                        <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl object-cover block">
                    </div>
                </x-image-lightbox>

                {{-- Estatística de confiança --}}
                @if($completedProjects > 0)
                <div class="sm:pb-1">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-base font-bold text-gray-900">{{ $completedProjects }}</span>
                        <span class="text-xs text-gray-400">{{ $completedProjects === 1 ? 'projecto concluído' : 'projectos concluídos' }}</span>
                    </div>
                </div>
                @endif
            </div>

            <h1 class="text-xl font-bold text-gray-900 leading-tight">{{ $user->name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">Cliente na plataforma</p>

            @if($user->location && $user->isFieldPublic('location'))
                <p class="text-sm text-gray-400 mt-2 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ $user->location }}
                </p>
            @endif

            {{-- Sobre --}}
            @if($user->bio && $user->isFieldPublic('bio'))
                <p class="text-sm text-gray-600 leading-relaxed mt-4 pt-4 border-t border-gray-100">{!! nl2br(e($user->bio)) !!}</p>
            @endif

            {{-- Interesses --}}
            @if($user->profile?->interests)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Interesses</h3>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($user->profile->interests as $interest)
                            <span class="text-xs bg-[#0055ff]/8 text-[#0055ff] border border-[#0055ff]/20 px-2.5 py-1 rounded-full font-medium">{{ $interest }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
</div>
