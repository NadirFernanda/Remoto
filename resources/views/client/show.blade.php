@extends('layouts.main')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-sky-50/40 pb-16">

    {{-- ── Header ── --}}
    <div class="bg-white border-b border-slate-100 shadow-sm">
        <div class="max-w-6xl mx-auto px-6 py-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#00baff] to-blue-600 flex items-center justify-center shadow-lg shadow-sky-200">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800 leading-tight">Perfil do Cliente</h1>
                <p class="text-sm text-slate-500">Quem está por trás deste projecto</p>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-6 pt-8">
    <div class="pub-container--md" style="padding-top:0.75rem;padding-bottom:3rem;">

    {{-- Card principal --}}
    <div class="pub-card" style="padding:1.75rem;">
        <div style="display:flex;align-items:flex-start;gap:1.5rem;flex-wrap:wrap;">
            {{-- Avatar --}}
            <x-image-lightbox :src="$user->avatarUrl()" :alt="$user->name">
                <div style="width:88px;height:88px;border-radius:14px;overflow:hidden;flex-shrink:0;border:2px solid #e8edf3;">
                    <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" style="width:100%;height:100%;object-fit:cover;">
                </div>
            </x-image-lightbox>

            {{-- Nome + localização --}}
            <div style="flex:1;min-width:0;">
                <h1 style="font-size:1.35rem;font-weight:900;color:#0f172a;margin:0 0 .3rem;">{{ $user->name }}</h1>
                <p style="font-size:.85rem;color:#64748b;margin:0 0 .3rem;">Cliente na plataforma</p>
                @if($user->location)
                    <p style="font-size:.8rem;color:#64748b;display:flex;align-items:center;gap:.3rem;margin:0;">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $user->location }}
                    </p>
                @endif
            </div>

            {{-- Estatística de confiança --}}
            @if($completedProjects > 0)
            <div style="text-align:center;background:#f8fafc;border-radius:12px;padding:.75rem 1.25rem;">
                <p style="font-size:1.35rem;font-weight:900;color:#00baff;margin:0;line-height:1;">{{ $completedProjects }}</p>
                <p style="font-size:.7rem;color:#64748b;margin:.25rem 0 0;">{{ $completedProjects === 1 ? 'projecto concluído' : 'projectos concluídos' }}</p>
            </div>
            @endif
        </div>

        {{-- Interesses --}}
        @if($user->profile?->interests)
        <div style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid #f1f5f9;">
            <h3 class="pub-section-title">Interesses</h3>
            <div style="display:flex;flex-wrap:wrap;gap:.5rem;margin-top:.6rem;">
                @foreach($user->profile->interests as $interest)
                    <span class="pub-skill">{{ $interest }}</span>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    </div>
    </div>
</div>
@endsection
