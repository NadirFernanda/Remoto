<div x-data x-init="setInterval(() => $wire.$refresh(), 30000)" class="inline-block">
    @if($unread > 0)
        <span class="inline-flex items-center justify-center w-5 h-5 text-xs bg-red-500 text-white rounded-full">{{ $unread > 9 ? '9+' : $unread }}</span>
    @endif
</div>