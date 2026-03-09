@php
    // Simple inline icon include. Usage: @include('components.icon', ['name' => 'save', 'class' => 'mr-2'])
    $icon = $name ?? null;
    $c = $class ?? '';
    if (! $icon) {
        echo '';
        return;
    }
@endphp

@switch($icon)
    @case('save')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 21H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h11l5 5v9a2 2 0 0 1-2 2z"></path>
            <polyline points="17 21 17 13 7 13 7 21"></polyline>
            <path d="M7 3v4"></path>
        </svg>
        @break
    @case('edit')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 20h9"></path>
            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
        </svg>
        @break
    @case('eye')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
        </svg>
        @break
    @case('chat')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 1 1-7.6-12.3 8.38 8.38 0 0 1 3.8.9"></path>
            <path d="M21 11.5v4a2 2 0 0 1-2 2H7l-4 4V7a2 2 0 0 1 2-2h4"/>
            <path d="M8 11h8M8 15h6" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>
        @break
    @case('check')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 13l4 4L19 7"></path>
        </svg>
        @break
    @case('close')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        @break
    @case('menu')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
        @break
    @case('dots')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M8 10h.01M12 10h.01M16 10h.01"></path>
        </svg>
        @break
    @case('arrow-left')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M15 19l-7-7 7-7"></path>
        </svg>
        @break
    @case('clock')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 8v4l3 3"></path>
            <circle cx="12" cy="12" r="10"></circle>
        </svg>
        @break
    @case('wallet')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h14"></path>
            <rect x="7" y="12" width="10" height="6" rx="1"></rect>
        </svg>
        @break
    @case('user')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M4 21v-2a4 4 0 0 1 3-3.87"></path>
            <circle cx="12" cy="7" r="4"></circle>
        </svg>
        @break
    @case('bell')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5"></path>
            <path d="M13.73 21a2 2 0 01-3.46 0"></path>
        </svg>
        @break
    @case('search')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <path d="M21 21l-4.35-4.35"></path>
        </svg>
        @break
    @case('flag')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
            <line x1="4" y1="22" x2="4" y2="15"></line>
        </svg>
        @break
    @case('send')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="22" y1="2" x2="11" y2="13"></line>
            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
        </svg>
        @break
    @default
        {{-- unknown icon --}}
@endswitch
