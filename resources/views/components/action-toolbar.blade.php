<div {{ $attributes->merge(['class' => trim('action-row ' . ($attributes->get('class') ?? '')) ]) }}>
    {{ $slot }}
</div>
