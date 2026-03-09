{{--
  Reusable file input with client-side preview.
  Usage:
    <x-file-input wire:model="foto" accept="image/*" label="Escolher foto" loading-target="foto">
        @error('foto') <span ...>{{ $message }}</span> @enderror
    </x-file-input>
--}}
@props([
    'label'         => 'Selecionar ficheiro',
    'loadingTarget' => null,
])
@php $uid = 'fi-' . uniqid(); @endphp

<div
    x-data="{
        files: [],
        handleChange(e) {
            this.files = Array.from(e.target.files).map(f => {
                const isImg   = f.type.startsWith('image/');
                const isAudio = f.type.startsWith('audio/');
                return {
                    name:      f.name,
                    ext:       f.name.split('.').pop().toUpperCase(),
                    sizeLabel: f.size > 1048576
                                ? (f.size / 1048576).toFixed(1) + ' MB'
                                : Math.round(f.size / 1024) + ' KB',
                    isImg,
                    isAudio,
                    url: (isImg || isAudio) ? URL.createObjectURL(f) : null,
                };
            });
        }
    }"
    class="space-y-2"
>
    {{--
        Real input hidden with inline CSS (sr-only Tailwind class may not compile in all contexts).
        The <label for="uid"> below opens the file picker natively — no JS needed — so
        Livewire reliably detects the change event via wire:model.
    --}}
    <input
        {{ $attributes->only(['wire:model', 'accept', 'multiple', 'wire:model.live']) }}
        type="file"
        id="{{ $uid }}"
        style="position:absolute;width:1px;height:1px;opacity:0;overflow:hidden"
        @change="handleChange($event)"
    >

    {{-- Styled label — clicking it opens the native file picker (browser behaviour, no JS) --}}
    <label
        for="{{ $uid }}"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-[#00baff] text-[#00baff] bg-white hover:bg-[#00baff]/5 active:scale-95 transition text-sm font-medium cursor-pointer"
    >
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
        </svg>
        <span x-text="files.length ? files.length + ' ficheiro(s) selecionado(s)' : '{{ $label }}'"></span>
    </label>

    {{-- Upload spinner --}}
    @if($loadingTarget)
        <div wire:loading wire:target="{{ $loadingTarget }}" class="flex items-center gap-1.5 text-xs text-[#00baff]">
            <svg class="w-3 h-3 animate-spin" viewBox="0 0 24 24" fill="none">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
            A enviar…
        </div>
    @endif

    {{-- Preview area (client-side, no server round-trip) --}}
    <div class="space-y-2">
        <template x-for="(f, i) in files" :key="i">
            <div>
                {{-- Image preview --}}
                <template x-if="f.isImg">
                    <div class="inline-block">
                        <img :src="f.url"
                             class="max-h-48 max-w-full rounded-2xl border border-gray-200 shadow-sm object-cover">
                        <p class="mt-1 text-xs text-gray-500" x-text="f.name + ' · ' + f.sizeLabel"></p>
                    </div>
                </template>

                {{-- Audio preview --}}
                <template x-if="f.isAudio">
                    <div class="p-3 bg-gray-50 rounded-2xl border border-gray-200">
                        <p class="text-xs font-medium text-gray-700 mb-1.5 truncate" x-text="f.name"></p>
                        <audio :src="f.url" controls class="w-full h-8 rounded-lg"></audio>
                        <p class="text-xs text-gray-400 mt-1" x-text="f.sizeLabel"></p>
                    </div>
                </template>

                {{-- Generic file (PDF, DOCX, etc.) --}}
                <template x-if="!f.isImg && !f.isAudio">
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-2xl border border-gray-200">
                        <svg class="w-9 h-9 text-[#00baff] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                        </svg>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate" x-text="f.name"></p>
                            <p class="text-xs text-gray-400" x-text="f.sizeLabel"></p>
                        </div>
                        <span class="text-xs font-bold bg-[#00baff]/10 text-[#00baff] px-2 py-0.5 rounded-full" x-text="f.ext"></span>
                    </div>
                </template>
            </div>
        </template>
    </div>

    {{-- Slot: error messages, hints --}}
    {{ $slot }}
</div>
