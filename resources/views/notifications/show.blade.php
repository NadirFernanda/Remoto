@extends('layouts.main')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">
    <a href="{{ url()->previous() }}" class="inline-flex items-center gap-1 text-sm text-[#00baff] hover:underline mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Voltar
    </a>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-5 border-b border-gray-50" style="background:linear-gradient(135deg,#f0f9ff 0%,#e0f2fe 100%);">
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#0070ff] bg-blue-50 border border-blue-100 rounded-full px-3 py-0.5">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                    </svg>
                    Mensagem do administrador {{ $notification->sender_name ?? 'Administração' }}
                </span>
            </div>
            <h1 class="text-xl font-bold text-gray-900 mt-2">{{ $notification->title ?: 'Mensagem do Admin' }}</h1>
            <div class="flex items-center gap-3 mt-2">
                <div class="flex items-center gap-1.5">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                         style="background:linear-gradient(135deg,#0070ff,#00baff);">
                        {{ strtoupper(substr($notification->sender_name ?? 'A', 0, 1)) }}
                    </div>
                    <span class="text-sm font-medium text-gray-700">{{ $notification->sender_name ?? 'Administração' }}</span>
                </div>
                <span class="text-gray-300">·</span>
                <span class="text-xs text-gray-400">{{ $notification->created_at->format('d/m/Y \à\s H:i') }}</span>
            </div>
        </div>

        {{-- Corpo --}}
        <div class="px-6 py-6">
            <p class="text-gray-700 leading-relaxed text-base whitespace-pre-line">{{ $notification->message }}</p>
        </div>
    </div>
</div>
@endsection
