@extends('layouts.main')

@section('main-padding', 'pt-0')
@section('main-style', 'background:#f8fafc')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-6 space-y-5">

    {{-- Voltar --}}
    <a href="{{ route('freelancers.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#0055ff] hover:underline">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Voltar aos freelancers
    </a>

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

                {{-- Rating --}}
                @if($reviewCount > 0)
                <div class="sm:pb-1">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-amber-400 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <span class="text-base font-bold text-gray-900">{{ number_format($avgRating, 1) }}</span>
                        <span class="text-xs text-gray-400">({{ $reviewCount }} {{ $reviewCount === 1 ? 'avaliação' : 'avaliações' }})</span>
                    </div>
                </div>
                @endif

                <div class="flex-1"></div>

                {{-- Ações --}}
                <div class="flex gap-2">
                    <a href="#report" title="Reportar" aria-label="Reportar"
                       class="w-10 h-10 flex items-center justify-center rounded-xl border-2 border-gray-200 text-gray-500 hover:border-gray-300 hover:bg-gray-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18M3 3h13l-1.5 4.5L16 12H3"/></svg>
                    </a>
                    <button onclick="Livewire.dispatch('openProposal', { recipientId: {{ $user->id }} })"
                        class="px-5 py-2 text-sm font-bold rounded-xl bg-[#0055ff] text-white hover:bg-[#0033cc] transition inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9-7-9-7v14z"/></svg>
                        Enviar proposta
                    </button>
                </div>
            </div>

            {{-- Nome + headline --}}
            <h1 class="text-xl font-bold text-gray-900 leading-tight">{{ $user->name }}</h1>
            @if($user->freelancerProfile?->headline)
                <p class="text-sm text-gray-500 mt-0.5">{{ $user->freelancerProfile->headline }}</p>
            @endif

            <div class="flex items-center gap-4 mt-2 flex-wrap text-sm">
                @if($user->freelancerProfile?->hourly_rate && $user->isFieldPublic('hourly_rate'))
                    <span class="font-bold text-[#0055ff]">Kz {{ number_format($user->freelancerProfile->hourly_rate, 0, ',', '.') }}/h</span>
                @endif
                @if($user->freelancerProfile?->availability_status && $user->isFieldPublic('availability_status'))
                    @php
                        $statusMap = ['available' => 'Disponível', 'busy' => 'Ocupado', 'unavailable' => 'Indisponível', 'vacation' => 'Em férias'];
                        $statusLabel = $statusMap[$user->freelancerProfile->availability_status] ?? ucfirst($user->freelancerProfile->availability_status);
                    @endphp
                    <span class="text-gray-500">· {{ $statusLabel }}</span>
                @endif
                @if($user->location && $user->isFieldPublic('location'))
                    <span class="text-gray-400 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $user->location }}
                    </span>
                @endif
            </div>

            {{-- Sobre --}}
            @if($user->freelancerProfile?->summary && $user->isFieldPublic('summary'))
                <p class="text-sm text-gray-600 leading-relaxed mt-4 pt-4 border-t border-gray-100">{!! nl2br(e($user->freelancerProfile->summary)) !!}</p>
            @endif

            {{-- Skills --}}
            @if($user->freelancerProfile?->skills && $user->isFieldPublic('skills'))
                <div class="mt-4 flex flex-wrap gap-1.5">
                    @foreach($user->freelancerProfile->skills as $skill)
                        <span class="text-xs bg-[#0055ff]/8 text-[#0055ff] border border-[#0055ff]/20 px-2.5 py-1 rounded-full font-medium">{{ $skill }}</span>
                    @endforeach
                </div>
            @endif

            {{-- Idiomas --}}
            @if($user->freelancerProfile?->languages && $user->isFieldPublic('languages'))
                <div class="mt-2 flex flex-wrap gap-1.5">
                    @foreach($user->freelancerProfile->languages as $lang)
                        <span class="text-xs bg-gray-50 text-gray-600 border border-gray-200 px-2.5 py-1 rounded-full font-medium">{{ $lang }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ── Experiência profissional ─────────────────────────────────────── --}}
    @if($user->workExperiences->isNotEmpty() && $user->isFieldPublic('work_experience'))
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400 mb-4">Experiência profissional</h3>
        <div class="flex flex-col gap-4">
            @foreach($user->workExperiences as $exp)
                <div class="flex gap-3">
                    <div class="w-9 h-9 rounded-lg bg-[#0055ff]/8 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-[#0055ff]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-900">{{ $exp->titulo }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $exp->empresa }}@if($exp->cidade || $exp->pais) · {{ trim(($exp->cidade ?? '') . (($exp->cidade && $exp->pais) ? ', ' : '') . ($exp->pais ?? '')) }}@endif
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $exp->mes_inicio ? str_pad($exp->mes_inicio, 2, '0', STR_PAD_LEFT) . '/' : '' }}{{ $exp->ano_inicio }}
                            —
                            {{ $exp->atual ? 'Actual' : (($exp->mes_fim ? str_pad($exp->mes_fim, 2, '0', STR_PAD_LEFT) . '/' : '') . $exp->ano_fim) }}
                        </p>
                        @if($exp->descricao)
                            <p class="text-sm text-gray-600 mt-1.5 leading-relaxed">{{ $exp->descricao }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Educação ──────────────────────────────────────────────────────── --}}
    @if($user->educations->isNotEmpty() && $user->isFieldPublic('education'))
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400 mb-4">Educação</h3>
        <div class="flex flex-col gap-4">
            @foreach($user->educations as $edu)
                <div class="flex gap-3">
                    <div class="w-9 h-9 rounded-lg bg-violet-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-900">{{ $edu->escola }}</p>
                        @if($edu->grau || $edu->area_estudo)
                            <p class="text-xs text-gray-500">{{ trim(($edu->grau ?? '') . (($edu->grau && $edu->area_estudo) ? ', ' : '') . ($edu->area_estudo ?? '')) }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-0.5">{{ $edu->ano_inicio }} — {{ $edu->atual ? 'Actual' : $edu->ano_fim }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Portfólio ─────────────────────────────────────────────────────── --}}
    @if($user->portfolios && $user->portfolios->count() && $user->isFieldPublic('portfolio'))
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400 mb-4">Portfólio</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @foreach($user->portfolios as $item)
                @php
                    $mediaUrl = (Str::startsWith($item->media_path, 'http') || Str::startsWith($item->media_path, '/'))
                        ? $item->media_path
                        : asset('storage/' . $item->media_path);
                @endphp
                <div class="rounded-xl overflow-hidden border border-gray-100 bg-gray-50">
                    <x-image-lightbox :src="$mediaUrl" :alt="$item->title ?? 'Portfólio'" trigger-class="block">
                        <img src="{{ $mediaUrl }}" alt="portfolio" class="w-full h-32 object-cover">
                    </x-image-lightbox>
                    @if($item->title)
                    <div class="p-3">
                        <p class="text-xs font-bold text-gray-900 truncate">{{ $item->title }}</p>
                        @if($item->description)
                            <p class="text-xs text-gray-400 mt-0.5 line-clamp-2">{{ $item->description }}</p>
                        @endif
                    </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Avaliações ────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400 mb-4">Avaliações</h3>

        @if($reviewCount === 0)
            <p class="text-sm text-gray-400">Este freelancer ainda não tem avaliações.</p>
        @else
            <div class="flex flex-col gap-4">
                @foreach($user->reviewsReceived as $review)
                <div class="flex items-start gap-3">
                    <img src="{{ $review->author->avatarUrl() }}" alt="{{ $review->author->name }}" class="w-9 h-9 rounded-full object-cover flex-shrink-0" onerror="this.src='{{ asset('img/default-avatar.svg') }}'">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <span class="text-sm font-bold text-gray-900">{{ $review->author->name }}</span>
                            <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex gap-0.5 my-1">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="{{ $i <= $review->rating ? '#facc15' : '#e5e7eb' }}"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        @if($review->comment)
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $review->comment }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- Modal fora de qualquer container para evitar clipping de overflow/transform --}}
@livewire('client.send-proposal')

@endsection
