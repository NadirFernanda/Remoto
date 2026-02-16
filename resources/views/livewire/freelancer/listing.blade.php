<div>
    <div class="bg-gray-50 rounded-lg p-4 flex flex-col md:flex-row gap-3 items-center">
        <div class="flex-1 w-full">
            <input type="text" wire:model.debounce.400ms="search" placeholder="Buscar freelancers ou habilidades" class="w-full rounded-full border border-gray-200 px-4 py-3 shadow-sm focus:ring-2 focus:ring-blue-400">
        </div>
        <div class="w-full md:w-64">
            <input type="text" wire:model.defer="skill" placeholder="Filtrar por skill" class="w-full rounded-full border border-gray-200 px-4 py-3 shadow-sm">
        </div>
        <div class="flex-shrink-0">
            <button wire:click="$refresh" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm hover:shadow-md">Filtrar</button>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($freelancers as $freelancer)
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition p-5">
                <div class="flex items-start gap-4">
                    <div class="flex items-center gap-4 no-underline text-current flex-1 cursor-pointer" onclick="Livewire.emit('openPreview', {{ $freelancer->id }})">
                        <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-100">
                            <img src="{{ $freelancer->avatarUrl() }}" alt="{{ $freelancer->name }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <div class="text-lg font-semibold">{{ $freelancer->name }}</div>
                                    @if($freelancer->freelancerProfile && $freelancer->freelancerProfile->headline)
                                        <div class="text-sm text-gray-500">{{ $freelancer->freelancerProfile->headline }}</div>
                                    @endif
                                </div>
                                @if($freelancer->freelancerProfile && $freelancer->freelancerProfile->hourly_rate)
                                    <div class="text-sm font-medium text-right text-gray-800">{{ number_format($freelancer->freelancerProfile->hourly_rate,2) }} <span class="text-xs text-gray-500">{{ $freelancer->freelancerProfile->currency }}</span></div>
                                @endif
                            </div>

                            @if($freelancer->freelancerProfile && $freelancer->freelancerProfile->skills)
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach(array_slice($freelancer->freelancerProfile->skills,0,4) as $skillTag)
                                        <span class="text-xs bg-blue-50 text-blue-700 px-3 py-1 rounded-full">{{ $skillTag }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        @if($freelancer->freelancerProfile && $freelancer->freelancerProfile->summary)
                            {{ Str::limit($freelancer->freelancerProfile->summary, 120) }}
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('freelancer.show', $freelancer->id) }}" class="px-3 py-1.5 text-sm rounded-full bg-white border border-gray-200 hover:bg-gray-50">Ver perfil</a>
                        @auth
                            <button type="button" wire:click="openProposal({{ $freelancer->id }})" class="px-3 py-1.5 text-sm rounded-full bg-blue-600 text-white hover:bg-blue-700">Enviar proposta</button>
                        @else
                            <a href="/login" class="px-3 py-1.5 text-sm rounded-full bg-blue-600 text-white hover:bg-blue-700">Enviar proposta</a>
                        @endauth
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center text-gray-600 py-8">Nenhum freelancer encontrado.</div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $freelancers->links() }}
    </div>
</div>
