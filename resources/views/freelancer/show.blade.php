@extends('layouts.main')

@section('content')
@php use Illuminate\Support\Str; @endphp
<div class="pub-page">
<div class="pub-container--md" style="padding-top:2rem;padding-bottom:3rem;">

    {{-- Voltar --}}
    <a href="{{ route('freelancers.index') }}" style="display:inline-flex;align-items:center;gap:.4rem;color:#00baff;font-weight:700;font-size:.875rem;text-decoration:none;margin-bottom:1.5rem;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M15 19l-7-7 7-7"/></svg>
        Voltar aos freelancers
    </a>

    {{-- Card principal --}}
    <div class="pub-card" style="padding:1.75rem;">
        <div style="display:flex;align-items:flex-start;gap:1.5rem;flex-wrap:wrap;">
            {{-- Avatar --}}
            <div style="width:88px;height:88px;border-radius:14px;overflow:hidden;flex-shrink:0;border:2px solid #e8edf3;">
                <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" style="width:100%;height:100%;object-fit:cover;">
            </div>

            {{-- Nome + headline --}}
            <div style="flex:1;min-width:0;">
                <h1 style="font-size:1.35rem;font-weight:900;color:#0f172a;margin:0 0 .3rem;">{{ $user->name }}</h1>
                @if($user->freelancerProfile && $user->freelancerProfile->headline)
                    <p style="font-size:.9rem;color:#475569;margin:0 0 .6rem;">{{ $user->freelancerProfile->headline }}</p>
                @endif
                <div style="display:flex;gap:1rem;font-size:.8rem;color:#64748b;flex-wrap:wrap;">
                    @if($user->freelancerProfile && $user->freelancerProfile->hourly_rate)
                        <span style="font-weight:700;color:#00baff;">{{ number_format($user->freelancerProfile->hourly_rate,2) }} {{ $user->freelancerProfile->currency }}/h</span>
                    @endif
                    @if($user->freelancerProfile && $user->freelancerProfile->availability_status)
                        <span>· {{ ucfirst($user->freelancerProfile->availability_status) }}</span>
                    @endif
                </div>
            </div>

            {{-- Ações --}}
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.5rem;">
                <div style="display:flex;gap:.5rem;">
                    <a href="#report" class="btn-outline action-icon" title="Reportar" aria-label="Reportar">
                        @include('components.icon', ['name' => 'flag', 'class' => 'w-5 h-5'])
                    </a>
                    <button onclick="Livewire.emit('openProposal', {{ $user->id }})" class="btn-primary action-icon" title="Enviar proposta" aria-label="Enviar proposta">
                        @include('components.icon', ['name' => 'send', 'class' => 'w-5 h-5'])
                    </button>
                </div>
                @livewire('client.send-proposal')
            </div>
        </div>

        {{-- Sobre --}}
        @if($user->freelancerProfile && $user->freelancerProfile->summary)
        <div style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid #f1f5f9;">
            <h2 class="pub-section-title">Sobre</h2>
            <p style="color:#475569;font-size:.9rem;line-height:1.7;margin:0;">{!! nl2br(e($user->freelancerProfile->summary)) !!}</p>
        </div>
        @endif

        {{-- Skills --}}
        @if($user->freelancerProfile && $user->freelancerProfile->skills)
        <div style="margin-top:1.25rem;">
            <h3 class="pub-section-title">Competências</h3>
            <div style="display:flex;flex-wrap:wrap;gap:.5rem;margin-top:.6rem;">
                @foreach($user->freelancerProfile->skills as $skill)
                    <span class="pub-skill">{{ $skill }}</span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Idiomas --}}
        @if($user->freelancerProfile && $user->freelancerProfile->languages)
        <div style="margin-top:1.25rem;">
            <h3 class="pub-section-title">Idiomas</h3>
            <div style="display:flex;flex-wrap:wrap;gap:.5rem;margin-top:.6rem;">
                @foreach($user->freelancerProfile->languages as $lang)
                    <span class="pub-skill" style="background:#f8fafc;color:#475569;">{{ $lang }}</span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Portfólio --}}
        @if($user->portfolios && $user->portfolios->count())
        <div style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid #f1f5f9;">
            <h3 class="pub-section-title">Portfólio</h3>
            <div class="pub-portfolio-grid">
                @foreach($user->portfolios as $item)
                    <div class="pub-portfolio-item">
                        @if(Str::startsWith($item->media_path, 'http') || Str::startsWith($item->media_path, '/'))
                            <img src="{{ $item->media_path }}" alt="portfolio" style="width:100%;height:160px;object-fit:cover;">
                        @else
                            <img src="{{ asset('storage/' . $item->media_path) }}" alt="portfolio" style="width:100%;height:160px;object-fit:cover;">
                        @endif
                        @if($item->title)
                        <div style="padding:.75rem;">
                            <div style="font-weight:700;font-size:.875rem;color:#0f172a;">{{ $item->title }}</div>
                            @if($item->description)
                                <div style="font-size:.8rem;color:#64748b;margin-top:.25rem;">{{ $item->description }}</div>
                            @endif
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Avaliações --}}
        <div style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid #f1f5f9;">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;margin-bottom:1rem;">
                <h3 class="pub-section-title" style="margin-bottom:0;">Avaliações</h3>
                @if($reviewCount > 0)
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <div style="display:flex;gap:.15rem;">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($avgRating))
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="#facc15"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @else
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="#d1d5db"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endif
                        @endfor
                    </div>
                    <span style="font-weight:800;color:#0f172a;font-size:.9rem;">{{ number_format($avgRating, 1) }}</span>
                    <span style="color:#94a3b8;font-size:.8rem;">({{ $reviewCount }} {{ $reviewCount === 1 ? 'avaliação' : 'avaliações' }})</span>
                </div>
                @endif
            </div>

            @if($reviewCount === 0)
                <p style="color:#94a3b8;font-size:.875rem;">Este freelancer ainda não tem avaliações.</p>
            @else
                <div style="display:flex;flex-direction:column;gap:.75rem;">
                    @foreach($user->reviewsReceived as $review)
                    <div class="pub-review">
                        <div style="display:flex;align-items:flex-start;gap:.75rem;">
                            <img src="{{ $review->author->avatarUrl() }}" alt="{{ $review->author->name }}" style="width:38px;height:38px;border-radius:50%;object-fit:cover;flex-shrink:0;border:2px solid #e8edf3;">
                            <div style="flex:1;min-width:0;">
                                <div style="display:flex;align-items:center;justify-content:space-between;gap:.5rem;flex-wrap:wrap;margin-bottom:.3rem;">
                                    <span style="font-weight:700;font-size:.875rem;color:#0f172a;">{{ $review->author->name }}</span>
                                    <span style="font-size:.75rem;color:#94a3b8;">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                <div style="display:flex;gap:.15rem;margin-bottom:.4rem;">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <svg width="14" height="14" viewBox="0 0 20 20" fill="#facc15"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @else
                                            <svg width="14" height="14" viewBox="0 0 20 20" fill="#d1d5db"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endif
                                    @endfor
                                </div>
                                @if($review->comment)
                                    <p style="font-size:.875rem;color:#475569;margin:0;line-height:1.5;">{{ $review->comment }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
</div>
@endsection
        <div class="flex items-start gap-6">
            <div class="w-24 h-24 bg-gray-100 rounded-lg overflow-hidden">
                <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
            </div>
            <div class="flex-1">
                <h1 class="text-2xl font-semibold">{{ $user->name }}</h1>
                @if($user->freelancerProfile && $user->freelancerProfile->headline)
                    <div class="text-gray-600">{{ $user->freelancerProfile->headline }}</div>
                @endif
                <div class="mt-3 flex gap-3 text-sm text-gray-500">
                    @if($user->freelancerProfile && $user->freelancerProfile->hourly_rate)
                        <div>Taxa: {{ number_format($user->freelancerProfile->hourly_rate,2) }} {{ $user->freelancerProfile->currency }}</div>
                    @endif
                    @if($user->freelancerProfile && $user->freelancerProfile->availability_status)
                        <div>• {{ ucfirst($user->freelancerProfile->availability_status) }}</div>
                    @endif
                </div>
            </div>
            <div class="text-right flex flex-col items-end gap-2">
                <div class="flex items-center gap-2">
                    <a href="#report" class="btn-outline action-icon" title="Reportar" aria-label="Reportar">
                        @include('components.icon', ['name' => 'flag', 'class' => 'w-5 h-5'])
                    </a>
                    <button onclick="Livewire.emit('openProposal', {{ $user->id }})" class="btn-primary action-icon" title="Enviar proposta" aria-label="Enviar proposta">
                        @include('components.icon', ['name' => 'send', 'class' => 'w-5 h-5'])
                    </button>
                </div>
                @livewire('client.send-proposal')
            </div>
        </div>

        @if($user->freelancerProfile && $user->freelancerProfile->summary)
        <div class="mt-6">
            <h2 class="text-lg font-medium">Sobre</h2>
            <p class="text-gray-700 mt-2">{!! nl2br(e($user->freelancerProfile->summary)) !!}</p>
        </div>
        @endif

        @if($user->freelancerProfile && $user->freelancerProfile->skills)
        <div class="mt-4">
            <h3 class="font-medium">Skills</h3>
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach($user->freelancerProfile->skills as $skill)
                    <span class="px-3 py-1 bg-gray-100 rounded-full text-sm">{{ $skill }}</span>
                @endforeach
            </div>
        </div>
        @endif

        @if($user->freelancerProfile && $user->freelancerProfile->languages)
        <div class="mt-4">
            <h3 class="font-medium">Idiomas</h3>
            <div class="mt-2 flex flex-wrap gap-2 text-sm text-gray-600">
                @foreach($user->freelancerProfile->languages as $lang)
                    <span>{{ $lang }}</span>
                @endforeach
            </div>
        </div>
        @endif

        @if($user->portfolios && $user->portfolios->count())
        <div class="mt-6">
            <h3 class="font-medium">Portfólio</h3>
            <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($user->portfolios as $item)
                    <div class="border rounded-lg overflow-hidden">
                        @if(Str::startsWith($item->media_path, 'http') || Str::startsWith($item->media_path, '/'))
                            <img src="{{ $item->media_path }}" alt="portfolio" class="w-full h-40 object-cover">
                        @else
                            <img src="{{ asset('storage/' . $item->media_path) }}" alt="portfolio" class="w-full h-40 object-cover">
                        @endif
                        @if($item->title)
                        <div class="p-3">
                            <div class="font-medium">{{ $item->title }}</div>
                            @if($item->description)
                                <div class="text-sm text-gray-600">{{ $item->description }}</div>
                            @endif
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ======= AVALIAÇÕES ======= --}}
        <div class="mt-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-medium text-lg">Avaliações</h3>
                @if($reviewCount > 0)
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($avgRating))
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @else
                                <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endif
                        @endfor
                    </div>
                    <span class="font-semibold text-gray-800">{{ number_format($avgRating, 1) }}</span>
                    <span class="text-gray-500 text-sm">({{ $reviewCount }} {{ $reviewCount === 1 ? 'avaliação' : 'avaliações' }})</span>
                </div>
                @endif
            </div>

            @if($reviewCount === 0)
                <p class="text-gray-500 text-sm">Este freelancer ainda não tem avaliações.</p>
            @else
                <div class="space-y-4">
                    @foreach($user->reviewsReceived as $review)
                    <div class="border rounded-xl p-4 bg-gray-50">
                        <div class="flex items-start gap-3">
                            <img src="{{ $review->author->avatarUrl() }}" alt="{{ $review->author->name }}" class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2 flex-wrap">
                                    <span class="font-medium text-sm">{{ $review->author->name }}</span>
                                    <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="flex items-center gap-0.5 mt-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @else
                                            <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endif
                                    @endfor
                                </div>
                                @if($review->comment)
                                    <p class="mt-2 text-sm text-gray-700">{{ $review->comment }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
</div>
@endsection
