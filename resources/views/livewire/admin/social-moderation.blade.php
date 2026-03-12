<div>
    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm font-medium">{{ session('success') }}</div>
    @endif

    {{-- Tabs / filters --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        {{-- Status tabs --}}
        <div class="flex gap-1 bg-gray-100 rounded-xl p-1">
            @foreach(['pendente' => 'Pendentes', 'resolvido' => 'Resolvidas', 'ignorado' => 'Ignoradas', '' => 'Todas'] as $val => $label)
                <button wire:click="$set('filterStatus', '{{ $val }}')"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition
                        {{ $filterStatus === $val ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    {{ $label }}
                    @if($val !== '' && isset($counts[$val]) && $counts[$val] > 0)
                        <span class="ml-1 bg-{{ $val === 'pendente' ? 'red' : 'gray' }}-400 text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ $counts[$val] }}</span>
                    @endif
                </button>
            @endforeach
        </div>

        {{-- Type filter --}}
        <select wire:model="filterType"
            class="text-xs border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#00baff]/30">
            <option value="">Todos os tipos</option>
            <option value="post">Posts</option>
            <option value="user">Utilizadores</option>
        </select>
    </div>

    {{-- Reports table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <th class="pb-3 text-left">Tipo</th>
                    <th class="pb-3 text-left">ID</th>
                    <th class="pb-3 text-left">Denunciado por</th>
                    <th class="pb-3 text-left">Motivo</th>
                    <th class="pb-3 text-left">Status</th>
                    <th class="pb-3 text-left">Data</th>
                    <th class="pb-3 text-left">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($reports as $report)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="py-3 pr-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $report->reportable_type === 'post' ? 'bg-blue-50 text-blue-600' : 'bg-orange-50 text-orange-600' }}">
                                {{ $report->reportable_type === 'post' ? 'Post' : 'Utilizador' }}
                            </span>
                        </td>
                        <td class="py-3 pr-3 font-mono text-gray-500">#{{ $report->reportable_id }}</td>
                        <td class="py-3 pr-3 text-gray-700">{{ $report->reporter?->name ?? '—' }}</td>
                        <td class="py-3 pr-3 text-gray-600 max-w-xs truncate" title="{{ $report->reason }}">{{ $report->reason }}</td>
                        <td class="py-3 pr-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $report->status === 'pendente' ? 'bg-yellow-50 text-yellow-700' : ($report->status === 'resolvido' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500') }}">
                                {{ ucfirst($report->status) }}
                            </span>
                        </td>
                        <td class="py-3 pr-3 text-gray-400 text-xs">{{ $report->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3">
                            <div class="flex items-center gap-2">
                                <button wire:click="viewReport({{ $report->id }})"
                                    class="text-xs text-[#00baff] hover:underline font-medium">
                                    Rever
                                </button>
                                @if($report->status === 'pendente')
                                    @if($report->reportable_type === 'post')
                                        <button wire:click="removeContent({{ $report->id }})"
                                            wire:confirm="Tem a certeza que quer remover este conteúdo?"
                                            class="text-xs text-red-500 hover:underline font-medium">
                                            Remover
                                        </button>
                                    @endif
                                    <button wire:click="ignore({{ $report->id }})"
                                        class="text-xs text-gray-400 hover:underline">
                                        Ignorar
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center text-gray-400 text-sm">
                            Nenhuma denúncia encontrada.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $reports->links() }}
    </div>

    {{-- View/note modal --}}
    @if($viewingReportId)
        @php $r = $reports->firstWhere('id', $viewingReportId) ?? \App\Models\SocialReport::find($viewingReportId); @endphp
        @if($r)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="text-lg font-bold">Denúncia #{{ $r->id }}</h3>
                    <button wire:click="closeReport" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <dl class="space-y-3 text-sm mb-4">
                    <div class="flex gap-2"><dt class="font-semibold text-gray-500 w-28">Tipo:</dt><dd>{{ $r->reportable_type === 'post' ? 'Post #'.$r->reportable_id : 'Utilizador #'.$r->reportable_id }}</dd></div>
                    <div class="flex gap-2"><dt class="font-semibold text-gray-500 w-28">Denunciado por:</dt><dd>{{ $r->reporter?->name }} ({{ $r->reporter?->email }})</dd></div>
                    <div class="flex gap-2"><dt class="font-semibold text-gray-500 w-28">Motivo:</dt><dd class="text-gray-700">{{ $r->reason }}</dd></div>
                    <div class="flex gap-2"><dt class="font-semibold text-gray-500 w-28">Data:</dt><dd>{{ $r->created_at->format('d/m/Y H:i') }}</dd></div>
                    <div class="flex gap-2"><dt class="font-semibold text-gray-500 w-28">Status:</dt>
                        <dd><span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $r->status === 'pendente' ? 'bg-yellow-50 text-yellow-700' : ($r->status === 'resolvido' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500') }}">
                            {{ ucfirst($r->status) }}</span></dd>
                    </div>
                </dl>

                {{-- Content preview --}}
                @if($r->reportable_type === 'post')
                    @php $post = \App\Models\SocialPost::with('images')->find($r->reportable_id); @endphp
                    @if($post)
                        <div class="bg-gray-50 rounded-xl p-3 mb-4">
                            <p class="text-xs font-semibold text-gray-500 mb-1">Conteúdo do post:</p>
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $post->content }}</p>
                            @if($post->images->isNotEmpty())
                                <div class="flex gap-2 mt-2 flex-wrap">
                                    @foreach($post->images->take(3) as $img)
                                        <img src="{{ Storage::url($img->path) }}" class="w-16 h-16 object-cover rounded-lg">
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                @endif

                {{-- Admin note --}}
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nota interna</label>
                    <textarea wire:model="adminNote" rows="3"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-[#00baff]/40"
                        placeholder="Observações internas..."></textarea>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button wire:click="saveNote({{ $r->id }})"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
                        Guardar nota
                    </button>
                    @if($r->status === 'pendente')
                        @if($r->reportable_type === 'post')
                            <button wire:click="removeContent({{ $r->id }})"
                                wire:confirm="Tem a certeza que quer remover este conteúdo?"
                                class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded-xl transition">
                                Remover conteúdo
                            </button>
                        @endif
                        <button wire:click="resolve({{ $r->id }})"
                            class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold rounded-xl transition">
                            Marcar resolvida
                        </button>
                        <button wire:click="ignore({{ $r->id }})"
                            class="px-4 py-2 border border-gray-200 text-gray-500 text-xs font-semibold rounded-xl hover:bg-gray-50 transition">
                            Ignorar
                        </button>
                    @endif
                    <button wire:click="closeReport"
                        class="px-4 py-2 border border-gray-200 text-gray-600 text-xs font-medium rounded-xl hover:bg-gray-50 transition ml-auto">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
        @endif
    @endif

</div>
