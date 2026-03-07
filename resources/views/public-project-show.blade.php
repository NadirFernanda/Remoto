@extends('layouts.main')

@section('content')
    <div class="light-page min-h-screen pt-8 pb-12">
        <div class="container mx-auto px-4 py-12 max-w-3xl">
            <a href="{{ route('public.projects') }}" class="inline-flex items-center gap-1 text-[#00baff] font-semibold mb-6 hover:underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Voltar aos projetos
            </a>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-start gap-6">
                    @php $briefing = json_decode($service->briefing, true); @endphp
                    @if(is_array($briefing) && !empty($briefing['thumbnail']) && file_exists(public_path('img/' . $briefing['thumbnail'])))
                        <img src="{{ asset('img/' . $briefing['thumbnail']) }}" alt="{{ $service->titulo }}" class="w-48 h-36 object-cover rounded-lg">
                    @else
                        <div class="w-20 h-20 rounded-xl bg-gradient-to-br from-[#00baff]/20 to-cyan-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-2xl font-extrabold text-[#00baff]">{{ strtoupper(substr($service->titulo,0,1)) }}</span>
                        </div>
                    @endif

                    <div class="flex-1">
                        <h1 class="text-2xl font-extrabold mb-2 text-gray-800">{{ $service->titulo }}</h1>
                        <div class="mb-4">
                            <span class="text-lg font-bold text-[#00baff]">Kz {{ number_format($service->valor, 2, ',', '.') }}</span>
                        </div>
                        <div class="mb-4 text-gray-600 leading-relaxed">
                            @if(is_array($briefing))
                                @foreach($briefing as $k => $v)
                                    @if($k === 'thumbnail') @continue @endif
                                    <div class="mb-1"><strong class="text-gray-700">{{ ucfirst(str_replace('_',' ',$k)) }}:</strong> {{ is_array($v) ? json_encode($v) : $v }}</div>
                                @endforeach
                            @else
                                {{ $service->briefing }}
                            @endif
                        </div>
                        <div class="flex gap-3 mt-6">
                            @guest
                                {{-- Não logado: convidar a registar como freelancer --}}
                                <a href="{{ route('register') }}" class="bg-[#00baff] text-white font-bold px-6 py-2.5 rounded-lg hover:bg-[#009ad6] transition">Criar conta para aceitar</a>
                                <a href="{{ route('login') }}" class="border border-gray-300 text-gray-600 font-semibold px-6 py-2.5 rounded-lg hover:bg-gray-50 transition">Já tenho conta</a>
                            @else
                                @php $role = auth()->user()->activeRole(); @endphp
                                @if($role === 'freelancer')
                                    {{-- Freelancer logado: aceitar directamente --}}
                                    @if($service->status === 'published')
                                        <form method="POST" action="{{ route('service.candidatar', $service->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-[#00baff] text-white font-bold px-6 py-2.5 rounded-lg hover:bg-[#009ad6] transition">Candidatar-me a este projeto</button>
                                        </form>
                                    @else
                                        <span class="bg-gray-100 text-gray-500 font-semibold px-6 py-2.5 rounded-lg">Projeto não disponível</span>
                                    @endif
                                @elseif(in_array($role, ['cliente', 'client']))
                                    {{-- Cliente logado: sugerir mudar para freelancer --}}
                                    @if(auth()->user()->canSwitchRole())
                                        <form method="POST" action="{{ route('switch.role') }}" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-[#00baff] text-white font-bold px-6 py-2.5 rounded-lg hover:bg-[#009ad6] transition">
                                                Mudar para Freelancer e aceitar
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('register') }}" class="bg-[#00baff] text-white font-bold px-6 py-2.5 rounded-lg hover:bg-[#009ad6] transition">Criar perfil Freelancer</a>
                                    @endif
                                @elseif($role === 'admin')
                                    {{-- Admin: apenas visualização --}}
                                    <span class="bg-gray-100 text-gray-500 font-semibold px-6 py-2.5 rounded-lg">Visualização administrativa</span>
                                @endif
                            @endguest
                            <a href="{{ route('public.projects') }}" class="border border-gray-300 text-gray-600 font-semibold px-6 py-2.5 rounded-lg hover:bg-gray-50 transition">Voltar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
