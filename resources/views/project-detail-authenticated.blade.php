@php
    // Versão desta página para quem já está autenticado — construída de raiz
    // com as MESMAS classes Tailwind usadas em "Projectos Disponíveis"
    // (available-projects.blade.php), em vez de reaproveitar os estilos da
    // página pública (pensada para visitantes, fundo escuro com cartão quase
    // transparente) que ficava com o texto ilegível dentro do dashboard.
    $briefing = json_decode($service->briefing, true);

    $labelMap = [
        'title'           => 'Título',
        'business_type'   => 'Tipo de negócio',
        'necessity'       => 'Necessidade',
        'target_audience' => 'Público-alvo',
        'usage'           => 'Utilização prevista',
        'budget'          => 'Orçamento',
        'deadline'        => 'Prazo',
        'description'     => 'Descrição',
        'objectives'      => 'Objectivos',
        'references'      => 'Referências',
        'notes'           => 'Notas adicionais',
        'platform'        => 'Plataforma',
        'features'        => 'Funcionalidades',
        'technologies'    => 'Tecnologias',
        'style'           => 'Estilo',
        'tone'            => 'Tom de comunicação',
        'language'        => 'Idioma',
        'pages'           => 'Número de páginas',
        'integrations'    => 'Integrações',
    ];

    $role = auth()->user()->activeRole();
@endphp
@extends('layouts.dashboard')

@php $dashboardTitle = $service->titulo; @endphp

@section('dashboard-content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="rounded-2xl shadow-lg p-6 border border-slate-100 bg-white text-gray-900">
        <div class="flex items-start justify-between gap-4 flex-wrap mb-4">
            <div class="flex items-start gap-4 min-w-0">
                @if(is_array($briefing) && !empty($briefing['thumbnail']) && file_exists(public_path('img/' . $briefing['thumbnail'])))
                    <img src="{{ asset('img/' . $briefing['thumbnail']) }}" alt="{{ $service->titulo }}"
                        class="w-20 h-20 rounded-2xl object-cover flex-shrink-0">
                @else
                    <div class="w-20 h-20 rounded-2xl flex-shrink-0 flex items-center justify-center bg-gradient-to-br from-sky-50 to-sky-100">
                        <span class="text-2xl font-black text-sky-500">{{ strtoupper(substr($service->titulo, 0, 1)) }}</span>
                    </div>
                @endif
                <div class="min-w-0">
                    <h1 class="text-xl font-extrabold text-slate-800 leading-snug">{{ $service->titulo }}</h1>
                </div>
            </div>
            <div class="text-right flex-shrink-0">
                <div class="text-[11px] uppercase tracking-wide text-gray-400">Valor</div>
                <div class="text-lg font-bold text-emerald-600">Kz {{ number_format($service->valor, 2, ',', '.') }}</div>
            </div>
        </div>

        <div class="text-sm text-gray-700 leading-relaxed space-y-2">
            @if(is_array($briefing))
                @foreach($briefing as $k => $v)
                    @if($k === 'thumbnail') @continue @endif
                    @php
                        $label = $labelMap[$k] ?? ucwords(str_replace('_', ' ', $k));
                        $display = is_array($v) ? implode(', ', $v) : $v;
                    @endphp
                    @if(!empty($display))
                        <div><strong class="text-gray-900 font-semibold">{{ $label }}:</strong> {{ $display }}</div>
                    @endif
                @endforeach
            @else
                <p>{{ $service->briefing }}</p>
            @endif
        </div>

        @if($service->cliente)
        <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-xl p-4 mt-6 flex-wrap">
            <img src="{{ $service->cliente->avatarUrl() }}"
                 alt="{{ $service->cliente->name }}"
                 class="w-11 h-11 rounded-full object-cover flex-shrink-0"
                 onerror="this.onerror=null;this.src='{{ asset('img/default-avatar.svg') }}'">
            <div class="min-w-0 flex-1">
                <p class="text-xs text-gray-400">Publicado por</p>
                <p class="text-sm font-bold text-gray-900 truncate">{{ $service->cliente->name }}</p>
                @if($service->cliente->location)
                    <p class="text-xs text-gray-400">{{ $service->cliente->location }}</p>
                @endif
            </div>
            <a href="{{ route('client.public', $service->cliente) }}"
               class="flex-shrink-0 text-sm font-semibold text-sky-600 border border-sky-400 hover:bg-sky-50 rounded-lg px-3 py-1.5 transition">
                Ver perfil
            </a>
        </div>
        @endif

        <div class="flex flex-wrap gap-3 mt-6">
            @if($role === 'freelancer')
                @if(auth()->id() === $service->cliente_id)
                    <span class="bg-gray-100 text-gray-500 font-semibold px-5 py-2.5 rounded-lg text-sm">Este é o seu projecto</span>
                @elseif($service->status === 'published')
                    <form method="POST" action="{{ route('service.candidatar', $service->id) }}">
                        @csrf
                        <button type="submit" class="bg-gradient-to-r from-[#00c8ff] to-[#0055ff] hover:from-sky-400 hover:to-blue-600 text-white font-semibold py-2.5 px-5 rounded-lg transition-all text-sm shadow-sm">
                            Candidatar-me a este projecto
                        </button>
                    </form>
                @else
                    <span class="bg-gray-100 text-gray-500 font-semibold px-5 py-2.5 rounded-lg text-sm">Projecto não disponível</span>
                @endif
            @elseif(in_array($role, ['cliente', 'client']))
                @if(auth()->user()->canSwitchRole())
                    <form method="POST" action="{{ route('switch.role') }}">
                        @csrf
                        <button type="submit" class="bg-gradient-to-r from-[#00c8ff] to-[#0055ff] hover:from-sky-400 hover:to-blue-600 text-white font-semibold py-2.5 px-5 rounded-lg transition-all text-sm shadow-sm">
                            Mudar para Freelancer e aceitar
                        </button>
                    </form>
                @else
                    <a href="{{ route('register') }}" class="bg-gradient-to-r from-[#00c8ff] to-[#0055ff] hover:from-sky-400 hover:to-blue-600 text-white font-semibold py-2.5 px-5 rounded-lg transition-all text-sm shadow-sm">
                        Criar perfil Freelancer
                    </a>
                @endif
            @elseif($role === 'admin')
                <span class="bg-gray-100 text-gray-500 font-semibold px-5 py-2.5 rounded-lg text-sm">Visualização administrativa</span>
            @endif
        </div>
    </div>
</div>
@endsection
