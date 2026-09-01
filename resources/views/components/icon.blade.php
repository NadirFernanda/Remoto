@php
    // Lucide-inspired inline icon system for Blade.
    // Usage: @include('components.icon', ['name' => 'save', 'class' => 'mr-2'])
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
    @case('star')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2.75l2.8 5.67 6.25.91-4.53 4.42 1.07 6.24L12 0.35l-5.59 2.94 1.07-6.24L2.95 9.33l6.25-.91L12 2.75z"></path>
        </svg>
        @break
    @case('sparkles')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 3l1.8 4.7L18.5 9l-4.7 1.3L12 15l-1.8-4.7L5.5 9l4.7-1.3L12 3z"></path>
            <path d="M19 15l.9 2.1L22 18l-2.1.9L19 21l-.9-2.1L16 18l2.1-.9L19 15z"></path>
            <path d="M5 15l.9 2.1L8 18l-2.1.9L5 21l-.9-2.1L2 18l2.1-.9L5 15z"></path>
        </svg>
        @break
    @case('shield-check')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 3l7 3v6c0 4.5-2.8 8.3-7 10-4.2-1.7-7-5.5-7-10V6l7-3z"></path>
            <path d="M9.5 12.5l1.5 1.5 3.5-4"></path>
        </svg>
        @break
    @case('briefcase')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="7" width="18" height="12" rx="2"></rect>
            <path d="M8 7V5a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2"></path>
            <path d="M3 12h18"></path>
        </svg>
        @break
    @case('globe')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="9"></circle>
            <path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"></path>
        </svg>
        @break
    @case('chart')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 18h18"></path>
            <path d="M7 14l3-3 3 2 5-7"></path>
            <path d="M17 6h3v3"></path>
        </svg>
        @break
    @case('store')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 9h16l-1 10H5L4 9z"></path>
            <path d="M8 9V7a4 4 0 1 1 8 0v2"></path>
        </svg>
        @break
    @case('badge-check')
        <svg xmlns="http://www.w3.org/2000/svg" class="icon {{ $c }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <path d="M7 12l3 3 7-7"></path>
            <path d="M12 3.5l7 3v5c0 4-2.7 7.5-7 9.5-4.3-2-7-5.5-7-9.5v-5l7-3z"></path>
        </svg>
        @break
    @default
        {{-- unknown icon --}}
@endswitch
