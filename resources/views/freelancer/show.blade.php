@extends('layouts.main')

@section('content')
@php use Illuminate\Support\Str; @endphp
<div class="light-page min-h-screen pt-8 pb-12">
<div class="max-w-4xl mx-auto px-4 py-8">
    <a href="{{ route('freelancers.index') }}" class="inline-flex items-center gap-1 text-[#00baff] font-semibold mb-6 hover:underline">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Voltar aos freelancers
    </a>
    <div class="bg-white p-6 rounded-2xl shadow">
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
                    <a href="#report" class="btn-outline">Reportar</a>
                    <button onclick="Livewire.emit('openProposal', {{ $user->id }})" class="px-3 py-1.5 text-sm rounded-full bg-blue-600 text-white hover:bg-blue-700">Enviar proposta</button>
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
    </div>
</div>
</div>
@endsection
