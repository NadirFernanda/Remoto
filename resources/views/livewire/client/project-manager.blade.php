<div class="space-y-6">

    {{-- ─── Header ─────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Gestão de Projectos</h1>
            <p class="text-sm text-gray-500">Acompanhe o progresso, milestones e entregas dos seus projectos.</p>
        </div>
        <a href="{{ route('client.briefing') }}" class="btn-primary self-start">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Novo Projecto
        </a>
    </div>

    {{-- ─── Pipeline Tabs ───────────────────────────────────── --}}
    @php
        $tabs = [
            ''                   => 'Todos',
            'published'          => 'Publicado',
            'negotiating'        => 'Em Negociação',
            'accepted'           => 'Proposta Aceita',
            'in_progress'        => 'Em Andamento',
            'delivered'          => 'Aguard. Revisão',
            'revision_requested' => 'Revisão Pedida',
            'completed'          => 'Concluído',
            'cancelled'          => 'Cancelado',
        ];
    @endphp
    <div class="flex gap-2 flex-wrap">
        @foreach($tabs as $val => $label)
            <button
                wire:click="$set('statusFilter', '{{ $val }}')"
                class="px-3 py-1.5 rounded-[10px] text-xs font-medium border transition
                    {{ $statusFilter === $val
                        ? 'bg-[#00baff] text-white border-[#00baff]'
                        : 'bg-white text-gray-600 border-gray-200 hover:border-[#00baff] hover:text-[#00baff]' }}"
            >
                {{ $label }}
                @if($val && ($pipeline[$val] ?? 0))
                    <span class="ml-1 {{ $statusFilter === $val ? 'opacity-80' : 'text-gray-400' }}">
                        ({{ $pipeline[$val] }})
                    </span>
                @endif
            </button>
        @endforeach
    </div>

    {{-- ─── Flash ──────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-[10px] px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-[10px] px-4 py-3 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- ─── Main layout: list + detail ────────────────────── --}}
    <div class="flex gap-5 items-start">

        {{-- Project list (left) --}}
        <div class="w-full {{ $selected ? 'hidden lg:block lg:w-72 flex-shrink-0' : '' }} space-y-3">
            @forelse($projects as $project)
                @php
                    $isSelected = $selected && $selected->id === $project->id;
                    $statusColor = match($project->status) {
                        'published'    => 'bg-blue-100 text-blue-700',
                        'accepted'     => 'bg-indigo-100 text-indigo-700',
                        'in_progress'  => 'bg-yellow-100 text-yellow-700',
                        'delivered'    => 'bg-orange-100 text-orange-700',
                        'completed'    => 'bg-green-100 text-green-700',
                        'cancelled'    => 'bg-red-100 text-red-600',
                        'em_moderacao' => 'bg-purple-100 text-purple-700',
                        default        => 'bg-gray-100 text-gray-600',
                    };
                    $done = $project->milestones->where('completed', true)->count();
                    $total = $project->milestones->count();
                @endphp
                <button
                    wire:click="selectService({{ $project->id }})"
                    class="w-full text-left bg-white rounded-2xl border p-4 transition
                        {{ $isSelected ? 'border-[#00baff] ring-1 ring-[#00baff]/30 shadow-sm' : 'border-gray-200 hover:border-[#00baff]/40 hover:shadow-sm' }}"
                >
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-semibold text-gray-800 line-clamp-1 flex-1">{{ $project->titulo }}</p>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $statusColor }} flex-shrink-0">
                            {{ $statusLabels[$project->status] ?? $project->status }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ $project->freelancer ? $project->freelancer->name : 'Sem freelancer' }}
                    </p>
                    @if($total > 0)
                        <div class="mt-2">
                            <div class="flex justify-between text-xs text-gray-400 mb-1">
                                <span>{{ $done }}/{{ $total }} marcos</span>
                                <span>{{ $total > 0 ? round($done/$total*100) : 0 }}%</span>
                            </div>
                            <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-[#00baff] rounded-full transition-all" style="width: {{ $total > 0 ? round($done/$total*100) : 0 }}%"></div>
                            </div>
                        </div>
                    @endif
                    <p class="text-xs text-gray-400 mt-2">{{ $project->updated_at->diffForHumans() }}</p>
                </button>
            @empty
                <div class="bg-white rounded-2xl border border-gray-200 p-8 text-center">
                    <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776"/>
                    </svg>
                    <p class="text-sm text-gray-500 font-medium">Nenhum projecto encontrado</p>
                    <a href="{{ route('client.briefing') }}" class="btn-primary mt-3 text-xs">Criar projecto</a>
                </div>
            @endforelse
        </div>

        {{-- ─── Detail panel (right) ──────────────────────── --}}
        @if($selected)
            @php
                $statusSteps = ['published','accepted','in_progress','delivered','completed'];
                $currentStep = array_search($selected->status, $statusSteps);
                $stepLabels  = ['Publicado','Aceite','Em Andamento','Entregue','Concluído'];
                $doneMilestones  = $selected->milestones->where('completed', true)->count();
                $totalMilestones = $selected->milestones->count();
            @endphp
            <div class="flex-1 min-w-0 space-y-4" x-data="{ tab: '{{ in_array($selected->status, ['published','accepted']) ? 'proposals' : ($selected->status === 'delivered' ? 'entrega' : 'milestones') }}' }">

                {{-- Back on mobile --}}
                <button wire:click="$set('selectedServiceId', null)" class="lg:hidden btn-outline text-xs flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Voltar à lista
                </button>

                {{-- Header card --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-5 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">{{ $selected->titulo }}</h2>
                            <div class="flex items-center gap-2 mt-1">
                                @php
                                    $sc = match($selected->status) {
                                        'published'    => 'bg-blue-100 text-blue-700',
                                        'accepted'     => 'bg-indigo-100 text-indigo-700',
                                        'in_progress'  => 'bg-yellow-100 text-yellow-700',
                                        'delivered'    => 'bg-orange-100 text-orange-700',
                                        'completed'    => 'bg-green-100 text-green-700',
                                        'cancelled'    => 'bg-red-100 text-red-600',
                                        'em_moderacao' => 'bg-purple-100 text-purple-700',
                                        default        => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp
                                <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $sc }}">
                                    {{ $statusLabels[$selected->status] ?? $selected->status }}
                                </span>
                                @if($selected->freelancer)
                                    <span class="text-xs text-gray-500 flex items-center gap-1">
                                        <img src="{{ $selected->freelancer->avatarUrl() }}" class="w-4 h-4 rounded-full object-cover">
                                        {{ $selected->freelancer->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">Sem freelancer</span>
                                @endif
                            </div>
                        </div>

                        {{-- Action buttons --}}
                        <div class="flex flex-wrap gap-2">
                            @if(($selected->freelancer_id || $selected->status === 'negotiating') && in_array($selected->status, ['in_progress', 'delivered']))
                                <a href="{{ route('service.chat', $selected->id) }}" class="btn-outline text-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 0 1-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    Chat
                                </a>
                            @endif
                            @if(in_array($selected->status, ['published','accepted']) && !$selected->freelancer_id)
                                <a href="{{ route('client.matching', $selected->id) }}" class="btn-primary text-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
                                    </svg>
                                    Sugestões
                                </a>
                            @endif
                            @if($selected->status === 'accepted' && $selected->freelancer_id)
                                <button
                                    wire:click="confirmarInicio({{ $selected->id }})"
                                    wire:confirm="Confirma o início do projecto? O pagamento será retido em escrow até à aprovação da entrega."
                                    class="btn-primary text-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z"/>
                                    </svg>
                                    Iniciar Projecto
                                </button>
                            @endif
                            @if($selected->status === 'revision_requested')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 border border-red-200 text-red-700 text-xs font-semibold">
                                    <span class="w-2 h-2 rounded-full bg-red-400 animate-pulse"></span>
                                    Revisão pedida — aguardando nova entrega
                                </span>
                            @endif
                            @if($selected->status === 'delivered')
                                <button wire:click="approveDelivery({{ $selected->id }})" class="btn-primary text-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                    </svg>
                                    Libertar Pagamento
                                </button>
                                <button wire:click="requestRevision({{ $selected->id }})" class="btn-outline text-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                                    </svg>
                                    Solicitar Revisão
                                </button>
                            @endif
                            @if($selected->status === 'completed' && !$hasReview)
                                <a href="{{ route('service.review.leave', $selected->id) }}" class="btn-primary text-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/>
                                    </svg>
                                    Avaliar
                                </a>
                            @elseif($selected->status === 'completed' && $hasReview)
                                <span class="inline-flex items-center gap-1 text-xs text-green-700 bg-green-50 border border-green-200 rounded-lg px-3 py-1.5 font-medium">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                    Avaliado
                                </span>
                            @endif
                            @if($selected->status === 'em_moderacao')
                                <a href="{{ route('service.dispute', $selected->id) }}" class="btn-outline text-xs text-amber-700 border-amber-300 bg-amber-50 hover:bg-amber-100 font-semibold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z"/>
                                    </svg>
                                    Ver mensagem da moderação
                                </a>
                            @endif
                            @if($selected->freelancer_id && in_array($selected->status, ['in_progress','delivered']))
                                <a href="{{ route('service.dispute', $selected->id) }}" class="btn-outline text-xs text-orange-600 border-orange-200 hover:bg-orange-50">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                                    </svg>
                                    Disputar
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Progress stepper --}}
                    @if(!in_array($selected->status, ['cancelled','em_moderacao']))
                        <div class="flex items-center gap-0 overflow-x-auto">
                            @foreach($statusSteps as $i => $step)
                                @php $active = $currentStep !== false && $i <= $currentStep; @endphp
                                <div class="flex items-center flex-shrink-0">
                                    <div class="flex flex-col items-center gap-1">
                                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                                            {{ $active ? 'bg-[#00baff] text-white' : 'bg-gray-100 text-gray-400' }}">
                                            @if($active && $i < $currentStep)
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                            @else
                                                {{ $i + 1 }}
                                            @endif
                                        </div>
                                        <span class="text-xs text-gray-500 whitespace-nowrap">{{ $stepLabels[$i] }}</span>
                                    </div>
                                    @if($i < count($statusSteps) - 1)
                                        <div class="h-0.5 w-8 sm:w-12 mx-1 mb-4 {{ $active && $currentStep > $i ? 'bg-[#00baff]' : 'bg-gray-200' }}"></div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Tab navigation --}}
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <div class="flex border-b border-gray-100 overflow-x-auto">
                        @if(in_array($selected->status, ['published', 'accepted']))
                        <button @click="tab = 'proposals'"
                            :class="tab === 'proposals' ? 'border-b-2 border-[#00baff] text-[#00baff] font-medium' : 'text-gray-500 hover:text-gray-700'"
                            class="px-5 py-3 text-sm transition whitespace-nowrap">
                            Propostas
                            @if($candidates->count() > 0)
                                <span class="ml-1 text-xs px-1.5 py-0.5 rounded-full bg-[#00baff]/10 text-[#00baff]">{{ $candidates->whereIn('status', ['pending','proposal_sent','invited'])->count() }}</span>
                            @endif
                        </button>
                        @endif
                        @if(in_array($selected->status, ['delivered','completed']))
                        <button @click="tab = 'entrega'"
                            :class="tab === 'entrega' ? 'border-b-2 border-[#00baff] text-[#00baff] font-medium' : 'text-gray-500 hover:text-gray-700'"
                            class="px-5 py-3 text-sm transition whitespace-nowrap font-semibold">
                            📦 Entrega
                        </button>
                        @endif
                        <button @click="tab = 'milestones'"
                            :class="tab === 'milestones' ? 'border-b-2 border-[#00baff] text-[#00baff] font-medium' : 'text-gray-500 hover:text-gray-700'"
                            class="px-5 py-3 text-sm transition whitespace-nowrap">
                            Marcos
                            @if($totalMilestones > 0)
                                <span class="ml-1 text-xs text-gray-400">({{ $doneMilestones }}/{{ $totalMilestones }})</span>
                            @endif
                        </button>
                        <button @click="tab = 'attachments'"
                            :class="tab === 'attachments' ? 'border-b-2 border-[#00baff] text-[#00baff] font-medium' : 'text-gray-500 hover:text-gray-700'"
                            class="px-5 py-3 text-sm transition whitespace-nowrap">
                            Anexos
                            @if($selected->attachments->count() > 0)
                                <span class="ml-1 text-xs text-gray-400">({{ $selected->attachments->count() }})</span>
                            @endif
                        </button>
                        <button @click="tab = 'briefing'"
                            :class="tab === 'briefing' ? 'border-b-2 border-[#00baff] text-[#00baff] font-medium' : 'text-gray-500 hover:text-gray-700'"
                            class="px-5 py-3 text-sm transition whitespace-nowrap">
                            Briefing
                        </button>
                    </div>

                    {{-- ─── PROPOSTAS TAB ───────────────────────── --}}
                    @if(in_array($selected->status, ['published', 'accepted']))
                    <div x-show="tab === 'proposals'" class="p-5 space-y-4">

                        @if($candidates->isEmpty())
                            <div class="text-center py-8">
                                <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                                </svg>
                                <p class="text-sm text-gray-400">Ainda não há propostas para este projecto.</p>
                                <a href="{{ route('client.matching', $selected->id) }}" class="btn-primary mt-3 text-xs inline-flex">Sugerir Freelancers</a>
                            </div>
                        @else
                            <p class="text-xs text-gray-500">{{ $candidates->whereIn('status', ['pending','proposal_sent','invited'])->count() }} proposta(s) recebida(s) · máx. 6</p>

                            <div class="space-y-3">
                                @foreach($candidates as $candidate)
                                    @php
                                        $fl = $candidate->freelancer;
                                        $fp = $fl?->freelancerProfile;
                                        $isPending = in_array($candidate->status, ['pending', 'proposal_sent', 'invited']);
                                        $statusBadge = match($candidate->status) {
                                            'pending'       => ['Candidatura', 'bg-blue-100 text-blue-700'],
                                            'proposal_sent' => ['Proposta', 'bg-indigo-100 text-indigo-700'],
                                            'invited'       => ['Convidado', 'bg-yellow-100 text-yellow-700'],
                                            'chosen'        => ['Escolhido', 'bg-green-100 text-green-700'],
                                            'rejected'      => ['Rejeitado', 'bg-red-100 text-red-500'],
                                            default         => [$candidate->status, 'bg-gray-100 text-gray-500'],
                                        };
                                    @endphp
                                    <div class="rounded-2xl border {{ $isPending ? 'border-gray-200' : 'border-gray-100 opacity-60' }} p-4 space-y-3">
                                        {{-- Cabeçalho do candidato --}}
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                                <a href="{{ route('freelancer.show', $fl->id) }}" target="_blank">
                                                    <img src="{{ $fl->avatarUrl() }}" alt="{{ $fl->name }}"
                                                        class="w-10 h-10 rounded-full object-cover ring-2 ring-[#00baff]/20 flex-shrink-0">
                                                </a>
                                                <div class="min-w-0">
                                                    <a href="{{ route('freelancer.show', $fl->id) }}" target="_blank"
                                                        class="text-sm font-semibold text-gray-900 hover:text-[#00baff] truncate block">
                                                        {{ $fl->name }}
                                                    </a>
                                                    @if($fp?->headline)
                                                        <p class="text-xs text-gray-400 truncate">{{ $fp->headline }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <span class="text-xs px-2.5 py-1 rounded-full font-medium flex-shrink-0 {{ $statusBadge[1] }}">
                                                {{ $statusBadge[0] }}
                                            </span>
                                        </div>

                                        {{-- Valor proposto --}}
                                        @if($candidate->proposal_value)
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs text-gray-500">Valor proposto:</span>
                                                <span class="text-sm font-bold text-[#00baff]">{{ number_format($candidate->proposal_value, 2) }} Kz</span>
                                            </div>
                                        @endif

                                        {{-- Mensagem da proposta --}}
                                        @if($candidate->proposal_message)
                                            <div class="bg-gray-50 rounded-[10px] border border-gray-100 p-3">
                                                <p class="text-xs text-gray-500 font-medium mb-1">Mensagem:</p>
                                                <p class="text-sm text-gray-700 leading-relaxed">{{ $candidate->proposal_message }}</p>
                                            </div>
                                        @endif

                                        {{-- Botões de ação (apenas para candidatos pendentes) --}}
                                        @if($isPending)
                                            <div class="flex gap-2 pt-1 flex-wrap">
                                                <a href="{{ route('service.chat', $selected->id) }}"
                                                    class="btn-outline text-xs flex-1 justify-center min-w-[80px]">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 0 1-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                                    </svg>
                                                    Chat
                                                </a>
                                                <button
                                                    wire:click="escolherFreelancer({{ $selected->id }}, {{ $fl->id }})"
                                                    wire:confirm="Confirma a escolha de {{ $fl->name }} para este projecto? Os outros candidatos serão notificados."
                                                    class="btn-primary text-xs flex-1 justify-center min-w-[80px]">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                                    </svg>
                                                    Aceitar
                                                </button>
                                                <button
                                                    wire:click="rejeitarFreelancer({{ $selected->id }}, {{ $fl->id }})"
                                                    wire:confirm="Rejeitar a proposta de {{ $fl->name }}?"
                                                    class="btn-outline text-xs flex-1 justify-center text-red-500 border-red-200 hover:bg-red-50 min-w-[80px]">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                    Rejeitar
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    @endif

                    {{-- ─── ENTREGA TAB ─────────────────────────── --}}
                    @if(in_array($selected->status, ['delivered','completed']))
                    <div x-show="tab === 'entrega'" class="p-5 space-y-4">
                        @php
                            $deliveryFiles = $selected->attachments->filter(fn($a) => str_starts_with($a->path, 'deliveries/'));
                        @endphp

                        {{-- Delivery message --}}
                        @if($selected->delivery_message)
                        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4">
                            <p class="text-xs font-semibold text-blue-600 mb-2 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 0 1-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                Mensagem do freelancer
                            </p>
                            <p class="text-sm text-gray-800 leading-relaxed whitespace-pre-line">{{ $selected->delivery_message }}</p>
                        </div>
                        @endif

                        {{-- Delivery files --}}
                        @if($deliveryFiles->isNotEmpty())
                        <div>
                            <p class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Ficheiro(s) entregue(s)</p>
                            <div class="space-y-2">
                                @foreach($deliveryFiles as $att)
                                @php
                                    $ext = strtolower(pathinfo($att->filename, PATHINFO_EXTENSION));
                                    $isImg = in_array($ext, ['jpg','jpeg','png','gif','webp','bmp']);
                                @endphp
                                <div class="flex items-center gap-3 bg-white rounded-2xl border border-gray-200 p-4">
                                    <div class="w-10 h-10 rounded-xl bg-[#00baff]/10 flex items-center justify-center flex-shrink-0 text-xl">
                                        @if($isImg) 🖼️ @else 📄 @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $att->filename }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ number_format($att->size / 1024, 1) }} KB · Entregue {{ $att->created_at->diffForHumans() }}</p>
                                    </div>
                                    <a href="{{ Storage::url($att->path) }}" target="_blank"
                                        class="btn-primary text-xs flex-shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                        Descarregar
                                    </a>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if(!$selected->delivery_message && $deliveryFiles->isEmpty())
                            <p class="text-sm text-gray-400 text-center py-8">Nenhum conteúdo de entrega encontrado.</p>
                        @endif

                        {{-- Action buttons reminder --}}
                        @if($selected->status === 'revision_requested')
                        <div class="bg-red-50 border border-red-200 rounded-2xl p-4 flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full bg-red-400 animate-pulse flex-shrink-0"></span>
                            <div>
                                <p class="text-sm font-semibold text-red-700">Revisão solicitada</p>
                                <p class="text-xs text-red-500 mt-0.5">Aguardando nova entrega do freelancer. Receberá uma notificação quando estiver pronto.</p>
                            </div>
                        </div>
                        @elseif($selected->status === 'delivered')
                        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center gap-3">
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-amber-800">Reviu a entrega?</p>
                                <p class="text-xs text-amber-600 mt-0.5">Aprove para libertar o pagamento ao freelancer, ou solicite uma revisão.</p>
                            </div>
                            <div class="flex gap-2 flex-shrink-0">
                                <button wire:click="requestRevision({{ $selected->id }})" class="btn-outline text-xs text-amber-700 border-amber-300 hover:bg-amber-100">
                                    Solicitar Revisão
                                </button>
                                <button wire:click="approveDelivery({{ $selected->id }})" class="btn-primary text-xs">
                                    Libertar Pagamento
                                </button>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    {{-- ─── MILESTONES TAB ──────────────────────── --}}
                    <div x-show="tab === 'milestones'" class="p-5 space-y-4">

                        {{-- Progress bar --}}
                        @if($totalMilestones > 0)
                            <div>
                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                    <span>Progresso geral</span>
                                    <span>{{ $doneMilestones }}/{{ $totalMilestones }} ({{ round($doneMilestones/$totalMilestones*100) }}%)</span>
                                </div>
                                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-[#00baff] to-[#0099d6] rounded-full transition-all"
                                         style="width: {{ round($doneMilestones/$totalMilestones*100) }}%"></div>
                                </div>
                            </div>
                        @endif

                        {{-- Milestone list --}}
                        <div class="space-y-2">
                            @forelse($selected->milestones as $milestone)
                                <div class="flex items-start gap-3 p-3 rounded-[10px] border
                                    {{ $milestone->completed ? 'bg-green-50 border-green-100' : 'bg-gray-50 border-gray-100' }}">
                                    <button wire:click="toggleMilestone({{ $milestone->id }})"
                                        class="mt-0.5 flex-shrink-0 w-5 h-5 rounded border-2 flex items-center justify-center transition
                                            {{ $milestone->completed ? 'bg-green-500 border-green-500' : 'border-gray-300 hover:border-[#00baff]' }}">
                                        @if($milestone->completed)
                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                            </svg>
                                        @endif
                                    </button>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium {{ $milestone->completed ? 'line-through text-gray-400' : 'text-gray-800' }}">
                                            {{ $milestone->title }}
                                        </p>
                                        @if($milestone->description)
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $milestone->description }}</p>
                                        @endif
                                        <div class="flex items-center gap-3 mt-1">
                                            @if($milestone->due_date)
                                                <span class="text-xs {{ $milestone->due_date->isPast() && !$milestone->completed ? 'text-red-500' : 'text-gray-400' }}">
                                                    <svg class="inline w-3 h-3 mr-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5"/>
                                                    </svg>
                                                    {{ $milestone->due_date->format('d/m/Y') }}
                                                </span>
                                            @endif
                                            @if($milestone->completed && $milestone->completed_at)
                                                <span class="text-xs text-green-500">Concluído {{ $milestone->completed_at->diffForHumans() }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <button wire:click="deleteMilestone({{ $milestone->id }})"
                                        wire:confirm="Remover este marco?"
                                        class="text-gray-300 hover:text-red-400 transition flex-shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            @empty
                                <p class="text-sm text-gray-400 text-center py-4">Nenhum marco adicionado ainda.</p>
                            @endforelse
                        </div>

                        {{-- Add milestone form --}}
                        <div class="border border-dashed border-gray-200 rounded-[10px] p-4 space-y-3" x-data="{ open: false }">
                            <button @click="open = !open" class="btn-outline text-xs w-full justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                </svg>
                                Adicionar Marco
                            </button>
                            <div x-show="open" x-transition class="space-y-3">
                                <input wire:model="milestoneTitle" type="text" placeholder="Título do marco *"
                                    class="w-full rounded-[10px] border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
                                @error('milestoneTitle') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                                <input wire:model="milestoneDate" type="date"
                                    class="w-full rounded-[10px] border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff]">
                                <textarea wire:model="milestoneDesc" rows="2" placeholder="Descrição (opcional)"
                                    class="w-full rounded-[10px] border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#00baff]/30 focus:border-[#00baff] resize-none"></textarea>
                                <div class="flex gap-2">
                                    <button wire:click="addMilestone" class="btn-primary text-xs">Guardar Marco</button>
                                    <button @click="open = false" class="btn-outline text-xs">Cancelar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ─── ATTACHMENTS TAB ─────────────────────── --}}
                    <div x-show="tab === 'attachments'" class="p-5 space-y-4">

                        {{-- Upload --}}
                        <x-file-input wire:model="attachmentFile" label="📎 Enviar ficheiro" loading-target="attachmentFile">
                            @error('attachmentFile') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            <p class="text-xs text-gray-500">Qualquer tipo · máx. 20 MB</p>
                        </x-file-input>
                        @if($attachmentFile)
                            <button wire:click="uploadAttachment" class="btn-primary text-xs">Enviar Ficheiro</button>
                        @endif

                        {{-- List --}}
                        <div class="space-y-2">
                            @forelse($selected->attachments->filter(fn($a) => !str_starts_with($a->path, 'deliveries/')) as $att)
                                <div class="flex items-center gap-3 bg-gray-50 rounded-[10px] border border-gray-100 p-3">
                                    <div class="w-8 h-8 rounded-lg bg-[#00baff]/10 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-[#00baff]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 002.112 2.13"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-800 truncate">{{ $att->filename }}</p>
                                        <p class="text-xs text-gray-400">{{ number_format($att->size / 1024, 1) }} KB · {{ $att->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <a href="{{ Storage::url($att->path) }}" target="_blank"
                                            class="text-xs text-[#00baff] hover:underline">Baixar</a>
                                        <button wire:click="deleteAttachment({{ $att->id }})"
                                            wire:confirm="Remover este ficheiro?"
                                            class="text-gray-300 hover:text-red-400 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-400 text-center py-4">Nenhum ficheiro enviado ainda.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- ─── BRIEFING TAB ────────────────────────── --}}
                    <div x-show="tab === 'briefing'" class="p-5">
                        <div class="bg-gray-50 rounded-[10px] border border-gray-100 p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-semibold text-gray-700">Descrição do Projecto</h3>
                                <a href="{{ route('client.briefing', ['edit' => $selected->id]) }}" class="btn-outline text-xs">Editar</a>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $selected->briefing }}</p>
                        </div>
                        @if($selected->valor)
                            <div class="mt-4">
                                <div class="bg-gray-50 rounded-[10px] border border-gray-100 p-3">
                                    <p class="text-xs text-gray-500">Valor do projeto</p>
                                    <p class="text-sm font-bold text-gray-800 mt-1">{{ number_format($selected->valor, 2, ',', '.') }} Kz</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
