@extends('layouts.main')

@section('content')
@php $briefing = json_decode($service->briefing, true); @endphp
<div class="pub-page">
    <div class="pub-container--md" style="padding-top:2rem;padding-bottom:3rem;">

        {{-- Voltar --}}
        <a href="{{ route('public.projects') }}" class="pub-back" style="display:inline-flex;align-items:center;gap:.4rem;color:#00baff;font-weight:700;font-size:.875rem;text-decoration:none;margin-bottom:1.5rem;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M15 19l-7-7 7-7"/></svg>
            Voltar aos projetos
        </a>

        <div class="pub-card" style="padding:2rem;">
            <div style="display:flex;align-items:flex-start;gap:1.5rem;flex-wrap:wrap;">
                {{-- Thumbnail --}}
                @if(is_array($briefing) && !empty($briefing['thumbnail']) && file_exists(public_path('img/' . $briefing['thumbnail'])))
                    <img src="{{ asset('img/' . $briefing['thumbnail']) }}" alt="{{ $service->titulo }}"
                        style="width:200px;height:150px;object-fit:cover;border-radius:12px;flex-shrink:0;">
                @else
                    <div style="width:80px;height:80px;border-radius:16px;background:linear-gradient(135deg,rgba(0,186,255,.18),rgba(0,186,255,.06));display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <span style="font-size:1.75rem;font-weight:900;color:#00baff;">{{ strtoupper(substr($service->titulo,0,1)) }}</span>
                    </div>
                @endif

                {{-- Conteúdo principal --}}
                <div style="flex:1;min-width:0;">
                    <h1 style="font-size:1.5rem;font-weight:900;color:#0f172a;margin:0 0 .5rem;line-height:1.25;">{{ $service->titulo }}</h1>

                    <div class="pub-price" style="margin-bottom:1.25rem;">Kz {{ number_format($service->valor, 2, ',', '.') }}</div>

                    {{-- Detalhes da briefing --}}
                    <div style="color:#475569;font-size:.9rem;line-height:1.7;">
                        @if(is_array($briefing))
                            @foreach($briefing as $k => $v)
                                @if($k === 'thumbnail') @continue @endif
                                <div style="margin-bottom:.5rem;">
                                    <strong style="color:#0f172a;min-width:130px;display:inline-block;">{{ ucfirst(str_replace('_',' ',$k)) }}:</strong>
                                    {{ is_array($v) ? implode(', ', $v) : $v }}
                                </div>
                            @endforeach
                        @else
                            <p>{{ $service->briefing }}</p>
                        @endif
                    </div>

                    {{-- Ações --}}
                    <div style="display:flex;flex-wrap:wrap;gap:.75rem;margin-top:1.75rem;">
                        @guest
                            <a href="{{ route('register') }}" class="pub-btn-primary">Criar conta para aceitar</a>
                            <a href="{{ route('login') }}" class="pub-btn-secondary">Já tenho conta</a>
                        @else
                            @php $role = auth()->user()->activeRole(); @endphp
                            @if($role === 'freelancer')
                                @if(auth()->id() === $service->cliente_id)
                                    <span style="background:#f1f5f9;color:#64748b;font-weight:700;padding:.6rem 1.25rem;border-radius:10px;font-size:.9rem;">Este é o seu projeto</span>
                                @elseif($service->status === 'published')
                                    <form method="POST" action="{{ route('service.candidatar', $service->id) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="pub-btn-primary">Candidatar-me a este projeto</button>
                                    </form>
                                @else
                                    <span style="background:#f1f5f9;color:#64748b;font-weight:700;padding:.6rem 1.25rem;border-radius:10px;font-size:.9rem;">Projeto não disponível</span>
                                @endif
                            @elseif(in_array($role, ['cliente', 'client']))
                                @if(auth()->user()->canSwitchRole())
                                    <form method="POST" action="{{ route('switch.role') }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="pub-btn-primary">Mudar para Freelancer e aceitar</button>
                                    </form>
                                @else
                                    <a href="{{ route('register') }}" class="pub-btn-primary">Criar perfil Freelancer</a>
                                @endif
                            @elseif($role === 'admin')
                                <span style="background:#f1f5f9;color:#64748b;font-weight:700;padding:.6rem 1.25rem;border-radius:10px;font-size:.9rem;">Visualização administrativa</span>
                            @endif
                        @endguest
                        <a href="{{ route('public.projects') }}" class="pub-btn-secondary">Voltar à lista</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
