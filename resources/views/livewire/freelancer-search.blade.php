<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 py-10">

        <h1 class="text-2xl font-bold text-gray-900 mb-1">Encontrar Freelancers</h1>
        <p class="text-gray-500 text-sm mb-8">Pesquise por habilidade, preço, avaliação e disponibilidade</p>

        <div class="flex flex-col lg:flex-row gap-6">

            {{-- ── Filters sidebar ── --}}
            <aside class="w-full lg:w-64 flex-shrink-0 space-y-5">
                {{-- Search --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Pesquisa</label>
                    <input type="text" wire:model.debounce.400ms="query"
                        placeholder="Nome, headline…"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-300 focus:outline-none">
                </div>

                {{-- Skill --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Habilidade</label>
                    <select wire:model="skill"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-300 focus:outline-none">
                        <option value="">Todas</option>
                        @foreach($allSkills as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Language --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Idioma</label>
                    <select wire:model="language"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-300 focus:outline-none">
                        <option value="">Todos</option>
                        @foreach($allLanguages as $lang)
                            <option value="{{ $lang }}">{{ $lang }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Availability --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Disponibilidade</label>
                    <select wire:model="availability"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-300 focus:outline-none">
                        <option value="">Qualquer</option>
                        <option value="disponivel">Disponível</option>
                        <option value="ocupado">Ocupado</option>
                        <option value="ferias">Férias</option>
                    </select>
                </div>

                {{-- Min Rating --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Avaliação mínima</label>
                    <select wire:model="minRating"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-300 focus:outline-none">
                        <option value="0">Qualquer</option>
                        <option value="3">3★ ou mais</option>
                        <option value="4">4★ ou mais</option>
                        <option value="4.5">4.5★ ou mais</option>
                        <option value="5">Apenas 5★</option>
                    </select>
                </div>

                {{-- Price range --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Taxa/hora (Kz)</label>
                    <div class="flex gap-2 items-center">
                        <input type="number" wire:model.debounce.500ms="minRate" min="0"
                            placeholder="Min"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-300 focus:outline-none">
                        <span class="text-gray-400 text-xs">–</span>
                        <input type="number" wire:model.debounce.500ms="maxRate" min="0"
                            placeholder="Máx"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-300 focus:outline-none">
                    </div>
                </div>

                {{-- Sort --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Ordenar por</label>
                    <select wire:model="sort"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-300 focus:outline-none">
                        <option value="relevancia">Relevância</option>
                        <option value="popularidade">Melhor avaliados</option>
                        <option value="preco_asc">Menor preço</option>
                        <option value="preco_desc">Maior preço</option>
                    </select>
                </div>
            </aside>

            {{-- ── Results grid ── --}}
            <div class="flex-1">
                <div wire:loading class="mb-4 text-sm text-gray-400">A filtrar...</div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                    @forelse($freelancers as $freelancer)
                        @php
                            $fp  = $freelancer->freelancerProfile;
                            $avg = $freelancer->averageRating();
                            $cnt = $freelancer->reviewsReceived->count();
                        @endphp
                        <a href="{{ route('freelancer.show', $freelancer) }}"
                           class="group bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md hover:border-cyan-100 hover:-translate-y-0.5 transition-all block">

                            <div class="flex items-center gap-3 mb-3">
                                <img src="{{ $freelancer->avatarUrl() }}"
                                     class="w-12 h-12 rounded-full object-cover border border-gray-200"
                                     alt="{{ $freelancer->name }}">
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-800 truncate group-hover:text-[#00baff] transition-colors">{{ $freelancer->name }}</div>
                                    <div class="text-xs text-gray-400 truncate">{{ $fp->headline ?? 'Freelancer' }}</div>
                                </div>
                            </div>

                            {{-- Rating --}}
                            <div class="flex items-center gap-1 mb-3">
                                <span class="text-yellow-400 text-sm">
                                    @for($i=1;$i<=5;$i++){{ $i <= round($avg) ? '★' : '☆' }}@endfor
                                </span>
                                <span class="text-xs text-gray-500">{{ $avg > 0 ? number_format($avg,1) : '—' }} ({{ $cnt }})</span>
                            </div>

                            {{-- Skills --}}
                            @if(!empty($fp->skills))
                                <div class="flex flex-wrap gap-1 mb-3">
                                    @foreach(array_slice($fp->skills, 0, 4) as $tag)
                                        <span class="text-xs bg-cyan-50 text-cyan-700 border border-cyan-100 rounded-full px-2 py-0.5">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="flex items-center justify-between text-xs text-gray-400 mt-2">
                                @if($fp && $fp->hourly_rate)
                                    <span class="font-semibold text-gray-700">{{ number_format($fp->hourly_rate, 0, ',', '.') }} Kz/h</span>
                                @else
                                    <span>—</span>
                                @endif

                                @if($fp)
                                    @php
                                        $avail = $fp->availability_status ?? 'disponivel';
                                        $availColor = ['disponivel' => 'text-green-500', 'ocupado' => 'text-yellow-500', 'ferias' => 'text-gray-400'][$avail] ?? 'text-gray-400';
                                        $availLabel = ['disponivel' => '● Disponível', 'ocupado' => '● Ocupado', 'ferias' => '● Férias'][$avail] ?? $avail;
                                    @endphp
                                    <span class="{{ $availColor }}">{{ $availLabel }}</span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full bg-white border border-dashed border-gray-200 rounded-2xl p-12 text-center text-gray-400">
                            Nenhum freelancer encontrado com esses filtros.
                        </div>
                    @endforelse
                </div>

                <div class="mt-8">
                    {{ $freelancers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
