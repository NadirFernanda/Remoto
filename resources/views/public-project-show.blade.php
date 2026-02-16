@extends('layouts.main')

@section('content')
    <div class="min-h-screen pt-24 section">
        <div class="container mx-auto px-4 py-12">
            <div class="container--card max-w-3xl mx-auto">
                <div class="flex items-start gap-6">
                    @php $briefing = json_decode($service->briefing, true); @endphp
                    @if(is_array($briefing) && !empty($briefing['thumbnail']) && file_exists(public_path('img/' . $briefing['thumbnail'])))
                        <img src="{{ asset('img/' . $briefing['thumbnail']) }}" alt="{{ $service->titulo }}" class="w-48 h-36 object-cover rounded-lg">
                    @else
                        <div class="project-avatar" style="width:72px;height:72px;font-size:1.5rem">{{ strtoupper(substr($service->titulo,0,1)) }}</div>
                    @endif

                    <div class="flex-1">
                        <h1 class="text-2xl font-extrabold mb-2 text-cyan-400">{{ $service->titulo }}</h1>
                        <div class="mb-4">
                            <span class="font-semibold text-cyan-400">Valor:</span>
                            <span class="text-white">Kz {{ number_format($service->valor, 2, ',', '.') }}</span>
                        </div>
                        <div class="mb-4 text-white/90">
                            @if(is_array($briefing))
                                @foreach($briefing as $k => $v)
                                    @if($k === 'thumbnail') @continue @endif
                                    <div><strong>{{ ucfirst(str_replace('_',' ',$k)) }}:</strong> {{ is_array($v) ? json_encode($v) : $v }}</div>
                                @endforeach
                            @else
                                {{ $service->briefing }}
                            @endif
                        </div>
                        <div class="flex gap-3">
                            <a href="/register" class="hero-btn">Entrar para aceitar</a>
                            <a href="/" class="hero-btn-outline">Voltar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
