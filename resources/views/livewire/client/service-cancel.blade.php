<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-red-50/30 pb-16">

    {{-- ── Gradient Header ── --}}
    <div class="bg-gradient-to-r from-red-600 via-rose-600 to-red-500 px-6 py-8 shadow-lg shadow-red-200/40">
        <div class="max-w-2xl mx-auto flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center flex-shrink-0 backdrop-blur-sm">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white leading-tight">Cancelar Pedido</h1>
                <p class="text-red-100 text-sm mt-0.5">Reveja os detalhes antes de confirmar</p>
            </div>
        </div>
    </div>

    <div class="max-w-2xl mx-auto px-4 pt-6 space-y-4">

        {{-- Flash messages --}}
        @if(session('error'))
            <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl font-medium text-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M12 3a9 9 0 1 0 0 18A9 9 0 0 0 12 3z"/></svg>
                {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl font-medium text-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('info'))
            <div class="flex items-center gap-3 p-4 bg-blue-50 border border-blue-200 text-blue-700 rounded-2xl font-medium text-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01"/></svg>
                {{ session('info') }}
            </div>
        @endif

        {{-- Back link --}}
        <a href="{{ route('client.orders') }}"
           class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-800 text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Voltar aos pedidos
        </a>

        {{-- ── Project detail card ── --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

            {{-- Card header: title + status badge --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="font-bold text-slate-800 text-base truncate" id="service-title">{{ $service->titulo }}</span>
                    <button type="button"
                            onclick="document.getElementById('editTitleModal').showModal()"
                            class="flex-shrink-0 px-2.5 py-1 text-xs font-medium bg-sky-50 text-sky-700 border border-sky-200 rounded-full hover:bg-sky-100 transition">
                        Editar título
                    </button>
                </div>
                <span class="flex-shrink-0 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border
                    @if($service->status === 'published') bg-blue-50 text-blue-700 border-blue-200
                    @elseif($service->status === 'cancelled') bg-red-50 text-red-700 border-red-200
                    @elseif($service->status === 'accepted') bg-violet-50 text-violet-700 border-violet-200
                    @elseif($service->status === 'in_progress') bg-amber-50 text-amber-700 border-amber-200
                    @elseif($service->status === 'delivered') bg-teal-50 text-teal-700 border-teal-200
                    @elseif($service->status === 'completed') bg-emerald-50 text-emerald-700 border-emerald-200
                    @else bg-slate-100 text-slate-600 border-slate-200 @endif">
                    @if($service->status === 'published') Publicado
                    @elseif($service->status === 'cancelled') Cancelado
                    @elseif($service->status === 'accepted') Aceite
                    @elseif($service->status === 'in_progress') Em andamento
                    @elseif($service->status === 'delivered') Entregue
                    @elseif($service->status === 'completed') Concluído
                    @else {{ $service->status }} @endif
                </span>
            </div>

            {{-- Briefing --}}
            <div class="px-6 py-5 border-b border-slate-100">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Briefing</p>
                @php
                    $briefing = $service->briefing;
                    $briefingArray = @json_decode($briefing, true);
                @endphp
                <div class="text-sm text-slate-700 leading-relaxed space-y-1">
                    @if(is_array($briefingArray))
                        @if(isset($briefingArray['title']))<p><span class="font-medium text-slate-500">Título:</span> {{ $briefingArray['title'] }}</p>@endif
                        @if(isset($briefingArray['business_type']))<p><span class="font-medium text-slate-500">Tipo de negócio:</span> {{ $briefingArray['business_type'] }}</p>@endif
                        @if(isset($briefingArray['necessity']))<p class="whitespace-pre-line">{{ $briefingArray['necessity'] }}</p>@endif
                    @else
                        <p class="whitespace-pre-line">{{ $briefing }}</p>
                    @endif
                </div>
            </div>

            {{-- Value --}}
            <div class="px-6 py-4 flex items-center justify-between border-b border-slate-100">
                <span class="text-sm font-medium text-slate-500">Valor do projecto</span>
                <span class="text-lg font-bold text-slate-800">{{ number_format($service->valor, 2, ',', '.') }} Kz</span>
            </div>

            {{-- Actions --}}
            <div class="px-6 py-5">
                @if($service->status === 'published')
                    <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl mb-5">
                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M12 3a9 9 0 1 0 0 18A9 9 0 0 0 12 3z"/>
                        </svg>
                        <div class="text-sm text-amber-800">
                            <p class="font-semibold">Tem a certeza?</p>
                            <p class="mt-0.5">Esta ação não pode ser desfeita. O reembolso será processado em até 5 dias úteis.</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <form wire:submit.prevent="cancelService">
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold shadow-sm shadow-red-200 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                                Confirmar cancelamento
                            </button>
                        </form>
                        <a href="{{ route('client.briefing', ['edit' => $service->id]) }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 1 1 2.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Editar pedido
                        </a>
                        <a href="{{ route('service.chat', $service->id) }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition relative">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 0 1-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            Chat do serviço
                            @livewire('chat.chat-badge', ['serviceId' => $service->id], key('chat-badge-'.$service->id))
                        </a>
                    </div>
                @else
                    <p class="text-sm text-slate-500 mb-4">Este pedido não pode ser cancelado no estado actual.</p>
                    <div class="flex flex-wrap gap-3">
                        @if(!in_array($service->status, ['cancelled', 'completed']))
                            <a href="{{ route('service.chat', $service->id) }}"
                               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition relative">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 0 1-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                Chat
                                @livewire('chat.chat-badge', ['serviceId' => $service->id], key('chat-badge-'.$service->id))
                            </a>
                        @endif
                        @if($service->status === 'completed' && !$hasReview)
                            <a href="{{ route('service.review.leave', $service->id) }}"
                               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 0 0 .95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 0 0-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 0 0-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 0 0-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 0 0 .951-.69l1.519-4.674z"/>
                                </svg>
                                Avaliar serviço
                            </a>
                        @elseif($service->status === 'completed' && $hasReview)
                            <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-semibold cursor-default">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                Já avaliado
                            </span>
                        @endif
                        @if(in_array($service->status, ['in_progress', 'delivered']))
                            <a href="{{ route('service.dispute', $service->id) }}"
                               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white border border-red-200 text-red-600 text-sm font-semibold hover:bg-red-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 21v-4m0 0V5a2 2 0 0 1 2-2h6.5l1 1H21l-3 6 3 6H12.5l-1-1H5a2 2 0 0 0-2 2zm9-13.5V9"/>
                                </svg>
                                Abrir disputa
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- ── Edit title modal ── --}}
        <dialog id="editTitleModal" class="rounded-2xl shadow-2xl p-0 max-w-sm w-full backdrop:bg-slate-900/50">
            <form method="dialog" class="p-6 bg-white rounded-2xl flex flex-col gap-4" onsubmit="event.preventDefault(); window.submitEditTitle()">
                <h3 class="font-bold text-slate-800 text-base">Editar título do pedido</h3>
                <input type="text" id="newTitleInput" value="{{ $service->titulo }}" maxlength="100"
                       class="border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-sky-400 focus:border-sky-400 focus:outline-none transition" required>
                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="document.getElementById('editTitleModal').close()"
                            class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-medium hover:bg-slate-200 transition">Cancelar</button>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-sky-500 text-white text-sm font-medium hover:bg-sky-600 transition">Guardar</button>
                </div>
            </form>
        </dialog>
        <script>
        function submitEditTitle(e) {
            if (e) e.preventDefault();
            const input = document.getElementById('newTitleInput');
            const modal = document.getElementById('editTitleModal');
            const titleSpan = document.getElementById('service-title');
            const csrfMeta = document.querySelector('meta[name=csrf-token]');
            if (!input || !modal || !titleSpan) { alert('Elementos do formulário não encontrados. Recarregue a página.'); return; }
            if (!csrfMeta) { alert('CSRF token não encontrado. Recarregue a página.'); return; }
            const newTitle = input.value;
            fetch(window.location.pathname + '/edit-title', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfMeta.content },
                body: JSON.stringify({ titulo: newTitle })
            }).then(resp => resp.json()).then(data => {
                if (data.success) { titleSpan.innerText = newTitle; modal.close(); }
                else { alert('Erro ao guardar o título!'); }
            });
        }
        window.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('#editTitleModal form');
            if (form) form.addEventListener('submit', submitEditTitle);
        });
        </script>
    </div>
</div>