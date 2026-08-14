@props(['label' => '', 'hint' => null])

<label class="flex items-center justify-between gap-4 py-2 cursor-pointer">
    <span class="min-w-0">
        <span class="block text-sm font-medium text-gray-700">{{ $label }}</span>
        @if($hint)
            <span class="block text-xs text-gray-400 mt-0.5">{{ $hint }}</span>
        @endif
    </span>
    <span class="relative inline-flex items-center flex-shrink-0">
        <input type="checkbox" {{ $attributes->merge(['class' => 'sr-only peer']) }}>
        <span class="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-5 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#0055ff]"></span>
    </span>
</label>
