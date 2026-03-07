@if(isset($steps))
<div class="bg-white border border-[#00baff]/30 rounded-2xl shadow-sm mb-6 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 bg-gradient-to-r from-[#00baff]/10 to-transparent">
        <div>
            <h2 class="font-semibold text-gray-800">Configure sua conta</h2>
            <p class="text-sm text-gray-500">Complete os passos para começar a receber projetos</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-right">
                <span class="text-2xl font-bold text-[#00baff]">{{ $completed }}</span>
                <span class="text-gray-400 text-sm">/{{ $total }}</span>
            </div>
            <button wire:click="dismiss" class="text-gray-400 hover:text-gray-600" title="Fechar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Progress bar --}}
    <div class="h-1 bg-gray-100">
        <div class="h-1 bg-[#00baff] transition-all duration-500"
             style="width: {{ round($completed / $total * 100) }}%"></div>
    </div>

    <div class="divide-y divide-gray-100">
        @foreach($steps as $step)
        <a href="{{ $step['link'] }}"
           class="flex items-center gap-4 px-5 py-3 hover:bg-gray-50 transition group {{ $step['done'] ? 'opacity-60' : '' }}">
            {{-- Status icon --}}
            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center
                {{ $step['done'] ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400 group-hover:bg-[#00baff]/10 group-hover:text-[#00baff]' }}">
                @if($step['done'])
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"/>
                    </svg>
                @endif
            </div>

            <div class="flex-1 min-w-0">
                <div class="text-sm font-medium {{ $step['done'] ? 'text-gray-500 line-through' : 'text-gray-800' }}">
                    {{ $step['label'] }}
                </div>
                @if(!$step['done'])
                    <div class="text-xs text-gray-400">{{ $step['descr'] }}</div>
                @endif
            </div>

            @if(!$step['done'])
            <svg class="w-4 h-4 text-gray-300 group-hover:text-[#00baff] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
            @endif
        </a>
        @endforeach
    </div>
</div>
@endif
